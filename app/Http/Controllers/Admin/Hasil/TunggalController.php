<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use App\Models\Partai;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
class TunggalController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%TUNGGAL%');
            })
            ->whereHas('detail_peserta', function ($q) {
                // 1. Pastikan statusnya sudah SELESAI
                $q->where('status_mulai', 2)
                    // 2. Pastikan BUKAN sistem pool (harus punya sudut)
                    ->whereNotNull('sudut')
                    // 3. Tambahan keamanan: pastikan sudut tidak kosong/string kosong
                    ->where('sudut', '!=', '');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(10);

        return view('pages.hasil.tunggal.index', compact('partais'));
    }

    public function exportPdf()
    {
        // 1. Ambil data dengan filter yang lebih longgar tapi tetap akurat
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%TUNGGAL%');
            })
            ->whereHas('detail_peserta', function ($q) {
                // Cukup pastikan partai ini sudah pernah dimainkan (status 2)
                $q->where('status_mulai', 2);
            })
            ->orderBy('no_partai', 'asc')
            ->get();

        // 2. Cek untuk debugging (Opsional: jika masih blank, aktifkan baris bawah ini)
        // return $partais; 

        $pdf = Pdf::loadView('pages.hasil.tunggal.pdf', compact('partais'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekapitulasi_Tunggal_' . date('Ymd_His') . '.pdf');
    }

    public function tetapkanPemenang($id)
    {
        try {
            $partai = Partai::with('detail_peserta.peserta')->findOrFail($id);
            $pesertas = $partai->detail_peserta;

            // Ambil sample dari masing-masing sudut (Ganda punya 2 baris per sudut, kita ambil satu sebagai anchor)
            $biru = $pesertas->whereIn('sudut', ['Biru', 'biru'])->first();
            $merah = $pesertas->whereIn('sudut', ['Merah', 'merah'])->first();

            if (!$biru || !$merah) {
                throw new \Exception("Data peserta biru atau merah tidak lengkap.");
            }

            $disqBiru = (($biru->is_disqualifikasi ?? $biru->is_disqulifikasi ?? 0) == 1);
            $disqMerah = (($merah->is_disqualifikasi ?? $merah->is_disqulifikasi ?? 0) == 1);

            $winnerSudut = null;
            $loserSudut = null;

            // Logika Penentuan
            if ($disqBiru && !$disqMerah) {
                $winnerSudut = 'Biru';
                $loserSudut = 'Merah'; // Terbalik? Tidak, Biru Disq maka Merah Menang
                $winnerSudut = 'Merah';
                $loserSudut = 'Biru';
            } elseif (!$disqBiru && $disqMerah) {
                $winnerSudut = 'Biru';
                $loserSudut = 'Merah';
            } else {
                // Perbandingan Nilai
                if ($biru->total_nilai > $merah->total_nilai) {
                    $winnerSudut = 'Biru';
                    $loserSudut = 'Merah';
                } elseif ($merah->total_nilai > $biru->total_nilai) {
                    $winnerSudut = 'Merah';
                    $loserSudut = 'Biru';
                } else {
                    // Seri: Cek Deviasi (Semakin kecil semakin baik)
                    $winnerSudut = ($biru->nilai_deviasi < $merah->nilai_deviasi) ? 'Biru' : 'Merah';
                    $loserSudut = ($winnerSudut == 'Biru') ? 'Merah' : 'Biru';
                }
            }

            // --- UPDATE DATABASE (KEDUA PESERTA DALAM SATU SUDUT) ---
            DB::transaction(function () use ($id, $winnerSudut) {
                // Reset semua di partai ini
                DB::table('detail_peserta_tanding')->where('id_partai', $id)->update(['is_pemenang' => 0]);
                // Set menang untuk SEMUA yang ada di sudut pemenang (otomatis 2 orang)
                DB::table('detail_peserta_tanding')
                    ->where('id_partai', $id)
                    ->where('sudut', $winnerSudut)
                    ->update(['is_pemenang' => 1]);
            });

            // Format data untuk dikirim ke Display (mengirim info tim ganda)
            $winData = $this->formatResultTunggal($id, $winnerSudut);
            $loseData = $this->formatResultTunggal($id, $loserSudut);

            if (class_exists(\App\Events\TampilkanPemenangTunggal::class)) {
                broadcast(new \App\Events\TampilkanPemenangTunggal($id, $winData, $loseData, 'show'));
            }

            return response()->json([
                'status' => 'success',
                'winner' => $winData,
                'loser' => $loseData
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    protected function formatResultTunggal($id_partai, $sudut)
    {
        // Query menggunakan DB::table persis seperti di Ganda, tapi pakai ->first() karena cuma 1 orang
        $peserta = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->join('peserta', 'detail_peserta_tanding.id_peserta', '=', 'peserta.id_peserta')
            ->where('id_partai', $id_partai)
            ->where('sudut', $sudut)
            ->select('detail_peserta_tanding.*', 'peserta.nama_peserta', 'peserta.kontingen')
            ->first();

        // Jika data tidak ditemukan, kembalikan null
        if (!$peserta) {
            return null;
        }

        // Return array dengan format kunci yang 100% sama dengan Ganda
        return [
            'nama' => $peserta->nama_peserta ?? 'Tidak Diketahui', // Tidak perlu digabung pakai &
            'kontingen' => $peserta->kontingen ?? '-',
            'sudut' => strtoupper($sudut),
            'total' => number_format($peserta->total_nilai ?? 0, 3, '.', ''),
            'median' => number_format($peserta->nilai_median ?? 0, 3, '.', ''),
            'hukuman' => number_format($peserta->hukuman_ketua ?? 0, 2, '.', ''),
            'deviasi' => $peserta->nilai_deviasi ?? 0,
            'waktu' => gmdate('i:s', $peserta->waktu_tampil ?? 0),
            'is_disq' => (($peserta->is_disqualifikasi ?? $peserta->is_disqulifikasi ?? 0) == 1)
        ];
    }

    public function tutupDisplay($id)
    {
        broadcast(new \App\Events\TampilkanPemenangTunggal($id, null, null, 'close'));
        return response()->json(['status' => 'success']);
    }
}
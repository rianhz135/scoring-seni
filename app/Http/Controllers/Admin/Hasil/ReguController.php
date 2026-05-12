<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use App\Models\Partai;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReguController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%REGU%');
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

        return view('pages.hasil.regu.index', compact('partais'));
    }

    public function exportPdf()
    {
        // 1. Ambil data dengan filter yang lebih longgar tapi tetap akurat
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%REGU%');
            })
            ->whereHas('detail_peserta', function ($q) {
                // Cukup pastikan partai ini sudah pernah dimainkan (status 2)
                $q->where('status_mulai', 2);
            })
            ->orderBy('no_partai', 'asc')
            ->get();

        // 2. Cek untuk debugging (Opsional: jika masih blank, aktifkan baris bawah ini)
        // return $partais; 

        $pdf = Pdf::loadView('pages.hasil.regu.pdf', compact('partais'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekapitulasi_Regu_' . date('Ymd_His') . '.pdf');
    }

    public function tetapkanPemenang($id)
    {
        try {
            $partai = Partai::with('detail_peserta.peserta')->findOrFail($id);
            $pesertas = $partai->detail_peserta;

            // Ambil sample dari masing-masing sudut (Regu punya 3 baris per sudut, kita ambil satu sebagai anchor)
            $biru = $pesertas->whereIn('sudut', ['Biru', 'biru', 'biru'])->first();
            $merah = $pesertas->whereIn('sudut', ['Merah', 'merah', 'merah'])->first();

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

            // Format data untuk dikirim ke Display (mengirim info tim regu)
            $winData = $this->formatResultRegu($id, $winnerSudut);
            $loseData = $this->formatResultRegu($id, $loserSudut);

            if (class_exists(\App\Events\TampilkanPemenangRegu::class)) {
                broadcast(new \App\Events\TampilkanPemenangRegu($id, $winData, $loseData, 'show'));
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

    // Helper untuk merapikan data JSON
    protected function formatResultRegu($id_partai, $sudut)
    {
        $tim = DB::table('detail_peserta_tanding')
            ->join('peserta', 'detail_peserta_tanding.id_peserta', '=', 'peserta.id_peserta')
            ->where('id_partai', $id_partai)
            ->where('sudut', $sudut)
            ->select('detail_peserta_tanding.*', 'peserta.nama_peserta', 'peserta.kontingen')
            ->get();

        if ($tim->isEmpty())
            return null;

        $anchor = $tim->first();
        // Gabungkan nama peserta dengan tanda "&"
        $namaGabungan = $tim->pluck('nama_peserta')->implode(' & ');

        return [
            'nama' => $namaGabungan,
            'kontingen' => $anchor->kontingen ?? '-',
            'sudut' => strtoupper($sudut),
            'total' => number_format($anchor->total_nilai, 3, '.', ''),
            'median' => number_format($anchor->nilai_median, 3, '.', ''),
            'hukuman' => number_format($anchor->hukuman_ketua ?? 0, 2, '.', ''),
            'deviasi' => $anchor->nilai_deviasi,
            'waktu' => gmdate('i:s', $anchor->waktu_tampil ?? 0),
            'is_disq' => (($anchor->is_disqualifikasi ?? $anchor->is_disqulifikasi ?? 0) == 1)
        ];
    }

    public function tutupDisplay($id)
    {
        broadcast(new \App\Events\TampilkanPemenangRegu($id, null, null, 'close'));
        return response()->json(['status' => 'success']);
    }
}
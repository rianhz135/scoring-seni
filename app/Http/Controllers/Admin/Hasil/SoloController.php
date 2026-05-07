<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use App\Models\Partai; // Pastikan Model Partai dipanggil
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini ditambahkan di atas
use App\Events\TampilkanPemenangSolo;



class SoloController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            ->whereHas('detail_peserta', function ($q) {
                // Syarat 1: Jangan tampilkan sistem pool (no_urut harus kosong/null)
                $q->whereNull('no_urut')
                    // Syarat 2: Pastikan sudah diplot ke sudut
                    ->whereNotNull('sudut')
                    // Syarat 3: Hanya tampilkan yang sudah selesai (status_mulai = 2)
                    ->where('status_mulai', 2);
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(10); // Sesuaikan jumlah baris per halaman

        // Sesuaikan nama string view dengan struktur folder resources/views Anda
        // Misalnya: 'admin.hasil.solo' atau 'pages.admin.hasil.solo'
        return view('pages.hasil.solo.index', compact('partais'));
    }

    public function exportPdf()
    {
        // Mengambil semua data yang selesai tanpa limit paginasi
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNull('no_urut')
                    ->whereNotNull('sudut')
                    ->where('status_mulai', 2);
            })
            ->orderBy('no_partai', 'asc')
            ->get();

        // Pastikan Anda membuat file blade baru khusus untuk desain cetak
        // (misalnya: resources/views/pages/hasil/solo/pdf.blade.php)
        $pdf = Pdf::loadView('pages.hasil.solo.pdf', compact('partais'))
            ->setPaper('a4', 'landscape'); // Format lanskap agar tabel muat

        // File langsung terbuka di tab baru browser
        return $pdf->stream('Rekapitulasi_Solo_' . date('Ymd_His') . '.pdf');
    }

    public function tetapkanPemenang($id)
    {
        try {
            $partai = \App\Models\Partai::with('detail_peserta.peserta')->findOrFail($id);
            $pesertas = $partai->detail_peserta;

            // Pisahkan Biru & Merah untuk perbandingan
            $biru = $pesertas->firstWhere('sudut', 'Biru') ?? $pesertas->firstWhere('sudut', 'biru');
            $merah = $pesertas->firstWhere('sudut', 'Merah') ?? $pesertas->firstWhere('sudut', 'merah');

            // Cek Disq
            $disqBiru = ($biru->is_disqualifikasi ?? $biru->is_disqulifikasi ?? 0) == 1;
            $disqMerah = ($merah->is_disqualifikasi ?? $merah->is_disqulifikasi ?? 0) == 1;

            $winner = null;
            $loser = null;

            // Logika Penentuan
            if ($disqBiru && !$disqMerah) {
                $winner = $merah;
                $loser = $biru;
            } elseif (!$disqBiru && $disqMerah) {
                $winner = $biru;
                $loser = $merah;
            } elseif (!$disqBiru && !$disqMerah) {
                if ($biru->total_nilai > $merah->total_nilai) {
                    $winner = $biru;
                    $loser = $merah;
                } elseif ($merah->total_nilai > $biru->total_nilai) {
                    $winner = $merah;
                    $loser = $biru;
                } else {
                    // Seri: Cek Deviasi
                    if ($biru->nilai_deviasi < $merah->nilai_deviasi) {
                        $winner = $biru;
                        $loser = $merah;
                    } else {
                        $winner = $merah;
                        $loser = $biru;
                    }
                }
            }

            // Update Database
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')->where('id_partai', $id)->update(['is_pemenang' => 0]);
            if ($winner) {
                \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')->where('id_detail', $winner->id_detail)->update(['is_pemenang' => 1]);
            }

            // Siapkan data yang sudah diformat untuk API dan Broadcast Display
            $winData = $this->formatResult($winner);
            $loseData = $this->formatResult($loser);

            // ----> TRIGGER WEBSOCKET REVERB KE LAYAR DISPLAY <----
            // Menggunakan fully-qualified name agar tidak error jika lupa import 'use' di atas file
            if (class_exists(\App\Events\TampilkanPemenangSolo::class)) {
                broadcast(new \App\Events\TampilkanPemenangSolo($id, $winData, $loseData, 'show'));
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

    public function tutupDisplay($id)
    {
        // Kirim event dengan action 'close'
        broadcast(new \App\Events\TampilkanPemenangSolo($id, null, null, 'close'));
        return response()->json(['status' => 'success']);
    }

    // Helper untuk merapikan data JSON
    protected function formatResult($p)
    {
        if (!$p)
            return null;

        // Pastikan relasi peserta dimuat
        $peserta = $p->peserta;

        return [
            // 1. Perbaikan Nama NULL (Cek semua kemungkinan kolom nama)
            'nama' => $peserta->nama ?? $peserta->nama_lengkap ?? $peserta->nama_peserta ?? 'NAMA TIDAK DITEMUKAN',
            'kontingen' => $peserta->kontingen ?? '-',
            'sudut' => strtoupper($p->sudut), // Pastikan KAPITAL untuk perbandingan di JS

            // 2. Perbaikan Hukuman Undefined (Gunakan key 'hukuman' agar match dengan JS)
            'total' => number_format($p->total_nilai, 3, '.', ''),
            'median' => number_format($p->nilai_median, 3, '.', ''),
            'hukuman' => number_format($p->hukuman_ketua ?? 0, 2, '.', ''),
            'deviasi' => $p->nilai_deviasi,
            'waktu' => gmdate('i:s', $p->waktu_tampil ?? 0),
            'is_disq' => (($p->is_disqualifikasi ?? $p->is_disqulifikasi ?? 0) == 1)
        ];
    }
}
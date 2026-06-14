<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partai;
use Barryvdh\DomPDF\Facade\Pdf;
class HasilPoolController extends Controller
{
    public function index(Request $request)
    {
        $kategori = ['solo', 'ganda', 'tunggal', 'regu'];
        $hasil = [];

        foreach ($kategori as $kat) {
            $hasil['hasil_' . $kat] = Partai::with([
                'kategori',
                'detail_peserta' => function ($query) {
                    $query->where('status_mulai', 2)
                        ->where(function ($q) {
                            $q->whereNull('sudut')->orWhere('sudut', '');
                        })
                        ->whereNotNull('no_urut')
                        // --- KUNCI 1: URUTKAN DISKUALIFIKASI KE PALING BAWAH ---
                        ->orderByRaw('COALESCE(is_disqualifikasi, 0) ASC')
                        ->orderByDesc('total_nilai')
                        ->orderBy('nilai_deviasi', 'asc');
                },
                'detail_peserta.peserta'
            ])
                ->whereHas('kategori', function ($query) use ($kat) {
                    $query->where('nama_kategori', 'LIKE', '%' . $kat . '%');
                })
                ->get()
                ->filter(function ($partai) {
                    return $partai->detail_peserta->count() > 0;
                });
        }

        return view('pages.hasil.pool.index', [
            'hasil_solo' => $hasil['hasil_solo'],
            'hasil_ganda' => $hasil['hasil_ganda'],
            'hasil_tunggal' => $hasil['hasil_tunggal'],
            'hasil_regu' => $hasil['hasil_regu'],
        ]);
    }

    public function tetapkanPemenang($id)
    {
        try {
            $partai = \App\Models\Partai::with('detail_peserta.peserta')->findOrFail($id);

            // Ambil dan urutkan peserta (Leaderboard)
            $pesertas = $partai->detail_peserta()
                ->where('status_mulai', 2)
                ->whereNotNull('no_urut')
                ->where(function ($q) {
                    $q->whereNull('sudut')->orWhere('sudut', '');
                })
                // --- KUNCI 2: URUTKAN DISKUALIFIKASI KE PALING BAWAH DI MODAL/DISPLAY ---
                ->orderByRaw('COALESCE(is_disqualifikasi, 0) ASC')
                ->orderByDesc('total_nilai')
                ->orderBy('nilai_deviasi', 'asc')
                ->get();

            // Format data untuk dikirim ke JS dan Display
            $rankedData = $pesertas->map(function ($p, $index) {
                
                $rawWaktu = $p->waktu_tampil;
                if (is_numeric($rawWaktu)) {
                    $waktuFormatted = gmdate('i:s', (int) $rawWaktu);
                } elseif (empty($rawWaktu)) {
                    $waktuFormatted = '00:00';
                } else {
                    $waktuFormatted = $rawWaktu;
                }

                $isDisq = ($p->is_disqualifikasi == 1);

                return [
                    'rank' => $isDisq ? 'DISQ' : $index + 1, // Ubah Rank jadi teks DISQ jika diskualifikasi
                    'nama' => $p->peserta->nama_peserta ?? 'NAMA KOSONG',
                    'kontingen' => $p->peserta->kontingen ?? '-',
                    'waktu' => $waktuFormatted, 
                    'median' => number_format((float) ($p->nilai_median ?? 0), 3, '.', ''),
                    'deviasi' => $p->nilai_deviasi ?? 0,
                    'hukuman' => number_format((float) ($p->hukuman_ketua ?? 0), 2, '.', ''),
                    // Jika DISQ, kirimkan teks 'DISQ' menggantikan skor
                    'total' => $isDisq ? 'DISQ' : number_format((float) ($p->total_nilai ?? 0), 3, '.', ''),
                    'is_disq' => $isDisq
                ];
            })->toArray();

            // Tembak ke WebSockets (Layar Display)
            if (class_exists(\App\Events\TampilkanPemenangPool::class)) {
                broadcast(new \App\Events\TampilkanPemenangPool($id, $rankedData, 'show'));
            }

            return response()->json([
                'status' => 'success',
                'data' => $rankedData
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function tutupDisplay($id)
    {
        broadcast(new \App\Events\TampilkanPemenangPool($id, [], 'close'));
        return response()->json(['status' => 'success']);
    }

    public function exportPdf()
    {
        // 1. Ambil SEMUA partai kategori Pool (Seni) dan urutkan berdasar no_partai ASC
        $partais = Partai::with([
            'kategori',
            'detail_peserta' => function ($query) {
                // Sorting klasemen / tie-breaker deviasi
                $query->where('status_mulai', 2)
                    ->whereNotNull('no_urut')
                    ->where(function ($q) {
                        $q->whereNull('sudut')->orWhere('sudut', '');
                    })
                    // --- KUNCI: Turunkan Diskualifikasi ke Paling Bawah ---
                    ->orderByRaw('COALESCE(is_disqualifikasi, 0) ASC')
                    ->orderByDesc('total_nilai')
                    ->orderBy('nilai_deviasi', 'asc');
            },
            'detail_peserta.peserta'
        ])
            ->whereHas('kategori', function ($query) {
                $query->where('nama_kategori', 'LIKE', '%solo%')
                    ->orWhere('nama_kategori', 'LIKE', '%ganda%')
                    ->orWhere('nama_kategori', 'LIKE', '%tunggal%')
                    ->orWhere('nama_kategori', 'LIKE', '%regu%');
            })
            ->orderBy('no_partai', 'asc') // PENGURUTAN PARTAI ASCENDING
            ->get()
            ->filter(function ($partai) {
                return $partai->detail_peserta->count() > 0;
            });

        // 2. Rapikan data untuk dikirim ke template PDF
        $dataExport = [];
        foreach ($partais as $partai) {
            $pesertaRanked = $partai->detail_peserta->map(function ($p, $index) {
                $rawWaktu = $p->waktu_tampil;
                if (is_numeric($rawWaktu)) {
                    $waktuFormatted = gmdate('i:s', (int) $rawWaktu);
                } elseif (empty($rawWaktu)) {
                    $waktuFormatted = '00:00';
                } else {
                    $waktuFormatted = $rawWaktu;
                }

                // Cek status diskualifikasi
                $isDisq = ($p->is_disqualifikasi == 1);

                return [
                    'rank' => $isDisq ? 'DISQ' : $index + 1,
                    'nama' => $p->peserta->nama_peserta ?? 'NAMA KOSONG',
                    'kontingen' => $p->peserta->kontingen ?? '-',
                    'waktu' => $waktuFormatted,
                    'median' => number_format((float) ($p->nilai_median ?? 0), 3, '.', ''),
                    'deviasi' => $p->nilai_deviasi ?? 0,
                    'hukuman' => number_format((float) ($p->hukuman_ketua ?? 0), 2, '.', ''),
                    'total' => $isDisq ? 'DISQ' : number_format((float) ($p->total_nilai ?? 0), 3, '.', ''),
                    'is_disq' => $isDisq // Bawa status ini ke tampilan PDF
                ];
            });

            $dataExport[] = [
                'no_partai' => $partai->no_partai,
                'gelanggang' => $partai->gelanggang,
                'kategori' => $partai->kategori->nama_kategori ?? '-',
                'peserta' => $pesertaRanked
            ];
        }

        // 3. Render file PDF dan set kertas menjadi Landscape agar tabelnya muat
        $pdf = Pdf::loadView('pages.hasil.pool.pdf', ['data' => $dataExport])->setPaper('a4', 'landscape');

        return $pdf->stream('Rekapitulasi_Hasil_Sistem_Pool.pdf');
    }
}
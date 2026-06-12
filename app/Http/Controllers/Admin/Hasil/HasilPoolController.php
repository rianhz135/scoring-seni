<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partai;

class HasilPoolController extends Controller
{
    public function index(Request $request)
    {
        $kategori = ['solo', 'ganda', 'tunggal', 'regu'];
        $hasil = [];

        foreach ($kategori as $kat) {
            $hasil['hasil_' . $kat] = Partai::with([
                'kategori',
                // KUNCI PERBAIKAN: Muat juga data peserta di dalam detail_peserta
                'detail_peserta' => function ($query) {
                    $query->where('status_mulai', 2)
                        ->where(function ($q) {
                            $q->whereNull('sudut')->orWhere('sudut', '');
                        })
                        ->whereNotNull('no_urut')
                        ->orderByDesc('total_nilai')
                        ->orderBy('nilai_deviasi', 'asc');
                },
                'detail_peserta.peserta' // <--- MENGATASI NAMA & KONTINGEN KOSONG
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

    // KODE TAMBAHAN DI HasilPoolController.php

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
                ->orderByDesc('total_nilai')
                ->orderBy('nilai_deviasi', 'asc')
                ->get();

            // Format data untuk dikirim ke JS dan Display
            $rankedData = $pesertas->map(function ($p, $index) {
                return [
                    'rank' => $index + 1,
                    'nama' => $p->peserta->nama_peserta ?? 'NAMA KOSONG',
                    'kontingen' => $p->peserta->kontingen ?? '-',
                    'waktu' => $p->waktu_tampil ?? '00:00',
                    'median' => number_format($p->nilai_median, 3, '.', ''),
                    'deviasi' => $p->nilai_deviasi,
                    'hukuman' => number_format($p->hukuman_ketua ?? 0, 2, '.', ''),
                    'total' => number_format($p->total_nilai, 3, '.', '')
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
}
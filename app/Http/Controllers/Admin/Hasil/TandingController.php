<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use App\Models\PartaiTanding;
use App\Models\LogNilaiJuriTanding;
use App\Models\PoinSahTanding;
use App\Models\JatuhanTanding;
use App\Models\PelanggaranTanding;
use App\Models\VerifikasiDewan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TandingController extends Controller
{
    /**
     * Menampilkan daftar rekapitulasi hasil pertandingan tanding.
     */
    public function index(Request $request)
    {
        $query = PartaiTanding::with(['pesertaMerah', 'pesertaBiru', 'pemenang']);

        // Filter status pertandingan jika dipilih
        if ($request->filled('status')) {
            $query->where('status_pertandingan', $request->status);
        }

        // Filter pencarian (nomor partai, kelas, atau nama/kontingen atlet)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_partai', 'LIKE', "%{$search}%")
                  ->orWhere('kelas', 'LIKE', "%{$search}%")
                  ->orWhereHas('pesertaMerah', function ($sq) use ($search) {
                      $sq->where('nama', 'LIKE', "%{$search}%")
                         ->orWhere('kontingen', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('pesertaBiru', function ($sq) use ($search) {
                      $sq->where('nama', 'LIKE', "%{$search}%")
                         ->orWhere('kontingen', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Jumlah item per halaman (default: 10)
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 10;
        }

        $partais = $query->orderBy('no_partai', 'asc')->paginate($perPage)->withQueryString();

        // Precalculate scores untuk setiap partai dalam halaman aktif
        $dewanCtrl = app(\App\Http\Controllers\Admin\Dewan\TandingController::class);
        $partaiScores = [];
        foreach ($partais as $p) {
            $partaiScores[$p->id] = $dewanCtrl->calculateScores($p->id);
        }

        return view('pages.hasil.tanding.index', compact('partais', 'partaiScores'));
    }

    /**
     * Mengambil detail lengkap hasil pertandingan dan log input juri via JSON (untuk Modal).
     */
    public function detail($id)
    {
        $partai = PartaiTanding::with(['pesertaMerah', 'pesertaBiru', 'pemenang'])->findOrFail($id);
        $dewanCtrl = app(\App\Http\Controllers\Admin\Dewan\TandingController::class);

        $scores = $dewanCtrl->calculateScores($partai->id);
        $evaluasi = $dewanCtrl->evaluasiPemenang($partai->id);

        // Log input juri
        $logs = LogNilaiJuriTanding::where('id_partai', $partai->id)
            ->orderBy('id', 'desc')
            ->get();

        // Statistik per juri (Juri 1, 2, 3)
        $juriStats = [];
        for ($j = 1; $j <= 3; $j++) {
            $juriLogs = $logs->where('no_juri', $j);
            $juriStats[$j] = [
                'total_input' => $juriLogs->count(),
                'biru_pukulan' => $juriLogs->where('sudut', 'Biru')->where('jenis_serangan', 'Pukulan')->count(),
                'biru_tendangan' => $juriLogs->where('sudut', 'Biru')->where('jenis_serangan', 'Tendangan')->count(),
                'merah_pukulan' => $juriLogs->where('sudut', 'Merah')->where('jenis_serangan', 'Pukulan')->count(),
                'merah_tendangan' => $juriLogs->where('sudut', 'Merah')->where('jenis_serangan', 'Tendangan')->count(),
                'input_sah' => $juriLogs->where('status_input', 'Berhasil')->count(),
            ];
        }

        // Poin Sah (Konsensus 2/3 Juri)
        $poinSah = PoinSahTanding::where('id_partai', $partai->id)
            ->orderBy('id', 'desc')
            ->get();

        // Pelanggaran & Jatuhan
        $jatuhan = JatuhanTanding::where('id_partai', $partai->id)->orderBy('id', 'desc')->get();
        $pelanggaran = PelanggaranTanding::where('id_partai', $partai->id)->orderBy('id', 'desc')->get();

        // Verifikasi VAR jika ada
        $verifikasi = VerifikasiDewan::with('jawabanJuri')
            ->where('id_partai', $partai->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'partai' => [
                'id' => $partai->id,
                'no_partai' => $partai->no_partai,
                'kelas' => $partai->kelas,
                'fase_babak' => $partai->fase_babak,
                'status_pertandingan' => $partai->status_pertandingan,
                'sudut_pemenang' => $partai->sudut_pemenang,
                'alasan_pemenang' => $partai->alasan_pemenang,
                'pemenang' => $partai->pemenang ? [
                    'nama' => $partai->pemenang->nama,
                    'kontingen' => $partai->pemenang->kontingen,
                ] : null,
                'peserta_biru' => $partai->pesertaBiru ? [
                    'nama' => $partai->pesertaBiru->nama,
                    'kontingen' => $partai->pesertaBiru->kontingen,
                ] : null,
                'peserta_merah' => $partai->pesertaMerah ? [
                    'nama' => $partai->pesertaMerah->nama,
                    'kontingen' => $partai->pesertaMerah->kontingen,
                ] : null,
            ],
            'scores' => $scores,
            'evaluasi' => $evaluasi,
            'juri_stats' => $juriStats,
            'logs' => $logs->map(function ($l) {
                return [
                    'id' => $l->id,
                    'babak' => $l->babak,
                    'no_juri' => $l->no_juri,
                    'sudut' => $l->sudut,
                    'jenis_serangan' => $l->jenis_serangan,
                    'poin' => $l->poin,
                    'status_input' => $l->status_input,
                    'keterangan' => $l->keterangan,
                    'time' => $l->created_at ? $l->created_at->format('H:i:s') : '-',
                ];
            }),
            'poin_sah_count' => $poinSah->count(),
            'jatuhan_count' => $jatuhan->count(),
            'pelanggaran_count' => $pelanggaran->count(),
            'verifikasi_count' => $verifikasi->count(),
        ]);
    }

    /**
     * Ekspor Rekapitulasi Hasil Pertandingan Tanding ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = PartaiTanding::with(['pesertaMerah', 'pesertaBiru', 'pemenang']);

        if ($request->filled('status')) {
            $query->where('status_pertandingan', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_partai', 'LIKE', "%{$search}%")
                  ->orWhere('kelas', 'LIKE', "%{$search}%")
                  ->orWhereHas('pesertaMerah', function ($sq) use ($search) {
                      $sq->where('nama', 'LIKE', "%{$search}%")
                         ->orWhere('kontingen', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('pesertaBiru', function ($sq) use ($search) {
                      $sq->where('nama', 'LIKE', "%{$search}%")
                         ->orWhere('kontingen', 'LIKE', "%{$search}%");
                  });
            });
        }

        $partais = $query->orderBy('no_partai', 'asc')->get();

        $dewanCtrl = app(\App\Http\Controllers\Admin\Dewan\TandingController::class);
        $partaiScores = [];
        foreach ($partais as $p) {
            $partaiScores[$p->id] = $dewanCtrl->calculateScores($p->id);
        }

        $pengaturan = \App\Models\PengaturanDisplay::first();

        $pdf = Pdf::loadView('pages.hasil.tanding.pdf', compact('partais', 'partaiScores', 'pengaturan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekapitulasi_Hasil_Tanding_' . date('Ymd_His') . '.pdf');
    }
}

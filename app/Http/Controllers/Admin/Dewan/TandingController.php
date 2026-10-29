<?php

namespace App\Http\Controllers\Admin\Dewan;

use App\Http\Controllers\Controller;
use App\Models\PartaiTanding;
use App\Models\PesertaTanding;
use App\Models\StatusKontrolJuriTanding;
use App\Models\PoinSahTanding;
use App\Models\LogNilaiJuriTanding;
use App\Models\PelanggaranTanding;
use App\Models\VerifikasiDewan;
use App\Models\JawabanVerifikasiJuri;
use App\Models\JatuhanTanding;
use App\Events\TimerTandingUpdated;
use App\Events\TandingScoreUpdated;
use App\Events\JuriTandingInputReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TandingController extends Controller
{
    /**
     * Menampilkan Halaman Kontrol Poin & Dewan Tanding.
     */
    public function index($id)
    {
        $partai = PartaiTanding::with(['pesertaMerah', 'pesertaBiru', 'pemenang'])->findOrFail($id);

        // Inisialisasi status kontrol juri (1, 2, 3) jika tabel sudah ada dan belum terisi
        if (Schema::hasTable('status_kontrol_juri_tanding')) {
            for ($noJuri = 1; $noJuri <= 3; $noJuri++) {
                StatusKontrolJuriTanding::firstOrCreate(
                    ['id_partai' => $partai->id, 'no_juri' => $noJuri],
                    ['is_active' => true, 'status' => 'Active']
                );
            }
        }

        $statusJuri = Schema::hasTable('status_kontrol_juri_tanding')
            ? StatusKontrolJuriTanding::where('id_partai', $partai->id)->orderBy('no_juri', 'asc')->get()
            : collect([]);

        // Hitung Skor Realtime
        $scores = $this->calculateScores($partai->id);

        // Verifikasi VAR yang sedang Aktif
        $verifikasiAktif = VerifikasiDewan::with('jawabanJuri')
            ->where('id_partai', $partai->id)
            ->where('status', 'Aktif')
            ->latest()
            ->first();

        // Riwayat Verifikasi Selesai
        $riwayatVerifikasi = VerifikasiDewan::with('jawabanJuri')
            ->where('id_partai', $partai->id)
            ->where('status', 'Selesai')
            ->latest()
            ->take(5)
            ->get();

        // Riwayat Log Aktivitas (Jatuhan, Pelanggaran, Poin Sah)
        $timeline = $this->getTimeline($partai->id);

        return view('pages.dewan.tanding', compact(
            'partai',
            'statusJuri',
            'scores',
            'verifikasiAktif',
            'riwayatVerifikasi',
            'timeline'
        ));
    }

    /**
     * Mengatur status Active / Pause untuk Juri 1, 2, atau 3.
     */
    public function toggleJuri(Request $request, $id, $no_juri)
    {
        $partai = PartaiTanding::findOrFail($id);

        if (!Schema::hasTable('status_kontrol_juri_tanding')) {
            return response()->json(['success' => false, 'message' => 'Tabel status kontrol juri belum siap'], 500);
        }

        $kontrol = StatusKontrolJuriTanding::firstOrCreate(
            ['id_partai' => $partai->id, 'no_juri' => $no_juri],
            ['is_active' => true, 'status' => 'Active']
        );

        if ($request->has('action')) {
            $isActive = ($request->action === 'active');
        } else {
            $isActive = !$kontrol->is_active;
        }

        $kontrol->update([
            'is_active' => $isActive,
            'status' => $isActive ? 'Active' : 'Paused',
        ]);

        try {
            broadcast(new \App\Events\ToggleKontrolJuriTanding($partai->id, (int)$no_juri, $kontrol->is_active, $kontrol->status));
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'no_juri' => $no_juri,
                'is_active' => $kontrol->is_active,
                'status' => $kontrol->status,
                'message' => 'Juri ' . $no_juri . ' berhasil ' . ($kontrol->is_active ? 'diaktifkan' : 'dijeda (pause)'),
            ]);
        }

        return redirect()->back()->with('success', 'Status Juri ' . $no_juri . ' berhasil diperbarui');
    }

    /**
     * Mengatur Active atau Pause untuk SEMUA Juri (1, 2, 3) sekaligus.
     */
    public function toggleAllJuri(Request $request, $id)
    {
        $partai = PartaiTanding::findOrFail($id);
        $action = $request->input('action', 'active'); // 'active' atau 'pause'
        $isActive = ($action === 'active');

        if (Schema::hasTable('status_kontrol_juri_tanding')) {
            for ($noJuri = 1; $noJuri <= 3; $noJuri++) {
                StatusKontrolJuriTanding::updateOrCreate(
                    ['id_partai' => $partai->id, 'no_juri' => $noJuri],
                    ['is_active' => $isActive, 'status' => $isActive ? 'Active' : 'Paused']
                );
                try {
                    broadcast(new \App\Events\ToggleKontrolJuriTanding($partai->id, $noJuri, $isActive, $isActive ? 'Active' : 'Paused'));
                } catch (\Throwable $e) {}
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $isActive,
                'message' => 'Semua Juri (1, 2, 3) berhasil ' . ($isActive ? 'diaktifkan' : 'dijeda (pause)'),
            ]);
        }

        return redirect()->back()->with('success', 'Semua Juri berhasil ' . ($isActive ? 'diaktifkan' : 'dijeda'));
    }

    /**
     * Menambahkan Jatuhan (Nilai +3) untuk sudut Merah atau Biru.
     */
    public function addJatuhan(Request $request, $id)
    {
        $request->validate([
            'sudut' => 'required|in:Merah,Biru',
            'babak' => 'nullable|integer|in:1,2,3',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $babak = $request->babak ?: ($partai->babak_aktif ?: 1);

        $jatuhan = JatuhanTanding::create([
            'id_partai' => $partai->id,
            'babak' => $babak,
            'sudut' => $request->sudut,
            'poin' => 3,
        ]);

        $scores = $this->calculateScores($partai->id);
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jatuhan (+3) berhasil ditambahkan untuk Sudut ' . $request->sudut . ' (Babak ' . $babak . ')',
                'scores' => $scores,
                'jatuhan' => $jatuhan,
            ]);
        }

        return redirect()->back()->with('success', 'Jatuhan (+3) Sudut ' . $request->sudut . ' berhasil dicatat.');
    }

    /**
     * Membatalkan (Undo) Jatuhan terakhir untuk sudut tertentu (pada babak aktif).
     */
    public function undoJatuhan(Request $request, $id)
    {
        $request->validate([
            'sudut' => 'required|in:Merah,Biru',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $babakAktif = $partai->babak_aktif ?: 1;

        // Hanya bisa batalkan jatuhan pada babak aktif saat ini
        $lastJatuhan = JatuhanTanding::where('id_partai', $partai->id)
            ->where('sudut', $request->sudut)
            ->where('babak', $babakAktif)
            ->latest('id')
            ->first();

        if ($lastJatuhan) {
            $lastJatuhan->delete();
            $msg = 'Jatuhan terakhir Sudut ' . $request->sudut . ' (Babak ' . $babakAktif . ') berhasil dibatalkan.';
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data jatuhan pada Babak ' . $babakAktif . ' untuk dibatalkan.',
                ], 400);
            }
            return redirect()->back()->with('error', 'Tidak ada data jatuhan pada Babak ' . $babakAktif . ' untuk dibatalkan.');
        }

        $scores = $this->calculateScores($partai->id);
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'scores' => $scores,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Menambahkan Pelanggaran (Binaan, Teguran, Peringatan, Disq).
     */
    public function addPelanggaran(Request $request, $id)
    {
        $request->validate([
            'sudut' => 'required|in:Merah,Biru',
            'jenis_pelanggaran' => 'required|in:Binaan 1,Binaan 2,Teguran 1,Teguran 2,Peringatan 1,Peringatan 2,Peringatan 3',
            'babak' => 'nullable|integer|in:1,2,3',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $babak = $request->babak ?: ($partai->babak_aktif ?: 1);

        // 1. Peringatan berlaku match-wide (semua babak sekaligus)
        if (in_array($request->jenis_pelanggaran, ['Peringatan 1', 'Peringatan 2'])) {
            $existingPeringatan = PelanggaranTanding::where('id_partai', $partai->id)
                ->where('sudut', $request->sudut)
                ->where('jenis_pelanggaran', $request->jenis_pelanggaran)
                ->first();
            if ($existingPeringatan) {
                return response()->json([
                    'success' => false,
                    'message' => "{$request->jenis_pelanggaran} sudah pernah diberikan untuk Sudut {$request->sudut}!",
                ], 422);
            }
        }

        // 2. Diskualifikasi (Peringatan 3)
        if ($request->jenis_pelanggaran === 'Peringatan 3') {
            $existingDisq = PelanggaranTanding::where('id_partai', $partai->id)
                ->where('sudut', $request->sudut)
                ->where(function ($q) {
                    $q->where('jenis_pelanggaran', 'Peringatan 3')
                      ->orWhere('poin_minus', '>=', 99);
                })
                ->first();
            if ($existingDisq) {
                return response()->json([
                    'success' => false,
                    'message' => "Sudut {$request->sudut} sudah didiskualifikasi!",
                ], 422);
            }
        }

        // 3. Binaan & Teguran berlaku per-babak
        if (in_array($request->jenis_pelanggaran, ['Binaan 1', 'Binaan 2', 'Teguran 1', 'Teguran 2'])) {
            $existingBabak = PelanggaranTanding::where('id_partai', $partai->id)
                ->where('sudut', $request->sudut)
                ->where('babak', $babak)
                ->where('jenis_pelanggaran', $request->jenis_pelanggaran)
                ->first();
            if ($existingBabak) {
                return response()->json([
                    'success' => false,
                    'message' => "{$request->jenis_pelanggaran} sudah diberikan di Babak {$babak} untuk Sudut {$request->sudut}!",
                ], 422);
            }
        }

        // Tentukan poin minus berdasarkan jenis pelanggaran
        $poinMinusMap = [
            'Binaan 1' => 0,
            'Binaan 2' => 0,
            'Teguran 1' => 1,
            'Teguran 2' => 2,
            'Peringatan 1' => 5,
            'Peringatan 2' => 10,
            'Peringatan 3' => 99, // Diskualifikasi
        ];

        $poinMinus = $poinMinusMap[$request->jenis_pelanggaran] ?? 0;

        $pelanggaran = PelanggaranTanding::create([
            'id_partai' => $partai->id,
            'sudut' => $request->sudut,
            'babak' => $babak,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'poin_minus' => $poinMinus,
        ]);

        $scores = $this->calculateScores($partai->id);
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        $msg = "{$request->jenis_pelanggaran} dicatat untuk Sudut {$request->sudut}";
        if ($poinMinus > 0 && $poinMinus < 99) {
            $msg .= " (-{$poinMinus} Poin)";
        } elseif ($poinMinus >= 99) {
            $msg .= " (DISKUALIFIKASI)";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'scores' => $scores,
                'pelanggaran' => $pelanggaran,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Membatalkan (Undo) Pelanggaran untuk kategori tertentu (binaan, teguran, peringatan, disq).
     */
    public function undoPelanggaran(Request $request, $id)
    {
        $request->validate([
            'sudut' => 'required|in:Merah,Biru',
            'kategori' => 'nullable|in:binaan,teguran,peringatan,disq',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $babakAktif = $partai->babak_aktif ?: 1;
        $kategori = $request->kategori;

        $query = PelanggaranTanding::where('id_partai', $partai->id)
            ->where('sudut', $request->sudut);

        if ($kategori === 'binaan') {
            // Hanya bisa batalkan Binaan pada babak aktif saat ini!
            $query->where('babak', $babakAktif)
                  ->whereIn('jenis_pelanggaran', ['Binaan 1', 'Binaan 2']);
        } elseif ($kategori === 'teguran') {
            // Hanya bisa batalkan Teguran pada babak aktif saat ini!
            $query->where('babak', $babakAktif)
                  ->whereIn('jenis_pelanggaran', ['Teguran 1', 'Teguran 2']);
        } elseif ($kategori === 'peringatan') {
            // Peringatan berlaku match-wide (semua babak sekaligus)
            $query->whereIn('jenis_pelanggaran', ['Peringatan 1', 'Peringatan 2']);
        } elseif ($kategori === 'disq') {
            // Disqualifikasi (Peringatan 3 atau poin_minus >= 99)
            $query->where(function ($q) {
                $q->where('jenis_pelanggaran', 'Peringatan 3')
                  ->orWhere('poin_minus', '>=', 99);
            });
        }

        $target = $query->latest('id')->first();

        if ($target) {
            $jenis = $target->jenis_pelanggaran;
            $target->delete();
            $msg = "{$jenis} Sudut {$request->sudut} berhasil dibatalkan.";
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data ' . ($kategori ? strtoupper($kategori) : 'pelanggaran') . ' pada babak ini yang dapat dibatalkan.',
                ], 400);
            }
            return redirect()->back()->with('error', 'Tidak ada pelanggaran yang dapat dibatalkan.');
        }

        $scores = $this->calculateScores($partai->id);
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'scores' => $scores,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Memulai Sesi Verifikasi Dewan (VAR).
     */
    public function startVerifikasi(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'babak' => 'nullable|integer|in:1,2,3',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $babak = $request->babak ?: ($partai->babak_aktif ?: 1);

        // Jika ada verifikasi yang masih aktif sebelumnya, selesaikan dulu
        VerifikasiDewan::where('id_partai', $partai->id)
            ->where('status', 'Aktif')
            ->update(['status' => 'Selesai']);

        $verifikasi = VerifikasiDewan::create([
            'id_partai' => $partai->id,
            'babak' => $babak,
            'keterangan' => $request->keterangan,
            'status' => 'Aktif',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Verifikasi Juri (VAR) dimulai: ' . $verifikasi->keterangan,
                'verifikasi' => $verifikasi,
            ]);
        }

        return redirect()->back()->with('success', 'Sesi verifikasi dewan dimulai.');
    }

    /**
     * Menyimpan respon / jawaban Juri (1, 2, atau 3) pada sesi verifikasi aktif.
     */
    public function recordJawabanVerifikasi(Request $request, $id_verifikasi)
    {
        $request->validate([
            'no_juri' => 'required|integer|in:1,2,3',
            'pilihan_sudut' => 'required|in:Merah,Biru,Batal',
        ]);

        $verifikasi = VerifikasiDewan::findOrFail($id_verifikasi);

        $jawaban = JawabanVerifikasiJuri::updateOrCreate(
            ['id_verifikasi' => $verifikasi->id, 'no_juri' => $request->no_juri],
            ['pilihan_sudut' => $request->pilihan_sudut]
        );

        $semuaJawaban = JawabanVerifikasiJuri::where('id_verifikasi', $verifikasi->id)->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Jawaban Juri {$request->no_juri}: {$request->pilihan_sudut}",
                'jawaban' => $jawaban,
                'semua_jawaban' => $semuaJawaban,
            ]);
        }

        return redirect()->back()->with('success', 'Respon juri berhasil dicatat.');
    }

    /**
     * Menyelesaikan Sesi Verifikasi Dewan dan mengeksekusi keputusannya.
     */
    public function selesaiVerifikasi(Request $request, $id_verifikasi)
    {
        $verifikasi = VerifikasiDewan::findOrFail($id_verifikasi);
        $partai = PartaiTanding::findOrFail($verifikasi->id_partai);

        $keputusan = $request->input('keputusan'); // e.g. 'jatuhan_biru', 'jatuhan_merah', 'sah_biru', 'sah_merah', 'batal'

        // Eksekusi otomatis jika keputusan menghasilkan poin/jatuhan
        if ($keputusan === 'jatuhan_biru') {
            JatuhanTanding::create([
                'id_partai' => $partai->id,
                'babak' => $verifikasi->babak,
                'sudut' => 'Biru',
                'poin' => 3,
            ]);
        } elseif ($keputusan === 'jatuhan_merah') {
            JatuhanTanding::create([
                'id_partai' => $partai->id,
                'babak' => $verifikasi->babak,
                'sudut' => 'Merah',
                'poin' => 3,
            ]);
        } elseif ($keputusan === 'sah_biru_tendangan') {
            PoinSahTanding::create([
                'id_partai' => $partai->id,
                'babak' => $verifikasi->babak,
                'sudut' => 'Biru',
                'poin_masuk' => 2,
            ]);
        } elseif ($keputusan === 'sah_merah_tendangan') {
            PoinSahTanding::create([
                'id_partai' => $partai->id,
                'babak' => $verifikasi->babak,
                'sudut' => 'Merah',
                'poin_masuk' => 2,
            ]);
        } elseif ($keputusan === 'sah_biru_pukulan') {
            PoinSahTanding::create([
                'id_partai' => $partai->id,
                'babak' => $verifikasi->babak,
                'sudut' => 'Biru',
                'poin_masuk' => 1,
            ]);
        } elseif ($keputusan === 'sah_merah_pukulan') {
            PoinSahTanding::create([
                'id_partai' => $partai->id,
                'babak' => $verifikasi->babak,
                'sudut' => 'Merah',
                'poin_masuk' => 1,
            ]);
        }

        $verifikasi->update(['status' => 'Selesai']);

        $scores = $this->calculateScores($partai->id);
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi verifikasi berhasil diselesaikan dengan keputusan: ' . ($keputusan ?: 'Selesai'),
                'scores' => $scores,
            ]);
        }

        return redirect()->back()->with('success', 'Sesi verifikasi selesai.');
    }

    /**
     * Memperbarui Timer Pertandingan (detik dan status: running, pause, stop).
     */
    public function updateTimer(Request $request, $id)
    {
        $partai = PartaiTanding::findOrFail($id);

        $updateData = [];
        if ($request->has('waktu_timer')) {
            $updateData['waktu_timer'] = max(0, intval($request->waktu_timer));
        }
        if ($request->has('status_timer')) {
            $updateData['status_timer'] = $request->status_timer;
        }

        if (!empty($updateData)) {
            $partai->update($updateData);
        }

        broadcast(new TimerTandingUpdated($partai->id, $partai->waktu_timer, $partai->status_timer, $partai->babak_aktif ?: 1));

        return response()->json([
            'success' => true,
            'waktu_timer' => $partai->waktu_timer,
            'status_timer' => $partai->status_timer,
        ]);
    }

    /**
     * Mengganti Babak Aktif Pertandingan (Babak 1, 2, atau 3).
     */
    public function updateBabak(Request $request, $id)
    {
        $request->validate([
            'babak_aktif' => 'required|integer|in:1,2,3',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $partai->update([
            'babak_aktif' => $request->babak_aktif,
            'waktu_timer' => 120, // Reset timer ke default 2 menit saat ganti babak
            'status_timer' => 'stop',
        ]);

        $scores = $this->calculateScores($partai->id);
        broadcast(new TimerTandingUpdated($partai->id, 120, 'stop', $partai->babak_aktif ?: 1));
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Babak aktif berhasil diubah ke Babak ' . $request->babak_aktif,
                'babak_aktif' => $partai->babak_aktif,
                'scores' => $scores,
            ]);
        }

        return redirect()->back()->with('success', 'Babak berhasil diubah ke Babak ' . $request->babak_aktif);
    }

    /**
     * Mengambil Data Skor & Status Terkini dalam bentuk JSON (untuk polling realtime).
     */
    public function getStatusData($id)
    {
        $partai = PartaiTanding::with(['pesertaMerah', 'pesertaBiru', 'pemenang'])->findOrFail($id);

        $statusJuri = Schema::hasTable('status_kontrol_juri_tanding')
            ? StatusKontrolJuriTanding::where('id_partai', $partai->id)->orderBy('no_juri', 'asc')->get()
            : collect([]);

        $scores = $this->calculateScores($partai->id);
        broadcast(new TandingScoreUpdated($partai->id, $scores, $partai->babak_aktif ?: 1));

        $verifikasiAktif = VerifikasiDewan::with('jawabanJuri')
            ->where('id_partai', $partai->id)
            ->where('status', 'Aktif')
            ->latest()
            ->first();

        $timeline = $this->getTimeline($partai->id);

        return response()->json([
            'success' => true,
            'partai' => [
                'id' => $partai->id,
                'no_partai' => $partai->no_partai,
                'kelas' => $partai->kelas,
                'fase_babak' => $partai->fase_babak,
                'babak_aktif' => $partai->babak_aktif,
                'status_pertandingan' => $partai->status_pertandingan,
                'waktu_timer' => $partai->waktu_timer,
                'status_timer' => $partai->status_timer,
                'id_pemenang' => $partai->id_pemenang,
            ],
            'status_juri' => $statusJuri,
            'scores' => $scores,
            'verifikasi_aktif' => $verifikasiAktif,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Menerima input nilai pukulan / tendangan dari juri (1, 2, 3, dst).
     * Menerapkan aturan konsensus IPSI:
     * - Minimal 2 juri menginput nilai yang sama dalam jendela waktu 2 detik untuk sudut yang sama.
     * - Pasangan konsensus 2 juri diproses menjadi Poin Sah (+1 Pukulan / +2 Tendangan).
     * - Jika ada juri ke-3 input tanpa pasangan baru (misal serangan yang sama sudah dipasangkan oleh 2 juri),
     *   dicatat ke log dengan keterangan "Tidak Diproses".
     * - Jika ada juri ke-4 (atau juri lain yang menginput bersamaan dalam 2 detik dengan juri ke-3),
     *   maka juri ke-3 dan ke-4 diproses menjadi poin sah berikutnya.
     */
    public function submitJuriInput(Request $request, $id)
    {
        $request->validate([
            'no_juri' => 'required|integer|min:1|max:10',
            'sudut' => 'required|in:Merah,Biru',
            'jenis_serangan' => 'required|in:Pukulan,Tendangan',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $babakAktif = $partai->babak_aktif ?: 1;
        $noJuri = (int) $request->no_juri;
        $sudut = ucfirst(strtolower($request->sudut));
        $jenisSerangan = ucfirst(strtolower($request->jenis_serangan));
        $poinVal = ($jenisSerangan === 'Tendangan') ? 2 : 1;

        // Cek status kontrol juri (apakah sedang aktif atau di-pause)
        $kontrolJuri = StatusKontrolJuriTanding::where('id_partai', $partai->id)
            ->where('no_juri', $noJuri)
            ->first();

        if ($kontrolJuri && !$kontrolJuri->is_active) {
            return response()->json([
                'success' => false,
                'message' => "Juri {$noJuri} sedang dijeda (Paused)!"
            ], 403);
        }

        // Jalankan seluruh evaluasi konsensus dalam DB Transaction dengan pessimistic lock (lockForUpdate)
        // untuk menangani input concurrent (bersamaan) dan serangan beruntun secara konsisten
        return \Illuminate\Support\Facades\DB::transaction(function () use (
            $partai, $babakAktif, $noJuri, $sudut, $jenisSerangan, $poinVal
        ) {
            $now = now();
            // Jendela konsensus persis 2 detik
            $twoSecondsAgo = $now->copy()->subSeconds(2);

            // 1. Bersihkan log lama yang belum berpasangan dan sudah lewat 3 detik (buffer toleransi)
            LogNilaiJuriTanding::where('id_partai', $partai->id)
                ->where('status_input', 'Belum Proses')
                ->where('created_at', '<', $now->copy()->subSeconds(3))
                ->update([
                    'status_input' => 'Tidak Diproses',
                    'keterangan' => 'Tidak Diproses (Batas waktu 2 detik terlampaui)',
                ]);

            // 2. Cari calon pasangan: input dari JURI LAIN yang belum diproses dalam 2 detik terakhir
            // Diurutkan berdasarkan id ASC (FIFO) dengan lockForUpdate untuk konkurensi aman
            $partnerLog = LogNilaiJuriTanding::where('id_partai', $partai->id)
                ->where('babak', $babakAktif)
                ->where('sudut', $sudut)
                ->where('jenis_serangan', $jenisSerangan)
                ->where('status_input', 'Belum Proses')
                ->where('no_juri', '!=', $noJuri)
                ->where('created_at', '>=', $twoSecondsAgo)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            $isPoinSah = false;
            $scores = null;

            if ($partnerLog) {
                // Tercapai konsensus sepasang 2 juri berbeda dalam batas waktu 2 detik!
                $poinSah = PoinSahTanding::create([
                    'id_partai' => $partai->id,
                    'babak' => $babakAktif,
                    'sudut' => $sudut,
                    'poin_masuk' => $poinVal,
                ]);

                $waktuPartner = $partnerLog->created_at ? $partnerLog->created_at->format('H:i:s') : $now->format('H:i:s');
                $waktuCurrent = $now->format('H:i:s');
                $ketKonsensus = "Poin Sah (Konsensus Juri {$partnerLog->no_juri} [{$waktuPartner}] & Juri {$noJuri} [{$waktuCurrent}])";

                // Simpan log juri saat ini langsung Berhasil
                $currentLog = LogNilaiJuriTanding::create([
                    'id_partai' => $partai->id,
                    'babak' => $babakAktif,
                    'no_juri' => $noJuri,
                    'sudut' => $sudut,
                    'jenis_serangan' => $jenisSerangan,
                    'poin' => $poinVal,
                    'status_input' => 'Berhasil',
                    'id_poin_sah' => $poinSah->id,
                    'keterangan' => $ketKonsensus,
                ]);

                // Update log juri pasangan menjadi Berhasil
                $partnerLog->update([
                    'status_input' => 'Berhasil',
                    'id_poin_sah' => $poinSah->id,
                    'keterangan' => $ketKonsensus,
                ]);

                $isPoinSah = true;
                $scores = $this->calculateScores($partai->id);

                $juristDetails = [
                    [
                        'no_juri' => $partnerLog->no_juri,
                        'time' => $waktuPartner,
                        'jenis' => $partnerLog->jenis_serangan,
                        'status' => 'Berhasil'
                    ],
                    [
                        'no_juri' => $noJuri,
                        'time' => $waktuCurrent,
                        'jenis' => $jenisSerangan,
                        'status' => 'Berhasil'
                    ]
                ];

                // Broadcast event ke display & dewan
                broadcast(new JuriTandingInputReceived(
                    $partai->id,
                    $noJuri,
                    $sudut,
                    $jenisSerangan,
                    true,
                    $scores,
                    $currentLog->keterangan,
                    $babakAktif,
                    $currentLog->created_at->format('H:i:s'),
                    $partnerLog->no_juri,
                    $juristDetails
                ));
                broadcast(new TandingScoreUpdated($partai->id, $scores, $babakAktif));

                $message = "Konsensus tercapai! Poin Sah {$jenisSerangan} (+{$poinVal}) untuk Sudut {$sudut}.";
            } else {
                // Belum ada pasangan dari juri lain (menunggu juri berbeda dalam batas 2 detik)
                $currentLog = LogNilaiJuriTanding::create([
                    'id_partai' => $partai->id,
                    'babak' => $babakAktif,
                    'no_juri' => $noJuri,
                    'sudut' => $sudut,
                    'jenis_serangan' => $jenisSerangan,
                    'poin' => $poinVal,
                    'status_input' => 'Belum Proses',
                    'keterangan' => 'Tidak Diproses (Menunggu Pasangan)',
                ]);

                broadcast(new JuriTandingInputReceived(
                    $partai->id,
                    $noJuri,
                    $sudut,
                    $jenisSerangan,
                    false,
                    null,
                    $currentLog->keterangan,
                    $babakAktif,
                    $currentLog->created_at->format('H:i:s')
                ));

                $message = "Input Juri {$noJuri} tercatat (menunggu kesepakatan juri lain dalam 2 detik).";
            }

            return response()->json([
                'success' => true,
                'is_poin_sah' => $isPoinSah,
                'scores' => $scores,
                'log' => [
                    'id' => $currentLog->id,
                    'no_juri' => $currentLog->no_juri,
                    'sudut' => $currentLog->sudut,
                    'jenis_serangan' => $currentLog->jenis_serangan,
                    'poin' => $currentLog->poin,
                    'status_input' => $currentLog->status_input,
                    'keterangan' => $currentLog->keterangan,
                    'time' => $currentLog->created_at->format('H:i:s'),
                ],
                'message' => $message,
            ]);
        });
    }

    /**
     * Memicu refresh global display layar proyektor untuk kategori tanding.
     */
    public function triggerRefreshDisplay()
    {
        broadcast(new \App\Events\RefreshDisplay('tanding.A'));
        return response()->json([
            'success' => true,
            'message' => 'Sinyal refresh display tanding gelanggang A berhasil dikirim!',
        ]);
    }

    /**
     * Helper: Menghitung akumulasi skor total dan rincian per babak.
     */
    public function calculateScores($idPartai)
    {
        $partai = PartaiTanding::findOrFail($idPartai);
        $babakAktif = $partai->babak_aktif ?: 1;

        // 1. Poin Sah Masuk dari Juri (Pukulan = 1, Tendangan = 2)
        $poinSah = PoinSahTanding::where('id_partai', $idPartai)->get();
        $poinMasukBiru = $poinSah->where('sudut', 'Biru')->sum('poin_masuk');
        $poinMasukMerah = $poinSah->where('sudut', 'Merah')->sum('poin_masuk');

        // 2. Jatuhan (Nilai +3)
        $jatuhan = JatuhanTanding::where('id_partai', $idPartai)->get();
        $totalJatuhanBiru = $jatuhan->where('sudut', 'Biru')->count();
        $totalJatuhanMerah = $jatuhan->where('sudut', 'Merah')->count();
        $poinJatuhanBiru = $totalJatuhanBiru * 3;
        $poinJatuhanMerah = $totalJatuhanMerah * 3;

        // Jatuhan babak aktif saat ini
        $jatuhanBiruBabak = $jatuhan->where('sudut', 'Biru')->where('babak', $babakAktif)->count();
        $jatuhanMerahBabak = $jatuhan->where('sudut', 'Merah')->where('babak', $babakAktif)->count();

        // 3. Pelanggaran (Binaan, Teguran, Peringatan, Diskualifikasi)
        $pelanggaran = PelanggaranTanding::where('id_partai', $idPartai)->get();

        // Binaan di babak aktif saat ini
        $binaanBiruBabak = $pelanggaran->where('sudut', 'Biru')
            ->where('babak', $babakAktif)
            ->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Binaan'))
            ->pluck('jenis_pelanggaran')
            ->values()
            ->all();

        $binaanMerahBabak = $pelanggaran->where('sudut', 'Merah')
            ->where('babak', $babakAktif)
            ->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Binaan'))
            ->pluck('jenis_pelanggaran')
            ->values()
            ->all();

        // Teguran di babak aktif saat ini
        $teguranBiruBabak = $pelanggaran->where('sudut', 'Biru')
            ->where('babak', $babakAktif)
            ->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Teguran'))
            ->pluck('jenis_pelanggaran')
            ->values()
            ->all();

        $teguranMerahBabak = $pelanggaran->where('sudut', 'Merah')
            ->where('babak', $babakAktif)
            ->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Teguran'))
            ->pluck('jenis_pelanggaran')
            ->values()
            ->all();

        // Peringatan berlaku match-wide (semua babak sekaligus)
        $peringatanBiruMatch = $pelanggaran->where('sudut', 'Biru')
            ->filter(fn($p) => in_array($p->jenis_pelanggaran, ['Peringatan 1', 'Peringatan 2']))
            ->pluck('jenis_pelanggaran')
            ->values()
            ->all();

        $peringatanMerahMatch = $pelanggaran->where('sudut', 'Merah')
            ->filter(fn($p) => in_array($p->jenis_pelanggaran, ['Peringatan 1', 'Peringatan 2']))
            ->pluck('jenis_pelanggaran')
            ->values()
            ->all();

        // Diskualifikasi berlaku match-wide (semua babak sekaligus)
        $isDisqBiru = $pelanggaran->where('sudut', 'Biru')
            ->filter(fn($p) => $p->jenis_pelanggaran === 'Peringatan 3' || $p->poin_minus >= 99)
            ->isNotEmpty();

        $isDisqMerah = $pelanggaran->where('sudut', 'Merah')
            ->filter(fn($p) => $p->jenis_pelanggaran === 'Peringatan 3' || $p->poin_minus >= 99)
            ->isNotEmpty();

        // Hitung total poin minus (kecuali diskualifikasi 99)
        $hukumanBiru = $pelanggaran->where('sudut', 'Biru')->where('poin_minus', '<', 99)->sum('poin_minus');
        $hukumanMerah = $pelanggaran->where('sudut', 'Merah')->where('poin_minus', '<', 99)->sum('poin_minus');

        // Total Skor Bersih
        $totalBiru = ($poinMasukBiru + $poinJatuhanBiru) - $hukumanBiru;
        $totalMerah = ($poinMasukMerah + $poinJatuhanMerah) - $hukumanMerah;

        // Seluruh daftar untuk rekap badge display
        $allBinaanBiruCount = $pelanggaran->where('sudut', 'Biru')->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Binaan'))->count();
        $allBinaanMerahCount = $pelanggaran->where('sudut', 'Merah')->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Binaan'))->count();

        $allTeguranBiru = $pelanggaran->where('sudut', 'Biru')->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Teguran'))->pluck('jenis_pelanggaran')->values();
        $allTeguranMerah = $pelanggaran->where('sudut', 'Merah')->filter(fn($p) => str_contains($p->jenis_pelanggaran, 'Teguran'))->pluck('jenis_pelanggaran')->values();

        // Rincian per babak (1, 2, 3)
        $rincianBabak = [];
        for ($b = 1; $b <= 3; $b++) {
            $pSahB = $poinSah->where('babak', $b);
            $jatB = $jatuhan->where('babak', $b);
            $pelB = $pelanggaran->where('babak', $b)->where('poin_minus', '<', 99);

            $rincianBabak[$b] = [
                'biru' => [
                    'poin_masuk' => $pSahB->where('sudut', 'Biru')->sum('poin_masuk'),
                    'jatuhan' => $jatB->where('sudut', 'Biru')->count() * 3,
                    'hukuman' => $pelB->where('sudut', 'Biru')->sum('poin_minus'),
                ],
                'merah' => [
                    'poin_masuk' => $pSahB->where('sudut', 'Merah')->sum('poin_masuk'),
                    'jatuhan' => $jatB->where('sudut', 'Merah')->count() * 3,
                    'hukuman' => $pelB->where('sudut', 'Merah')->sum('poin_minus'),
                ],
            ];
        }

        return [
            'biru' => [
                'total' => $totalBiru,
                'poin_masuk' => $poinMasukBiru,
                'jatuhan_count' => $totalJatuhanBiru,
                'poin_jatuhan' => $poinJatuhanBiru,
                'poin_minus' => $hukumanBiru,
                'binaan_count' => $allBinaanBiruCount,
                'binaan_count_babak' => count($binaanBiruBabak),
                'teguran_list' => $allTeguranBiru,
                'peringatan_list' => $peringatanBiruMatch,
                'is_disq' => $isDisqBiru,
                // Status tombol di babak aktif:
                'has_binaan_1' => in_array('Binaan 1', $binaanBiruBabak),
                'has_binaan_2' => in_array('Binaan 2', $binaanBiruBabak),
                'has_teguran_1' => in_array('Teguran 1', $teguranBiruBabak),
                'has_teguran_2' => in_array('Teguran 2', $teguranBiruBabak),
                'has_peringatan_1' => in_array('Peringatan 1', $peringatanBiruMatch),
                'has_peringatan_2' => in_array('Peringatan 2', $peringatanBiruMatch),
                // Status undo (aktif / inaktif):
                'can_undo_jatuhan' => $jatuhanBiruBabak > 0,
                'can_undo_binaan' => count($binaanBiruBabak) > 0,
                'can_undo_teguran' => count($teguranBiruBabak) > 0,
                'can_undo_peringatan' => count($peringatanBiruMatch) > 0,
                'can_undo_disq' => $isDisqBiru,
            ],
            'merah' => [
                'total' => $totalMerah,
                'poin_masuk' => $poinMasukMerah,
                'jatuhan_count' => $totalJatuhanMerah,
                'poin_jatuhan' => $poinJatuhanMerah,
                'poin_minus' => $hukumanMerah,
                'binaan_count' => $allBinaanMerahCount,
                'binaan_count_babak' => count($binaanMerahBabak),
                'teguran_list' => $allTeguranMerah,
                'peringatan_list' => $peringatanMerahMatch,
                'is_disq' => $isDisqMerah,
                // Status tombol di babak aktif:
                'has_binaan_1' => in_array('Binaan 1', $binaanMerahBabak),
                'has_binaan_2' => in_array('Binaan 2', $binaanMerahBabak),
                'has_teguran_1' => in_array('Teguran 1', $teguranMerahBabak),
                'has_teguran_2' => in_array('Teguran 2', $teguranMerahBabak),
                'has_peringatan_1' => in_array('Peringatan 1', $peringatanMerahMatch),
                'has_peringatan_2' => in_array('Peringatan 2', $peringatanMerahMatch),
                // Status undo (aktif / inaktif):
                'can_undo_jatuhan' => $jatuhanMerahBabak > 0,
                'can_undo_binaan' => count($binaanMerahBabak) > 0,
                'can_undo_teguran' => count($teguranMerahBabak) > 0,
                'can_undo_peringatan' => count($peringatanMerahMatch) > 0,
                'can_undo_disq' => $isDisqMerah,
            ],
            'per_babak' => $rincianBabak,
        ];
    }

    /**
     * Helper: Mendapatkan riwayat log aksi dalam partai ini.
     */
    private function getTimeline($idPartai)
    {
        $events = collect([]);

        // 1. Jatuhan
        $jatuhans = JatuhanTanding::where('id_partai', $idPartai)->get();
        foreach ($jatuhans as $j) {
            $events->push([
                'id' => $j->id,
                'type' => 'jatuhan',
                'badge' => 'Jatuhan (+3)',
                'sudut' => $j->sudut,
                'babak' => $j->babak,
                'keterangan' => 'Jatuhan sah (+3 poin)',
                'time' => $j->created_at ? $j->created_at->format('H:i:s') : '-',
                'created_at' => $j->created_at,
            ]);
        }

        // 2. Pelanggaran
        $pelanggarans = PelanggaranTanding::where('id_partai', $idPartai)->get();
        foreach ($pelanggarans as $p) {
            $events->push([
                'id' => $p->id,
                'type' => 'pelanggaran',
                'badge' => $p->jenis_pelanggaran,
                'sudut' => $p->sudut,
                'babak' => $p->babak,
                'keterangan' => $p->poin_minus > 0 ? "Potongan -{$p->poin_minus} poin" : 'Peringatan administratif',
                'time' => $p->created_at ? $p->created_at->format('H:i:s') : '-',
                'created_at' => $p->created_at,
            ]);
        }

        // 3. Poin Sah
        $poinSah = PoinSahTanding::where('id_partai', $idPartai)->get();
        foreach ($poinSah as $ps) {
            $jenis = ($ps->poin_masuk == 2) ? 'Tendangan (+2)' : 'Pukulan (+1)';

            // Ambil waktu input juri yang berkontribusi menghasilkan poin sah ini
            $contributingLogs = LogNilaiJuriTanding::where('id_poin_sah', $ps->id)
                ->orderBy('created_at')
                ->get();

            $detailJuriArray = [];
            if ($contributingLogs->isNotEmpty()) {
                $detailJuriArray = $contributingLogs->map(function ($l) {
                    $jam = $l->created_at ? $l->created_at->format('H:i:s') : '-';
                    return [
                        'no_juri' => $l->no_juri,
                        'time' => $jam,
                        'jenis' => $l->jenis_serangan,
                        'status' => $l->status_input,
                    ];
                })->values()->toArray();

                $detailText = $contributingLogs->map(function ($l) {
                    $jam = $l->created_at ? $l->created_at->format('H:i:s') : '-';
                    return "Juri {$l->no_juri} [{$jam}]";
                })->implode(' & ');
                $keterangan = "Poin Sah (Konsensus {$detailText})";
            } else {
                $keterangan = 'Serangan sah berhasil masuk';
            }

            $events->push([
                'id' => 'ps-' . $ps->id,
                'raw_id' => $ps->id,
                'type' => 'poin',
                'badge' => $jenis,
                'sudut' => $ps->sudut,
                'babak' => $ps->babak,
                'keterangan' => $keterangan,
                'time' => $ps->created_at ? $ps->created_at->format('H:i:s') : '-',
                'created_at' => $ps->created_at,
                'jurist_details' => $detailJuriArray,
            ]);
        }

        // 4. Log Input Nilai Juri (SEMUA: Berhasil, Belum Proses, Tidak Diproses - Tetap Tampil)
        $allJuriLogs = LogNilaiJuriTanding::where('id_partai', $idPartai)
            ->latest('created_at')
            ->take(50)
            ->get();
        foreach ($allJuriLogs as $uj) {
            $waktuJuri = $uj->created_at ? $uj->created_at->format('H:i:s') : '-';
            $events->push([
                'id' => 'uj-' . $uj->id,
                'raw_id' => $uj->id,
                'type' => 'juri_input',
                'no_juri' => $uj->no_juri,
                'jenis_serangan' => $uj->jenis_serangan,
                'status_input' => $uj->status_input,
                'badge' => "Juri {$uj->no_juri}: {$uj->jenis_serangan}",
                'sudut' => $uj->sudut,
                'babak' => $uj->babak,
                'keterangan' => $uj->keterangan ?: ($uj->status_input === 'Berhasil' ? 'Poin Sah (Konsensus)' : $uj->status_input),
                'time' => $waktuJuri,
                'created_at' => $uj->created_at,
            ]);
        }

        return $events->sortByDesc('created_at')->values()->take(30);
    }
}

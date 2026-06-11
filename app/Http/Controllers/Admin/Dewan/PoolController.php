<?php

namespace App\Http\Controllers\Admin\Dewan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Partai;

class PoolController extends Controller
{
    /**
     * Menampilkan halaman kontrol Dewan untuk sistem Pool
     */
    public function index($id_partai)
    {
        // 1. Ambil data partai beserta relasi yang dibutuhkan
        // Urutkan peserta berdasarkan nomor urut tampil
        $partai = Partai::with([
            'kategori',
            'kelas',
            'detail_peserta' => function ($q) {
                $q->orderBy('no_urut', 'asc');
            },
            'detail_peserta.peserta'
        ])
            ->findOrFail($id_partai);

        $namaKategori = strtoupper($partai->kategori->nama_kategori ?? '');

        // 2. Tentukan peserta mana yang sedang "Aktif" (status_mulai = 1)
        // Jika belum ada yang aktif, ambil urutan pertama sebagai default
        $pesertaAktif = $partai->detail_peserta->firstWhere('status_mulai', 1)
            ?? $partai->detail_peserta->first();

        // ====================================================================
        // --- LOGIKA AUTO-GENERATE DATA JURI (1-6) UNTUK PESERTA AKTIF ---
        // ====================================================================
        if ($pesertaAktif) {

            // A. Tentukan Baseline Skor & Ketersediaan Nilai C secara Dinamis
            if (str_contains($namaKategori, 'TUNGGAL') || str_contains($namaKategori, 'REGU')) {
                $baselineSkor = 9.90; // Default untuk Tunggal & Regu
                $defaultNilaiC = null; // Kolom Nilai C kosong (NULL)
            } else {
                $baselineSkor = 9.10; // Default untuk Solo & Ganda

                // --- PERBAIKAN DI SINI ---
                // Jadikan 0.00 untuk SEMUA yang masuk ke blok ini (Solo maupun Ganda)
                $defaultNilaiC = 0.00;
            }

            // B. Buat Wadah Nilai Juri (jika belum ada 6 baris)
            $jumlahJuri = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                ->where('id_detail', $pesertaAktif->id_detail)
                ->count();

            if ($jumlahJuri < 6) {
                $dataJuri = [];
                for ($i = 1; $i <= 6; $i++) {
                    $dataJuri[] = [
                        'id_detail' => $pesertaAktif->id_detail,
                        'no_juri' => $i,
                        'nilai_a' => 0.00,
                        'nilai_b' => 0.00,
                        'nilai_c' => $defaultNilaiC,
                        'skor_akhir_juri' => $baselineSkor, // Sesuai kategori masing-masing
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                \Illuminate\Support\Facades\DB::table('input_nilai_juri')->insert($dataJuri);
            }

            // C. Buat Wadah Kontrol Status Juri (jika belum ada 6 baris untuk partai ini)
            $jumlahKontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->count();

            if ($jumlahKontrol < 6) {
                $dataKontrol = [];
                for ($i = 1; $i <= 6; $i++) {
                    $dataKontrol[] = [
                        'id_partai' => $id_partai,
                        'no_juri' => $i,
                        'is_on' => 0,               // Default awal OFF (Merah)
                        'status_input' => 'Proses', // Default awal Proses (Oranye)
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->insert($dataKontrol);
            }
        }
        // ====================================================================

        // 3. Logika Switching Template
        if (str_contains($namaKategori, 'TUNGGAL') || str_contains($namaKategori, 'REGU')) {
            // Arahkan ke template 2 Input (Default 9.90)
            return view('pages.dewan.pool.tunggal_regu', compact('partai', 'pesertaAktif'));
        } else {
            // Arahkan ke template 3 Input (Default 9.10) untuk Solo/Ganda
            return view('pages.dewan.pool.solo_ganda', compact('partai', 'pesertaAktif'));
        }
    }

    public function updateTimer(Request $request, $id, $id_detail)
    {
        try {
            $waktu = $request->input('waktu');
            $statusRunning = $request->input('status_running');

            // 1. Update ke Database
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->update([
                    'waktu_tampil' => $waktu,
                    'is_timer_running' => $statusRunning
                ]);

            // 2. Identifikasi Kategori untuk Menentukan Channel Tujuan secara Dinamis
            $partai = \App\Models\Partai::with('kategori')->find($id);
            $namaKatLower = strtolower($partai->kategori->nama_kategori ?? '');

            $jenis = 'solo';
            if (str_contains($namaKatLower, 'ganda')) {
                $jenis = 'ganda';
            } elseif (str_contains($namaKatLower, 'regu')) {
                $jenis = 'regu';
            } elseif (str_contains($namaKatLower, 'tunggal')) {
                $jenis = 'tunggal';
            }

            // 3. Broadcast ke Reverb menggunakan variabel $jenis yang dinamis
            broadcast(new \App\Events\TimerPoolUpdated($id, $waktu, $statusRunning, $jenis));

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Di dalam PoolController.php (Dewan)
    public function selesai($id, $id_detail)
    {
        try {
            // 1. Ambil data partai untuk deteksi kategori & gelanggang
            $partai = \App\Models\Partai::with('kategori')->find($id);
            $namaKatLower = strtolower($partai->kategori->nama_kategori ?? '');
            $gelanggang = $partai->gelanggang ?? 'A';

            // Tentukan jenis untuk channel broadcast (sesuaikan dengan yang didengar Juri & Display)
            $jenis = str_contains($namaKatLower, 'ganda') ? 'ganda' :
                (str_contains($namaKatLower, 'regu') ? 'regu' :
                    (str_contains($namaKatLower, 'tunggal') ? 'tunggal' : 'solo'));

            // 2. Ubah status menjadi 2 (SELESAI) di detail_peserta_tanding
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('id_detail', $id_detail)
                ->update(['status_mulai' => 2]);

            // 3. Tembakkan Alarm ke channel kategori (Display) dan channel pool (Juri)
            // Kita kirim ke keduanya agar Juri & Display sama-sama refresh
            broadcast(new \App\Events\RefreshDisplay($jenis . '.' . $gelanggang));
            broadcast(new \App\Events\RefreshDisplay('pool.' . $gelanggang));

            // 4. Redirect kembali ke manajemen pool
            return redirect()->route('admin.manajemen.pool.index')
                ->with('success', 'Pertandingan Pool selesai dan nilai berhasil dikunci!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyelesaikan pertandingan: ' . $e->getMessage());
        }
    }
    public function updateHukuman(Request $request)
    {
        $id_detail = $request->input('id_detail');
        $action = $request->input('action'); // 'add' atau 'undo'

        // 1. QUERY BACA (PAKE KODE ASLI ANDA)
        $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_detail', $id_detail)
            ->first();

        if (!$detail) {
            return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
        }

        // --- CARI JENIS KATEGORI (BERDASARKAN MODEL ref_kategori) ---
        // Kita ambil data partai untuk tahu ini channel solo, ganda, atau regu
        $partai = \App\Models\Partai::with('kategori')->find($detail->id_partai);
        $namaKat = strtolower($partai->kategori->nama_kategori ?? '');

        $jenis = 'solo';
        if (str_contains($namaKat, 'ganda'))
            $jenis = 'ganda';
        elseif (str_contains($namaKat, 'regu'))
            $jenis = 'regu';
        elseif (str_contains($namaKat, 'tunggal'))
            $jenis = 'tunggal';
        // ------------------------------------------------------------

        $currentHukuman = $detail->hukuman_ketua ?? 0;
        $currentMedian = (float) ($detail->nilai_median ?? 0);

        // Kalkulasi Hukuman (PAKE KODE ASLI ANDA)
        if ($action == 'add') {
            $newHukuman = $currentHukuman + 0.50;
        } else {
            $newHukuman = max(0, $currentHukuman - 0.50);
        }

        // Kalkulasi Total Nilai Bersih (PAKE KODE ASLI ANDA)
        $totalNilaiBersih = $currentMedian - $newHukuman;
        $totalNilaiFormat = number_format($totalNilaiBersih, 3, '.', '');

        // 2. QUERY SIMPAN (UPDATE) (PAKE KODE ASLI ANDA)
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_detail', $id_detail)
            ->update([
                'hukuman_ketua' => $newHukuman,
                'total_nilai' => $totalNilaiFormat
            ]);

        // 3. BROADCAST (DIBUNGKUS TRY-CATCH AGAR DEWAN TIDAK ERROR JIKA REVERB MATI)
        try {
            broadcast(new \App\Events\UpdateHukumanPool(
                $detail->id_partai,
                $id_detail,
                $newHukuman,
                $totalNilaiFormat,
                $jenis
            ));
        } catch (\Exception $e) {
            // Biarkan kosong agar dewan tetap bisa simpan data meski broadcast gagal
        }

        return response()->json([
            'status' => 'success',
            'new_value' => number_format($newHukuman, 2),
            'new_total' => $totalNilaiFormat
        ]);
    }

    public function diskualifikasi(Request $request, $id, $id_detail)
    {
        try {
            // 1. Ambil data peserta dan partai beserta kategorinya
            $peserta = DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->first();

            // Kita butuh data partai untuk tahu ini kategori apa (Solo/Ganda/Regu)
            $partai = \App\Models\Partai::with('kategori')->find($id);

            if (!$peserta || !$partai) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
            }

            // 2. Tentukan Jenis Channel secara Dinamis
            $namaKategori = strtolower($partai->kategori->nama_kategori ?? '');
            $jenisChannel = 'solo'; // Default

            if (str_contains($namaKategori, 'ganda')) {
                $jenisChannel = 'ganda';
            } elseif (str_contains($namaKategori, 'regu')) {
                $jenisChannel = 'regu';
            } elseif (str_contains($namaKategori, 'tunggal')) {
                $jenisChannel = 'tunggal';
            }

            // 3. Update Status DQ di Database
            $column = property_exists($peserta, 'is_disqualifikasi') ? 'is_disqualifikasi' : 'is_disqulifikasi';
            $newStatus = ($peserta->$column == 1) ? 0 : 1;

            DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->update([$column => $newStatus]);


            // 4. BROADCAST dengan Jenis Channel Dinamis
            broadcast(new \App\Events\UpdateDisqPool(
                $id,
                $id_detail,
                $newStatus,
                $peserta->total_nilai,
                $jenisChannel // <-- Sekarang otomatis kirim 'solo', 'ganda', atau 'regu'
            ));

            return response()->json([
                'status' => 'success',
                'is_disqualifikasi' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ==============================================================
    // 1. FUNGSI TOGGLE JURI (ON/OFF) - 11 PARAMETER MATCH WITH EVENT
    // ==============================================================
    public function toggleJuri(Request $request, $id_partai, $no_juri)
    {
        try {
            $isOn = $request->input('isOn');

            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update(['is_on' => $isOn]);

            $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->first();

            $statusInputDb = $kontrol ? $kontrol->status_input : 'Proses';

            $partai = \App\Models\Partai::with('kategori')->find($id_partai);
            $namaKatLower = strtolower($partai->kategori->nama_kategori ?? '');

            $jenis = str_contains($namaKatLower, 'ganda') ? 'ganda' :
                (str_contains($namaKatLower, 'regu') ? 'regu' :
                    (str_contains($namaKatLower, 'tunggal') ? 'tunggal' : 'solo'));

            // PERBAIKAN UTAMA: Pasang 11 parameter lengkap dengan urutan yang benar
            broadcast(new \App\Events\ToggleKontrolJuriPool(
                $id_partai,        // 1. partaiId
                $no_juri,          // 2. noJuri
                $isOn,             // 3. isOn
                $statusInputDb,    // 4. statusInput
                null,              // 5. median
                null,              // 6. deviasi
                null,              // 7. totalNilai
                null,              // 8. sudut
                0,                 // 9. hukuman
                0,                 // 10. isDisq
                $jenis             // 11. jenis (Penentu nama channel dinamis)
            ));

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ==============================================================
    // 2. FUNGSI TOGGLE STATUS INPUT (PROSES/SELESAI) - 11 PARAMETER
    // ==============================================================
    public function toggleStatusInput(Request $request, $id_partai, $no_juri)
    {
        try {
            $status = $request->input('status');

            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update(['status_input' => $status]);

            $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->first();

            $isOnDb = $kontrol ? $kontrol->is_on : 1;

            $partai = \App\Models\Partai::with('kategori')->find($id_partai);
            $namaKatLower = strtolower($partai->kategori->nama_kategori ?? '');

            $jenis = str_contains($namaKatLower, 'ganda') ? 'ganda' :
                (str_contains($namaKatLower, 'regu') ? 'regu' :
                    (str_contains($namaKatLower, 'tunggal') ? 'tunggal' : 'solo'));

            // PERBAIKAN UTAMA: Pasang 11 parameter lengkap dengan urutan yang benar
            broadcast(new \App\Events\ToggleKontrolJuriPool(
                $id_partai,        // 1. partaiId
                $no_juri,          // 2. noJuri
                $isOnDb,           // 3. isOn
                $status,           // 4. statusInput
                null,              // 5. median
                null,              // 6. deviasi
                null,              // 7. totalNilai
                null,              // 8. sudut
                0,                 // 9. hukuman
                0,                 // 10. isDisq
                $jenis             // 11. jenis (Penentu nama channel dinamis)
            ));

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function simpanNilaiPool(Request $request, $id_detail, $no_juri)
    {
        // 1. Cari data detail peserta dan ambil relasi Kategorinya
        $detail = \App\Models\DetailPesertaTanding::with('partai.kategori')
            ->where('id_detail', $id_detail)
            ->first();

        if (!$detail) {
            return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
        }

        $id_partai = $detail->id_partai;
        $namaKategori = strtoupper($detail->partai->kategori->nama_kategori ?? '');

        // 2. Ambil input dari request JS
        $nilaiA = (float) $request->input('nilai_a', 0);
        $nilaiB = (float) $request->input('nilai_b', 0);
        $nilaiC = (float) $request->input('nilai_c', 0);

        // 3. Tentukan Baseline Dinamis (Tunggal/Regu = 9.90, Solo/Ganda = 9.10)
        $baselineSkor = (str_contains($namaKategori, 'TUNGGAL') || str_contains($namaKategori, 'REGU')) ? 9.90 : 9.10;

        // Kalkulasi skor akhir juri ini
        $skorAkhirJuri = $baselineSkor + $nilaiA + $nilaiB + $nilaiC;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 4. Simpan Nilai ke input_nilai_juri
            \Illuminate\Support\Facades\DB::table('input_nilai_juri')->updateOrInsert(
                ['id_detail' => $id_detail, 'no_juri' => $no_juri],
                [
                    'nilai_a' => $nilaiA,
                    'nilai_b' => $nilaiB,
                    'nilai_c' => $nilaiC,
                    'skor_akhir_juri' => $skorAkhirJuri,
                    'updated_at' => now()
                ]
            );

            // 5. Kunci status input juri untuk partai ini menjadi 'Selesai'
            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update(['status_input' => 'Selesai']);

            // ==========================================================
            // 6. KALKULASI ULANG MEDIAN & DEVIASI (Diadaptasi dari Battle)
            // ==========================================================
            $juriScores = \Illuminate\Support\Facades\DB::table('input_nilai_juri')->where('id_detail', $id_detail)->get();
            $kontrolScores = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->where('id_partai', $id_partai)->get();

            $activeScores = [];
            foreach ($kontrolScores as $kontrol) {
                if ($kontrol->is_on == 1) { // Hanya hitung Juri yang ON (Hijau)
                    $scoreRow = $juriScores->where('no_juri', $kontrol->no_juri)->first();
                    if ($scoreRow && $scoreRow->skor_akhir_juri > 0) {
                        $activeScores[] = (float) $scoreRow->skor_akhir_juri;
                    }
                }
            }

            $median = 0;
            $deviasi = 0;
            $count = count($activeScores);

            if ($count > 0) {
                sort($activeScores);
                $mid = floor($count / 2);
                $median = ($count % 2 == 0) ? ($activeScores[$mid - 1] + $activeScores[$mid]) / 2 : $activeScores[$mid];

                $mean = array_sum($activeScores) / $count;
                $variance = 0;
                foreach ($activeScores as $score) {
                    $variance += pow($score - $mean, 2);
                }
                $deviasi = sqrt($variance / $count);
            }

            // Total Nilai Akhir (Median dikurangi Hukuman)
            $hukuman = (float) $detail->hukuman_ketua;
            $totalNilai = $median - $hukuman;

            // Format desimal agar konsisten
            $medianFormat = number_format($median, 3, '.', '');
            $deviasiFormat = number_format($deviasi, 15, '.', '');
            $totalFormat = number_format($totalNilai, 3, '.', '');

            // 7. Simpan hasil Median/Deviasi ke detail_peserta_tanding
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->update([
                    'nilai_median' => $medianFormat,
                    'nilai_deviasi' => $deviasiFormat,
                    'total_nilai' => $totalFormat
                ]);

            \Illuminate\Support\Facades\DB::commit();

            // Ambil state is_on juri ini untuk modal Toggle
            $kontrolJuriIni = $kontrolScores->where('no_juri', $no_juri)->first();
            $isOnDb = $kontrolJuriIni->is_on ?? 1;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()], 500);
        }
        // ==========================================================
        // 8. BROADCAST KE DEWAN DAN DISPLAY 
        // ==========================================================
        try {
            $namaKatLower = strtolower($namaKategori);

            $jenis = 'solo';
            if (str_contains($namaKatLower, 'ganda')) {
                $jenis = 'ganda';
            } elseif (str_contains($namaKatLower, 'regu')) {
                $jenis = 'regu';
            } elseif (str_contains($namaKatLower, 'tunggal')) {
                $jenis = 'tunggal';
            }

            // BROADCAST 1: UPDATE ANGKA (Urutan disamakan dengan Battle)
            broadcast(new \App\Events\UpdateNilaiJuriPool(
                $id_partai,
                $no_juri,
                $nilaiA,          // <-- Nilai A di depan
                $nilaiB,          // <-- Nilai B
                $nilaiC,          // <-- Nilai C
                $skorAkhirJuri,   // <-- Skor Akhir
                $medianFormat,
                $deviasiFormat,
                $totalFormat,
                $jenis
            ));

            // BROADCAST 2: KUNCI FORM JURI & UPDATE BADGE DEWAN
            // (11 Parameter disamakan dengan Battle)
            broadcast(new \App\Events\ToggleKontrolJuriPool(
                $id_partai,
                $no_juri,
                $isOnDb,               // isOn
                'Selesai',             // statusInput
                $medianFormat,         // median
                $deviasiFormat,        // deviasi
                $totalFormat,          // totalNilai
                null,                  // sudut (null karena pool)
                $hukuman,              // hukuman
                0,                     // isDisq
                $jenis                 // jenis
            ));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Broadcast Pool Gagal: ' . $e->getMessage());
            return response()->json([
                'status' => 'success',
                'total' => number_format($skorAkhirJuri, 2),
                'warning' => 'Nilai tersimpan, tapi Broadcast Reverb gagal.'
            ]);
        }

        return response()->json(['status' => 'success', 'total' => number_format($skorAkhirJuri, 2)]);

    }

    public function triggerRefreshDisplay(Request $request)
    {
        try {
            $jenis = $request->input('jenis', 'pool');
            $gelanggang = $request->input('gelanggang', 'A');

            // 1. Tembak ke antena kategori spesifik (Misal: solo.A atau tunggal.A)
            broadcast(new \App\Events\RefreshDisplay($jenis . '.' . $gelanggang));

            // 2. Tembak juga ke antena pool universal sebagai pengaman (pool.A)
            broadcast(new \App\Events\RefreshDisplay('pool.' . $gelanggang));

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


}
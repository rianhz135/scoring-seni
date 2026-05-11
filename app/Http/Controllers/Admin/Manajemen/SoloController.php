<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Partai;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\TimerSoloUpdated; // Import event untuk broadcast timer
use App\Models\DetailPesertaTanding;
use App\Events\UpdateNilaiJuri;
use App\Events\ToggleKontrolJuri;
use App\Events\UpdateHukumanSolo;
class SoloController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            // KUNCI UTAMA: Hanya ambil yang detail_pesertanya punya 'sudut' (otomatis membuang POOL/Kosong)
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(4); // Silakan ubah angkanya jika ingin menyesuaikan jumlah baris per halaman

        return view('pages.manajemen.solo.index', compact('partais'));
    }
    // FUNGSI TRIGGER UNTUK MENGUBAH DATABASE MENJADI 1
    public function mulai($id, $sudut)
    {
        $sudutDb = ucfirst($sudut);

        // 1. KEAMANAN CERDAS: Reset ke 0 HANYA untuk sudut yang sedang aktif/berjalan (1).
        // Sudut yang sudah selesai (2) aman dan tidak akan ikut ter-reset!
        DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('status_mulai', 1) // <--- INI KUNCI UTAMANYA
            ->update(['status_mulai' => 0]);

        // 2. TRIGGER UTAMA: Ubah status peserta yang diklik menjadi 1 (AKTIF)
        DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->update(['status_mulai' => 1]);

        // 3. RESET STATUS KONTROL JURI (PENTING!)
        // Mengembalikan juri ke mode siap tempur untuk sudut yang baru
        DB::table('status_kontrol_juri')
            ->where('id_partai', $id)
            ->update([
                'status_input' => 'Proses', // Ubah status dari Selesai kembali ke Proses
                'is_on' => 1                // Pastikan kotak juri aktif menyala
            ]);

        // 4. Refresh Layar Display
        broadcast(new \App\Events\RefreshDisplay('solo.A'));

        // 5. Lempar ke halaman dewan (Gunakan admin.dewan.solo jika pakai prefix admin)
        return redirect()->route('dewan.solo', ['id' => $id, 'sudut' => $sudut]);
    }

    public function selesai($id, $sudut)
    {
        $sudutDb = ucfirst($sudut);

        // 1. Ubah status menjadi 2 (SELESAI)
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->update(['status_mulai' => 2]);

        // 2. Tembakkan Alarm agar Layar Display kembali ke MODE STANDBY
        broadcast(new \App\Events\RefreshDisplay('solo.A'));

        // 3. Kembalikan operator ke halaman Manajemen Solo
        // (Pastikan nama route-nya sesuai dengan yang ada di web.php Anda)
        return redirect()->route('admin.manajemen.solo.index')
            ->with('success', 'Pertandingan selesai dan nilai berhasil dikunci!');
    }

    public function updateHukuman(Request $request, $id, $sudut)
    {
        $sudutDb = ucfirst($sudut);
        $action = $request->input('action'); // 'add' atau 'undo'

        $peserta = DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb);

        $detail = $peserta->first();

        if (!$detail) {
            return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
        }

        $currentHukuman = $detail->hukuman_ketua ?? 0;
        // Ambil nilai median saat ini (pastikan jadi float)
        $currentMedian = (float) ($detail->nilai_median ?? 0);

        if ($action == 'add') {
            $newHukuman = $currentHukuman + 0.50;
        } else {
            // Undo: kurangi 0.50 tapi jangan sampai di bawah 0
            $newHukuman = max(0, $currentHukuman - 0.50);
        }

        // Kalkulasi Total Nilai Bersih yang baru
        $totalNilaiBersih = $currentMedian - $newHukuman;

        // Update Hukuman dan Total Nilai ke Database
        $peserta->update([
            'hukuman_ketua' => $newHukuman,
            'total_nilai' => number_format($totalNilaiBersih, 3, '.', '')
        ]);
        broadcast(new \App\Events\ToggleKontrolJuri(
            $id,
            0,
            1,
            $currentMedian,
            null,
            null,
            $totalNilaiBersih,
            null,
            $newHukuman
        ));
        return response()->json([
            'status' => 'success',
            'new_value' => number_format($newHukuman, 2),
            // Kirim juga total nilai yang baru ke frontend jika sewaktu-waktu dibutuhkan
            'new_total' => number_format($totalNilaiBersih, 3, '.', '')
        ]);
    }

    public function diskualifikasi(Request $request, $id, $sudut)
    {
        try {
            $sudutDb = ucfirst($sudut);
            $peserta = DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->first();

            if (!$peserta) {
                return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
            }

            $column = property_exists($peserta, 'is_disqualifikasi') ? 'is_disqualifikasi' : 'is_disqulifikasi';
            $newStatus = ($peserta->$column == 1) ? 0 : 1;

            DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->update([$column => $newStatus]);

            // ========================================================
            // BROADCAST KE REVERB (Menitipkan status DISQ ke ToggleKontrolJuri)
            // ========================================================
            broadcast(new \App\Events\ToggleKontrolJuri(
                $id,
                0,              // no_juri 0 (sinyal sistem)
                1,              // isOn
                null,           // statusInput
                null,           // median
                null,           // deviasi
                $peserta->total_nilai, // Kirim nilai asli, nanti JS yang ubah teksnya
                null,           // sudut
                null,           // hukuman
                $newStatus      // Parameter baru: isDisq
            ));

            return response()->json([
                'status' => 'success',
                'is_disqualifikasi' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateTimer(Request $request, $id, $sudut)
    {
        $sudutDb = ucfirst($sudut);
        $detail = \App\Models\DetailPesertaTanding::where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->first();

        if ($detail) {
            $detail->update([
                'waktu_tampil' => (int) $request->waktu, // Simpan sebagai angka detik (misal: 120)
                'is_timer_running' => $request->status_running
            ]);
            broadcast(new TimerSoloUpdated(
                $id,
                $sudut,
                $request->waktu,
                $request->status_running
            ));

            return response()->json([
                'status' => 'success',
                'waktu_simpan' => $detail->waktu_tampil,
                'is_running' => $detail->is_timer_running
            ]);
        }

        return response()->json(['status' => 'error'], 404);
    }

    public function getStatus($id, $sudut)
    {
        try {
            $sudutDb = ucfirst($sudut);

            // Pastikan nama model Anda BENAR di sini
            $detail = \App\Models\DetailPesertaTanding::where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->first();

            if ($detail) {
                return response()->json([
                    'status' => 'success',
                    // Gunakan ?? 0 untuk mencegah error jika data di database null
                    'waktu_tampil' => (int) ($detail->waktu_tampil ?? 0),
                    'is_timer_running' => (int) ($detail->is_timer_running ?? 0),
                    'is_disqualifikasi' => (int) ($detail->is_disqualifikasi ?? 0),
                    'hukuman_ketua' => (float) ($detail->hukuman_ketua ?? 0),
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Data Peserta Tidak Ditemukan'], 404);

        } catch (\Exception $e) {
            // Menangkap error dari Laravel dan mengirimkannya ke browser
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Tambahkan "Request $request" di parameternya
    public function toggleStatusJuri(Request $request, $id_partai, $no_juri)
    {
        try {
            $juri = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->first();

            if ($juri) {
                // 1. Balikkan status ON/OFF
                $newStatus = $juri->is_on == 1 ? 0 : 1;

                \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                    ->where('id_partai', $id_partai)
                    ->where('no_juri', $no_juri)
                    ->update(['is_on' => $newStatus]);

                // ==========================================
                // 2. KALKULASI ULANG REKAP NILAI 
                // ==========================================
                // Ambil sudut dari fetch JS untuk tahu atlet mana yang sedang main
                $sudutDb = ucfirst($request->sudut);
                $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                    ->where('id_partai', $id_partai)
                    ->where('sudut', $sudutDb)
                    ->first();

                $medianFormat = null;
                $deviasiFormat = null;
                $totalNilaiFormat = null;

                if ($detail) {
                    $id_detail = $detail->id_detail;
                    $juriScores = \Illuminate\Support\Facades\DB::table('input_nilai_juri')->where('id_detail', $id_detail)->get();
                    $kontrolScores = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->where('id_partai', $id_partai)->get();

                    $activeScores = [];
                    foreach ($kontrolScores as $kontrol) {
                        if ($kontrol->is_on == 1) { // Hanya Juri yang ON yang dihitung!
                            $scoreRow = $juriScores->where('no_juri', $kontrol->no_juri)->first();
                            if ($scoreRow) {
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
                        if ($count % 2 == 0) {
                            $median = ($activeScores[$mid - 1] + $activeScores[$mid]) / 2;
                        } else {
                            $median = $activeScores[$mid];
                        }

                        $mean = array_sum($activeScores) / $count;
                        $variance = 0;
                        foreach ($activeScores as $score) {
                            $variance += pow($score - $mean, 2);
                        }
                        $variance /= $count;
                        $deviasi = sqrt($variance);
                    }

                    $hukuman = (float) $detail->hukuman_ketua;
                    $totalNilai = $median - $hukuman;

                    $medianFormat = number_format($median, 3, '.', '');
                    $deviasiFormat = number_format($deviasi, 15, '.', '');
                    $totalNilaiFormat = number_format($totalNilai, 3, '.', '');

                    \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                        ->where('id_detail', $id_detail)
                        ->update([
                            'nilai_median' => $medianFormat,
                            'nilai_deviasi' => $deviasiFormat,
                            'total_nilai' => $totalNilaiFormat
                        ]);
                }

                // 3. BROADCAST REVERB (Bawa data Median/Deviasi juga)
                broadcast(new ToggleKontrolJuri(
                    $id_partai,
                    $no_juri,
                    $newStatus,
                    $juri->status_input,
                    $medianFormat,
                    $deviasiFormat,
                    $totalNilaiFormat,
                    $sudutDb
                ));

                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatusInput(Request $request, $id_partai, $no_juri)
    {
        try {
            // Gunakan Query Builder agar lebih ringan dan langsung ke tabel
            $affected = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update([
                    'status_input' => $request->status,
                    'updated_at' => now() // Tambahkan jika ada kolom ini
                ]);

            if ($affected === 0) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
            }

            // Ambil state is_on untuk dikirim ke broadcast
            $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->first();

            // ==========================================
            // BROADCAST REVERB: UPDATE BADGE PROSES/SELESAI
            // ==========================================
            broadcast(new ToggleKontrolJuri(
                $id_partai,
                $no_juri,
                $kontrol->is_on,
                $request->status,
                null, // Kita isi null karena Median tidak berubah saat tombol ini diklik
                null, // Kita isi null karena Deviasi tidak berubah
                null, // Kita isi null karena Total Nilai tidak berubah
                $sudutDb = null // Kita isi null karena Sudut tidak berubah
            ));

            return response()->json(['status' => 'success', 'new_status' => $request->status]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function simpanNilaiJuri(Request $request, $id_detail, $no_juri)
    {
        try {
            // 1. Ambil nilai dari request
            $nilaiA = (float) ($request->nilai_a ?? 0);
            $nilaiB = (float) ($request->nilai_b ?? 0);
            $nilaiC = (float) ($request->nilai_c ?? 0);
            $skorAkhir = 9.10 + $nilaiA + $nilaiB + $nilaiC;

            // 2. Simpan Nilai Juri ke DB
            \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                ->where('id_detail', $id_detail)
                ->where('no_juri', $no_juri)
                ->update([
                    'nilai_a' => $nilaiA,
                    'nilai_b' => $nilaiB,
                    'nilai_c' => $nilaiC,
                    'skor_akhir_juri' => $skorAkhir,
                    'updated_at' => now(),
                ]);

            // 3. Ambil data Detail Peserta untuk mendapatkan id_partai
            $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->first();
            $id_partai = $detail->id_partai;

            // 4. Update status juri menjadi 'Selesai' di tabel kontrol
            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update(['status_input' => 'Selesai']);

            // ==========================================
            // 5. KALKULASI MEDIAN & DEVIASI (DIPINDAH DARI getJuriScores)
            // ==========================================
            $juriScores = \Illuminate\Support\Facades\DB::table('input_nilai_juri')->where('id_detail', $id_detail)->get();
            $kontrolScores = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->where('id_partai', $id_partai)->get();

            $activeScores = [];
            foreach ($kontrolScores as $kontrol) {
                if ($kontrol->is_on == 1) {
                    $scoreRow = $juriScores->where('no_juri', $kontrol->no_juri)->first();
                    if ($scoreRow) {
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
                if ($count % 2 == 0) {
                    $median = ($activeScores[$mid - 1] + $activeScores[$mid]) / 2;
                } else {
                    $median = $activeScores[$mid];
                }

                $mean = array_sum($activeScores) / $count;
                $variance = 0;
                foreach ($activeScores as $score) {
                    $variance += pow($score - $mean, 2);
                }
                $variance /= $count;
                $deviasi = sqrt($variance);
            }

            $hukuman = (float) $detail->hukuman_ketua;
            $totalNilai = $median - $hukuman;

            $medianFormat = number_format($median, 3, '.', '');
            $deviasiFormat = number_format($deviasi, 15, '.', '');
            $totalNilaiFormat = number_format($totalNilai, 3, '.', '');

            // 6. Simpan Hasil Kalkulasi Akhir
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->update([
                    'nilai_median' => $medianFormat,
                    'nilai_deviasi' => $deviasiFormat,
                    'total_nilai' => $totalNilaiFormat
                ]);

            // ==========================================
            // 7. BROADCAST REVERB: UPDATE TABEL JURI & REKAP
            // ==========================================
            broadcast(new UpdateNilaiJuri(
                $id_partai,
                $no_juri,
                $nilaiA,
                $nilaiB,
                $nilaiC,
                $skorAkhir,
                $medianFormat,
                $deviasiFormat,
                $totalNilaiFormat
            ));

            // Ambil state is_on juri ini
            $kontrolJuriIni = $kontrolScores->where('no_juri', $no_juri)->first();

            // ==========================================
            // 8. BROADCAST REVERB: KUNCI FORM JURI & UBAH BADGE DEWAN
            // ==========================================
            broadcast(new ToggleKontrolJuri(
                $id_partai,
                $no_juri,
                $kontrolJuriIni->is_on ?? 1,
                'Selesai',
                $medianFormat,         // 5. median
                $deviasiFormat,        // 6. deviasi
                $totalNilaiFormat,     // 7. total nilai
                null,                  // 8. sudutDb (tidak perlu di sini)
                $hukuman               // 9. HUKUMAN ASLI! <--- INI KUNCI UTAMANYA
            ));

            return response()->json([
                'status' => 'success',
                'skor_akhir' => $skorAkhir
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk menembak sinyal refresh ke Layar Display secara manual
    public function triggerRefreshDisplay()
    {
        // Sesuaikan 'A' dengan gelanggang Anda jika sistem multi-gelanggang
        broadcast(new \App\Events\RefreshDisplay('solo.A'));

        return response()->json([
            'status' => 'success',
            'message' => 'Sinyal refresh berhasil dikirim ke Layar Display!'
        ]);
    }
}
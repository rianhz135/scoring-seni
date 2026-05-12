<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Partai;
use App\Events\ToggleKontrolJuriRegu;
use App\Events\UpdateNilaiJuriRegu;
use App\Events\UpdateHukumanRegu;
use App\Events\TimerReguUpdated;

class ReguController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%REGU%');
            })
            // KUNCI UTAMA: Hanya ambil yang detail_pesertanya punya 'sudut' (otomatis membuang POOL/Kosong)
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(4); // Silakan ubah angkanya jika ingin menyesuaikan jumlah baris per halaman

        return view('pages.manajemen.regu.index', compact('partais'));
    }

    public function mulai($id, $sudut)
    {
        try {
            $sudutDb = ucfirst($sudut);

            // Gunakan Transaction agar data terkunci dengan aman
            \Illuminate\Support\Facades\DB::transaction(function () use ($id, $sudutDb) {
                // 1. Reset sudut lain yang mungkin masih 'aktif' (1) menjadi 'standby' (0)
                \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                    ->where('id_partai', $id)
                    ->where('status_mulai', 1)
                    ->update(['status_mulai' => 0]);

                // 2. Aktifkan sudut terpilih
                \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                    ->where('id_partai', $id)
                    ->where('sudut', $sudutDb)
                    ->update(['status_mulai' => 1]);

                // 3. Reset Kontrol Juri
                \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                    ->where('id_partai', $id)
                    ->update([
                        'status_input' => 'Proses',
                        'is_on' => 1
                    ]);
            });

            // 4. Kirim sinyal refresh (Sama seperti Solo)
            // Pastikan Event RefreshDisplay menggunakan 'ShouldBroadcastNow'
            broadcast(new \App\Events\RefreshDisplay('regu.A'));

            // 5. Kembali ke Dewan
            return redirect()->route('dewan.regu', ['id' => $id, 'sudut' => $sudut]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updateTimer(Request $request, $id, $sudut)
    {
        try {
            // Catat ke log Laravel (storage/logs/laravel.log)
            \Illuminate\Support\Facades\Log::info("AJAX Timer Regu Masuk: ID Partai $id, Sudut $sudut");

            $sudutDb = ucfirst($sudut);

            // 1. Update Database (Tanpa peduli affected rows)
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->update([
                    'waktu_tampil' => (int) $request->waktu,
                    'is_timer_running' => $request->status_running
                ]);

            // 2. PAKSA BROADCAST (Gunakan namespace penuh agar tidak error jika lupa 'use')
            broadcast(new TimerReguUpdated(
                $id,
                $sudut,
                $request->waktu,
                $request->status_running
            ));

            return response()->json([
                'status' => 'success',
                'waktu_simpan' => (int) $request->waktu,
                'is_running' => $request->status_running
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error Timer Regu: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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
        broadcast(new UpdateHukumanRegu(
            $id,
            $newHukuman,
            $totalNilaiBersih
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

            // Ambil data salah satu peserta sebagai acuan
            $peserta = DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->first();

            if (!$peserta) {
                return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
            }

            // Cek nama kolom yang benar di DB (handle typo is_disqulifikasi)
            $column = property_exists($peserta, 'is_disqualifikasi') ? 'is_disqualifikasi' : 'is_disqulifikasi';

            // Toggle status
            $newStatus = ($peserta->$column == 1) ? 0 : 1;

            // UPDATE KETIGA ANGGOTA REGU SEKALIGUS
            DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->update([$column => $newStatus]);

            // ========================================================
            // BROADCAST KE REVERB
            // ========================================================
            broadcast(new ToggleKontrolJuriRegu(
                $id,
                0,                      // no_juri 0
                1,                      // isOn
                'Selesai',              // statusInput (Kunci layar juri)
                null,                   // median
                null,                   // deviasi
                $peserta->total_nilai,
                $sudutDb,               // <--- SEKARANG TERISI (Biru/Merah)
                null,                   // hukuman
                $newStatus              // isDisq
            ));

            return response()->json([
                'status' => 'success',
                'is_disqualifikasi' => $newStatus
            ]);

        } catch (\Exception $e) {
            // Log error ke file laravel.log agar Anda bisa cek jika broadcast gagal
            \Log::error("Error Diskualifikasi Regu: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatusJuriRegu(Request $request, $id_partai, $no_juri)
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

                    $hukuman = (float) ($detail->hukuman_ketua ?? 0);
                    $totalNilai = $median - $hukuman;

                    // Presisi 3 Desimal Khusus Regu
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

                // ==========================================
                // 3. BROADCAST REVERB KHUSUS REGU
                // ==========================================
                // Pastikan class Event 'ToggleKontrolJuriRegu' sudah ada di app/Events
                broadcast(new ToggleKontrolJuriRegu(
                    $id_partai,
                    $no_juri,
                    $newStatus,
                    $juri->status_input ?? 'Proses',
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
            // PERBAIKAN: Gunakan \App\Events\ToggleKontrolJuriRegu
            broadcast(new ToggleKontrolJuriRegu(
                $id_partai,
                $no_juri,
                $kontrol->is_on,
                $request->status, // 'Proses' atau 'Selesai'
                null, // median
                null, // deviasi
                null, // totalNilai
                null  // sudut
            ));

            return response()->json(['status' => 'success', 'new_status' => $request->status]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function selesai($id, $sudut)
    {
        try {
            $sudutDb = ucfirst($sudut);

            // 1. Ubah status menjadi 2 (SELESAI) - SAMAKAN DENGAN SOLO
            // Ini kunci agar sistem tahu pertandingan sudah berakhir
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_partai', $id)
                ->where('sudut', $sudutDb)
                ->update(['status_mulai' => 2]);

            // 2. Tembakkan Alarm agar Layar Display kembali ke MODE STANDBY
            broadcast(new \App\Events\RefreshDisplay('regu.A'));

            // 3. Kembalikan ke halaman Manajemen Regu
            // Pastikan nama route-nya benar. Jika Solo adalah 'admin.manajemen.solo.index',
            // kemungkinan besar Regu adalah 'admin.manajemen.regu.index'
            return redirect()->route('admin.manajemen.regu.index')
                ->with('success', 'Pertandingan Regu selesai dan nilai berhasil dikunci!');

        } catch (\Exception $e) {
            // Jika error, balik ke halaman dewan dengan pesan error
            return redirect()->back()->with('error', 'Gagal menyelesaikan pertandingan: ' . $e->getMessage());
        }
    }

    public function simpanNilaiJuri(Request $request, $id_detail, $no_juri)
    {
        try {
            // 1. Ambil nilai dari request
            $nilaiA = (float) ($request->nilai_a ?? 0);
            $nilaiB = (float) ($request->nilai_b ?? 0);
            $skorAkhir = 9.90 - $nilaiA + $nilaiB;

            // 2. Simpan Nilai Juri ke DB
            \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                ->where('id_detail', $id_detail)
                ->where('no_juri', $no_juri)
                ->update([
                    'nilai_a' => $nilaiA,
                    'nilai_b' => $nilaiB,
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

            // 7. BROADCAST UPDATE TABEL (8 Parameter)
            broadcast(new UpdateNilaiJuriRegu(
                $id_partai,
                $no_juri,
                $nilaiA,
                $nilaiB,
                $skorAkhir,
                $medianFormat,
                $deviasiFormat,
                $totalNilaiFormat
            ));

            // 8. BROADCAST KUNCI STATUS (Pastikan menggunakan \App\Events\)
            broadcast(new ToggleKontrolJuriRegu(
                $id_partai,
                $no_juri,
                $kontrolJuriIni->is_on ?? 1,
                'Selesai',             // status input
                $medianFormat,         // nilai median
                $deviasiFormat,        // nilai deviasi
                $totalNilaiFormat,     // total nilai
                null,                  // sudut (bisa diisi null jika tidak perlu pindah sudut)
                $hukuman               // hukuman dari ketua
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

    public function inputNilaiJuri(Request $request, $id_partai, $no_juri, $sudut)
    {
        try {
            $sudutDb = ucfirst($sudut);
            $nilaiA = (float) ($request->nilai_a ?? 0);
            $nilaiB = (float) ($request->nilai_b ?? 0);

            $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_partai', $id_partai)
                ->where('sudut', $sudutDb)
                ->first();

            if ($detail) {
                $skorAkhirJuri = 9.90 - $nilaiA + $nilaiB;

                // 1. Simpan nilai Juri ke DB
                \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                    ->where('id_detail', $detail->id_detail)
                    ->where('no_juri', $no_juri)
                    ->update([
                        'nilai_a' => $nilaiA,
                        'nilai_b' => $nilaiB,
                        'skor_akhir_juri' => $skorAkhirJuri,
                        'updated_at' => now()
                    ]);

                // 2. AMBIL JURI YANG SEDANG "ON" SAJA UNTUK KALKULASI
                $kontrolScores = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                    ->where('id_partai', $id_partai)
                    ->where('is_on', 1) // HANYA YANG ON
                    ->get();

                $juriScores = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                    ->where('id_detail', $detail->id_detail)
                    ->get();

                $activeScores = [];
                foreach ($kontrolScores as $kontrol) {
                    $scoreRow = $juriScores->where('no_juri', $kontrol->no_juri)->first();
                    if ($scoreRow) {
                        $activeScores[] = (float) $scoreRow->skor_akhir_juri;
                    }
                }

                // 3. HITUNG MEDIAN & DEVIASI (Sesuai Logika Toggle)
                $median = 0;
                $deviasi = 0;
                $count = count($activeScores);
                if ($count > 0) {
                    sort($activeScores);
                    $mid = floor($count / 2);
                    $median = ($count % 2 == 0) ? ($activeScores[$mid - 1] + $activeScores[$mid]) / 2 : $activeScores[$mid];

                    // Hitung Deviasi
                    $mean = array_sum($activeScores) / $count;
                    $variance = 0;
                    foreach ($activeScores as $score) {
                        $variance += pow($score - $mean, 2);
                    }
                    $deviasi = sqrt($variance / $count);
                }

                $hukuman = (float) ($detail->hukuman_ketua ?? 0);
                $totalNilai = $median - $hukuman; // TOTAL REGU = MEDIAN - HUKUMAN

                // 4. SIARKAN DATA (Pastikan urutan 8 parameter pas)
                broadcast(new UpdateNilaiJuriRegu(
                    $id_partai,
                    (int) $no_juri,
                    (float) $nilaiA,
                    (float) $nilaiB,
                    (float) $skorAkhirJuri,
                    (float) $median,
                    (float) $deviasi,
                    (float) $totalNilai
                ));

                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    public function triggerRefreshDisplay()
    {
        try {
            // Ingat, gunakan 'regu.A'
            broadcast(new \App\Events\RefreshDisplay('regu.A'));
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
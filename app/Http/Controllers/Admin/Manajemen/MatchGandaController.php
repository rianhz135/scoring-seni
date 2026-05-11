<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Partai;
use App\Events\TimerGandaUpdated;
use Illuminate\Http\Request;
use App\Events\UpdateNilaiJuriGanda;
use Illuminate\Support\Facades\DB;
use App\Events\ToggleKontrolJuriGanda;
class MatchGandaController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%GANDA%');
            })
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(4);

        return view('pages.manajemen.ganda.index', compact('partais'));
    }

    public function mulai($id, $sudut)
    {
        $sudutDb = ucfirst($sudut);

        // 1. Reset sudut yang sedang aktif ke 0 (sudut yang selesai [2] aman)
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('status_mulai', 1)
            ->update(['status_mulai' => 0]);

        // 2. Aktifkan sudut yang diklik (Otomatis 2 peserta ganda di sudut ini akan berubah jadi 1)
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->update(['status_mulai' => 1]);

        // 3. Reset status juri
        \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $id)
            ->update([
                'status_input' => 'Proses',
                'is_on' => 1
            ]);

        // 4. Refresh Layar Display
        broadcast(new \App\Events\RefreshDisplay('ganda.A'));

        // 5. Lempar ke halaman dewan ganda
        return redirect()->route('dewan.ganda', ['id' => $id, 'sudut' => $sudut]);
    }
    public function selesai($id, $sudut)
    {
        $sudutDb = ucfirst($sudut);

        // 1. Ubah status menjadi 2 (SELESAI) secara massal (2 orang Ganda)
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->update(['status_mulai' => 2]);

        // 2. Tembakkan Alarm agar Layar Display kembali ke MODE STANDBY
        broadcast(new \App\Events\RefreshDisplay('ganda.A'));

        // 3. Kembalikan operator ke halaman Manajemen Ganda (PERBAIKAN DI SINI)
        return redirect()->route('admin.manajemen.ganda.index')
            ->with('success', 'Pertandingan Ganda selesai dan nilai berhasil dikunci!');
    }

    public function updateTimer(Request $request, $id, $sudut)
    {
        $sudutDb = ucfirst($sudut);

        // UPDATE MASSAL KE KEDUA PESERTA GANDA DI SUDUT TERSEBUT
        $affected = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->update([
                'waktu_tampil' => (int) $request->waktu,
                'is_timer_running' => $request->status_running
            ]);

        if ($affected > 0) {
            broadcast(new \App\Events\TimerGandaUpdated(
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
        }

        return response()->json(['status' => 'error'], 404);
    }
    public function updateHukuman(Request $request, $id, $sudut)
    {
        $sudutDb = ucfirst($sudut);
        $action = $request->input('action'); // 'add' atau 'undo'

        // 1. QUERY BACA (SELECT)
        $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->first();

        if (!$detail) {
            return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
        }

        $currentHukuman = $detail->hukuman_ketua ?? 0;
        $currentMedian = (float) ($detail->nilai_median ?? 0);

        if ($action == 'add') {
            $newHukuman = $currentHukuman + 0.50;
        } else {
            $newHukuman = max(0, $currentHukuman - 0.50);
        }

        // Kalkulasi Total Nilai Bersih yang baru
        $totalNilaiBersih = $currentMedian - $newHukuman;
        $totalNilaiFormat = number_format($totalNilaiBersih, 3, '.', '');

        // 2. QUERY SIMPAN (UPDATE MASSAL) - Gunakan awalan DB::table() baru agar bersih dari LIMIT 1
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_partai', $id)
            ->where('sudut', $sudutDb)
            ->update([
                'hukuman_ketua' => $newHukuman,
                'total_nilai' => $totalNilaiFormat
            ]);

        // 3. BROADCAST EVENT BARU (Atau gunakan ToggleKontrolJuriGanda sesuai pilihan Anda)
        broadcast(new \App\Events\UpdateHukumanGanda(
            $id,
            $sudutDb,
            $newHukuman,
            $totalNilaiFormat
        ));

        // JIKA DI DISPLAY ANDA MENGGUNAKAN TOGGLE KONTROL JURI UNTUK HUKUMAN, BUKA KOMENTAR DI BAWAH INI:
        /*
        broadcast(new \App\Events\ToggleKontrolJuriGanda(
            $id, 0, 1, 'Proses', $currentMedian, (float) ($detail->nilai_deviasi ?? 0), 
            $totalNilaiFormat, $sudutDb, $newHukuman
        ));
        */

        return response()->json([
            'status' => 'success',
            'new_value' => number_format($newHukuman, 2),
            'new_total' => $totalNilaiFormat
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
            broadcast(new \App\Events\ToggleKontrolJuriGanda(
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
                        ->where('id_partai', $id_partai)
                        ->where('sudut', $sudutDb)
                        ->update([
                            'nilai_median' => $medianFormat,
                            'nilai_deviasi' => $deviasiFormat,
                            'total_nilai' => $totalNilaiFormat
                        ]);
                }

                // 3. BROADCAST REVERB (Bawa data Median/Deviasi juga)
                broadcast(new ToggleKontrolJuriGanda(
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
            broadcast(new ToggleKontrolJuriGanda(
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

            // 3. Ambil data Detail Peserta untuk mendapatkan id_partai & sudut
            $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_detail', $id_detail)
                ->first();

            $id_partai = $detail->id_partai;
            $sudutAktif = $detail->sudut;

            // 4. Update status juri (HAPUS ->where('sudut') KARENA BIKIN ERROR 500)
            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update(['status_input' => 'Selesai']);

            // ==========================================
            // 5. KALKULASI MEDIAN & DEVIASI
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

            // 6. Simpan Hasil Kalkulasi Akhir KE KEDUA ATLET GANDA (Ubah ke id_partai & sudut)
            \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_partai', $id_partai)
                ->where('sudut', $sudutAktif)
                ->update([
                    'nilai_median' => $medianFormat,
                    'nilai_deviasi' => $deviasiFormat,
                    'total_nilai' => $totalNilaiFormat
                ]);

            // ==========================================
            // 7. BROADCAST REVERB: UPDATE TABEL JURI & REKAP
            // ==========================================
            broadcast(new \App\Events\UpdateNilaiJuriGanda(
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
            broadcast(new \App\Events\ToggleKontrolJuriGanda(
                $id_partai,
                $no_juri,
                $kontrolJuriIni->is_on ?? 1,
                'Selesai',
                $medianFormat,         // 5. median
                $deviasiFormat,        // 6. deviasi
                $totalNilaiFormat,     // 7. total nilai
                null,                  // 8. sudutDb (tidak perlu di sini)
                $hukuman               // 9. HUKUMAN ASLI
            ));

            return response()->json([
                'status' => 'success',
                'skor_akhir' => $skorAkhir
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function triggerRefreshDisplay()
    {
        try {
            // Ingat, gunakan 'ganda.A'
            broadcast(new \App\Events\RefreshDisplay('ganda.A'));
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

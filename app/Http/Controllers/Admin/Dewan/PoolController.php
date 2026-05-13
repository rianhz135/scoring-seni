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
        $waktu = $request->input('waktu');
        $statusRunning = $request->input('status_running');

        // Update DB
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_detail', $id_detail)
            ->update([
                'waktu_tampil' => $waktu,
                'is_timer_running' => $statusRunning
            ]);

        // Broadcast ke Reverb (Gunakan Event TimerPoolUpdated)
        // Tentukan jenis channel (solo/ganda/regu) secara dinamis jika perlu
        broadcast(new \App\Events\TimerPoolUpdated($id, $waktu, $statusRunning, 'solo'));

        return response()->json(['status' => 'success']);
    }

    // Di dalam PoolController.php (Dewan)
    public function selesai(Request $request, $id, $id_detail)
    {
        // Update status peserta ini menjadi 2 (SELESAI)
        \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
            ->where('id_detail', $id_detail)
            ->update(['status_mulai' => 2]);

        // Redirect kembali ke halaman manajemen atau tetap di dewan dengan pesan sukses
        return redirect()->back()->with('success', 'Pertandingan Selesai dan Nilai Disimpan.');
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

    public function toggleJuri(Request $request, $id_partai, $no_juri)
    {
        // 1. Ambil input
        $isOn = $request->input('isOn');

        // 2. Simpan status Juri ke Database
        \DB::table('status_kontrol_juri')
            ->where('id_partai', $id_partai)
            ->where('no_juri', $no_juri)
            ->update([
                'is_on' => $isOn,
                'updated_at' => now()
            ]);

        // 3. KALKULASI ULANG (Agar Median di Dewan langsung berubah)
        $aktif = \App\Models\DetailPesertaTanding::where('id_partai', $id_partai)
            ->where('status_mulai', 1)
            ->first();

        $median = null;
        $deviasi = null;
        $totalNilai = null;

        if ($aktif) {
            // Ambil semua juri yang sedang ON untuk partai ini
            $juriOn = \DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('is_on', 1)
                ->pluck('no_juri');

            // Ambil skor dari juri-juri yang ON saja
            $scores = \DB::table('input_nilai_juri')
                ->where('id_detail', $aktif->id_detail)
                ->whereIn('no_juri', $juriOn)
                // Sesuai existing: gunakan nama kolom 'skor_akhir_juri'
                ->where('skor_akhir_juri', '>', 0)
                ->pluck('skor_akhir_juri')
                ->toArray();

            if (count($scores) > 0) {
                sort($scores);
                $count = count($scores);
                $mid = floor($count / 2);
                $median = ($count % 2 == 0) ? ($scores[$mid - 1] + $scores[$mid]) / 2 : $scores[$mid];

                // Hitung Deviasi (Opsional, jika dibutuhkan real-time)
                $mean = array_sum($scores) / $count;
                $variance = 0;
                foreach ($scores as $s) {
                    $variance += pow($s - $mean, 2);
                }
                $deviasi = sqrt($variance / $count);

                $totalNilai = $median - (float) $aktif->hukuman_ketua;

                // Simpan hasil kalkulasi terbaru ke record peserta
                $aktif->update([
                    'nilai_median' => $median,
                    'nilai_deviasi' => $deviasi,
                    'total_nilai' => $totalNilai
                ]);
            }
        }

        // 4. Tentukan Jenis Channel
        $partai = \App\Models\Partai::with('kategori')->find($id_partai);
        $namaKat = strtolower($partai->kategori->nama_kategori ?? '');
        $jenis = str_contains($namaKat, 'ganda') ? 'ganda' : (str_contains($namaKat, 'regu') ? 'regu' : 'solo');

        // 5. Broadcast dengan DATA LENGKAP
        broadcast(new \App\Events\ToggleKontrolJuriPool(
            $id_partai,
            $no_juri,
            $isOn,
            null, // statusInput
            $median,
            $deviasi,
            $totalNilai,
            null, // sudut
            null, // hukuman
            null, // isDisq
            $jenis
        ));

        return response()->json([
            'status' => 'success',
            'isOn' => $isOn,
            'median' => $median
        ]);
    }

    public function toggleStatusInput(Request $request, $id_partai, $no_juri)
    {
        // 1. Ambil input dari request
        $status = $request->input('status'); // 'Selesai' atau 'Proses'

        // 2. SIMPAN KE DATABASE (Agar data permanen saat di-refresh)
        \DB::table('status_kontrol_juri')
            ->where('id_partai', $id_partai)
            ->where('no_juri', $no_juri)
            ->update([
                'status_input' => $status,
                'updated_at' => now()
            ]);

        // 3. Ambil data partai untuk menentukan jenis channel
        $partai = \App\Models\Partai::with('kategori')->find($id_partai);
        $namaKat = strtolower($partai->kategori->nama_kategori ?? '');

        $jenis = 'solo';
        if (str_contains($namaKat, 'ganda'))
            $jenis = 'ganda';
        elseif (str_contains($namaKat, 'regu'))
            $jenis = 'regu';
        elseif (str_contains($namaKat, 'tunggal'))
            $jenis = 'tunggal';

        // 4. Broadcast sinyal
        broadcast(new \App\Events\ToggleKontrolJuriPool(
            $id_partai,
            $no_juri,
            1,       // Tetap ON box-nya
            $status, // Sinyal status input
            null,
            null,
            null,
            null,
            null,
            null,
            $jenis
        ));

        return response()->json(['status' => 'success', 'statusInput' => $status]);
    }

    public function simpanNilaiPool(Request $request, $id_detail, $no_juri)
    {
        // 1. Cari data detail peserta
        $detail = DB::table('detail_peserta_seni')->where('id_detail', $id_detail)->first();

        if (!$detail) {
            return response()->json(['status' => 'error', 'message' => 'Peserta tidak ditemukan'], 404);
        }

        // 2. Ambil input (Nilai C otomatis 0 jika tidak dikirim, misal pada Regu)
        $nilaiA = (float) $request->input('nilai_a', 0);
        $nilaiB = (float) $request->input('nilai_b', 0);
        $nilaiC = (float) $request->input('nilai_c', 0);

        // 3. Kalkulasi (Seni Pool standar baseline 9.10)
        $total = 9.10 + $nilaiA + $nilaiB + $nilaiC;

        DB::beginTransaction();
        try {
            // 4. Simpan Nilai
            DB::table('input_nilai_juri')->updateOrInsert(
                ['id_detail' => $id_detail, 'no_juri' => $no_juri],
                [
                    'nilai_a' => $nilaiA,
                    'nilai_b' => $nilaiB,
                    'nilai_c' => $nilaiC,
                    'skor_akhir_juri' => $total,
                    'updated_at' => now()
                ]
            );

            // 5. Kunci status input juri untuk partai ini
            DB::table('status_kontrol_juri')
                ->where('id_partai', $detail->id_partai)
                ->where('no_juri', $no_juri)
                ->update(['status_input' => 'Selesai']);

            DB::commit();
            return response()->json(['status' => 'success', 'total' => number_format($total, 2)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

}
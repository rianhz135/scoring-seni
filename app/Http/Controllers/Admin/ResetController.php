<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetController extends Controller
{
    /**
     * Reset seluruh data kategori Tanding:
     * - peserta_tanding
     * - partai_tanding
     * - log_nilai_juri_tanding
     * - poin_sah_tanding
     * - jatuhan_tanding
     * - pelanggaran_tanding
     * - verifikasi_dewan
     * - jawaban_verifikasi_juri
     * - status_kontrol_juri_tanding
     */
    public function resetTanding()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tables = [
                'jawaban_verifikasi_juri',
                'verifikasi_dewan',
                'log_nilai_juri_tanding',
                'poin_sah_tanding',
                'jatuhan_tanding',
                'pelanggaran_tanding',
                'status_kontrol_juri_tanding',
                'partai_tanding',
                'peserta_tanding',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Broadcast refresh layar display tanding agar kembali ke mode standby
            try {
                broadcast(new \App\Events\RefreshDisplay('tanding.A'));
            } catch (\Throwable $e) {}

            return response()->json([
                'status'  => 'success',
                'message' => 'Seluruh data peserta & partai Tanding berhasil direset.',
            ]);
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reset seluruh data kategori Seni & Pool:
     * - input_nilai_juri
     * - detail_peserta_tanding (plot giliran sudut/pool)
     * - status_kontrol_juri
     * - partai (jadwal partai seni)
     * - partais
     * - peserta (master peserta seni & pool)
     */
    public function resetSeniPool()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tables = [
                'input_nilai_juri',
                'detail_peserta_tanding',
                'status_kontrol_juri',
                'partai',
                'partais',
                'peserta',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Broadcast refresh display seni
            try {
                broadcast(new \App\Events\RefreshDisplay('solo.A'));
                broadcast(new \App\Events\RefreshDisplay('ganda.A'));
                broadcast(new \App\Events\RefreshDisplay('tunggal.A'));
                broadcast(new \App\Events\RefreshDisplay('regu.A'));
            } catch (\Throwable $e) {}

            return response()->json([
                'status'  => 'success',
                'message' => 'Seluruh data peserta & partai Seni (Solo, Ganda, Tunggal, Regu, Pool) berhasil direset.',
            ]);
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reset SEMUA data pertandingan & peserta:
     * - Seluruh data Tanding
     * - Seluruh data Seni & Pool
     * - Master kelas dipastikan tetap ada default (KELAS A - E)
     * - Akun users (admin & juri) tetap aman
     */
    public function resetSemua()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tables = [
                // Tanding
                'jawaban_verifikasi_juri',
                'verifikasi_dewan',
                'log_nilai_juri_tanding',
                'poin_sah_tanding',
                'jatuhan_tanding',
                'pelanggaran_tanding',
                'status_kontrol_juri_tanding',
                'partai_tanding',
                'peserta_tanding',

                // Seni & Pool
                'input_nilai_juri',
                'detail_peserta_tanding',
                'status_kontrol_juri',
                'partai',
                'partais',
                'peserta',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Pastikan master kelas tetap tersedia untuk input data baru
            if (Schema::hasTable('ref_kelas')) {
                $kelasDefaults = ['KELAS A', 'KELAS B', 'KELAS C', 'KELAS D', 'KELAS E'];
                foreach ($kelasDefaults as $k) {
                    \App\Models\RefKelas::firstOrCreate(['label_kelas' => $k]);
                }
            }

            // Broadcast refresh display
            try {
                broadcast(new \App\Events\RefreshDisplay('tanding.A'));
                broadcast(new \App\Events\RefreshDisplay('solo.A'));
                broadcast(new \App\Events\RefreshDisplay('ganda.A'));
                broadcast(new \App\Events\RefreshDisplay('tunggal.A'));
                broadcast(new \App\Events\RefreshDisplay('regu.A'));
            } catch (\Throwable $e) {}

            return response()->json([
                'status'  => 'success',
                'message' => 'Seluruh data pertandingan & peserta (Tanding, Seni, & Pool) berhasil direset bersih.',
            ]);
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}

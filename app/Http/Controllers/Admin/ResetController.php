<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ResetController extends Controller
{
    /**
     * Reset hanya nilai juri
     * Urutan: input_nilai_juri saja (tidak ada child)
     */
    public function resetNilai()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('input_nilai_juri')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'status'  => 'success',
                'message' => 'Semua data nilai juri berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reset partai dan semua yang bergantung padanya
     * Urutan child → parent:
     *   input_nilai_juri → detail_peserta_tanding → status_kontrol_juri → partai
     */
    public function resetPartai()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('input_nilai_juri')->truncate();
            DB::table('detail_peserta_tanding')->truncate();
            DB::table('status_kontrol_juri')->truncate();
            DB::table('partai')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'status'  => 'success',
                'message' => 'Semua data partai berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reset semua data
     * Urutan child → parent:
     *   input_nilai_juri
     *   → detail_peserta_tanding
     *   → status_kontrol_juri
     *   → partai
     *   → peserta
     *   → ref_kelas
     */
    public function resetSemua()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('input_nilai_juri')->truncate();
            DB::table('detail_peserta_tanding')->truncate();
            DB::table('status_kontrol_juri')->truncate();
            DB::table('partai')->truncate();
            DB::table('peserta')->truncate();
            DB::table('ref_kelas')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'status'  => 'success',
                'message' => 'Seluruh data sistem berhasil direset.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
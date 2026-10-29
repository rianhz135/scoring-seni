<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('log_nilai_juri_tanding', function (Blueprint $table) {
            if (!Schema::hasColumn('log_nilai_juri_tanding', 'keterangan')) {
                $table->string('keterangan')->nullable()->after('status_input');
            }
        });

        // Widen status_input to allow 'Tidak Diproses'
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE log_nilai_juri_tanding MODIFY COLUMN status_input VARCHAR(50) DEFAULT 'Belum Proses'");
        } catch (\Throwable $e) {
            // Ignore if DB driver doesn't support raw ALTER
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_nilai_juri_tanding', function (Blueprint $table) {
            if (Schema::hasColumn('log_nilai_juri_tanding', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};

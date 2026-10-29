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
        Schema::table('partai_tanding', function (Blueprint $table) {
            $table->string('sudut_pemenang', 30)->nullable()->after('id_pemenang');
            $table->text('alasan_pemenang')->nullable()->after('sudut_pemenang');
            $table->boolean('status_pemenang')->default(0)->after('alasan_pemenang');
            $table->boolean('is_babak_tambahan')->default(0)->after('status_pemenang');
            $table->decimal('berat_badan_merah', 5, 2)->nullable()->after('is_babak_tambahan');
            $table->decimal('berat_badan_biru', 5, 2)->nullable()->after('berat_badan_merah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partai_tanding', function (Blueprint $table) {
            $table->dropColumn([
                'sudut_pemenang',
                'alasan_pemenang',
                'status_pemenang',
                'is_babak_tambahan',
                'berat_badan_merah',
                'berat_badan_biru',
            ]);
        });
    }
};

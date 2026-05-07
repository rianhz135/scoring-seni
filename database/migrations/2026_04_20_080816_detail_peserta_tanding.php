<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_peserta_tanding', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_partai')->constrained('partai', 'id_partai')->onDelete('cascade');
            $table->foreignId('id_peserta')->constrained('peserta', 'id_peserta');
            $table->enum('sudut', ['Merah', 'Biru']);
            $table->decimal('nilai_median', 16, 15)->default(0.000000000000000);
            $table->decimal('nilai_deviasi', 16, 15)->default(0.000000000000000);
            $table->decimal('hukuman_ketua', 5, 3)->default(0.000);
            $table->decimal('total_nilai', 5, 3)->default(0.000);
            $table->integer('waktu_tampil')->default(0); // dalam detik
            $table->boolean('is_disqualifikasi')->default(false);
            $table->boolean('status_mulai')->default(false);
            $table->boolean('is_pemenang')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

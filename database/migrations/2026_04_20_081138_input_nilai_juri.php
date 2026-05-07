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
        Schema::create('input_nilai_juri', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->foreignId('id_detail')->constrained('detail_peserta_tanding', 'id_detail')->onDelete('cascade');
            $table->integer('no_juri'); // 1-6
            $table->decimal('nilai_a', 5, 2)->default(0.00);
            $table->decimal('nilai_b', 5, 2)->default(0.00);
            $table->decimal('nilai_c', 5, 2)->default(0.00);
            $table->decimal('skor_akhir_juri', 5, 2)->default(0.00);
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

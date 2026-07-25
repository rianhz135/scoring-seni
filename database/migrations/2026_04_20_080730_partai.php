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
        Schema::create('partai', function (Blueprint $table) {
            $table->id('id_partai');
            $table->foreignId('id_kategori')->constrained('ref_kategori', 'id_kategori');
            $table->foreignId('id_kelas')->constrained('ref_kelas', 'id_kelas');
            $table->string('no_partai', 10);
            $table->string('babak')->nullable(); // Penyesuaian ke nullable
            $table->string('gelanggang')->nullable(); // Penyesuaian ke nullable
            $table->string('status_pertandingan')->default('Waiting');
            $table->string('timer_live')->default('03:00');
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

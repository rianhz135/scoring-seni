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
        Schema::create('status_kontrol_juri', function (Blueprint $table) {
            $table->id('id_kontrol');
            $table->foreignId('id_partai')->constrained('partai', 'id_partai')->onDelete('cascade');
            $table->integer('no_juri'); // 1-6
            $table->boolean('is_on')->default(false);
            $table->enum('status_input', ['Proses', 'Selesai'])->default('Proses');
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

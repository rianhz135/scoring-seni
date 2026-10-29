<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_display', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kejuaraan')->default('PERTANDINGAN SMI CUP 2026');
            $table->string('gelanggang')->default('GELANGGANG 1');
            $table->string('logo_kiri')->nullable()->default('smi.png');
            $table->string('logo_kanan')->nullable();
            $table->timestamps();
        });

        DB::table('pengaturan_display')->insert([
            'id' => 1,
            'nama_kejuaraan' => 'PERTANDINGAN SMI CUP 2026',
            'gelanggang' => 'GELANGGANG 1',
            'logo_kiri' => 'smi.png',
            'logo_kanan' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_display');
    }
};


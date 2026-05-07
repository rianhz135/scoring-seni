<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('detail_peserta_tanding', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->boolean('is_timer_running')->default(0)->after('waktu_tampil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
       
    }
};

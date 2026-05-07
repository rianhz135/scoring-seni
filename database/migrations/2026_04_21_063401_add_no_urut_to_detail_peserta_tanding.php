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
            // Menambah urutan tampil setelah ID Peserta
            $table->integer('no_urut')->nullable()->after('id_peserta');

            // Memastikan kolom sudut bisa dikosongkan (untuk kategori Pool)
            $table->string('sudut')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_peserta_tanding', function (Blueprint $table) {
            // Balikkan perubahan jika migration di-rollback
            $table->string('sudut')->nullable(false)->change();
            $table->dropColumn('no_urut');
        });
    }
};

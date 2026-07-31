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
        // 1. TABEL MASTER PESERTA
        Schema::create('peserta_tanding', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kontingen');
            // $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('kategori_umur')->nullable(); // Misal: Pra-Remaja, Remaja, Dewasa
            $table->timestamps();
        });

        // 2. TABEL PARTAI / JADWAL TANDING
        Schema::create('partai_tanding', function (Blueprint $table) {
            $table->id();
            $table->string('no_partai');
            $table->string('kelas'); // Misal: Kelas A Putra
            $table->enum('fase_babak', ['Penyisihan', 'Semi', 'Final']);

            // Relasi ke peserta (Bisa null jika masih nunggu pemenang partai sebelumnya)
            $table->foreignId('id_merah')->nullable()->constrained('peserta_tanding')->nullOnDelete();
            $table->foreignId('id_biru')->nullable()->constrained('peserta_tanding')->nullOnDelete();
            $table->foreignId('id_pemenang')->nullable()->constrained('peserta_tanding')->nullOnDelete();

            // Sistem Bagan (Menunggu Pemenang)
            $table->string('menunggu_pemenang_merah_dari')->nullable()->comment('Isi dengan no_partai');
            $table->string('menunggu_pemenang_biru_dari')->nullable()->comment('Isi dengan no_partai');

            // Kontrol Dewan
            $table->integer('babak_aktif')->default(1);
            $table->enum('status_pertandingan', ['Belum', 'Berlangsung', 'Selesai'])->default('Belum');
            $table->integer('waktu_timer')->default(120)->comment('Waktu dalam detik');
            $table->enum('status_timer', ['stop', 'running', 'pause'])->default('stop');

            $table->timestamps();
        });

        // 3. TABEL POIN SAH (Data Final yang Tampil di Papan Skor)
        // Dibuat lebih dulu dari log_juri karena log_juri butuh id_poin_sah sebagai Foreign Key
        Schema::create('poin_sah_tanding', function (Blueprint $table) {
            $table->id(); // BigInt by default
            $table->foreignId('id_partai')->constrained('partai_tanding')->cascadeOnDelete();
            $table->integer('babak');
            $table->enum('sudut', ['Merah', 'Biru']);
            $table->integer('poin_masuk'); // Nilai 1 (Pukulan) atau 2 (Tendangan)
            $table->timestamps();
        });

        // 4. TABEL LOG NILAI JURI (Rekam Jejak Setiap Klik)
        Schema::create('log_nilai_juri_tanding', function (Blueprint $table) {
            $table->id(); // BigInt
            $table->foreignId('id_partai')->constrained('partai_tanding')->cascadeOnDelete();
            $table->integer('babak');
            $table->integer('no_juri'); // 1, 2, atau 3
            $table->enum('sudut', ['Merah', 'Biru']);
            $table->enum('jenis_serangan', ['Pukulan', 'Tendangan']);
            $table->integer('poin');
            $table->enum('status_input', ['Belum Proses', 'Berhasil', 'Tidak Sah'])->default('Belum Proses');

            // Mengikat ke tabel poin sah jika klik ini berhasil mencetak poin
            $table->foreignId('id_poin_sah')->nullable()->constrained('poin_sah_tanding')->nullOnDelete();

            $table->timestamps();
        });

        // 5. TABEL PELANGGARAN
        Schema::create('pelanggaran_tanding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_partai')->constrained('partai_tanding')->cascadeOnDelete();
            $table->enum('sudut', ['Merah', 'Biru']);
            $table->integer('babak');
            // Menjabarkan array Enum agar validasi langsung aman di level DB
            $table->enum('jenis_pelanggaran', [
                'Binaan 1',
                'Binaan 2',
                'Teguran 1',
                'Teguran 2',
                'Peringatan 1',
                'Peringatan 2',
                'Peringatan 3'
            ]);
            $table->integer('poin_minus')->comment('0, -1, -2, -5, -10, -99');
            $table->timestamps();
        });

        // 6. TABEL VERIFIKASI DEWAN (Sistem VAR)
        Schema::create('verifikasi_dewan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_partai')->constrained('partai_tanding')->cascadeOnDelete();
            $table->integer('babak');
            $table->string('keterangan'); // Misal: "Verifikasi Pukulan Biru", "Verifikasi Jatuhan Merah"
            $table->enum('status', ['Aktif', 'Selesai'])->default('Aktif');
            $table->timestamps();
        });

        // 7. TABEL JAWABAN VERIFIKASI JURI
        Schema::create('jawaban_verifikasi_juri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_verifikasi')->constrained('verifikasi_dewan')->cascadeOnDelete();
            $table->integer('no_juri');
            $table->enum('pilihan_sudut', ['Merah', 'Biru', 'Batal']);
            $table->timestamps();
        });

        Schema::create('jatuhan_tanding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_partai')->constrained('partai_tanding')->cascadeOnDelete();
            $table->integer('babak');
            $table->enum('sudut', ['Merah', 'Biru']);
            $table->integer('poin')->default(3)->comment('Nilai mutlak 3 untuk jatuhan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PENTING: Urutan drop (hapus) tabel harus dari anak (Child) ke induk (Parent)
        // untuk mencegah error "Cannot truncate or drop a table referenced by a foreign key"
        Schema::dropIfExists('jawaban_verifikasi_juri');
        Schema::dropIfExists('verifikasi_dewan');
        Schema::dropIfExists('pelanggaran_tanding');
        Schema::dropIfExists('log_nilai_juri_tanding');
        Schema::dropIfExists('poin_sah_tanding');
        Schema::dropIfExists('partai_tanding');
        Schema::dropIfExists('peserta_tanding');
        Schema::dropIfExists('jatuhan_tanding');
    }
};
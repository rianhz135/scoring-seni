<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PesertaSoloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Pastikan Kategori SOLO ada
        // updateOrCreate mencegah error jika data sudah pernah diinput
        $kategori = \App\Models\RefKategori::updateOrCreate(
            ['id_kategori' => 1], // cari id 1
            ['nama_kategori' => 'TANDING SOLO'] // kalau ga ada, buat ini
        );

        // 2. Pastikan Kelas A ada
        $kelas = \App\Models\RefKelas::updateOrCreate(
            ['id_kelas' => 1],
            ['label_kelas' => 'KELAS A']
        );

        // 3. Buat Data Peserta
        $p1 = \App\Models\Peserta::create(['nama_peserta' => 'RIAN HIDAYAT', 'kontingen' => 'BANTEN']);
        $p2 = \App\Models\Peserta::create(['nama_peserta' => 'FAJAR ALFIAN', 'kontingen' => 'JAWA BARAT']);

        // 4. Buat Partai (Sekarang id_kategori & id_kelas sudah PASTI ada)
        $partai = \App\Models\Partai::create([
            'id_kategori' => $kategori->id_kategori,
            'id_kelas' => $kelas->id_kelas,
            'no_partai' => 1,
            'babak' => 'Penyisihan',
            'gelanggang' => 'A',
            'status_pertandingan' => 'Waiting'
        ]);

        // 5. Masukkan ke Detail (Sudut)
        \App\Models\DetailPesertaTanding::create([
            'id_partai' => $partai->id_partai,
            'id_peserta' => $p1->id_peserta,
            'sudut' => 'Biru'
        ]);

        \App\Models\DetailPesertaTanding::create([
            'id_partai' => $partai->id_partai,
            'id_peserta' => $p2->id_peserta,
            'sudut' => 'Merah'
        ]);


        // --- DATA PARTAI BARU (#002) ---

        // 1. Buat Peserta Baru
        $p3 = \App\Models\Peserta::create(['nama_peserta' => 'JONATAN CHRISTIE', 'kontingen' => 'DKI JAKARTA']);
        $p4 = \App\Models\Peserta::create(['nama_peserta' => 'ANTHONY GINTING', 'kontingen' => 'JAWA TENGAH']);

        // 2. Buat Partai Baru (Gunakan ID Kategori dan Kelas yang sudah ada)
        $partai2 = \App\Models\Partai::create([
            'id_kategori' => 1,
            'id_kelas' => 1,
            'no_partai' => 2, // Bedakan nomor partainya
            'babak' => 'Penyisihan',
            'gelanggang' => 'A',
            'status_pertandingan' => 'Waiting'
        ]);

        // 3. Masukkan ke Detail (Sudut)
        \App\Models\DetailPesertaTanding::create([
            'id_partai' => $partai2->id_partai,
            'id_peserta' => $p3->id_peserta,
            'sudut' => 'Biru'
        ]);

        \App\Models\DetailPesertaTanding::create([
            'id_partai' => $partai2->id_partai,
            'id_peserta' => $p4->id_peserta,
            'sudut' => 'Merah'
        ]);
    }
}

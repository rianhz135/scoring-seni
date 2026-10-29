<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = ['KELAS A', 'KELAS B', 'KELAS C', 'KELAS D', 'KELAS E'];

        foreach ($kelas as $k) {
            \App\Models\RefKelas::firstOrCreate([
                'label_kelas' => $k
            ]);
        }
    }
}

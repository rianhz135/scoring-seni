<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PartaiTanding;
use App\Models\StatusKontrolJuriTanding;

class JuriTandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Akun Pengguna untuk Juri 1, 2, 3 Tanding
        $juris = [
            [
                'name' => 'Juri 1 Tanding',
                'username' => 'juri1_tanding',
                'email' => 'juri1_tanding@gmail.com',
                'role' => 'juri',
                'no_juri' => 1,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Juri 2 Tanding',
                'username' => 'juri2_tanding',
                'email' => 'juri2_tanding@gmail.com',
                'role' => 'juri',
                'no_juri' => 2,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Juri 3 Tanding',
                'username' => 'juri3_tanding',
                'email' => 'juri3_tanding@gmail.com',
                'role' => 'juri',
                'no_juri' => 3,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($juris as $juri) {
            DB::table('users')->updateOrInsert(
                ['username' => $juri['username']],
                array_merge($juri, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }

        // 2. Inisialisasi Status Kontrol Juri Tanding untuk seluruh partai tanding yang sudah ada
        if (\Illuminate\Support\Facades\Schema::hasTable('status_kontrol_juri_tanding')) {
            $partais = PartaiTanding::all();
            foreach ($partais as $partai) {
                for ($noJuri = 1; $noJuri <= 3; $noJuri++) {
                    StatusKontrolJuriTanding::updateOrInsert(
                        [
                            'id_partai' => $partai->id,
                            'no_juri' => $noJuri,
                        ],
                        [
                            'is_active' => true,
                            'status' => 'Active',
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}

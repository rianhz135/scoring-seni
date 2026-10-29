<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Insert Data Kategori
        $kategoriList = [
            ['id_kategori' => 1, 'nama_kategori' => 'SOLO'],
            ['id_kategori' => 2, 'nama_kategori' => 'GANDA'],
            ['id_kategori' => 3, 'nama_kategori' => 'TUNGGAL'],
            ['id_kategori' => 4, 'nama_kategori' => 'REGU'],
        ];

        foreach ($kategoriList as $kat) {
            DB::table('ref_kategori')->updateOrInsert(
                ['id_kategori' => $kat['id_kategori']],
                [
                    'nama_kategori' => $kat['nama_kategori'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // 2. Insert Data Users
        $userList = [
            [
                'id' => 1, 
                'name' => 'Administrator', 
                'username' => 'admin', 
                'email' => 'admin@gmail.com', 
                'email_verified_at' => null, 
                'password' => '$2y$12$qLA8od.VlQK01F3VkVLDheAhiFSYz5eWZ/avfhH3iJrvTWPkpHmIK', 
                'role' => 'admin', 
                'no_juri' => null, 
                'remember_token' => null, 
            ],
            [
                'id' => 2, 
                'name' => 'Juri', 
                'username' => 'juri', 
                'email' => 'juri@gmail.com', 
                'email_verified_at' => null, 
                'password' => '$2y$12$cwxEgEmZiQKrLVq.16NNVOlNYEmYSDiYmIEq4oOt0AvT5YEZb7YdW', 
                'role' => 'juri', 
                'no_juri' => null, 
                'remember_token' => null, 
            ],
        ];

        foreach ($userList as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                array_merge($user, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }

        // 3. Seed Kelas
        $this->call(KelasSeeder::class);

        // 4. Seed Juri Tanding & Inisialisasi Status Kontrol Juri Tanding
        $this->call(JuriTandingSeeder::class);
    }
}
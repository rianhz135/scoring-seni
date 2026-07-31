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
        DB::table('ref_kategori')->insert([
            ['id_kategori' => 1, 'nama_kategori' => 'SOLO', 'created_at' => '2026-04-20 04:01:58', 'updated_at' => '2026-04-20 04:01:58'],
            ['id_kategori' => 2, 'nama_kategori' => 'GANDA', 'created_at' => '2026-04-20 23:55:23', 'updated_at' => '2026-04-20 23:55:23'],
            ['id_kategori' => 3, 'nama_kategori' => 'TUNGGAL', 'created_at' => '2026-04-20 23:55:23', 'updated_at' => '2026-04-20 23:55:23'],
            ['id_kategori' => 4, 'nama_kategori' => 'REGU', 'created_at' => '2026-04-20 23:55:27', 'updated_at' => '2026-04-20 23:55:27'],
        ]);

        // 2. Insert Data Users
        DB::table('users')->insert([
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
                'created_at' => '2026-05-13 03:13:39', 
                'updated_at' => '2026-05-13 03:13:39'
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
                'created_at' => '2026-05-13 03:51:56', 
                'updated_at' => '2026-05-13 03:51:56'
            ],
        ]);
    }
}
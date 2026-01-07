<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin Utama
        // Gunakan firstOrCreate agar tidak error jika dijalankan 2x
        User::firstOrCreate(
            ['email' => 'admin1@admin.com'], // Cek apakah email ini ada?
            [
                'name' => 'Super Admin',
                'password' => Hash::make('miaw0005'), // Password default
                'email_verified_at' => now(),
            ]
        );

        // 2. Tambah user lain (contoh: user biasa)
        User::firstOrCreate(
            ['email' => 'admin2@admin.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('miaw0005'),
                'email_verified_at' => now(),
            ]
        );
        
        // Jika ingin membuat banyak dummy user, gunakan factory:
        // User::factory(10)->create();
        
    }
}
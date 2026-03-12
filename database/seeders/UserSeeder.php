<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus user yang sudah ada
        User::truncate();

        // Buat user admin default (Superadmin)
        User::create([
            'nip' => '199906282025061003',
            'name' => 'Admin Sistem',
            'email' => 'admin@quicktansi.com',
            'no_telp' => '081234567890',
            'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'is_superadmin' => true, // Superadmin dapat melihat semua user
            'password' => Hash::make('199906282025061003'), // Password default = NIP
            'email_verified_at' => now(),
        ]);

        // Buat user operator untuk testing
        User::create([
            'nip' => '199505152021012001',
            'name' => 'Operator Keuangan',
            'email' => 'operator@quicktansi.com',
            'no_telp' => '081234567891',
            'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'password' => Hash::make('199505152021012001'), // Password default = NIP
            'email_verified_at' => now(),
        ]);

        // Buat user bendahara untuk testing
        User::create([
            'nip' => '198803102019031002',
            'name' => 'Bendahara Pengeluaran',
            'email' => 'bendahara@quicktansi.com',
            'no_telp' => '081234567892',
            'instansi' => 'Dinas Kesehatan',
            'password' => Hash::make('198803102019031002'), // Password default = NIP
            'email_verified_at' => now(),
        ]);
    }
}

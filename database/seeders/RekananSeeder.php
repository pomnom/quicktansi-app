<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rekanan;

class RekananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks untuk truncate
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rekanan::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $rekanans = [
            [
                'npwp' => null,
                'nama_perusahaan' => 'PT Cipta Teknologi',
                'nomor_rekening' => '1234567890',
                'bank' => 'Bank Mandiri',
                'nama_pemilik_rekening' => 'Budi Santoso'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'CV Logistik Nusantara',
                'nomor_rekening' => '0987654321',
                'bank' => 'Bank BRI',
                'nama_pemilik_rekening' => 'Siti Nurhaliza'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'UD Mandiri Sejahtera',
                'nomor_rekening' => '1122334455',
                'bank' => 'Bank BCA',
                'nama_pemilik_rekening' => 'Ahmad Dahlan'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'Toko Maju Bersama',
                'nomor_rekening' => '5566778899',
                'bank' => 'Bank BNI',
                'nama_pemilik_rekening' => 'Dewi Lestari'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'PT Solusi Media',
                'nomor_rekening' => '3344556677',
                'bank' => 'Bank Danamon',
                'nama_pemilik_rekening' => 'Rama Putra'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'CV Prima Karya',
                'nomor_rekening' => '8877665544',
                'bank' => 'Bank CIMB Niaga',
                'nama_pemilik_rekening' => 'Indah Lestari'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'Toko Sinar Jaya',
                'nomor_rekening' => '7766554433',
                'bank' => 'Bank Permata',
                'nama_pemilik_rekening' => 'Candra Wijaya'
            ],
            [
                'npwp' => null,
                'nama_perusahaan' => 'CV Abadi Maintenance',
                'nomor_rekening' => '6655443322',
                'bank' => 'Bank BTN',
                'nama_pemilik_rekening' => 'Eko Prasetyo'
            ],
        ];

        foreach ($rekanans as $rekanan) {
            Rekanan::create($rekanan);
        }
    }
}

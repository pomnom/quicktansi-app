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
                 'npwp' => '01.234.567.8-901.000',
                 'nama_perusahaan' => 'PT Cipta Teknologi Indonesia',
                 'nomor_rekening' => '1370012345678',
                'bank' => 'Bank Mandiri',
                 'nama_pemilik_rekening' => 'PT Cipta Teknologi Indonesia'
            ],
            [
                 'npwp' => '02.345.678.9-012.000',
                 'nama_perusahaan' => 'CV Logistik Nusantara Jaya',
                 'nomor_rekening' => '002301234567890',
                'bank' => 'Bank BRI',
                 'nama_pemilik_rekening' => 'CV Logistik Nusantara Jaya'
            ],
            [
                 'npwp' => '03.456.789.0-123.000',
                 'nama_perusahaan' => 'PT Mandiri Sejahtera Abadi',
                 'nomor_rekening' => '0361234567',
                'bank' => 'Bank BCA',
                 'nama_pemilik_rekening' => 'PT Mandiri Sejahtera Abadi'
            ],
            [
                 'npwp' => '89.012.345.6-789.000',
                 'nama_perusahaan' => 'UD Maju Bersama',
                 'nomor_rekening' => '0461234567890',
                'bank' => 'Bank BNI',
                 'nama_pemilik_rekening' => 'Budi Santoso'
            ],
            [
                 'npwp' => '04.567.890.1-234.000',
                 'nama_perusahaan' => 'PT Solusi Media Kreatif',
                 'nomor_rekening' => '1171234567890',
                'bank' => 'Bank Danamon',
                 'nama_pemilik_rekening' => 'PT Solusi Media Kreatif'
            ],
            [
                 'npwp' => '05.678.901.2-345.000',
                 'nama_perusahaan' => 'CV Prima Karya Mandiri',
                 'nomor_rekening' => '7001234567890',
                'bank' => 'Bank CIMB Niaga',
                 'nama_pemilik_rekening' => 'CV Prima Karya Mandiri'
            ],
            [
                 'npwp' => null,
                 'nama_perusahaan' => 'Toko Sinar Jaya Elektronik',
                 'nomor_rekening' => '1121234567890',
                'bank' => 'Bank Permata',
                 'nama_pemilik_rekening' => 'Ahmad Wijaya'
            ],
            [
                'npwp' => null,
                 'nama_perusahaan' => 'CV Abadi Service dan Maintenance',
                 'nomor_rekening' => '2001234567890',
                'bank' => 'Bank BTN',
                 'nama_pemilik_rekening' => 'Eko Prasetyo Putra'
              ],
              [
                 'npwp' => '06.789.012.3-456.000',
                 'nama_perusahaan' => 'PT Global Supplies Indonesia',
                 'nomor_rekening' => '1480123456789',
                 'bank' => 'Bank Syariah Indonesia',
                 'nama_pemilik_rekening' => 'PT Global Supplies Indonesia'
              ],
              [
                 'npwp' => null,
                 'nama_perusahaan' => 'UD Berkah Jaya Makmur',
                 'nomor_rekening' => '5671234567890',
                 'bank' => 'Bank Mandiri',
                 'nama_pemilik_rekening' => 'Siti Rahayu'
            ],
        ];

        foreach ($rekanans as $rekanan) {
            Rekanan::create($rekanan);
        }
    }
}

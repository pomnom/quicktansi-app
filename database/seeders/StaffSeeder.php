<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks untuk truncate
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Staff::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $staff = [
            // 1 Pengguna Anggaran
            [
                'nip' => '19731007 199803 1 006',
                'nama' => 'Muhammad Syahroni, SP., MM',
                'jabatan' => 'Kepala BPKAD',
                'golongan' => 'IV/c',
                'status' => 'Pengguna Anggaran',
            ],

            // 1 PPK
            [
                'nip' => '19680915 199203 1 005',
                'nama' => 'Drs. Ahmad Fauzi, M.Si',
                'jabatan' => 'Kepala Bidang Anggaran',
                'golongan' => 'IV/a',
                'status' => 'PPK',
            ],

            // 5 PPTK
            [
                'nip' => '19750412 200012 1 004',
                'nama' => 'Budi Santoso, S.E., M.M',
                'jabatan' => 'Kepala Sub Bidang Perencanaan',
                'golongan' => 'III/d',
                'status' => 'PPTK',
            ],
            [
                'nip' => '19820703 201001 1 008',
                'nama' => 'Siti Aminah, S.Kom., M.T',
                'jabatan' => 'Kepala Sub Bidang Teknologi',
                'golongan' => 'III/c',
                'status' => 'PPTK',
            ],
            [
                'nip' => '19780225 200505 2 003',
                'nama' => 'Dr. Hendra Wijaya, M.Pd',
                'jabatan' => 'Kepala Sub Bidang Pembangunan',
                'golongan' => 'III/d',
                'status' => 'PPTK',
            ],
            [
                'nip' => '19851120 201103 1 002',
                'nama' => 'Rudi Hartono, S.T., M.Eng',
                'jabatan' => 'Kepala Sub Bidang Infrastruktur',
                'golongan' => 'III/b',
                'status' => 'PPTK',
            ],
            [
                'nip' => '19900308 201504 2 001',
                'nama' => 'Dewi Lestari, S.Sos., M.AP',
                'jabatan' => 'Kepala Sub Bidang Kesejahteraan',
                'golongan' => 'III/a',
                'status' => 'PPTK',
            ],

            // 1 Bendahara Pengeluaran
            [
                'nip' => '19770518 200112 1 005',
                'nama' => 'Yudi Prasetyo, S.E',
                'jabatan' => 'Bendahara Pengeluaran',
                'golongan' => 'III/c',
                'status' => 'Bendahara Pengeluaran',
            ],

            // 1 Bendahara Barang
            [
                'nip' => '19840922 200903 2 006',
                'nama' => 'Rina Kusuma, S.E',
                'jabatan' => 'Bendahara Barang',
                'golongan' => 'III/b',
                'status' => 'Bendahara Barang',
            ],

            // 5 Tanpa Status
            [
                'nip' => '19920115 201601 1 003',
                'nama' => 'Andi Nugroho, S.H',
                'jabatan' => 'Staf Hukum',
                'golongan' => 'III/a',
                'status' => null,
            ],
            [
                'nip' => '19880607 201402 2 004',
                'nama' => 'Maya Sari, S.Psi',
                'jabatan' => 'Staf Kepegawaian',
                'golongan' => 'II/d',
                'status' => null,
            ],
            [
                'nip' => '19950430 201805 1 002',
                'nama' => 'Rizki Ramadan, A.Md',
                'jabatan' => 'Staf Administrasi',
                'golongan' => 'II/c',
                'status' => null,
            ],
            [
                'nip' => '19870812 201303 2 005',
                'nama' => 'Fitri Handayani, S.Kom',
                'jabatan' => 'Staf IT',
                'golongan' => 'II/d',
                'status' => null,
            ],
            [
                'nip' => '19930220 201706 1 001',
                'nama' => 'Agus Setiawan, S.AP',
                'jabatan' => 'Staf Umum',
                'golongan' => 'II/b',
                'status' => null,
            ],
        ];

        foreach ($staff as $s) {
            Staff::create($s);
        }
    }
}

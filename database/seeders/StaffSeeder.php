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
            // Pengguna Anggaran
            [
                'nip' => '197305082006041009',
                'nama' => 'Dr. Ir. Bambang Suryanto, M.M.',
                'jabatan' => 'Kepala Badan Pengelolaan Keuangan dan Aset Daerah',
                'golongan' => 'IV/d',
                'status' => 'Pengguna Anggaran',
            ],

            // PPK (Pejabat Pembuat Komitmen)
            [
                'nip' => '196809151992031005',
                'nama' => 'Dra. Siti Fatimah, M.Si.',
                'jabatan' => 'Kepala Bidang Anggaran dan Perbendaharaan',
                'golongan' => 'IV/b',
                'status' => 'PPK',
            ],

            // PPTK (Pejabat Pelaksana Teknis Kegiatan)
            [
                'nip' => '197504122000121004',
                'nama' => 'Ir. Ahmad Budiman, M.T.',
                'jabatan' => 'Kepala Sub Bidang Perencanaan Anggaran',
                'golongan' => 'III/d',
                'status' => 'PPTK',
            ],
            [
                'nip' => '198207032010011008',
                'nama' => 'Siti Nurhasanah, S.Kom., M.Kom.',
                'jabatan' => 'Kepala Sub Bidang Sistem Informasi Keuangan',
                'golongan' => 'III/c',
                'status' => 'PPTK',
            ],
            [
                'nip' => '197802252005052003',
                'nama' => 'Hendra Gunawan, S.E., M.Ak.',
                'jabatan' => 'Kepala Sub Bidang Akuntansi dan Pelaporan',
                'golongan' => 'III/d',
                'status' => 'PPTK',
            ],
            [
                'nip' => '198511202011031002',
                'nama' => 'Rudi Hermawan, S.E., M.M.',
                'jabatan' => 'Kepala Sub Bidang Pendapatan Daerah',
                'golongan' => 'III/b',
                'status' => 'PPTK',
            ],
            [
                'nip' => '199003082015042001',
                'nama' => 'Dewi Puspitasari, S.E., M.M.',
                'jabatan' => 'Kepala Sub Bidang Aset Daerah',
                'golongan' => 'III/a',
                'status' => 'PPTK',
            ],

            // Bendahara Pengeluaran
            [
                'nip' => '197705182001121005',
                'nama' => 'Yudi Prasetyo, S.E., M.Ak.',
                'jabatan' => 'Bendahara Pengeluaran BPKAD',
                'golongan' => 'III/c',
                'status' => 'Bendahara Pengeluaran',
            ],

            // Bendahara Barang (Pengurus Barang)
            [
                'nip' => '198409222009032006',
                'nama' => 'Rina Kusuma Wardani, S.E.',
                'jabatan' => 'Pengurus Barang BPKAD',
                'golongan' => 'III/b',
                'status' => 'Bendahara Barang',
            ],

            // Staf Pendukung (Tanpa Status Khusus)
            [
                'nip' => '199201152016011003',
                'nama' => 'Andi Nugroho, S.H., M.H.',
                'jabatan' => 'Analis Hukum',
                'golongan' => 'III/a',
                'status' => null,
            ],
            [
                'nip' => '198807202013022007',
                'nama' => 'Putri Ayu Lestari, S.Psi., M.M.',
                'jabatan' => 'Analis Kepegawaian',
                'golongan' => 'III/b',
                'status' => null,
            ],
            [
                'nip' => '199505282018051001',
                'nama' => 'Fajar Ramadhan, S.Kom.',
                'jabatan' => 'Analis Sistem Informasi',
                'golongan' => 'III/a',
                'status' => null,
            ],
            [
                'nip' => '199309102017042002',
                'nama' => 'Rina Wijayanti, S.E.',
                'jabatan' => 'Analis Keuangan',
                'golongan' => 'III/a',
                'status' => null,
            ],
            [
                'nip' => '198904162015061009',
                'nama' => 'Dedi Setiawan, S.Sos.',
                'jabatan' => 'Analis Perencanaan',
                'golongan' => 'III/a',
                'status' => null,
            ],
        ];

        foreach ($staff as $s) {
            Staff::create($s);
        }
    }
}

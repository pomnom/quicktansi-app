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
            // Staff BPKAD
            // Pengguna Anggaran
            [
                'nip' => '197305082006041009',
                'nama' => 'Dr. Ir. Bambang Suryanto, M.M.',
                'jabatan' => 'Kepala Badan Pengelolaan Keuangan dan Aset Daerah',
                'golongan' => 'IV/d',
                'status' => 'Pengguna Anggaran',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],

            // PPK (Pejabat Pembuat Komitmen)
            [
                'nip' => '196809151992031005',
                'nama' => 'Dra. Siti Fatimah, M.Si.',
                'jabatan' => 'Kepala Bidang Anggaran dan Perbendaharaan',
                'golongan' => 'IV/b',
                'status' => 'PPK',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],

            // PPTK (Pejabat Pelaksana Teknis Kegiatan)
            [
                'nip' => '197504122000121004',
                'nama' => 'Ir. Ahmad Budiman, M.T.',
                'jabatan' => 'Kepala Sub Bidang Perencanaan Anggaran',
                'golongan' => 'III/d',
                'status' => 'PPTK',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '198207032010011008',
                'nama' => 'Siti Nurhasanah, S.Kom., M.Kom.',
                'jabatan' => 'Kepala Sub Bidang Sistem Informasi Keuangan',
                'golongan' => 'III/c',
                'status' => 'PPTK',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '197802252005052003',
                'nama' => 'Hendra Gunawan, S.E., M.Ak.',
                'jabatan' => 'Kepala Sub Bidang Akuntansi dan Pelaporan',
                'golongan' => 'III/d',
                'status' => 'PPTK',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '198511202011031002',
                'nama' => 'Rudi Hermawan, S.E., M.M.',
                'jabatan' => 'Kepala Sub Bidang Pendapatan Daerah',
                'golongan' => 'III/b',
                'status' => 'PPTK',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '199003082015042001',
                'nama' => 'Dewi Puspitasari, S.E., M.M.',
                'jabatan' => 'Kepala Sub Bidang Aset Daerah',
                'golongan' => 'III/a',
                'status' => 'PPTK',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],

            // Bendahara Pengeluaran
            [
                'nip' => '197705182001121005',
                'nama' => 'Yudi Prasetyo, S.E., M.Ak.',
                'jabatan' => 'Bendahara Pengeluaran BPKAD',
                'golongan' => 'III/c',
                'status' => 'Bendahara Pengeluaran',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],

            // Bendahara Barang (Pengurus Barang)
            [
                'nip' => '198409222009032006',
                'nama' => 'Rina Kusuma Wardani, S.E.',
                'jabatan' => 'Pengurus Barang BPKAD',
                'golongan' => 'III/b',
                'status' => 'Bendahara Barang',
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],

            // Staf Pendukung
            [
                'nip' => '199201152016011003',
                'nama' => 'Andi Nugroho, S.H., M.H.',
                'jabatan' => 'Analis Hukum',
                'golongan' => 'III/a',
                'status' => null,
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '198807202013022007',
                'nama' => 'Putri Ayu Lestari, S.Psi., M.M.',
                'jabatan' => 'Analis Kepegawaian',
                'golongan' => 'III/b',
                'status' => null,
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '199505282018051001',
                'nama' => 'Fajar Ramadhan, S.Kom.',
                'jabatan' => 'Analis Sistem Informasi',
                'golongan' => 'III/a',
                'status' => null,
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '199309102017042002',
                'nama' => 'Rina Wijayanti, S.E.',
                'jabatan' => 'Analis Keuangan',
                'golongan' => 'III/a',
                'status' => null,
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],
            [
                'nip' => '198904162015061009',
                'nama' => 'Dedi Setiawan, S.Sos.',
                'jabatan' => 'Analis Perencanaan',
                'golongan' => 'III/a',
                'status' => null,
                'instansi' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            ],

            // Staff Dinas Kesehatan
            [
                'nip' => '197108152000031007',
                'nama' => 'dr. H. Sutrisno, M.Kes., Sp.PD.',
                'jabatan' => 'Kepala Dinas Kesehatan',
                'golongan' => 'IV/c',
                'status' => 'Pengguna Anggaran',
                'instansi' => 'Dinas Kesehatan',
            ],
            [
                'nip' => '197612102005022003',
                'nama' => 'Dra. Hj. Mulyati, M.M.',
                'jabatan' => 'Sekretaris Dinas Kesehatan',
                'golongan' => 'IV/a',
                'status' => 'PPK',
                'instansi' => 'Dinas Kesehatan',
            ],
            [
                'nip' => '198003152009031010',
                'nama' => 'Dr. Adi Kurniawan, S.KM., M.Kes.',
                'jabatan' => 'Kepala Bidang Pelayanan Kesehatan',
                'golongan' => 'III/d',
                'status' => 'PPTK',
                'instansi' => 'Dinas Kesehatan',
            ],
            [
                'nip' => '198305222009122004',
                'nama' => 'Siti Mardiana, S.E., M.Ak.',
                'jabatan' => 'Bendahara Pengeluaran Dinkes',
                'golongan' => 'III/c',
                'status' => 'Bendahara Pengeluaran',
                'instansi' => 'Dinas Kesehatan',
            ],
            [
                'nip' => '198709142012032008',
                'nama' => 'Ani Sulistyowati, S.E.',
                'jabatan' => 'Pengurus Barang Dinkes',
                'golongan' => 'III/a',
                'status' => 'Bendahara Barang',
                'instansi' => 'Dinas Kesehatan',
            ],
        ];

        foreach ($staff as $s) {
            Staff::create($s);
        }
    }
}

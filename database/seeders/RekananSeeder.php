<?php

namespace Database\Seeders;

use App\Models\Rekanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RekananSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rekanan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $instansi = 'Badan Pengelolaan Keuangan dan Aset Daerah';

        // [nama_pemilik_rekening, npwp, nama_perusahaan]
        // nomor_rekening dan bank dikosongkan — diisi manual
        $data = [
            ['RIRIN PURNAMASARI',    '5205087112900004', 'Cafe Rahmat'],
            ['HENDRA MURA SAPUTRA',  '5205010606500001', 'Gemini'],
            ['MUAMMAR KHADAFI',      '5205013103860006', 'Jadi.com'],
            ['NINING SRI WAHYUTI',   '5205015205680003', 'Rato Mantika'],
            ['MADE IIN DHARMANTI',   '5205015504820006', 'Rumah Buah'],
            ['SRI RAMADHAN',         '5205086404840001', 'UD ALL-AISYAH'],
            ['EFENDI',               '5205012106770003', 'UD Anugrah'],
            ['KURNIAWAN',            '5205011004720001', 'UD Pemuda'],
            ['NASRULLAH',            '5205052102810002', null],
            ['MUHYIDDIN',            '5205010110680002', null],
            ['NASARUDIN',            '5205012102850002', null],
            ['AMRIN',                '5205050107770391', null],
        ];

        foreach ($data as [$nama, $npwp, $perusahaan]) {
            Rekanan::create([
                'instansi'              => $instansi,
                'npwp'                  => $npwp,
                'nama_perusahaan'       => $perusahaan ?? $nama,
                'nomor_rekening'        => '-',
                'bank'                  => '-',
                'nama_pemilik_rekening' => $nama,
            ]);
        }

        $this->command->info('Rekanan selesai: ' . count($data) . ' rekanan.');
    }
}

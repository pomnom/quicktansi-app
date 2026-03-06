<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use App\Models\KodeRekening;
use App\Models\SubKegiatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KodeRekening::truncate();
        SubKegiatan::truncate();
        Kegiatan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $instansi = 'Dinas Komunikasi dan Informatika';

        Kegiatan::create([
            'instansi' => $instansi,
            'id_giat' => 8714,
            'kode_giat' => 'X.XX.01.2.06',
            'nama_giat' => 'Administrasi Umum Perangkat Daerah',
        ]);

        $subKegiatanData = [
            ['id_sub_giat' => 20334, 'kode_sub_giat' => '5.02.01.2.06.0001', 'nama_sub_giat' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['id_sub_giat' => 20335, 'kode_sub_giat' => '5.02.01.2.06.0002', 'nama_sub_giat' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['id_sub_giat' => 20336, 'kode_sub_giat' => '5.02.01.2.06.0003', 'nama_sub_giat' => 'Penyediaan Peralatan Rumah Tangga'],
            ['id_sub_giat' => 20337, 'kode_sub_giat' => '5.02.01.2.06.0004', 'nama_sub_giat' => 'Penyediaan Bahan Logistik Kantor'],
            ['id_sub_giat' => 20338, 'kode_sub_giat' => '5.02.01.2.06.0005', 'nama_sub_giat' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['id_sub_giat' => 20339, 'kode_sub_giat' => '5.02.01.2.06.0006', 'nama_sub_giat' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['id_sub_giat' => 20342, 'kode_sub_giat' => '5.02.01.2.06.0009', 'nama_sub_giat' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
        ];

        foreach ($subKegiatanData as $subKegiatan) {
            SubKegiatan::create([
                'instansi' => $instansi,
                'id_giat' => 8714,
                'id_sub_giat' => $subKegiatan['id_sub_giat'],
                'kode_sub_giat' => $subKegiatan['kode_sub_giat'],
                'nama_sub_giat' => $subKegiatan['nama_sub_giat'],
            ]);
        }

        $kodeRekeningData = [
            ['id_sub_giat' => 20334, 'id_akun' => 16419, 'kode_akun' => '5.1.02.01.01.0031', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Listrik'],
            ['id_sub_giat' => 20334, 'id_akun' => 16521, 'kode_akun' => '5.1.02.02.01.0035', 'nama_akun' => 'Belanja Jasa Tenaga Teknisi Mekanik dan Listrik'],
            ['id_sub_giat' => 20335, 'id_akun' => 16412, 'kode_akun' => '5.1.02.01.01.0024', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor'],
            ['id_sub_giat' => 20335, 'id_akun' => 16415, 'kode_akun' => '5.1.02.01.01.0027', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Benda Pos'],
            ['id_sub_giat' => 20336, 'id_akun' => 16418, 'kode_akun' => '5.1.02.01.01.0030', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Perabot Kantor'],
            ['id_sub_giat' => 20336, 'id_akun' => 16424, 'kode_akun' => '5.1.02.01.01.0036', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat/Bahan untuk Kegiatan Kantor Lainnya'],
            ['id_sub_giat' => 20337, 'id_akun' => 16394, 'kode_akun' => '5.1.02.01.01.0005', 'nama_akun' => 'Belanja Bahan-Bahan Baku'],
            ['id_sub_giat' => 20337, 'id_akun' => 16440, 'kode_akun' => '5.1.02.01.01.0052', 'nama_akun' => 'Belanja Makanan dan Minuman Rapat'],
            ['id_sub_giat' => 20338, 'id_akun' => 16414, 'kode_akun' => '5.1.02.01.01.0026', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor- Bahan Cetak'],
            ['id_sub_giat' => 20339, 'id_akun' => 16541, 'kode_akun' => '5.1.02.02.01.0055', 'nama_akun' => 'Belanja Jasa Iklan/Reklame, Film, dan Pemotretan'],
            ['id_sub_giat' => 20342, 'id_akun' => 16544, 'kode_akun' => '5.1.02.01.01.0052', 'nama_akun' => 'Belanja Makanan dan Minuman Rapat'],
        ];

        foreach ($kodeRekeningData as $kodeRekening) {
            KodeRekening::create([
                'instansi' => $instansi,
                'id_sub_giat' => $kodeRekening['id_sub_giat'],
                'id_akun' => $kodeRekening['id_akun'],
                'kode_akun' => $kodeRekening['kode_akun'],
                'nama_akun' => $kodeRekening['nama_akun'],
                'is_blokir' => false,
            ]);
        }
    }
}

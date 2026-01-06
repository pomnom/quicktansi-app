<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data untuk testing - Kegiatan
        $kegiatan = \App\Models\Kegiatan::create([
            'id_daerah' => 409,
            'tahun' => 2025,
            'id_unit' => 362,
            'id_skpd' => 362,
            'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000',
            'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15,
            'id_bidang_urusan' => 236,
            'id_fungsi' => 1,
            'id_sub_fungsi' => 1,
            'id_program' => 1186,
            'id_giat' => 8714,
            'kode_giat' => 'X.XX.01.2.06',
            'nama_giat' => 'Administrasi Umum Perangkat Daerah',
            'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 1: Penyediaan Komponen Instalasi Listrik
        $subKegiatan1 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20334, 'kode_sub_giat' => '5.02.01.2.06.0001',
            'nama_sub_giat' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor', 'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 2: Penyediaan Peralatan dan Perlengkapan Kantor
        $subKegiatan2 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20335, 'kode_sub_giat' => '5.02.01.2.06.0002',
            'nama_sub_giat' => 'Penyediaan Peralatan dan Perlengkapan Kantor', 'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 3: Penyediaan Peralatan Rumah Tangga
        $subKegiatan3 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20336, 'kode_sub_giat' => '5.02.01.2.06.0003',
            'nama_sub_giat' => 'Penyediaan Peralatan Rumah Tangga', 'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 4: Penyediaan Bahan Logistik Kantor
        $subKegiatan4 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20337, 'kode_sub_giat' => '5.02.01.2.06.0004',
            'nama_sub_giat' => 'Penyediaan Bahan Logistik Kantor', 'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 5: Penyediaan Barang Cetakan dan Penggandaan
        $subKegiatan5 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20338, 'kode_sub_giat' => '5.02.01.2.06.0005',
            'nama_sub_giat' => 'Penyediaan Barang Cetakan dan Penggandaan', 'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 6: Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan
        $subKegiatan6 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20339, 'kode_sub_giat' => '5.02.01.2.06.0006',
            'nama_sub_giat' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan', 'nilai_anggaran' => 0
        ]);

        // Sub Kegiatan 7: Penyelenggaraan Rapat Koordinasi dan Konsultasi
        $subKegiatan7 = \App\Models\SubKegiatan::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714,
            'id_sub_giat' => 20342, 'kode_sub_giat' => '5.02.01.2.06.0009',
            'nama_sub_giat' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD', 'nilai_anggaran' => 0
        ]);

        // Kode Rekening untuk Sub Kegiatan 1 (Penyediaan Komponen Instalasi Listrik)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20334,
            'id_akun' => 16419, 'kode_akun' => '5.1.02.01.01.0031', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Listrik',
            'nilai_anggaran' => 700120, 'id_rak_belanja' => 88525, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20334,
            'id_akun' => 16521, 'kode_akun' => '5.1.02.02.01.0035', 'nama_akun' => 'Belanja Jasa Tenaga Teknisi Mekanik dan Listrik',
            'nilai_anggaran' => 6834660, 'id_rak_belanja' => 88836, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        // Kode Rekening untuk Sub Kegiatan 2 (Penyediaan Peralatan dan Perlengkapan)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20335,
            'id_akun' => 16412, 'kode_akun' => '5.1.02.01.01.0024', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor',
            'nilai_anggaran' => 1866000, 'id_rak_belanja' => 88995, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20335,
            'id_akun' => 16415, 'kode_akun' => '5.1.02.01.01.0027', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Benda Pos',
            'nilai_anggaran' => 4050000, 'id_rak_belanja' => 88998, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        // Kode Rekening untuk Sub Kegiatan 3 (Penyediaan Peralatan Rumah Tangga)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20336,
            'id_akun' => 16418, 'kode_akun' => '5.1.02.01.01.0030', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Perabot Kantor',
            'nilai_anggaran' => 185000, 'id_rak_belanja' => 88449, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20336,
            'id_akun' => 16424, 'kode_akun' => '5.1.02.01.01.0036', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat/Bahan untuk Kegiatan Kantor Lainnya',
            'nilai_anggaran' => 60000, 'id_rak_belanja' => 88457, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        // Kode Rekening untuk Sub Kegiatan 4 (Penyediaan Bahan Logistik Kantor)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20337,
            'id_akun' => 16394, 'kode_akun' => '5.1.02.01.01.0005', 'nama_akun' => 'Belanja Bahan-Bahan Baku',
            'nilai_anggaran' => 8928000, 'id_rak_belanja' => 88847, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20337,
            'id_akun' => 16440, 'kode_akun' => '5.1.02.01.01.0052', 'nama_akun' => 'Belanja Makanan dan Minuman Rapat',
            'nilai_anggaran' => 261000, 'id_rak_belanja' => 88862, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        // Kode Rekening untuk Sub Kegiatan 5 (Penyediaan Barang Cetakan dan Penggandaan)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20338,
            'id_akun' => 16414, 'kode_akun' => '5.1.02.01.01.0026', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor- Bahan Cetak',
            'nilai_anggaran' => 3660100, 'id_rak_belanja' => 88484, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        // Kode Rekening untuk Sub Kegiatan 6 (Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20339,
            'id_akun' => 16541, 'kode_akun' => '5.1.02.02.01.0055', 'nama_akun' => 'Belanja Jasa Iklan/Reklame, Film, dan Pemotretan',
            'nilai_anggaran' => 37295000, 'id_rak_belanja' => 88976, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        // Kode Rekening untuk Sub Kegiatan 7 (Penyelenggaraan Rapat Koordinasi dan Konsultasi)
        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20342,
            'id_akun' => 16543, 'kode_akun' => '5.1.02.01.01.0026', 'nama_akun' => 'Belanja Alat/Bahan untuk Kegiatan Kantor- Bahan Cetak',
            'nilai_anggaran' => 1600000, 'id_rak_belanja' => 89815, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);

        \App\Models\KodeRekening::create([
            'id_daerah' => 409, 'tahun' => 2025, 'id_unit' => 362, 'id_skpd' => 362, 'id_sub_skpd' => 362,
            'kode_sub_skpd' => '5.02.0.00.0.00.01.0000', 'nama_sub_skpd' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
            'id_urusan' => 15, 'id_bidang_urusan' => 236, 'id_fungsi' => 1, 'id_sub_fungsi' => 1,
            'id_program' => 1186, 'id_giat' => 8714, 'id_sub_giat' => 20342,
            'id_akun' => 16544, 'kode_akun' => '5.1.02.01.01.0052', 'nama_akun' => 'Belanja Makanan dan Minuman Rapat',
            'nilai_anggaran' => 65334000, 'id_rak_belanja' => 89813, 'distribusi' => 'PA', 'id_pegawai_pa_kpa' => 0, 'is_blokir' => false
        ]);
    }
}

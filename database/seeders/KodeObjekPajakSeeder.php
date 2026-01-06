<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeObjekPajakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => '24-101-01', 'nama' => 'Dividen', 'tarif' => 15],
            ['kode' => '24-104-05', 'nama' => 'Jasa Aktuaris', 'tarif' => 2],
            ['kode' => '28-409-25', 'nama' => 'Pekerjaan Konstruksi Terintegrasi yang Dilakukan oleh Penyedia Jasa yang Memiliki Sertifikat Badan Usaha', 'tarif' => 2.65],
            ['kode' => '28-409-26', 'nama' => 'Pekerjaan Konstruksi Terintegrasi yang Dilakukan oleh Penyedia Jasa yang Tidak Memiliki Sertifikat Badan Usaha', 'tarif' => 4],
            ['kode' => '28-409-27', 'nama' => 'Jasa Konsultansi Konstruksi yang Dilakukan oleh Penyedia Jasa yang Memiliki Sertifikat Badan Usaha atau Sertifikat Kompetensi Kerja untuk Usaha Orang Perseorangan', 'tarif' => 3.5],
            ['kode' => '28-409-28', 'nama' => 'Jasa Konsultansi Konstruksi yang Dilakukan oleh Penyedia Jasa yang Tidak Memiliki Sertifikat Badan Usaha atau Sertifikat Kompetensi Kerja untuk Usaha Orang Perseorangan', 'tarif' => 6],
            ['kode' => '24-104-06', 'nama' => 'Jasa Akuntansi, Pembukuan, dan Atestasi Laporan Keuangan', 'tarif' => 2],
            ['kode' => '28-417-01', 'nama' => 'Bunga Simpanan yang Dibayarkan oleh Koperasi kepada Anggota Wajib Pajak Orang Pribadi (bunga sampai dengan Rp240.000,00)', 'tarif' => 0],
            ['kode' => '28-417-02', 'nama' => 'Bunga Simpanan yang Dibayarkan oleh Koperasi kepada Anggota Wajib Pajak Orang Pribadi (bunga di atas Rp240.000,00)', 'tarif' => 10],
            ['kode' => '28-419-01', 'nama' => 'Dividen yang Diterima/Diperoleh Wajib Pajak Orang Pribadi Dalam Negeri', 'tarif' => 10],
            ['kode' => '28-423-01', 'nama' => 'Pemotongan atau pemungutan PPh atas penjualan barang atau penyerahan jasa yang dilakukan oleh Wajib Pajak dengan peredaran bruto tertentu sesuai dengan Peraturan Pemerintah Nomor 23 Tahun 2018 atau Peraturan Pemerintah Nomor 55 Tahun 2022.', 'tarif' => 0.5],
            ['kode' => '28-423-02', 'nama' => 'Pemotongan atau pemungutan PPh atas transaksi pembelian yang dilakukan oleh Wajib Pajak dengan peredaran bruto tertentu sesuai dengan Peraturan Pemerintah Nomor 55 Tahun 2022.', 'tarif' => 0.5],
            ['kode' => '28-410-02', 'nama' => 'Imbalan yang Dibayarkan/Terutang kepada Perusahaan Pelayaran Dalam Negeri', 'tarif' => 1.2],
            ['kode' => '28-411-02', 'nama' => 'Imbalan Charter Kapal Laut dan/atau Pesawat Udara yang Dibayarkan/ Terutang kepada Perusahaan Pelayaran dan/atau Penerbangan Luar Negeri  melalui BUT di Indonesia', 'tarif' => 2.64],
            ['kode' => '29-101-01', 'nama' => 'Imbalan Charter Pesawat Udara yang Dibayarkan/Terutang kepada Perusahaan Penerbangan Dalam Negeri oleh Pemotong Pajak', 'tarif' => 1.8],
            ['kode' => '28-421-01', 'nama' => 'Uplift Hulu Migas', 'tarif' => 20],
            ['kode' => '24-104-07', 'nama' => 'Jasa Hukum', 'tarif' => 2],
            ['kode' => '28-421-02', 'nama' => 'Participating Interest Eksplorasi Hulu Migas', 'tarif' => 5],
            ['kode' => '28-421-03', 'nama' => 'Participating Interest Eksploitasi Hulu Migas', 'tarif' => 7],
            ['kode' => '22-900-01', 'nama' => 'Pembayaran atas Pembelian Barang dan/atau Bahan untuk Kegiatan Usahanya oleh BUMN/Badan Usaha Tertentu', 'tarif' => 1.5],
            ['kode' => '22-100-07', 'nama' => 'Penjualan Hasil Produksi Kepada Distributor di Dalam Negeri oleh Badan Usaha/Industri Tertentu (Industri Semen)', 'tarif' => 0.25],
            ['kode' => '22-100-08', 'nama' => 'Penjualan Hasil Produksi Kepada Distributor di Dalam Negeri oleh Badan Usaha/Industri Tertentu (Industri Baja)', 'tarif' => 0.3],
            ['kode' => '24-104-08', 'nama' => 'Jasa Arsitektur', 'tarif' => 2],
            ['kode' => '22-100-09', 'nama' => 'Penjualan Hasil Produksi Kepada Distributor di Dalam Negeri oleh Badan Usaha/Industri Tertentu (Industri Otomotif)', 'tarif' => 0.45],
            ['kode' => '22-100-10', 'nama' => 'Penjualan Hasil Produksi Kepada Distributor di Dalam Negeri oleh Badan Usaha/Industri Tertentu (Industri Farmasi)', 'tarif' => 0.3],
            ['kode' => '22-100-11', 'nama' => 'Penjualan Hasil Produksi Kepada Distributor di Dalam Negeri oleh Badan Usaha/Industri Tertentu (industri Kertas)', 'tarif' => 0.1],
            ['kode' => '22-100-12', 'nama' => 'Penjualan Kendaraan Bermotor di Dalam Negeri oleh ATPM, APM dan Importir Umum Kendaraan Bermotor', 'tarif' => 0.45],
            ['kode' => '22-100-13', 'nama' => 'Pembelian oleh Badan Usaha Berupa Komoditas Tambang Batubara, Mineral Logam dan Mineral Bukan Logam dari Badan atau Orang Pribadi Pemegang IUP', 'tarif' => 1.5],
            ['kode' => '22-100-14', 'nama' => 'Penjualan Emas Batangan di Dalam Negeri oleh Badan Usaha', 'tarif' => 0.45],
            ['kode' => '22-100-15', 'nama' => 'Pembelian Bahan Hasil Kehutanan yang Belum Melalui Proses Industri Manufaktur, untuk Keperluan Industrinya/Ekspornya oleh Badan Usaha Industri/Eksportir', 'tarif' => 0.25],
            ['kode' => '22-100-16', 'nama' => 'Pembelian Bahan Hasil Perkebunan yang Belum Melalui Proses Industri Manufaktur, untuk Keperluan Industrinya/Ekspornya Oleh Badan Usaha Industri/Eksportir', 'tarif' => 0.25],
            ['kode' => '22-100-17', 'nama' => 'Pembelian Bahan Hasil Pertanian yang Belum Melalui Proses Industri Manufaktur, untuk Keperluan Industrinya/Ekspornya Oleh Badan Usaha Industri/Eksportir', 'tarif' => 0.25],
            ['kode' => '22-100-18', 'nama' => 'Pembelian Bahan Hasil Peternakan yang Belum Melalui Proses Industri Manufaktur, untuk Keperluan Industrinya/Ekspornya Oleh Badan Usaha Industri/Eksportir', 'tarif' => 0.25],
            ['kode' => '24-104-09', 'nama' => 'Jasa Perencanaan Kota dan Arsitektur Landscape;', 'tarif' => 2],
            ['kode' => '22-100-19', 'nama' => 'Pembelian Bahan Hasil Perikanan yang Belum Melalui Proses Industri Manufaktur, untuk Keperluan Industrinya/Ekspornya Oleh Badan Usaha Industri/Eksportir', 'tarif' => 0.25],
            ['kode' => '22-401-01', 'nama' => 'Penjualan BBM oleh Pertamina atau Anak Perusahaan Pertamina Kepada SPBU (Final)', 'tarif' => 0.25],
            ['kode' => '22-100-20', 'nama' => 'Penjualan BBM oleh Pertamina atau Anak Perusahaan Pertamina Kepada Selain SPBU/Agen/Penyalur (Tidak Final)', 'tarif' => 0.3],
            ['kode' => '22-401-02', 'nama' => 'Penjualan BBM oleh Badan Usaha Selain Pertamina atau Anak Perusahaan Pertamina Kepada SPBU/Agen/Penyalur  (Final)', 'tarif' => 0.3],
            ['kode' => '22-100-21', 'nama' => 'Penjualan BBM oleh Badan Usaha Selain Pertamina atau Anak Perusahaan Pertamina Kepada Selain SPBU/Agen/Penyalur (Tidak Final)', 'tarif' => 0.3],
            ['kode' => '22-100-22', 'nama' => 'Penjualan Pelumas oleh Importir/Produsen', 'tarif' => 0.3],
            ['kode' => '22-100-23', 'nama' => 'Penjualan  Pulsa dan Kartu Perdana oleh Penyelenggara Distribusi Tingkat Kedua yang Merupakan Pemungut PPh Pasal 22', 'tarif' => 0.5],
            ['kode' => '22-100-24', 'nama' => 'Penjualan BBG oleh Produsen/Importir Kepada Selain SPBU/Agen/Penyalur (Tidak Final)', 'tarif' => 0.3],
            ['kode' => '22-401-03', 'nama' => 'Penjualan BBG oleh produsen/importir Kepada SPBU/Agen/Penyalur (Final)', 'tarif' => 0.3],
            ['kode' => '22-401-04', 'nama' => 'Penjualan BBM oleh Pertamina atau Anak Perusahaan Pertamina kepada Agen/Penyalur selain SPBU (Final)', 'tarif' => 0.3],
            ['kode' => '24-104-10', 'nama' => 'Jasa Perancang (Design)', 'tarif' => 2],
            ['kode' => '22-403-01', 'nama' => 'Penjualan Barang yang Tergolong Sangat Mewah Selain Rumah Beserta Tanahnya, Apartemen, Kondominium dan Sejenisnya', 'tarif' => 5],
            ['kode' => '22-403-02', 'nama' => 'Penjualan Barang yang Tergolong Sangat Mewah Untuk Rumah Beserta Tanahnya, Apartemen, Kondominium dan Sejenisnya', 'tarif' => 1],
            ['kode' => '22-404-01', 'nama' => 'Ekspor Komoditas Tambang Batubara, Mineral Logam, dan Mineral Bukan Logam yang Dilakukan Oleh Eksportir, Kecuali WP yang Terikat dalam PKP2B dan KK', 'tarif' => 1.5],
            ['kode' => '22-405-01', 'nama' => 'Penghasilan Sehubungan dengan Aset Kripto yang dipungut oleh Penyelenggara Perdagangan Melalui Sistem Elektronik yang Merupakan Pedagang Fisik Aset Kripto', 'tarif' => 0.1],
            ['kode' => '22-910-01', 'nama' => 'Pemungutan oleh Bendaharawan', 'tarif' => 1.5],
            ['kode' => '24-104-24', 'nama' => 'Jasa Sehubungan Dengan Software Atau Hardware Atau Sistem Komputer, Termasuk Perawatan, Pemeliharaan dan Perbaikan.', 'tarif' => 2],
            ['kode' => '24-104-39', 'nama' => 'Jasa Katering Atau Tata Boga', 'tarif' => 2],
            ['kode' => '24-104-28', 'nama' => 'Jasa Instalasi/Pemasangan Mesin, Peralatan, Listrik, Telepon, Air, Gas, Ac dan/atau Tv Kabel', 'tarif' => 2],
            ['kode' => '24-104-23', 'nama' => 'Jasa Pembuatan Sarana Promosi Film, Iklan, Poster, Foto, Slide, Klise, Banner, Pamphlet, Baliho dan Folder', 'tarif' => 2],
            ['kode' => '24-104-01', 'nama' => 'Jasa Teknik', 'tarif' => 2],
            ['kode' => '24-104-30', 'nama' => 'Jasa Perawatan Kendaraan dan/atau Alat Transportasi Darat, Laut dan Udara', 'tarif' => 2],
            ['kode' => '24-104-54', 'nama' => 'Jasa Pencetakan/Penerbitan', 'tarif' => 2],
        ];

        foreach ($data as $item) {
            DB::table('kode_objek_pajaks')->updateOrInsert(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'tarif' => $item['tarif'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }    }
}
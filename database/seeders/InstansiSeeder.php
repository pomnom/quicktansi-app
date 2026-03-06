<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instansi;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instansis = [
            // Sekretariat
            [
                'nama' => 'Sekretariat Daerah (Setda)',
                'alamat' => 'Komplek Perkantoran Pemda Kabupaten Dompu, Jl. Soekarno Hatta, Dompu',
                'no_telp' => '0373-21012',
                'email' => 'setda@dompukab.go.id',
                'website' => 'https://setda.dompukab.go.id',
            ],
            [
                'nama' => 'Sekretariat DPRD',
                'alamat' => 'Gedung DPRD Kabupaten Dompu, Jl. Soekarno Hatta, Dompu',
                'no_telp' => '0373-21234',
                'email' => 'sekretariat@dprd-dompukab.go.id',
                'website' => 'https://dprd.dompukab.go.id',
            ],
            [
                'nama' => 'Inspektorat Daerah',
                'alamat' => 'Komplek Perkantoran Pemda Kabupaten Dompu, Dompu',
                'no_telp' => '0373-21345',
                'email' => 'inspektorat@dompukab.go.id',
                'website' => 'https://inspektorat.dompukab.go.id',
            ],
            
            // Dinas
            [
                'nama' => 'Dinas Pendidikan, Pemuda, dan Olahraga',
                'alamat' => 'Jl. Pendidikan No. 1, Dompu',
                'no_telp' => '0373-21456',
                'email' => 'disdikpora@dompukab.go.id',
                'website' => 'https://disdikpora.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Kesehatan',
                'alamat' => 'Jl. Kesehatan No. 12, Dompu',
                'no_telp' => '0373-21567',
                'email' => 'dinkes@dompukab.go.id',
                'website' => 'https://dinkes.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)',
                'alamat' => 'Jl. Diponegoro No. 45, Dompu',
                'no_telp' => '0373-21678',
                'email' => 'dpupr@dompukab.go.id',
                'website' => 'https://dpupr.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Perumahan dan Kawasan Permukiman (Perkim)',
                'alamat' => 'Jl. Permukiman No. 23, Dompu',
                'no_telp' => '0373-21789',
                'email' => 'perkim@dompukab.go.id',
                'website' => 'https://perkim.dompukab.go.id',
            ],
            [
                'nama' => 'Satuan Polisi Pamong Praja (Satpol PP)',
                'alamat' => 'Jl. Keamanan No. 7, Dompu',
                'no_telp' => '0373-21890',
                'email' => 'satpolpp@dompukab.go.id',
                'website' => 'https://satpolpp.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Sosial',
                'alamat' => 'Jl. Sosial No. 15, Dompu',
                'no_telp' => '0373-22001',
                'email' => 'dinsos@dompukab.go.id',
                'website' => 'https://dinsos.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Tenaga Kerja dan Transmigrasi',
                'alamat' => 'Jl. Pahlawan No. 32, Dompu',
                'no_telp' => '0373-22112',
                'email' => 'disnakertrans@dompukab.go.id',
                'website' => 'https://disnakertrans.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Ketahanan Pangan',
                'alamat' => 'Jl. Pertanian No. 18, Dompu',
                'no_telp' => '0373-22223',
                'email' => 'disketanpangan@dompukab.go.id',
                'website' => 'https://disketanpangan.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Lingkungan Hidup',
                'alamat' => 'Jl. Lingkungan Hijau No. 9, Dompu',
                'no_telp' => '0373-22334',
                'email' => 'dlh@dompukab.go.id',
                'website' => 'https://dlh.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'alamat' => 'Jl. Kependudukan No. 25, Dompu',
                'no_telp' => '0373-22445',
                'email' => 'disdukcapil@dompukab.go.id',
                'website' => 'https://disdukcapil.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Pemberdayaan Masyarakat dan Desa',
                'alamat' => 'Jl. Desa No. 11, Dompu',
                'no_telp' => '0373-22556',
                'email' => 'dpmd@dompukab.go.id',
                'website' => 'https://dpmd.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Pengendalian Penduduk dan Keluarga Berencana',
                'alamat' => 'Jl. Keluarga Sejahtera No. 14, Dompu',
                'no_telp' => '0373-22667',
                'email' => 'dppkb@dompukab.go.id',
                'website' => 'https://dppkb.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Perhubungan',
                'alamat' => 'Jl. Terminal No. 8, Dompu',
                'no_telp' => '0373-22778',
                'email' => 'dishub@dompukab.go.id',
                'website' => 'https://dishub.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Komunikasi dan Informatika',
                'alamat' => 'Jl. Teknologi No. 20, Dompu',
                'no_telp' => '0373-22889',
                'email' => 'diskominfo@dompukab.go.id',
                'website' => 'https://diskominfo.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Koperasi dan UMKM',
                'alamat' => 'Jl. Ekonomi Rakyat No. 16, Dompu',
                'no_telp' => '0373-22990',
                'email' => 'diskopukm@dompukab.go.id',
                'website' => 'https://diskopukm.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
                'alamat' => 'Jl. Investasi No. 5, Dompu',
                'no_telp' => '0373-23101',
                'email' => 'dpmptsp@dompukab.go.id',
                'website' => 'https://dpmptsp.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Kebudayaan dan Pariwisata',
                'alamat' => 'Jl. Wisata No. 27, Dompu',
                'no_telp' => '0373-23212',
                'email' => 'disbudpar@dompukab.go.id',
                'website' => 'https://disbudpar.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Perpustakaan dan Kearsipan',
                'alamat' => 'Jl. Ilmu Pengetahuan No. 19, Dompu',
                'no_telp' => '0373-23323',
                'email' => 'dispusip@dompukab.go.id',
                'website' => 'https://dispusip.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Kelautan dan Perikanan',
                'alamat' => 'Jl. Pantai No. 13, Dompu',
                'no_telp' => '0373-23434',
                'email' => 'dislautkan@dompukab.go.id',
                'website' => 'https://dislautkan.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Pertanian dan Perkebunan',
                'alamat' => 'Jl. Tani No. 21, Dompu',
                'no_telp' => '0373-23545',
                'email' => 'distanbun@dompukab.go.id',
                'website' => 'https://distanbun.dompukab.go.id',
            ],
            [
                'nama' => 'Dinas Perindustrian dan Perdagangan',
                'alamat' => 'Jl. Industri No. 10, Dompu',
                'no_telp' => '0373-23656',
                'email' => 'disperindag@dompukab.go.id',
                'website' => 'https://disperindag.dompukab.go.id',
            ],
            
            // Badan
            [
                'nama' => 'Badan Perencanaan Pembangunan Daerah dan Litbang',
                'alamat' => 'Komplek Perkantoran Pemda, Jl. Soekarno Hatta, Dompu',
                'no_telp' => '0373-23767',
                'email' => 'bappeda@dompukab.go.id',
                'website' => 'https://bappeda.dompukab.go.id',
            ],
            [
                'nama' => 'Badan Kepegawaian Daerah dan PSDM',
                'alamat' => 'Komplek Perkantoran Pemda, Jl. Soekarno Hatta, Dompu',
                'no_telp' => '0373-23878',
                'email' => 'bkd@dompukab.go.id',
                'website' => 'https://bkd.dompukab.go.id',
            ],
            [
                'nama' => 'Badan Pengelolaan Keuangan dan Aset Daerah',
                'alamat' => 'Komplek Perkantoran Pemda, Jl. Soekarno Hatta, Dompu',
                'no_telp' => '0373-23989',
                'email' => 'bpkad@dompukab.go.id',
                'website' => 'https://bpkad.dompukab.go.id',
            ],
            [
                'nama' => 'Badan Pengelolaan Pendapatan Daerah',
                'alamat' => 'Jl. Pajak No. 6, Dompu',
                'no_telp' => '0373-24090',
                'email' => 'bppd@dompukab.go.id',
                'website' => 'https://bppd.dompukab.go.id',
            ],
            [
                'nama' => 'Badan Penanggulangan Bencana Daerah (BPBD)',
                'alamat' => 'Jl. Siaga No. 4, Dompu',
                'no_telp' => '0373-24201',
                'email' => 'bpbd@dompukab.go.id',
                'website' => 'https://bpbd.dompukab.go.id',
            ],
            [
                'nama' => 'Badan Kesatuan Bangsa dan Politik (Bakesbangpol)',
                'alamat' => 'Komplek Perkantoran Pemda, Jl. Soekarno Hatta, Dompu',
                'no_telp' => '0373-24312',
                'email' => 'bakesbangpol@dompukab.go.id',
                'website' => 'https://bakesbangpol.dompukab.go.id',
            ],
            
            // RSUD & MPP
            [
                'nama' => 'RSUD Kabupaten Dompu',
                'alamat' => 'Jl. Rumah Sakit No. 1, Dompu',
                'no_telp' => '0373-24423',
                'email' => 'rsud@dompukab.go.id',
                'website' => 'https://rsud.dompukab.go.id',
            ],
            [
                'nama' => 'Mal Pelayanan Publik (MPP)',
                'alamat' => 'Jl. Pelayanan Publik No. 2, Dompu',
                'no_telp' => '0373-24534',
                'email' => 'mpp@dompukab.go.id',
                'website' => 'https://mpp.dompukab.go.id',
            ],
            
            // Kecamatan
            [
                'nama' => 'Kecamatan Dompu',
                'alamat' => 'Jl. Kecamatan Dompu No. 1, Dompu',
                'no_telp' => '0373-24645',
                'email' => 'kec.dompu@dompukab.go.id',
                'website' => 'https://kec-dompu.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Woja',
                'alamat' => 'Jl. Kecamatan Woja No. 1, Woja',
                'no_telp' => '0373-24756',
                'email' => 'kec.woja@dompukab.go.id',
                'website' => 'https://kec-woja.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Kempo',
                'alamat' => 'Jl. Kecamatan Kempo No. 1, Kempo',
                'no_telp' => '0373-24867',
                'email' => 'kec.kempo@dompukab.go.id',
                'website' => 'https://kec-kempo.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Manggelewa',
                'alamat' => 'Jl. Kecamatan Manggelewa No. 1, Manggelewa',
                'no_telp' => '0373-24978',
                'email' => 'kec.manggelewa@dompukab.go.id',
                'website' => 'https://kec-manggelewa.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Kilo',
                'alamat' => 'Jl. Kecamatan Kilo No. 1, Kilo',
                'no_telp' => '0373-25089',
                'email' => 'kec.kilo@dompukab.go.id',
                'website' => 'https://kec-kilo.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Hu\'u',
                'alamat' => 'Jl. Kecamatan Hu\'u No. 1, Hu\'u',
                'no_telp' => '0373-25190',
                'email' => 'kec.huu@dompukab.go.id',
                'website' => 'https://kec-huu.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Pajo',
                'alamat' => 'Jl. Kecamatan Pajo No. 1, Pajo',
                'no_telp' => '0373-25201',
                'email' => 'kec.pajo@dompukab.go.id',
                'website' => 'https://kec-pajo.dompukab.go.id',
            ],
            [
                'nama' => 'Kecamatan Pekat',
                'alamat' => 'Jl. Kecamatan Pekat No. 1, Pekat',
                'no_telp' => '0373-25312',
                'email' => 'kec.pekat@dompukab.go.id',
                'website' => 'https://kec-pekat.dompukab.go.id',
            ],
        ];

        foreach ($instansis as $instansi) {
            Instansi::create($instansi);
        }
    }
}

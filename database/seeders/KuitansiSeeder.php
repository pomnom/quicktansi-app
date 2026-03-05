<?php

namespace Database\Seeders;

use App\Models\Kuitansi;
use App\Models\Rekanan;
use App\Models\KodeRekening;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KuitansiSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks untuk truncate
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kuitansi::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rekanans = Rekanan::all();
        $kodeRekenings = KodeRekening::all();
        $pptks = Staff::where('status', 'PPTK')->get();
        $allStaff = Staff::all();
        
        if ($rekanans->isEmpty()) {
            echo "Tidak ada rekanan. Jalankan RekananSeeder terlebih dahulu.\n";
            return;
        }

        if ($kodeRekenings->isEmpty()) {
            echo "Tidak ada kode rekening. Jalankan KegiatanSeeder terlebih dahulu.\n";
            return;
        }

        if ($pptks->isEmpty()) {
            echo "Tidak ada staff. Jalankan StaffSeeder terlebih dahulu.\n";
            return;
        }

        // Load kode objek pajak dari database
        $allTaxCodes = \DB::table('kode_objek_pajaks')->get()->keyBy('kode');
        
        if ($allTaxCodes->isEmpty()) {
            echo "Tidak ada kode objek pajak. Jalankan KodeObjekPajakSeeder terlebih dahulu.\n";
            return;
        }

        // Snapshot fixed staff (boleh null jika belum ada)
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->first();
        $bendaharaBarang = Staff::where('status', 'Bendahara Barang')->first();

        // Helper to compute pajak dan total with new rule
        $computePajak = function(array $items, ?string $jenisPph, ?float $tarifPajak): array {
            $dpp = 0;
            foreach ($items as $it) {
                $dpp += ((int)($it['jumlah'] ?? 0)) * ((float)($it['harga_satuan'] ?? 0));
            }
            $dpp = (int) round($dpp);

            // PPN hanya untuk belanja ≥ 2 juta
            $ppn = $dpp >= 2000000 ? (int) ceil($dpp * 0.11) : 0;

            $pph = 0;
            if (!empty($jenisPph) && $tarifPajak) {
                if ($jenisPph === '22' && $dpp < 2000000) {
                    // PPH 22 tidak dipotong untuk DPP < 2 juta
                    $pph = 0;
                } else {
                    // PPH 23 dan PPH 22 (untuk DPP ≥ 2 juta) dipotong sesuai tarif
                    $pph = (int) round($dpp * $tarifPajak / 100);
                }
            }

            // Total mengikuti logika controller: DPP + PPN - PPH
            $totalAkhir = $dpp + $ppn - $pph;

            return [
                'dpp' => $dpp,
                'ppn' => $ppn,
                'pph' => $pph,
                'total_akhir' => $totalAkhir,
            ];
        };

        /**
         * Dataset kuitansi dengan skenario beragam:
         * - TU dan GU
         * - Mix PPH 22 (pembelian barang), PPH 23 (jasa), dan tanpa PPH
         * - Variasi nilai: di bawah 2M (no PPN/PPH22) dan di atas 2M (dengan PPN)
         * - Kode objek pajak yang realistis sesuai jenis transaksi
         */
        $dataset = [
            // === TU-1: Belanja modal dan ATK kecil ===
            [
                'periode_type' => 'TU', 'periode_number' => 1, 'nomor_urut' => 1,
                'kode_pajak' => null, // Tidak ada pajak (belanja kecil)
                'items' => [
                    ['nama' => 'Kertas HVS A4 80gr (10 rim)', 'jumlah' => 10, 'harga_satuan' => 45000],
                    ['nama' => 'Tinta Printer HP Original', 'jumlah' => 5, 'harga_satuan' => 85000],
                    ['nama' => 'Stapler Joyko HD-10D', 'jumlah' => 15, 'harga_satuan' => 12000],
                ],
                'pembayaran' => 'Pengadaan Alat Tulis Kantor (ATK) Triwulan I Tahun 2026'
            ],
            [
                'periode_type' => 'TU', 'periode_number' => 1, 'nomor_urut' => 2,
                'kode_pajak' => '22-910-01', // Pemungutan bendahara 1.5%
                'items' => [
                    ['nama' => 'Laptop ASUS TUF Gaming F15', 'jumlah' => 2, 'harga_satuan' => 12500000],
                ],
                'pembayaran' => 'Pengadaan Laptop untuk Kepala Seksi Perencanaan dan Seksi Keuangan'
            ],
            [
                'periode_type' => 'TU', 'periode_number' => 1, 'nomor_urut' => 3,
                'kode_pajak' => '24-104-28', // Jasa instalasi 2%
                'items' => [
                    ['nama' => 'Jasa Instalasi Jaringan LAN Gedung Kantor', 'jumlah' => 1, 'harga_satuan' => 8500000],
                ],
                'pembayaran' => 'Pembayaran Jasa Instalasi Jaringan LAN Gedung Kantor Lantai 1-3'
            ],

            // === TU-2: Jasa dan operasional ===
            [
                'periode_type' => 'TU', 'periode_number' => 2, 'nomor_urut' => 1,
                'kode_pajak' => null,
                'items' => [
                    ['nama' => 'Tinta Canon GI-790 Black', 'jumlah' => 12, 'harga_satuan' => 65000],
                    ['nama' => 'Flashdisk Sandisk 32GB', 'jumlah' => 20, 'harga_satuan' => 55000],
                ],
                'pembayaran' => 'Pengadaan Supplies Kantor Bulanan Februari 2026'
            ],
            [
                'periode_type' => 'TU', 'periode_number' => 2, 'nomor_urut' => 2,
                'kode_pajak' => '24-104-39', // Jasa katering 2%
                'items' => [
                    ['nama' => 'Catering Rapat Koordinasi (50 pax x 3 hari)', 'jumlah' => 150, 'harga_satuan' => 35000],
                ],
                'pembayaran' => 'Pembayaran Jasa Katering Rapat Koordinasi Regional Tanggal 10-12 Februari 2026'
            ],
            [
                'periode_type' => 'TU', 'periode_number' => 2, 'nomor_urut' => 3,
                'kode_pajak' => '24-104-24', // Jasa software/hardware 2%
                'items' => [
                    ['nama' => 'Jasa Maintenance Server & Backup System', 'jumlah' => 1, 'harga_satuan' => 4800000],
                ],
                'pembayaran' => 'Pembayaran Jasa Perawatan dan Maintenance Server serta Sistem Backup Periode Januari-Maret 2026'
            ],

            // === GU-1: Proyek konstruksi dan konsultansi ===
            [
                'periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 1,
                'kode_pajak' => '28-409-25', // Konstruksi terintegrasi bersertifikat 2.65%
                'items' => [
                    ['nama' => 'Pekerjaan Renovasi Gedung Kantor Lt. 2 (Progress 30%)', 'jumlah' => 1, 'harga_satuan' => 85000000],
                ],
                'pembayaran' => 'Pembayaran Termin I (30%) Pekerjaan Renovasi Gedung Kantor Lantai 2 sesuai Kontrak No. 245/SPK/2026'
            ],
            [
                'periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 2,
                'kode_pajak' => '28-409-27', // Konsultansi konstruksi bersertifikat 3.5%
                'items' => [
                    ['nama' => 'Jasa Konsultan Pengawas Proyek Renovasi', 'jumlah' => 1, 'harga_satuan' => 15000000],
                ],
                'pembayaran' => 'Pembayaran Jasa Konsultan Pengawas Proyek Renovasi Gedung Periode Januari-Maret 2026'
            ],
            [
                'periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 3,
                'kode_pajak' => '24-104-01', // Jasa teknik 2%
                'items' => [
                    ['nama' => 'Jasa Survey dan Pemetaan Lahan', 'jumlah' => 1, 'harga_satuan' => 12500000],
                ],
                'pembayaran' => 'Pembayaran Jasa Survey dan Pemetaan Lahan untuk Rencana Pembangunan Gedung Baru'
            ],

            // === GU-2: Pelatihan, hukum, dan pencetakan ===
            [
                'periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 1,
                'kode_pajak' => '24-104-07', // Jasa hukum 2%
                'items' => [
                    ['nama' => 'Jasa Konsultan Hukum Penyelesaian Sengketa Lahan', 'jumlah' => 1, 'harga_satuan' => 18000000],
                ],
                'pembayaran' => 'Pembayaran Jasa Konsultan Hukum Penanganan Sengketa Lahan Sesuai Perjanjian No. 089/HK/2026'
            ],
            [
                'periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 2,
                'kode_pajak' => '24-104-54', // Jasa pencetakan 2%
                'items' => [
                    ['nama' => 'Cetak Buku Laporan Tahunan 2025 (Full Color, 500 eksemplar)', 'jumlah' => 500, 'harga_satuan' => 12500],
                ],
                'pembayaran' => 'Pembayaran Jasa Pencetakan Buku Laporan Tahunan 2025 sebanyak 500 eksemplar'
            ],
            [
                'periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 3,
                'kode_pajak' => '24-104-06', // Jasa akuntansi 2%
                'items' => [
                    ['nama' => 'Jasa Audit Keuangan Internal Tahun 2025', 'jumlah' => 1, 'harga_satuan' => 22000000],
                ],
                'pembayaran' => 'Pembayaran Jasa Audit Keuangan Internal Tahun Anggaran 2025'
            ],

            // === TU-3: Pemeliharaan dan operasional rutin ===
            [
                'periode_type' => 'TU', 'periode_number' => 3, 'nomor_urut' => 1,
                'kode_pajak' => '24-104-30', // Jasa perawatan kendaraan 2%
                'items' => [
                    ['nama' => 'Service Berkala Kendaraan Dinas (5 unit)', 'jumlah' => 5, 'harga_satuan' => 850000],
                ],
                'pembayaran' => 'Pembayaran Jasa Service Berkala Kendaraan Dinas Triwulan I Tahun 2026'
            ],
            [
                'periode_type' => 'TU', 'periode_number' => 3, 'nomor_urut' => 2,
                'kode_pajak' => null,
                'items' => [
                    ['nama' => 'Bahan Pembersih dan Kebersihan Kantor', 'jumlah' => 1, 'harga_satuan' => 1250000],
                ],
                'pembayaran' => 'Pengadaan Bahan Kebersihan untuk Keperluan Operasional Kantor Bulan Maret 2026'
            ],
            [
                'periode_type' => 'TU', 'periode_number' => 3, 'nomor_urut' => 3,
                'kode_pajak' => '22-910-01', // Pembelian barang bendahara 1.5%
                'items' => [
                    ['nama' => 'AC Split 2 PK Daikin Inverter', 'jumlah' => 3, 'harga_satuan' => 7500000],
                ],
                'pembayaran' => 'Pengadaan AC Split untuk Ruang Rapat dan Ruang Kepala Dinas'
            ],
        ];

        // Variasi tanggal untuk distribusi data lintas bulan
        $datePool = [
            Carbon::create(2026, 1, 15),
            Carbon::create(2026, 1, 22),
            Carbon::create(2026, 1, 28),
            Carbon::create(2026, 2, 8),
            Carbon::create(2026, 2, 18),
            Carbon::create(2026, 2, 25),
            Carbon::create(2026, 3, 5),
            Carbon::create(2026, 3, 12),
            Carbon::create(2026, 3, 20),
            Carbon::create(2026, 3, 28),
        ];

        // Process each kuitansi
        foreach ($dataset as $i => $data) {
            $tanggalKuitansi = $datePool[$i % count($datePool)];
            
            $items = $data['items'];
            $kodePajak = $data['kode_pajak']; // Full kode like '22-910-01' atau null
            
            // Determine jenis_pph and tarif from kode_pajak
            $jenisPph = null;
            $tarifPajak = null;
            $kodeObjekPajak = null;
            
            if ($kodePajak && isset($allTaxCodes[$kodePajak])) {
                $taxData = $allTaxCodes[$kodePajak];
                $kodeObjekPajak = $taxData->kode;
                $tarifPajak = (float) $taxData->tarif;
                
                // Determine jenis_pph (22 or 23) from kode prefix
                if (str_starts_with($kodePajak, '22-')) {
                    $jenisPph = '22';
                } elseif (str_starts_with($kodePajak, '24-') || str_starts_with($kodePajak, '28-') || str_starts_with($kodePajak, '29-')) {
                    $jenisPph = '23';
                }
            }
            
            // Calculate pajak using the helper
            $pajak = $computePajak($items, $jenisPph, $tarifPajak);
            
            // Random selection
            $kodeRekening = $kodeRekenings->random();
            $rekanan = $rekanans->random();
            $pptk = ($pptks->isNotEmpty() ? $pptks : $allStaff)->random();
            
            // Generate no_buku
            $noBuku = $data['periode_type'] . '-' . $data['periode_number'] . '-' . str_pad($data['nomor_urut'], 3, '0', STR_PAD_LEFT);
            
            // Build payload
            $payload = [
                'nomor_rekening' => $kodeRekening->kode_akun,
                'id_akun' => $kodeRekening->id_akun,
                'periode_type' => $data['periode_type'],
                'periode_number' => $data['periode_number'],
                'nomor_urut' => $data['nomor_urut'],
                'no_buku' => $noBuku,
                'rekanan_id' => $rekanan->id,
                'nama_penerima' => $rekanan->nama_perusahaan,
                'tanggal_kuitansi' => $tanggalKuitansi,
                'tanggal_pemotongan' => $tanggalKuitansi,
                'ppn' => $pajak['ppn'],
                'pph' => $pajak['pph'],
                'jenis_pph' => $jenisPph,
                'kode_objek_pajak' => $kodeObjekPajak,
                'tarif_pajak' => $tarifPajak,
                'untuk_pembayaran' => $data['pembayaran'],
                'total_akhir' => $pajak['total_akhir'],
                'rincian_item' => $items,
                'pptk_1_id' => $pptk->id,
                'nama_pengguna_anggaran' => $penggunaAnggaran->nama ?? null,
                'nip_pengguna_anggaran' => $penggunaAnggaran->nip ?? null,
                'nama_bendahara_pengeluaran' => $bendaharaPengeluaran->nama ?? null,
                'nip_bendahara_pengeluaran' => $bendaharaPengeluaran->nip ?? null,
                'nama_bendahara_barang' => $bendaharaBarang->nama ?? null,
                'nip_bendahara_barang' => $bendaharaBarang->nip ?? null,
                'nama_pptk' => $pptk->nama,
                'nip_pptk' => $pptk->nip,
                'dpp' => $pajak['dpp'],
                'jenis_dokumen' => 'PaymentProof',
            ];
            
            Kuitansi::create($payload);
        }

        echo "✓ KuitansiSeeder berhasil! " . count($dataset) . " kuitansi telah dibuat.\n";
        echo "  - TU: " . collect($dataset)->where('periode_type', 'TU')->count() . " records\n";
        echo "  - GU: " . collect($dataset)->where('periode_type', 'GU')->count() . " records\n";
        echo "  - Dengan PPH: " . collect($dataset)->whereNotNull('kode_pajak')->count() . " records\n";
        echo "  - Tanpa PPH: " . collect($dataset)->whereNull('kode_pajak')->count() . " records\n";
    }
}


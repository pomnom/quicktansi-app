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

        // Ambil kode pajak spesifik untuk PPh 22 dan PPh 23 (fallback ke prefix jika exact match tidak ada)
        $taxCode22 = \DB::table('kode_objek_pajaks')->where('kode', '22-910-01')->first()
            ?? \DB::table('kode_objek_pajaks')->where('kode', 'like', '22-%')->first();
        $taxCode23 = \DB::table('kode_objek_pajaks')->where('kode', '24-104-01')->first()
            ?? \DB::table('kode_objek_pajaks')->where('kode', 'like', '24-%')->first();

        if (!$taxCode22 || !$taxCode23) {
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

            $ppn = $dpp > 2000000 ? (int) ceil($dpp * 0.11) : 0;

            $pph = 0;
            if (!empty($jenisPph) && $tarifPajak) {
                if ($jenisPph === '22' && $dpp <= 2000000) {
                    $pph = 0; // Tidak kena pajak
                } else {
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

        $dataset = [
            // GU 1 (6 records)
            ['periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 1, 'jenis_pph' => '22', 'items' => [ ['nama' => 'Monitor LED 24 inch', 'jumlah' => 2, 'harga_satuan' => 700000 ] ]], // 1.4M
            ['periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 2, 'jenis_pph' => '22', 'items' => [ ['nama' => 'Laptop Ultrabook', 'jumlah' => 1, 'harga_satuan' => 2600000 ] ]], // 2.6M
            ['periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 3, 'jenis_pph' => '23', 'items' => [ ['nama' => 'Jasa Instalasi Jaringan', 'jumlah' => 1, 'harga_satuan' => 3100000 ] ]], // 3.1M
            ['periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 4, 'jenis_pph' => null, 'items' => [ ['nama' => 'ATK Campuran', 'jumlah' => 1, 'harga_satuan' => 800000 ] ]], // 0.8M
            ['periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 5, 'jenis_pph' => '23', 'items' => [ ['nama' => 'Jasa Cleaning Office', 'jumlah' => 1, 'harga_satuan' => 1900000 ] ]], // 1.9M
            ['periode_type' => 'GU', 'periode_number' => 1, 'nomor_urut' => 6, 'jenis_pph' => '22', 'items' => [ ['nama' => 'PC Workstation', 'jumlah' => 1, 'harga_satuan' => 2100000 ] ]], // 2.1M

            // GU 2 (6 records)
            ['periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 1, 'jenis_pph' => null, 'items' => [ ['nama' => 'Tinta Printer', 'jumlah' => 20, 'harga_satuan' => 25000 ] ]], // 0.5M
            ['periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 2, 'jenis_pph' => '22', 'items' => [ ['nama' => 'Server Rackmount', 'jumlah' => 1, 'harga_satuan' => 4200000 ] ]], // 4.2M
            ['periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 3, 'jenis_pph' => '23', 'items' => [ ['nama' => 'Jasa Konsultan IT', 'jumlah' => 1, 'harga_satuan' => 2400000 ] ]], // 2.4M
            ['periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 4, 'jenis_pph' => '22', 'items' => [ ['nama' => 'Kursi Ergonomis', 'jumlah' => 2, 'harga_satuan' => 850000 ] ]], // 1.7M
            ['periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 5, 'jenis_pph' => '23', 'items' => [ ['nama' => 'Jasa Pelatihan Software', 'jumlah' => 1, 'harga_satuan' => 3600000 ] ]], // 3.6M
            ['periode_type' => 'GU', 'periode_number' => 2, 'nomor_urut' => 6, 'jenis_pph' => '22', 'items' => [ ['nama' => 'Kipas Angin Industri', 'jumlah' => 3, 'harga_satuan' => 300000 ] ]], // 0.9M
        ];

        $datePool = [
            Carbon::create(2026, 1, 10),
            Carbon::create(2026, 1, 18),
            Carbon::create(2026, 1, 27),
            Carbon::create(2026, 2, 5),
            Carbon::create(2026, 2, 14),
            Carbon::create(2026, 2, 28),
            Carbon::create(2026, 3, 8),
            Carbon::create(2026, 3, 19),
            Carbon::create(2026, 3, 30),
            Carbon::create(2026, 4, 10),
            Carbon::create(2026, 4, 20),
            Carbon::create(2026, 4, 30),
        ];

        foreach ($dataset as $i => $data) {
            $tanggalKuitansi = $datePool[$i % count($datePool)]; // variasi tanggal lintas bulan

            $items = $data['items'];
            $jenisPph = $data['jenis_pph'];

            $tarifPajak = null;
            if ($jenisPph === '23') {
                $tarifPajak = (float) $taxCode23->tarif;
            } elseif ($jenisPph === '22') {
                $tarifPajak = (float) $taxCode22->tarif;
            }

            $pajak = $computePajak($items, $jenisPph, $tarifPajak);

            $kodeRekening = $kodeRekenings->random();
            $rekanan = $rekanans->random();
            $pptk = ($pptks->isNotEmpty() ? $pptks : $allStaff)->random();

            $noBuku = $data['periode_type'] . ' ' . $data['periode_number'] . ' / ' . str_pad($data['nomor_urut'], 3, '0', STR_PAD_LEFT);

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
                'untuk_pembayaran' => $items[0]['nama'],
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

            // Isi kode objek pajak dan tarif untuk semua jenis_pph
            if ($jenisPph) {
                $payload['kode_objek_pajak'] = $jenisPph === '23' ? $taxCode23->kode : $taxCode22->kode;
                $payload['tarif_pajak'] = $tarifPajak;
            } else {
                $payload['kode_objek_pajak'] = null;
                $payload['tarif_pajak'] = null;
            }

            Kuitansi::create($payload);
        }

        echo "KuitansiSeeder berhasil dijalankan dengan " . count($dataset) . " records!\n";
    }
}


<?php

namespace Database\Seeders;

use App\Models\Kuitansi;
use App\Models\Rekanan;
use App\Models\KodeRekening;
use App\Models\Staff;
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
        $pptks = Staff::all();
        
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
        $computePajak = function(array $items, ?string $jenisPph, float $tarifPajak): array {
            $grand = 0;
            foreach ($items as $it) {
                $grand += ((int)($it['jumlah'] ?? 0)) * ((float)($it['harga_satuan'] ?? 0));
            }
            $grand = (int) round($grand);
            $ppn = $grand > 2000000 ? (int) ceil($grand * 0.11) : 0;
            
            // PPH calculation dengan rule baru: PPH 22 hanya jika belanja > 2M
            $pph = 0;
            if (!empty($jenisPph) && $tarifPajak > 0) {
                if ($jenisPph === '22' && $grand <= 2000000) {
                    $pph = 0; // Tidak kena pajak
                } else {
                    // PPH 23 atau PPH 22 dengan belanja > 2M
                    $pph = (int) round($grand * $tarifPajak / 100);
                }
            }
            return [
                'ppn' => $ppn,
                'pph' => $pph,
                'total_akhir' => $grand + $ppn + $pph
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

        foreach ($dataset as $data) {
            $items = $data['items'];
            $jenisPph = $data['jenis_pph'];
            $tarifPajak = $jenisPph === '23' ? (float) $taxCode23->tarif : (float) $taxCode22->tarif;

            $pajak = $computePajak($items, $jenisPph, $tarifPajak);
            $dpp = 0;
            foreach ($items as $it) {
                $dpp += ((int)$it['jumlah']) * ((float)$it['harga_satuan']);
            }
            $dpp = (int) round($dpp);

            $kodeRekening = $kodeRekenings->random();
            $rekanan = $rekanans->random();
            $pptk = $pptks->random();

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
                'tanggal_kuitansi' => now(),
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
            ];

            // Isi kode objek pajak dan tarif untuk semua jenis_pph
            if ($jenisPph) {
                $payload['kode_objek_pajak'] = $jenisPph === '23' ? $taxCode23->kode : $taxCode22->kode;
                $payload['tarif_pajak'] = $jenisPph === '23' ? $taxCode23->tarif : $taxCode22->tarif;
            }

            // Isi BuPot fields hanya jika dpp >= 2M
            if ($dpp >= 2000000 && $jenisPph) {
                $payload['dpp'] = $dpp;
                $payload['jenis_dokumen'] = 'PaymentProof';
                $payload['tanggal_pemotongan'] = now();
            }

            Kuitansi::create($payload);
        }

        echo "KuitansiSeeder berhasil dijalankan dengan " . count($dataset) . " records!\n";
    }
}


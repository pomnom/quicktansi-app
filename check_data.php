<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kuitansi;
use App\Models\Staff;

$kuitansis = Kuitansi::with('pptk')->get();

echo "=== DATA KUITANSI ===\n\n";

foreach ($kuitansis as $kuitansi) {
    echo "No. Buku: {$kuitansi->no_buku}\n";
    echo "Nama Penerima: {$kuitansi->nama_penerima}\n";
    echo "Total Akhir: Rp " . number_format($kuitansi->total_akhir, 0, ',', '.') . "\n";
    echo "PPN: Rp " . number_format($kuitansi->ppn, 0, ',', '.') . "\n";
    echo "PPH ({$kuitansi->jenis_pph}): Rp " . number_format($kuitansi->pph, 0, ',', '.') . "\n";
    echo "PPTK dari relasi: " . ($kuitansi->pptk ? $kuitansi->pptk->nama : 'Tidak ada') . "\n";
    echo "Jabatan PPTK: " . ($kuitansi->pptk ? $kuitansi->pptk->jabatan : 'Tidak ada') . "\n";
    echo "nama_pptk (field): {$kuitansi->nama_pptk}\n";
    echo "nip_pptk (field): {$kuitansi->nip_pptk}\n";
    echo "---\n\n";
}

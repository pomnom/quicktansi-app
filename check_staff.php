<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Staff;

$staffs = Staff::all();

echo "=== DATA STAFF ===\n\n";

foreach ($staffs as $staff) {
    echo "Nama: {$staff->nama}\n";
    echo "Jabatan: {$staff->jabatan}\n";
    echo "NIP: {$staff->nip}\n";
    echo "---\n\n";
}

<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use App\Models\KodeRekening;
use App\Models\SubKegiatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RekeningSeeder extends Seeder
{
    private function loadJson(string $filename): array
    {
        $path = database_path("seeders/data/{$filename}");
        return json_decode(file_get_contents($path), true);
    }

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KodeRekening::truncate();
        SubKegiatan::truncate();
        Kegiatan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Load & deduplicate (raw SIPD response bisa punya duplikat)
        $kegiatanData    = collect($this->loadJson('sipd_kegiatan.json'))
            ->unique('id_giat')->values()->all();
        $subKegiatanData = collect($this->loadJson('sipd_sub_kegiatan.json'))
            ->unique('id_sub_giat')->values()->all();
        $rekeningData    = collect($this->loadJson('sipd_kode_rekening.json'))
            ->unique(fn($i) => $i['id_sub_giat'] . '_' . $i['id_akun'])->values()->all();

        // Ambil instansi dari entri pertama (field nama_sub_skpd sesuai raw SIPD)
        $instansi = $kegiatanData[0]['nama_sub_skpd'];

        // ── Kegiatan ─────────────────────────────────────────────────────────
        foreach ($kegiatanData as $item) {
            Kegiatan::create([
                'instansi'  => $instansi,
                'id_giat'   => $item['id_giat'],
                'kode_giat' => $item['kode_giat'],
                'nama_giat' => $item['nama_giat'],
            ]);
        }

        // ── Sub Kegiatan ─────────────────────────────────────────────────────
        foreach ($subKegiatanData as $item) {
            SubKegiatan::create([
                'instansi'      => $instansi,
                'id_giat'       => $item['id_giat'],
                'id_sub_giat'   => $item['id_sub_giat'],
                'kode_sub_giat' => $item['kode_sub_giat'],
                'nama_sub_giat' => trim(preg_replace('/\s+/', ' ', $item['nama_sub_giat'])),
            ]);
        }

        // ── Kode Rekening ────────────────────────────────────────────────────
        foreach ($rekeningData as $item) {
            KodeRekening::create([
                'instansi'    => $instansi,
                'id_sub_giat' => $item['id_sub_giat'],
                'id_akun'     => $item['id_akun'],
                'kode_akun'   => $item['kode_akun'],
                'nama_akun'   => $item['nama_akun'],
                'is_blokir'   => $item['is_blokir'],
            ]);
        }

        $this->command->info(
            "Seeder selesai: " . count($kegiatanData) . " kegiatan, "
            . count($subKegiatanData) . " sub kegiatan, "
            . count($rekeningData) . " kode rekening."
            . " (duplikat dari SIPD diabaikan otomatis)"
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeRekening extends Model
{
    use HasFactory;

    protected $table = 'kode_rekening';

    protected $fillable = [
        'id_daerah', 'tahun', 'id_unit', 'id_skpd', 'id_sub_skpd',
        'kode_sub_skpd', 'nama_sub_skpd', 'id_urusan', 'id_bidang_urusan',
        'id_fungsi', 'id_sub_fungsi', 'id_program', 'id_giat', 'id_sub_giat',
        'id_akun', 'kode_akun', 'nama_akun', 'nilai_anggaran',
        'id_rak_belanja', 'distribusi', 'id_pegawai_pa_kpa', 'is_blokir'
    ];

    protected $casts = [
        'is_blokir' => 'boolean',
        'nilai_anggaran' => 'decimal:2'
    ];

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'id_sub_giat', 'id_sub_giat');
    }
}

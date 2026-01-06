<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $fillable = [
        'id_daerah', 'tahun', 'id_unit', 'id_skpd', 'id_sub_skpd',
        'kode_sub_skpd', 'nama_sub_skpd', 'id_urusan', 'id_bidang_urusan',
        'id_fungsi', 'id_sub_fungsi', 'id_program', 'id_giat', 'kode_giat',
        'nama_giat', 'nilai_anggaran'
    ];

    public function subKegiatan()
    {
        return $this->hasMany(SubKegiatan::class, 'id_giat', 'id_giat');
    }
}

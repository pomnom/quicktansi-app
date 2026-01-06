<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $table = 'sub_kegiatan';

    protected $fillable = [
        'id_daerah', 'tahun', 'id_unit', 'id_skpd', 'id_sub_skpd',
        'kode_sub_skpd', 'nama_sub_skpd', 'id_urusan', 'id_bidang_urusan',
        'id_fungsi', 'id_sub_fungsi', 'id_program', 'id_giat', 'id_sub_giat',
        'kode_sub_giat', 'nama_sub_giat', 'nilai_anggaran'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'id_giat', 'id_giat');
    }

    public function kodeRekening()
    {
        return $this->hasMany(KodeRekening::class, 'id_sub_giat', 'id_sub_giat');
    }
}

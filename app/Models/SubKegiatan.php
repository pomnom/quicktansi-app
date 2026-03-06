<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $table = 'sub_kegiatan';

    protected $fillable = [
        'instansi', 'id_giat', 'id_sub_giat',
        'kode_sub_giat', 'nama_sub_giat'
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

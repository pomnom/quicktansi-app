<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $fillable = [
        'instansi', 'id_giat', 'kode_giat',
        'nama_giat'
    ];

    public function subKegiatan()
    {
        return $this->hasMany(SubKegiatan::class, 'id_giat', 'id_giat');
    }
}

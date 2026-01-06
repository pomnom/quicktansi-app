<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'npwp',
        'nama_perusahaan',
        'nomor_rekening',
        'bank',
        'nama_pemilik_rekening'
    ];
}

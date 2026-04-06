<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nama_pemerintah',
        'npwp',
        'alamat',
        'no_telp',
        'email',
        'website',
        'logo',
    ];

    /**
     * Get the users for the instansi.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'instansi', 'nama');
    }
}

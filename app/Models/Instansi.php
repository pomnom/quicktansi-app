<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'email',
        'website',
    ];

    /**
     * Get the users for the instansi.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'instansi', 'nama');
    }
}

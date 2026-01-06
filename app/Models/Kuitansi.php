<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuitansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_rekening',
        'id_akun',
        'periode_type',
        'periode_number',
        'nomor_urut',
        'no_buku',
        'rekanan_id',
        'nama_penerima',
        'tanggal_kuitansi',
        'tanggal_pemotongan',
        'ppn',
        'pph',
        'jenis_pph',
        'kode_objek_pajak',
        'tarif_pajak',
        'dpp',
        'jenis_dokumen',
        'untuk_pembayaran',
        'total_akhir',
        'rincian_item',
        'pptk_1_id',
        'nama_pengguna_anggaran',
        'nip_pengguna_anggaran',
        'nama_bendahara_pengeluaran',
        'nip_bendahara_pengeluaran',
        'nama_bendahara_barang',
        'nip_bendahara_barang',
        'nama_pptk',
        'nip_pptk',
    ];

    protected $casts = [
        'rincian_item' => 'array',
    ];

    /**
     * Get the rekanan that owns the kuitansi.
     */
    public function rekanan()
    {
        return $this->belongsTo(Rekanan::class);
    }

    /**
     * Get the PPTK staff.
     */
    public function pptk()
    {
        return $this->belongsTo(Staff::class, 'pptk_1_id');
    }

    /**
     * Get the kode rekening.
     */
    public function kodeRekening()
    {
        return $this->belongsTo(KodeRekening::class, 'id_akun', 'id_akun');
    }

}

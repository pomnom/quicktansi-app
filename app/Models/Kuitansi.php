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
        'id_kode_rekening',
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
        'pph_22',
        'pph_23',
        'jenis_pph',
        'kode_objek_pajak',
        'tarif_pajak',
        'kode_objek_pajak_23',
        'tarif_pajak_23',
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
        'instansi',
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
     * Get the kode rekening using primary key (id) for unambiguous reference.
     * Previously used id_akun which was non-unique (one account code in multiple sub_kegiatan).
     */
    public function kodeRekening()
    {
        return $this->belongsTo(KodeRekening::class, 'id_kode_rekening', 'id');
    }

    /**
     * Get formatted nomor_rekening with activity and sub-activity codes.
     * Format: XX.YYYY.full_kode_akun
     * XX = last part after last dot of activity code (kode_giat)
     * YYYY = last part after last dot of sub-activity code (kode_sub_giat)
     * full_kode_akun = entire account code
     */
    public function getFormattedNomorRekeningAttribute()
    {
        try {
            // Check if relationships are loaded
            if (!isset($this->relations['kodeRekening'])) {
                return $this->nomor_rekening; // Fallback if not loaded
            }

            // Get KodeRekening
            $kodeRekening = $this->kodeRekening;
            if (!$kodeRekening) {
                return $this->nomor_rekening;
            }

            // Get SubKegiatan
            if (!isset($kodeRekening->relations['subKegiatan'])) {
                return $this->nomor_rekening;
            }
            $subKegiatan = $kodeRekening->subKegiatan;
            if (!$subKegiatan) {
                return $this->nomor_rekening;
            }

            // Get Kegiatan
            if (!isset($subKegiatan->relations['kegiatan'])) {
                return $this->nomor_rekening;
            }
            $kegiatan = $subKegiatan->kegiatan;
            if (!$kegiatan) {
                return $this->nomor_rekening;
            }

            // Extract last part after last dot from kode_giat
            $kodeGiatParts = explode('.', $kegiatan->kode_giat ?? '');
            $lastPartKegiatan = end($kodeGiatParts);

            // Extract last part after last dot from kode_sub_giat
            $kodeSubGiatParts = explode('.', $subKegiatan->kode_sub_giat ?? '');
            $lastPartSubKegiatan = end($kodeSubGiatParts);

            // Get full kode_akun
            $kodeAkun = $kodeRekening->kode_akun ?? $this->nomor_rekening;

            // Format as lastPartKegiatan.lastPartSubKegiatan.kode_akun
            return "$lastPartKegiatan.$lastPartSubKegiatan.$kodeAkun";
        } catch (\Exception $e) {
            // If anything goes wrong, return original
            return $this->nomor_rekening;
        }
    }

}

# QuickTansi App

Aplikasi manajemen kuitansi berbasis web yang sederhana dan mudah digunakan untuk mengelola pembayaran dan dokumentasi kuitansi.

## Fitur

- Manajemen Kuitansi (Buat, Edit, Hapus, Lihat)
- Manajemen Staff dan Rekanan
- Perhitungan PPN/PPH Otomatis
- Export ke XML
- Preview dan Cetak

## Stack Teknologi

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **Database**: MySQL

## Setup Cepat

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL

### Instalasi

```bash
# Clone repository
git clone https://github.com/pomnom/quicktansi-app.git
cd quicktansi-app

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate:fresh --seed

# Jalankan aplikasi
php artisan serve
```

Akses aplikasi di `http://localhost:8000`
- **staff**: Data pegawai dengan role, jabatan, dan status
- **rekanans**: Data vendor/supplier dengan NPWP
- **kegiatan**: Data kegiatan anggaran
- **sub_kegiatan**: Data sub kegiatan per kegiatan
- **kode_rekening**: Data kode rekening per sub kegiatan
- **kuitansis**: Data kuitansi dengan periode TU/GU, nomor urut, dan snapshot staff untuk historis

### Relasi

- Kuitansi → Rekanan (many-to-one)
- Kuitansi → Staff (many-to-one untuk PPTK via pptk_1_id)
- Kuitansi → Kode Rekening (many-to-one via id_akun)
- Kode Rekening → Sub Kegiatan → Kegiatan (hierarki)
- Kuitansi menyimpan snapshot nama & NIP staff untuk akurasi historis

## Perhitungan Pajak

Sistem menggunakan rules berikut:

### PPN (Pajak Pertambahan Nilai)
- **Rate**: 11%
- **Threshold**: Hanya dikenakan jika grand total > Rp 2.000.000
- **Formula**: `ppn = grand_total * 0.11`

### PPH 22 (Pajak Penghasilan Pasal 22)
- **Rate**: 
  - 2% jika rekanan memiliki NPWP
  - 4% jika rekanan tidak memiliki NPWP
- **Threshold**: Hanya dikenakan jika grand total > Rp 2.000.000
- **Formula**: `pph = grand_total * (0.02 atau 0.04)`

### PPH 23 (Pajak Penghasilan Pasal 23)
- **Rate**:
  - 1.5% jika rekanan memiliki NPWP
  - 3% jika rekanan tidak memiliki NPWP
- **Threshold**: Tidak ada threshold (selalu dikenakan)
- **Formula**: `pph = grand_total * (0.015 atau 0.03)`

### Total Akhir
`total_akhir = grand_total + ppn - pph`

## Penggunaan

### 1. Kelola Staff
- Akses menu **Rekanan > Staff**
- Tambah/edit data pegawai dengan role yang sesuai
- Pastikan ada minimal 1 pegawai untuk setiap role penting (Pengguna Anggaran, Bendahara Pengeluaran, Bendahara Barang)

### 2. Kelola Rekanan
- Akses menu **Rekanan > Rekanan**
- Tambah data vendor/supplier dengan NPWP dan informasi rekening

### 3. Buat Kuitansi
- Akses menu **Kuitansi**
- Klik **Tambah Kuitansi**
- Pilih **Kode Rekening** menggunakan modal tingkat 2 (Kegiatan → Sub Kegiatan → Kode Rekening)
- Pilih **Periode** (TU/GU) dan nomor periode
- Isi data: tanggal, pilih rekanan, PPTK, jenis PPH, dan item barang/jasa
- Sistem akan otomatis menghitung:
  - PPN 11% jika total > 2 juta
  - PPH 22 atau 23 berdasarkan NPWP rekanan
  - Total akhir = grand total + PPN - PPH
- Simpan dan preview untuk melihat hasil

### 4. Preview & Print
- Klik tombol preview pada kuitansi
- Preview menampilkan:
  - No. Buku format: `TU 1 / 001` atau `GU 2 / 003`
  - Rincian barang/jasa dengan perhitungan otomatis
  - PPN dan PPH jika ada
  - Tanda tangan 5 pihak dengan jabatan dari database staff
- Gunakan zoom controls untuk menyesuaikan tampilan (zoom in/out, reset)
- Klik tombol print untuk mencetak dokumen

## Troubleshooting

### Migration Error
Jika terjadi error saat migration, reset database:
```bash
php artisan migrate:fresh --seed
```

### Permission Error (Linux/Mac)
Pastikan folder storage dan bootstrap/cache writable:
```bash
chmod -R 775 storage bootstrap/cache
```

### DomPDF Error
Jika preview PDF error, pastikan extension PHP mbstring dan GD terinstall:
```bash
# Ubuntu/Debian
sudo apt-get install php-mbstring php-gd

# Windows
# Enable di php.ini: extension=mbstring dan extension=gd
```

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

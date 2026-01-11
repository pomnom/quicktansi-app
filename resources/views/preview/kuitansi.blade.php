<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Kuitansi - {{ $kuitansi->nama_penerima }}</title>
    <link rel="stylesheet" href="{{ asset('css/kuitansi-preview.css') }}">
</head>
<body>
    <!-- Preview Toolbar -->
    <div class="toolbar">
        <div class="toolbar-left">
            <a href="{{ route('kuitansi.index') }}" title="Kembali">
                <span class="icon">←</span>
            </a>
            <span class="toolbar-title">{{ $kuitansi->nama_penerima }}</span>
        </div>
        
        <div class="toolbar-center">
            <div class="zoom-control">
                <button onclick="zoomOut()" title="Zoom Out (Ctrl-)">
                    <span class="icon">−</span>
                </button>
                <span class="zoom-value" id="zoomValue">100%</span>
                <button onclick="zoomIn()" title="Zoom In (Ctrl+)">
                    <span class="icon">+</span>
                </button>
            </div>
            <button onclick="resetZoom()" title="Reset Zoom (Ctrl+0)">
                <span class="icon">⛶</span>
            </button>
        </div>
        
        <div class="toolbar-right">
            <button onclick="window.print()" class="primary" title="Print">
                <span class="icon">🖨</span>
            </button>
        </div>
    </div>
    
    <!-- Preview Container -->
    <div class="preview-container">
        <div class="preview-page" id="previewPage">
            <div class="document-content">
    <!-- Top Left Info -->
    <div class="top-left-info">UNTUK PEMERINTAH</div>
    
    <!-- Top Right Info -->
    <div class="top-right-info">
        <table class="info-table">
            <tr>
                <td class="label">No. Rekening</td>
                <td class="colon">:</td>
                <td class="value">{{ $kuitansi->nomor_rekening }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Dibukukan</td>
                <td class="colon">:</td>
                <td class="value">{{ $kuitansi->tanggal_kuitansi ? \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('d-m-Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">No. Buku</td>
                <td class="colon">:</td>
                <td class="value">{{ str_pad($kuitansi->nomor_urut, 3, '0', STR_PAD_LEFT) }} / {{ $kuitansi->periode_type }} {{ $kuitansi->periode_number }}</td>
            </tr>
            <tr>
                <td class="label">Paraf</td>
                <td class="colon">:</td>
                <td class="value">&nbsp;</td>
            </tr>
        </table>
    </div>

    <!-- kuitansi Title -->
    <div class="kuitansi-title">KUITANSI</div>

    @php
        use App\Helpers\NumberToWordHelper;
        
        $ppnAmount = (int) ($kuitansi->ppn ?? 0);
        $pphAmount = (int) ($kuitansi->pph ?? 0);
        $totalAkhir = (int) ($kuitansi->total_akhir ?? 0);
        
        $terbilang = NumberToWordHelper::terbilang($totalAkhir);
    @endphp

    <div class="content">
        <table class="content-table">
            <tr>
                <td class="label">Telah Terima Dari</td>
                <td class="colon">:</td>
                <td class="value">Pengguna Anggaran Badan Pengelolaan Keuangan dan Aset Daerah Kabupaten Dompu Tahun {{ now()->year }}</td>
            </tr>
            <tr>
                <td class="label">Terbilang</td>
                <td class="colon">:</td>
                <td class="value"><strong><em>{{ $terbilang }} Rupiah</em></strong></td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $kuitansi->untuk_pembayaran  }}</td>
                <!-- ?? 'Belanja Alat/Bahan Untuk Alat Tulis Kantor Kebutuhan Bidang Perbendaharaan dan Kas Daerah Kegiatan Rekonsiliasi Data Penerimaan dan Pengeluaran Kas serta Pemungutan dan Pemotongan atas SP2D dengan Intensi Terkait pada Badan Pengelolaan Keuangan dan Aset Daerah Kabupaten Dompu Sesuai Nota Kontan' -->
            </tr>
        </table>
    </div>

    <!-- Items Table if exists -->
    @if($kuitansi->rincian_item && count($kuitansi->rincian_item) > 0)
    <table class="items-table">
        <thead>
            <tr>
                <th>Nama Barang/Jasa</th>
                <th width="80px" class="number">Jumlah</th>
                <th width="100px" class="number">Harga Satuan</th>
                <th width="100px" class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($kuitansi->rincian_item as $item)
            @php 
                $itemTotal = $item['jumlah'] * $item['harga_satuan'];
                $grandTotal += $itemTotal;
            @endphp
            <tr>
                <td>{{ $item['nama'] }}</td>
                <td class="number">{{ $item['jumlah'] }}</td>
                <td class="number">{{ number_format($item['harga_satuan'], 2, ',', '.') }}</td>
                <td class="number">{{ number_format($itemTotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 8px;">Total:</td>
                <td class="number">{{ number_format($grandTotal, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <table class="amount-section">
        @if($ppnAmount > 0)
        <tr>
            <td class="label">PPN</td>
            <td class="colon">:</td>
            <td class="value">Rp {{ number_format($ppnAmount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($pphAmount > 0)
        <tr>
            <td class="label">PPH {{ $kuitansi->jenis_pph ?? '-' }}</td>
            <td class="colon">:</td>
            <td class="value">Rp {{ number_format($pphAmount, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>
    
    <table class="uang-sejumlah-section">
        <tr>
            <td class="label">Uang Sejumlah</td>
            <td class="colon">:</td>
            <td class="value">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</td>
        </tr>
    </table>
    @endif

    @php
        // Get Bendahara Barang for random selection (already loaded in controller)
    @endphp

    <table class="signature-table">
        <!-- Baris Pertama: 3 Orang -->
        <tr>
            <!-- Mengetahui - Pengguna Anggaran -->
            <td class="signature-cell">
                <p class="role-title">Mengetahui</p>
                <p class="role-subtitle">Pengguna Anggaran<br>BPKAD Kab. Dompu</p>
                <p class="sig-name">{{ $kuitansi->nama_pengguna_anggaran ?? $penggunaAnggaran->nama ?? 'N/A' }}</p>
                <div class="sig-line"></div>
                <p class="sig-nip">NIP. {{ $kuitansi->nip_pengguna_anggaran ?? $penggunaAnggaran->nip ?? '-' }}</p>
            </td>

            <!-- Lunas dibayar - Bendahara Pengeluaran -->
            <td class="signature-cell">
                <p class="role-title">Lunas dibayar</p>
                <p class="role-subtitle">Bendahara Pengeluaran</p>
                <p class="sig-name">{{ $kuitansi->nama_bendahara_pengeluaran ?? $bendaharaPengeluaran->nama ?? 'N/A' }}</p>
                <div class="sig-line"></div>
                <p class="sig-nip">NIP. {{ $kuitansi->nip_bendahara_pengeluaran ?? $bendaharaPengeluaran->nip ?? '-' }}</p>
            </td>

            <!-- Yang Menerima Uang - Rekanan -->
            <td class="signature-cell">
                <p class="role-title">Yang Menerima Uang</p>
                <p class="role-subtitle">Rekanan</p>
                <p class="sig-name">{{ $kuitansi->nama_penerima }}</p>
                <div class="sig-line"></div>
                <p class="sig-nip">{{ $kuitansi->rekanan->npwp ?? '-' }}</p>
            </td>
        </tr>

        <!-- Baris Kedua: Bendahara Barang & PPTK (Centered) -->
        <tr>
            <td colspan="3" class="signature-center-cell">
                <div class="signature-row-center">
                    @if($kuitansi->nama_bendahara_barang)
                    <div class="signature-block">
                        <p class="role-title">Telah diperiksa cukup</p>
                        <p class="role-subtitle">Bendahara Barang</p>
                        <p class="sig-name">{{ $kuitansi->nama_bendahara_barang }}</p>
                        <div class="sig-line"></div>
                        <p class="sig-nip">{{ $kuitansi->nip_bendahara_barang ? 'NIP. ' . $kuitansi->nip_bendahara_barang : '' }}</p>
                    </div>
                    @endif
                    <div class="signature-block">
                        <p class="role-title">Pejabat Pelaksana Teknis Kegiatan</p>
                        <p class="role-subtitle">{{ $kuitansi->pptk->jabatan ?? 'PPTK' }}</p>
                        <p class="sig-name">{{ $kuitansi->nama_pptk ?? $kuitansi->pptk->nama ?? 'N/A' }}</p>
                        <div class="sig-line"></div>
                        <p class="sig-nip">NIP. {{ $kuitansi->nip_pptk ?? $kuitansi->pptk->nip ?? '-' }}</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/kuitansi-preview.js') }}"></script>
</body>
</html>

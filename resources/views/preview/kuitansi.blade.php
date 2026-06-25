<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Kuitansi — {{ $kuitansi->nama_penerima }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/kuitansi-preview.css') }}">
</head>
<body>
    <!-- Preview Toolbar -->
    <div class="toolbar">
        <div class="toolbar-left">
            <a href="#" onclick="window.close(); return false;" class="btn-back" title="Tutup tab ini">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <div class="toolbar-divider"></div>
            <div class="doc-info">
                <div class="doc-title">{{ $kuitansi->nama_penerima }}</div>
                <div class="doc-meta">No. Buku: {!! ($kuitansi->no_buku && $kuitansi->no_buku !== 'null') ? $kuitansi->no_buku : ($kuitansi->periode_type.' '.$kuitansi->periode_number.' / '.($kuitansi->nomor_urut ? str_pad($kuitansi->nomor_urut,3,'0',STR_PAD_LEFT) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')) !!}</div>
            </div>
        </div>

        <div class="toolbar-center">
            <div class="zoom-control">
                <button onclick="zoomOut()" title="Zoom Out (Ctrl −)"><i class="fas fa-minus"></i></button>
                <span class="zoom-value" id="zoomValue">100%</span>
                <button onclick="zoomIn()" title="Zoom In (Ctrl +)"><i class="fas fa-plus"></i></button>
            </div>
            <button class="btn-icon" onclick="resetZoom()" title="Reset Zoom (Ctrl 0)">
                <i class="fas fa-expand-arrows-alt"></i>
            </button>
        </div>

        <div class="toolbar-right">
            <button class="btn-print" onclick="window.print()" title="Cetak (Ctrl+P)">
                <i class="fas fa-print"></i>
                <span>Cetak</span>
            </button>
        </div>
    </div>

    <!-- Info Strip -->
    @php
        use App\Helpers\NumberToWordHelper;
        $dppAmount   = (int) ($kuitansi->dpp    ?? 0);
        $ppnAmount   = (int) ($kuitansi->ppn    ?? 0);
        $pphAmount   = (int) ($kuitansi->pph    ?? 0);
        $pph22Amount = (int) ($kuitansi->pph_22 ?? 0);
        $pph23Amount = (int) ($kuitansi->pph_23 ?? 0);
        $totalAkhir  = (int) ($kuitansi->total_akhir ?? 0);
        $terbilang   = NumberToWordHelper::terbilang($totalAkhir);
    @endphp
    <div class="info-strip">
        <div class="info-chip">
            <i class="fas fa-hashtag"></i>
            <span>No. Buku:&nbsp;<strong>{!! ($kuitansi->no_buku && $kuitansi->no_buku !== 'null') ? $kuitansi->no_buku : ($kuitansi->periode_type.' '.$kuitansi->periode_number.' / '.($kuitansi->nomor_urut ? str_pad($kuitansi->nomor_urut,3,'0',STR_PAD_LEFT) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')) !!}</strong>
            </span>
        </div>
        <div class="info-chip">
            <i class="fas fa-building"></i>
            <span>Rekanan:&nbsp;<strong>{{ $kuitansi->nama_penerima }}</strong></span>
        </div>
        <div class="info-chip">
            <i class="fas fa-calendar-alt"></i>
            <span>Tanggal:&nbsp;<strong>{{ $kuitansi->tanggal_kuitansi ? \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->isoFormat('D MMMM Y') : '—' }}</strong></span>
        </div>
        <div class="info-chip">
            <i class="fas fa-money-bill-wave"></i>
            <span>Total:&nbsp;<strong>Rp {{ number_format($totalAkhir, 0, ',', '.') }}</strong></span>
        </div>
        @if($pph22Amount > 0 || $pph23Amount > 0)
        <div class="info-chip">
            <i class="fas fa-receipt"></i>
            <span>PPH:&nbsp;<strong>Rp {{ number_format($pphAmount, 0, ',', '.') }}</strong></span>
        </div>
        @endif
    </div>

    <!-- Preview Container -->
    <div class="preview-container">
        <div class="preview-page" id="previewPage">
            <div class="document-content">

    <!-- KOP SURAT -->
    <div class="kop-surat">
        @if($instansiData?->logo)
        <div class="kop-logo">
            <img src="{{ asset('images/logos/' . $instansiData->logo) }}" alt="Logo" style="max-height:80px;max-width:80px;object-fit:contain;">
        </div>
        @else
        <div class="kop-logo" style="width:80px;"></div>
        @endif
        <div class="kop-text">
            @if($instansiData?->nama_pemerintah)
            <div class="kop-pemerintah">{{ strtoupper($instansiData->nama_pemerintah) }}</div>
            @endif
            <div class="kop-instansi">{{ strtoupper($instansiData?->nama ?? $kuitansi->instansi ?? '') }}</div>
            @if($instansiData?->alamat)
            <div class="kop-alamat">{{ $instansiData->alamat }}</div>
            @endif
        </div>
    </div>
    <div class="kop-divider"></div>

    <!-- Document Header: left stamp + right info table -->
    <div class="doc-header">
        <div class="doc-header-left">UNTUK PEMERINTAH</div>
        <div class="doc-header-right">
            <table class="info-table">
                <tr>
                    <td class="label">No. Rekening</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $kuitansi->nomor_rekening }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Dibukukan</td>
                    <td class="colon">:</td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label">No. Buku</td>
                    <td class="colon">:</td>
                    <td class="value">{!! ($kuitansi->no_buku && $kuitansi->no_buku !== 'null') ? $kuitansi->no_buku : ($kuitansi->periode_type.' '.$kuitansi->periode_number.' / '.($kuitansi->nomor_urut ? str_pad($kuitansi->nomor_urut, 3, '0', STR_PAD_LEFT) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')) !!}</td>
                </tr>
                <tr>
                    <td class="label">Paraf</td>
                    <td class="colon">:</td>
                    <td class="value">&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- kuitansi Title -->
    <div class="kuitansi-title">KUITANSI</div>

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
                <th>Rincian</th>
                <th width="80px" class="number">Jumlah</th>
                <th width="100px" class="number">Harga Satuan</th>
                <th width="100px" class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($kuitansi->rincian_item as $item)
            @php 
                $qtyRaw = $item['jumlah'] ?? null;
                $qty = (is_numeric($qtyRaw) && (float)$qtyRaw > 0) ? (int)$qtyRaw : 1;
                $unit = trim((string)($item['satuan'] ?? ''));
                $qtyDisplay = trim($qty . ($unit !== '' ? ' ' . $unit : ''));
                $itemTotal = $qty * $item['harga_satuan'];
                $grandTotal += $itemTotal;
                $isJasa = !empty($item['is_jasa']);
            @endphp
            <tr>
                <td>{{ $item['nama'] }}@if($isJasa) <em style="font-size:10px; color:#36b9cc;">(Jasa)</em>@endif</td>
                <td class="number">{{ $qtyDisplay }}</td>
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
            <td class="label">PPN 11%</td>
            <td class="colon">:</td>
            <td class="value">Rp {{ number_format($ppnAmount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($pph22Amount > 0)
        <tr>
            <td class="label">PPH 22</td>
            <td class="colon">:</td>
            <td class="value">Rp {{ number_format($pph22Amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($pph23Amount > 0)
        <tr>
            <td class="label">PPH 23</td>
            <td class="colon">:</td>
            <td class="value">Rp {{ number_format($pph23Amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($pphAmount > 0 && $pph22Amount > 0 && $pph23Amount > 0)
        <tr>
            <td class="label" style="font-style:italic;">Total PPH</td>
            <td class="colon">:</td>
            <td class="value" style="font-weight:bold;">Rp {{ number_format($pphAmount, 0, ',', '.') }}</td>
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
                <div class="sig-header">
                    <p class="role-title">Mengetahui</p>
                    <p class="role-subtitle">Pengguna Anggaran<br>BPKAD Kab. Dompu</p>
                </div>
                <p class="sig-name">{{ $kuitansi->nama_pengguna_anggaran ?? $penggunaAnggaran->nama ?? 'N/A' }}</p>
                <div class="sig-line"></div>
                <p class="sig-nip">NIP. {{ $kuitansi->nip_pengguna_anggaran ?? $penggunaAnggaran->nip ?? '-' }}</p>
            </td>

            <!-- Lunas dibayar - Bendahara Pengeluaran -->
            <td class="signature-cell">
                <div class="sig-header">
                    <p class="role-title">Lunas dibayar</p>
                    <p class="role-subtitle">Bendahara Pengeluaran</p>
                </div>
                <p class="sig-name">{{ $kuitansi->nama_bendahara_pengeluaran ?? $bendaharaPengeluaran->nama ?? 'N/A' }}</p>
                <div class="sig-line"></div>
                <p class="sig-nip">NIP. {{ $kuitansi->nip_bendahara_pengeluaran ?? $bendaharaPengeluaran->nip ?? '-' }}</p>
            </td>

            <!-- Yang Menerima Uang - Rekanan -->
            <td class="signature-cell">
                <div class="sig-header">
                    <p class="role-subtitle">Dompu,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ now()->year }}</p>
                    <p class="role-title">Yang Menerima Uang</p>
                </div>
                <p class="sig-name">{{ $kuitansi->nama_penerima }}</p>
            </td>
        </tr>

        <!-- Baris Kedua: Bendahara Barang & PPTK (Centered) -->
        <tr>
            <td colspan="3" class="signature-center-cell">
                <div class="signature-row-center">
                    @if($kuitansi->nama_bendahara_barang)
                    <div class="signature-block">
                        <div class="sig-header">
                            <p class="role-title">Telah diperiksa cukup</p>
                            <p class="role-subtitle">Bendahara Barang</p>
                        </div>
                        <p class="sig-name">{{ $kuitansi->nama_bendahara_barang }}</p>
                        <div class="sig-line"></div>
                        <p class="sig-nip">{{ $kuitansi->nip_bendahara_barang ? 'NIP. ' . $kuitansi->nip_bendahara_barang : '' }}</p>
                    </div>
                    @endif
                    <div class="signature-block">
                        <div class="sig-header">
                            <p class="role-title">Pejabat Pelaksana Teknis Kegiatan</p>
                        </div>
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

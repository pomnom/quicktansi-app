@extends('layouts.app')

@section('title', 'Kuitansi')


@section('content')

@php
    $totalKuitansi = $kuitansis->count();
    $totalNominal  = $kuitansis->sum('total_akhir');
    $bulanIni      = $kuitansis->filter(fn($k) => $k->tanggal_kuitansi && \Carbon\Carbon::parse($k->tanggal_kuitansi)->isCurrentMonth());
    $countBulanIni = $bulanIni->count();
    $nominalBulanIni = $bulanIni->sum('total_akhir');
@endphp

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Manajemen Kuitansi
            </div>
            <div class="hero-title">Data Kuitansi</div>
            <p class="hero-sub">Kelola, filter, dan ekspor kuitansi instansi Anda.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-receipt"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-file-invoice hs-icon"></i>
            <div>
                <div class="hs-label">Total Kuitansi</div>
                <div class="hs-value">{{ number_format($totalKuitansi, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-wallet hs-icon"></i>
            <div>
                <div class="hs-label">Total Nominal</div>
                <div class="hs-value" style="font-size:14px;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-calendar-check hs-icon"></i>
            <div>
                <div class="hs-label">Bulan Ini</div>
                <div class="hs-value">{{ number_format($countBulanIni, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-money-bill-wave hs-icon"></i>
            <div>
                <div class="hs-label">Nominal Bulan Ini</div>
                <div class="hs-value" style="font-size:14px;">Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-2">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary h-100">
            <div class="card-body">
                <div class="stat-label">Total Kuitansi</div>
                <div class="stat-value">{{ number_format($totalKuitansi, 0, ',', '.') }}</div>
                <i class="fas fa-receipt stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua periode</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success h-100">
            <div class="card-body">
                <div class="stat-label">Total Nominal</div>
                <div class="stat-value small-value">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                <i class="fas fa-wallet stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua periode</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-warning h-100">
            <div class="card-body">
                <div class="stat-label">Kuitansi Bulan Ini</div>
                <div class="stat-value">{{ number_format($countBulanIni, 0, ',', '.') }}</div>
                <i class="fas fa-calendar-check stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-clock"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-info h-100">
            <div class="card-body">
                <div class="stat-label">Nominal Bulan Ini</div>
                <div class="stat-value small-value">Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}</div>
                <i class="fas fa-money-bill-wave stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-clock"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="kuitansi-action-bar">
    <button class="btn btn-primary" data-toggle="modal" data-target="#selectRekeningModal">
        <i class="fas fa-plus mr-2"></i>Tambah Kuitansi
    </button>
    <button class="btn btn-success" id="exportXmlBtn" style="display:none;">
        <i class="fas fa-download mr-2"></i>Export XML (<span id="selectedCount">0</span>)
    </button>
    <button class="btn btn-outline-secondary ml-auto" id="toggleFilterBtn" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="true">
        <i class="fas fa-filter mr-2"></i>Filter
        <i class="fas fa-chevron-up ml-1 filter-toggle-icon" id="filterChevron"></i>
    </button>
</div>

<!-- DataTable Card -->
<div class="card recent-card mb-4">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-table"></i></div>
        <h6>Data Kuitansi</h6>
        <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalKuitansi }} data</span>
    </div>
    <div class="card-body">

        <!-- Filter Panel (Collapsible) -->
        <div class="collapse show" id="filterCollapse">
            <div class="filter-panel-wrap mb-3">
                <div class="filter-panel-header" data-toggle="collapse" data-target="#filterInner" aria-expanded="true">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sliders-h mr-2"></i>Filter Data
                    </h6>
                    <i class="fas fa-chevron-up text-muted filter-toggle-icon" id="filterInnerChevron"></i>
                </div>
                <div class="collapse show" id="filterInner">
                    <div class="filter-panel-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="filter_no_buku" class="small font-weight-bold">No. Buku</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-hashtag"></i></span></div>
                                    <input type="text" class="form-control" id="filter_no_buku" placeholder="Contoh: TU-1-001">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="filter_rekening" class="small font-weight-bold">Nomor Rekening</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-code-branch"></i></span></div>
                                    <input type="text" class="form-control" id="filter_rekening" placeholder="Cari rekening">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="filter_penerima" class="small font-weight-bold">Nama Penerima</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                    <input type="text" class="form-control" id="filter_penerima" placeholder="Cari penerima">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="filter_pembayaran" class="small font-weight-bold">Untuk Pembayaran</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-align-left"></i></span></div>
                                    <input type="text" class="form-control" id="filter_pembayaran" placeholder="Cari pembayaran">
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-3">
                                <label for="filter_tanggal_mulai" class="small font-weight-bold">Tanggal Mulai</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar"></i></span></div>
                                    <input type="date" class="form-control" id="filter_tanggal_mulai">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="filter_tanggal_selesai" class="small font-weight-bold">Tanggal Selesai</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                    <input type="date" class="form-control" id="filter_tanggal_selesai">
                                </div>
                            </div>
                            <div class="form-group col-md-6 text-md-right">
                                <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="resetFilterBtn">
                                    <i class="fas fa-undo mr-1"></i>Reset
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" id="applyFilterBtn">
                                    <i class="fas fa-check mr-1"></i>Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th width="40px" class="text-center">
                            <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                        </th>
                        <th>No</th>
                        <th>No. Buku</th>
                        <th>Nomor Rekening</th>
                        <th>Untuk Pembayaran</th>
                        <th>Grand Total</th>
                        <th>Nama Penerima</th>
                        <th>Pajak</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kuitansis as $kuitansi)
                    <tr class="kuitansi-row"
                        data-no-buku="{{ strtolower($kuitansi->no_buku ?? '') }}"
                        data-rekening="{{ strtolower($kuitansi->nomor_rekening ?? '') }}"
                        data-penerima="{{ strtolower($kuitansi->nama_penerima ?? '') }}"
                        data-pembayaran="{{ strtolower($kuitansi->untuk_pembayaran ?? '') }}"
                        data-tanggal="{{ $kuitansi->tanggal_kuitansi }}">
                        <td class="text-center">
                            <input type="checkbox" class="kuitansi-checkbox" value="{{ $kuitansi->id }}" title="Pilih kuitansi ini">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        @php
                            $noBukuDisplay = ($kuitansi->no_buku && $kuitansi->no_buku !== 'null')
                                ? $kuitansi->no_buku
                                : ($kuitansi->periode_type . ($kuitansi->periode_number ? ' ' . $kuitansi->periode_number : '') . ' / ' . ($kuitansi->nomor_urut ? str_pad($kuitansi->nomor_urut, 3, '0', STR_PAD_LEFT) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'));
                        @endphp
                        <td data-search="{{ $kuitansi->no_buku ?? ($kuitansi->periode_type . ($kuitansi->periode_number ? ' ' . $kuitansi->periode_number : '')) }}">
                            <span class="no-buku-badge">{!! $noBukuDisplay !!}</span>
                        </td>
                        <td data-search="{{ $kuitansi->nomor_rekening }}">
                            <small class="text-muted">{{ $kuitansi->formatted_nomor_rekening }}</small>
                        </td>
                        <td data-search="{{ $kuitansi->untuk_pembayaran }}">
                            <small>{{ \Illuminate\Support\Str::limit($kuitansi->untuk_pembayaran, 80) }}</small>
                        </td>
                        <td data-search="{{ (int)($kuitansi->total_akhir ?? 0) }}">
                            <span class="badge-nominal">Rp {{ number_format((int)($kuitansi->total_akhir ?? 0), 0, ',', '.') }}</span>
                        </td>
                        <td data-search="{{ $kuitansi->nama_penerima }}">{{ $kuitansi->nama_penerima }}</td>
                        <td data-search="{{ $kuitansi->pph_22 }} {{ $kuitansi->pph_23 }} {{ $kuitansi->ppn }}">
                            <small>
                                @php
                                    $pajakArray = [];
                                    if ($kuitansi->pph_22 && $kuitansi->pph_22 > 0) {
                                        $pajakArray[] = 'PPh 22: ' . number_format($kuitansi->pph_22, 0, ',', '.');
                                    }
                                    if ($kuitansi->pph_23 && $kuitansi->pph_23 > 0) {
                                        $pajakArray[] = 'PPh 23: ' . number_format($kuitansi->pph_23, 0, ',', '.');
                                    }
                                    if ($kuitansi->ppn && $kuitansi->ppn > 0) {
                                        $pajakArray[] = 'PPN: ' . number_format($kuitansi->ppn, 0, ',', '.');
                                    }
                                    $pajak = !empty($pajakArray) ? implode(' / ', $pajakArray) : '-';
                                @endphp
                                {{ $pajak }}
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="aksi-buttons d-inline-flex">
                                <button class="btn btn-info edit-btn" 
                                        title="Edit"
                                        data-id="{{ $kuitansi->id }}" 
                                        data-rekening="{{ $kuitansi->nomor_rekening }}" 
                                        data-periode_type="{{ $kuitansi->periode_type }}"
                                        data-periode_number="{{ $kuitansi->periode_number }}"
                                        data-rekanan_id="{{ $kuitansi->rekanan_id }}" 
                                        data-jenis_pph="{{ $kuitansi->jenis_pph }}"
                                        data-untuk_pembayaran="{{ $kuitansi->untuk_pembayaran }}"
                                        data-toggle="modal" 
                                        data-target="#editkuitansiModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('kuitansi.preview', $kuitansi->id) }}" class="btn btn-warning" target="_blank" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('kuitansi.destroy', $kuitansi->id) }}" style="display:inline;" id="deleteForm{{ $kuitansi->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="button" title="Hapus" onclick="confirmDelete({{ $kuitansi->id }}, '{{ addslashes($kuitansi->nama_penerima) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Select Rekening Modal -->
<div class="modal fade" id="selectRekeningModal" tabindex="-1" role="dialog" aria-labelledby="selectRekeningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="selectRekeningModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>Pilih Kode Rekening
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Langkah 1:</strong> Pilih Kegiatan, Sub Kegiatan, dan Kode Rekening terlebih dahulu
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div class="form-group">
                    <label for="select_kegiatan" class="font-weight-bold">Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_kegiatan" required>
                        <option value="">-- Pilih Kegiatan --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_sub_kegiatan" class="font-weight-bold">Sub Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_sub_kegiatan" required disabled>
                        <option value="">-- Pilih Sub Kegiatan --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_kode_rekening" class="font-weight-bold">Kode Rekening <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_kode_rekening" required disabled>
                        <option value="">-- Pilih Kode Rekening --</option>
                    </select>
                    <small class="form-text text-muted">Kode akun akan diisi otomatis ke form kuitansi</small>
                </div>

                <div id="selected_rekening_info" class="alert alert-success mt-3" style="display:none;">
                    <strong>✓ Rekening Terpilih:</strong>
                    <p class="mb-1"><strong>Kode:</strong> <span id="info_kode_akun"></span></p>
                    <p class="mb-0"><strong>Nama:</strong> <span id="info_nama_akun"></span></p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button class="btn btn-primary" type="button" id="btnLanjutKeForm" disabled>
                    <i class="fas fa-arrow-right mr-1"></i>Lanjut ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Kuitansi Modal -->
<div class="modal fade" id="addkuitansiModal" tabindex="-1" role="dialog" aria-labelledby="addkuitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addkuitansiModalLabel">
                    <i class="fas fa-file-invoice mr-2"></i>Tambah Kuitansi
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('kuitansi.store') }}">
                @csrf
                <div class="modal-body" style="max-height: 72vh; overflow-y: auto; padding: 1.25rem;">

                    {{-- ── IDENTITAS KUITANSI ──────────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-primary text-white"><i class="fas fa-id-card"></i></span>
                        <span class="font-weight-bold ml-2 text-primary" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Identitas Kuitansi</span>
                    </div>

                    <div class="alert alert-primary py-2 px-3 mb-3 d-flex align-items-center justify-content-between" role="alert" style="border-radius:10px;">
                        <div>
                            <i class="fas fa-clipboard-list mr-1"></i>
                            <strong>Kode Rekening:</strong>
                            <span id="display_kode_rekening" class="ml-1 font-weight-bold text-dark"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnGantiRekening" style="border-radius:8px;">
                            <i class="fas fa-exchange-alt mr-1"></i>Ganti
                        </button>
                    </div>
                    <input type="hidden" id="nomor_rekening" name="nomor_rekening" required>
                    <input type="hidden" id="selected_id_akun" name="id_akun">
                    <input type="hidden" id="selected_id_kode_rekening" name="id_kode_rekening">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_kuitansi" class="font-weight-bold small">Tanggal Kuitansi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_kuitansi" name="tanggal_kuitansi" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="periode_lengkap" class="font-weight-bold small">Periode</label>
                                <input type="text" class="form-control" id="periode_lengkap" name="periode_lengkap" placeholder="Contoh: TU-1 atau UP 1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nomor_urut" class="font-weight-bold small">No. Urut Kuitansi</label>
                                <input type="text" class="form-control" id="nomor_urut" name="nomor_urut" placeholder="001" maxlength="3">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="penerima_lookup" class="font-weight-bold small">Penerima (Rekanan / Staff) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="penerima_lookup" list="penerimaList" placeholder="Ketik nama penerima..." autocomplete="off" required>
                        <input type="hidden" id="rekanan_id" name="rekanan_id">
                        <input type="hidden" id="staff_id" name="staff_id">
                        <input type="hidden" id="penerima_type" name="penerima_type">
                        <small class="text-muted">Pilih dari daftar: rekanan atau staff (untuk perjalanan dinas).</small>
                    </div>

                    <hr class="my-3">

                    {{-- ── KETERANGAN PEMBAYARAN ───────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-success text-white"><i class="fas fa-align-left"></i></span>
                        <span class="font-weight-bold ml-2 text-success" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Keterangan Pembayaran</span>
                    </div>

                    <div class="form-group position-relative">
                        <label for="untuk_pembayaran" class="font-weight-bold small">Untuk Pembayaran <span class="text-danger">*</span></label>
                        <textarea class="form-control textarea-autocomplete" id="untuk_pembayaran" name="untuk_pembayaran" rows="3" placeholder="Jelaskan tujuan pembayaran, misal: Pembayaran pengadaan ATK bulan Maret..." required></textarea>
                        <div class="autocomplete-suggestions" id="untukPembayaranSuggestions" style="display:none; position:absolute; top:100%; left:0; right:0; background:white; border:1px solid #ddd; border-top:none; max-height:200px; overflow-y:auto; z-index:1000;"></div>
                    </div>

                    <hr class="my-3">

                    {{-- ── RINCIAN ITEM ────────────────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-warning text-white"><i class="fas fa-list-ul"></i></span>
                        <span class="font-weight-bold ml-2 text-warning" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Rincian Item Barang/Jasa</span>
                    </div>

                    <div class="table-responsive mb-2">
                        <table class="table table-sm table-bordered mb-1" id="itemsTable">
                            <thead style="background:#fff9ed;">
                                <tr>
                                    <th>Nama Item</th>
                                    <th style="width:90px;">Jumlah</th>
                                    <th style="width:120px;">Satuan</th>
                                    <th style="width:140px;">Harga Satuan (Rp)</th>
                                    <th class="jasa-col text-center" style="width:64px;" title="Centang jika item ini adalah jasa (berlaku PPH 23)">Jasa?</th>
                                    <th style="width:46px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="addItemRow()" style="border-radius:8px;">
                        <i class="fas fa-plus mr-1"></i>Tambah Item
                    </button>
                    <input type="hidden" id="rincian_item_json" name="rincian_item_json" value="[]">

                    <hr class="my-3">

                    {{-- ── PAJAK ───────────────────────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-danger text-white"><i class="fas fa-receipt"></i></span>
                        <span class="font-weight-bold ml-2 text-danger" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Pemotongan Pajak (PPH)</span>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="card h-100" style="border-left:4px solid #f6c23e;">
                                <div class="card-body py-2 px-3">
                                    <p class="font-weight-bold text-warning mb-1 small"><i class="fas fa-boxes mr-1"></i>PPH 22 — Belanja Barang</p>
                                    <div class="form-group mb-1">
                                        <input type="text" class="form-control form-control-sm" id="kode_objek_pajak_22" name="kode_objek_pajak" list="kodeObjekPajakList" placeholder="Cari kode objek pajak PPH 22..." autocomplete="off">
                                        <small class="text-muted">Berlaku jika total barang &gt; Rp 2.000.000</small>
                                    </div>
                                    <input type="hidden" id="tarif_pajak" name="tarif_pajak" value="0">
                                    <div class="form-group mb-0">
                                        <label class="small mb-0 font-weight-bold">PPH 22 Dipotong:</label>
                                        <input type="text" class="form-control form-control-sm" id="pph_22_nominal" value="Rp 0" readonly>
                                        <small id="pph_22_info" class="text-muted" style="display:none;"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100" style="border-left:4px solid #36b9cc;">
                                <div class="card-body py-2 px-3">
                                    <p class="font-weight-bold text-info mb-1 small"><i class="fas fa-tools mr-1"></i>PPH 23 — Belanja Jasa</p>
                                    <div class="form-group mb-1">
                                        <input type="text" class="form-control form-control-sm" id="kode_objek_pajak_23" name="kode_objek_pajak_23" list="kodeObjekPajakList" placeholder="Cari kode objek pajak PPH 23..." autocomplete="off">
                                        <small class="text-muted">Berlaku pada item bertanda Jasa &#10003;</small>
                                    </div>
                                    <input type="hidden" id="tarif_pajak_23" name="tarif_pajak_23" value="0">
                                    <div class="form-group mb-0">
                                        <label class="small mb-0 font-weight-bold">PPH 23 Dipotong:</label>
                                        <input type="text" class="form-control form-control-sm" id="pph_23_nominal" value="Rp 0" readonly>
                                        <small id="pph_23_info" class="text-muted" style="display:none;"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">DPP (Dasar Pengenaan Pajak)</label>
                                <input type="text" class="form-control form-control-sm" id="dpp_display" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">Total PPH Dipotong</label>
                                <input type="text" class="form-control form-control-sm" id="pph_nominal" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">PPN 11%</label>
                                <input type="text" class="form-control form-control-sm" id="ppn_nominal" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="ppn_checkbox" name="ppn_checkbox">
                        <label class="custom-control-label font-weight-bold" for="ppn_checkbox">Tambahkan PPN 11%</label>
                    </div>

                    <div class="form-group mb-1">
                        <div class="p-3 rounded d-flex align-items-center justify-content-between" style="background:#e8f4f8;border:2px solid #4e73df;">
                            <div>
                                <span class="font-weight-bold text-primary" style="font-size:14px;"><i class="fas fa-calculator mr-1"></i>Grand Total</span><br>
                                <small class="text-muted">Total Belanja (DPP)</small>
                            </div>
                            <input type="text" id="total_akhir_display" value="Rp 0" readonly
                                style="border:none;background:transparent;font-size:22px;font-weight:800;color:#4e73df;text-align:right;width:55%;padding:0;box-shadow:none;">
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ── PENANDATANGAN / STAFF ───────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-info text-white"><i class="fas fa-user-tie"></i></span>
                        <span class="font-weight-bold ml-2 text-info" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Penandatangan</span>
                    </div>

                    <div class="form-group">
                        <label for="pptk_1_id" class="font-weight-bold small">PPTK <span class="text-danger">*</span></label>
                        <select class="form-control" id="pptk_1_id" name="pptk_1_id" required>
                            <option value="">-- Pilih PPTK --</option>
                            @foreach($pptks as $pptk)
                                <option value="{{ $pptk->id }}">{{ $pptk->nama }} - {{ $pptk->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="bendahara_checkbox" name="bendahara_checkbox">
                        <label class="custom-control-label font-weight-bold" for="bendahara_checkbox">Sertakan Bendahara Barang</label>
                    </div>
                    <div id="bendahara_info" class="alert alert-info py-2" style="display:none;">
                        <i class="fas fa-user mr-1"></i><strong>Bendahara Barang:</strong> <span id="display_bendahara_nama"></span>
                    </div>
                    <input type="hidden" id="nama_bendahara_barang" name="nama_bendahara_barang">
                    <input type="hidden" id="nip_bendahara_barang" name="nip_bendahara_barang">

                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal" style="border-radius:8px;">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button class="btn btn-primary" type="button" id="btnSimpanKuitansi" onclick="updateAddFormBefore(event)" style="border-radius:8px;" disabled>
                        <i class="fas fa-save mr-1"></i>Simpan Kuitansi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Kuitansi Modal -->
<div class="modal fade" id="editkuitansiModal" tabindex="-1" role="dialog" aria-labelledby="editkuitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editkuitansiModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Kuitansi
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="max-height: 72vh; overflow-y: auto; padding: 1.25rem;">

                    {{-- ── IDENTITAS KUITANSI ──────────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-primary text-white"><i class="fas fa-id-card"></i></span>
                        <span class="font-weight-bold ml-2 text-primary" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Identitas Kuitansi</span>
                    </div>

                    <div class="alert alert-primary py-2 px-3 mb-3 d-flex align-items-center" role="alert" style="border-radius:10px;">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        <strong>Kode Rekening:</strong>
                        <span id="edit_display_kode_rekening" class="ml-2 font-weight-bold text-dark"></span>
                    </div>
                    <input type="hidden" id="edit_nomor_rekening" name="nomor_rekening">
                    <input type="hidden" id="edit_id_akun" name="id_akun">
                    <input type="hidden" id="edit_id_kode_rekening" name="id_kode_rekening">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_tanggal_kuitansi" class="font-weight-bold small">Tanggal Kuitansi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_tanggal_kuitansi" name="tanggal_kuitansi" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_periode_lengkap" class="font-weight-bold small">Periode</label>
                                <input type="text" class="form-control" id="edit_periode_lengkap" name="periode_lengkap" placeholder="Contoh: TU-1 atau UP 1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_nomor_urut" class="font-weight-bold small">No. Urut Kuitansi</label>
                                <input type="text" class="form-control" id="edit_nomor_urut" name="nomor_urut" placeholder="001" maxlength="3">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_penerima_lookup" class="font-weight-bold small">Penerima (Rekanan / Staff) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_penerima_lookup" list="editPenerimaList" placeholder="Ketik nama penerima..." autocomplete="off" required>
                        <input type="hidden" id="edit_rekanan_id" name="rekanan_id">
                        <input type="hidden" id="edit_staff_id" name="staff_id">
                        <input type="hidden" id="edit_penerima_type" name="penerima_type">
                        <small class="text-muted">Pilih dari daftar: rekanan atau staff (untuk perjalanan dinas).</small>
                    </div>

                    <hr class="my-3">

                    {{-- ── KETERANGAN PEMBAYARAN ───────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-success text-white"><i class="fas fa-align-left"></i></span>
                        <span class="font-weight-bold ml-2 text-success" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Keterangan Pembayaran</span>
                    </div>

                    <div class="form-group position-relative">
                        <label for="edit_untuk_pembayaran" class="font-weight-bold small">Untuk Pembayaran <span class="text-danger">*</span></label>
                        <textarea class="form-control textarea-autocomplete" id="edit_untuk_pembayaran" name="untuk_pembayaran" rows="3" placeholder="Jelaskan tujuan pembayaran..." required></textarea>
                        <div class="autocomplete-suggestions" id="editUntukPembayaranSuggestions" style="display:none; position:absolute; top:100%; left:0; right:0; background:white; border:1px solid #ddd; border-top:none; max-height:200px; overflow-y:auto; z-index:1000;"></div>
                    </div>

                    <hr class="my-3">

                    {{-- ── RINCIAN ITEM ────────────────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-warning text-white"><i class="fas fa-list-ul"></i></span>
                        <span class="font-weight-bold ml-2 text-warning" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Rincian Item Barang/Jasa</span>
                    </div>

                    <div class="table-responsive mb-2">
                        <table class="table table-sm table-bordered mb-1" id="editItemsTable">
                            <thead style="background:#fff9ed;">
                                <tr>
                                    <th>Nama Item</th>
                                    <th style="width:90px;">Jumlah</th>
                                    <th style="width:120px;">Satuan</th>
                                    <th style="width:140px;">Harga Satuan (Rp)</th>
                                    <th class="jasa-col text-center" style="width:64px;" title="Centang jika item ini adalah jasa (berlaku PPH 23)">Jasa?</th>
                                    <th style="width:46px;"></th>
                                </tr>
                            </thead>
                            <tbody id="editItemsBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="addEditItemRow()" style="border-radius:8px;">
                        <i class="fas fa-plus mr-1"></i>Tambah Item
                    </button>
                    <input type="hidden" id="edit_rincian_item_json" name="rincian_item_json" value="[]">

                    <hr class="my-3">

                    {{-- ── PAJAK ───────────────────────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-danger text-white"><i class="fas fa-receipt"></i></span>
                        <span class="font-weight-bold ml-2 text-danger" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Pemotongan Pajak (PPH)</span>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="card h-100" style="border-left:4px solid #f6c23e;">
                                <div class="card-body py-2 px-3">
                                    <p class="font-weight-bold text-warning mb-1 small"><i class="fas fa-boxes mr-1"></i>PPH 22 — Belanja Barang</p>
                                    <div class="form-group mb-1">
                                        <input type="text" class="form-control form-control-sm" id="edit_kode_objek_pajak_22" name="kode_objek_pajak" list="kodeObjekPajakList" placeholder="Kode objek pajak PPH 22..." autocomplete="off">
                                        <small class="text-muted">Berlaku jika total barang &gt; Rp 2.000.000</small>
                                    </div>
                                    <input type="hidden" id="edit_tarif_pajak" name="tarif_pajak" value="0">
                                    <div class="form-group mb-0">
                                        <label class="small mb-0 font-weight-bold">PPH 22 Dipotong:</label>
                                        <input type="text" class="form-control form-control-sm" id="edit_pph_22_nominal" value="Rp 0" readonly>
                                        <small id="edit_pph_22_info" class="text-muted" style="display:none;"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100" style="border-left:4px solid #36b9cc;">
                                <div class="card-body py-2 px-3">
                                    <p class="font-weight-bold text-info mb-1 small"><i class="fas fa-tools mr-1"></i>PPH 23 — Belanja Jasa</p>
                                    <div class="form-group mb-1">
                                        <input type="text" class="form-control form-control-sm" id="edit_kode_objek_pajak_23" name="kode_objek_pajak_23" list="kodeObjekPajakList" placeholder="Kode objek pajak PPH 23..." autocomplete="off">
                                        <small class="text-muted">Berlaku pada item bertanda Jasa &#10003;</small>
                                    </div>
                                    <input type="hidden" id="edit_tarif_pajak_23" name="tarif_pajak_23" value="0">
                                    <div class="form-group mb-0">
                                        <label class="small mb-0 font-weight-bold">PPH 23 Dipotong:</label>
                                        <input type="text" class="form-control form-control-sm" id="edit_pph_23_nominal" value="Rp 0" readonly>
                                        <small id="edit_pph_23_info" class="text-muted" style="display:none;"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">DPP (Dasar Pengenaan Pajak)</label>
                                <input type="text" class="form-control form-control-sm" id="edit_dpp_display" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">Total PPH Dipotong</label>
                                <input type="text" class="form-control form-control-sm" id="edit_pph_nominal" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold small">PPN 11%</label>
                                <input type="text" class="form-control form-control-sm" id="edit_ppn_nominal" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="edit_ppn_checkbox" name="ppn_checkbox">
                        <label class="custom-control-label font-weight-bold" for="edit_ppn_checkbox">Tambahkan PPN 11%</label>
                    </div>

                    <div class="form-group mb-1">
                        <div class="p-3 rounded d-flex align-items-center justify-content-between" style="background:#e8f4f8;border:2px solid #4e73df;">
                            <div>
                                <span class="font-weight-bold text-primary" style="font-size:14px;"><i class="fas fa-calculator mr-1"></i>Grand Total</span><br>
                                <small class="text-muted">Total Belanja (DPP)</small>
                            </div>
                            <input type="text" id="edit_total_akhir_display" value="Rp 0" readonly
                                style="border:none;background:transparent;font-size:22px;font-weight:800;color:#4e73df;text-align:right;width:55%;padding:0;box-shadow:none;">
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ── PENANDATANGAN / STAFF ───────────────────────────── --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-info text-white"><i class="fas fa-user-tie"></i></span>
                        <span class="font-weight-bold ml-2 text-info" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Penandatangan</span>
                    </div>

                    <div class="form-group">
                        <label for="edit_pptk_1_id" class="font-weight-bold small">PPTK <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_pptk_1_id" name="pptk_1_id" required>
                            <option value="">-- Pilih PPTK --</option>
                            @foreach($pptks as $pptk)
                                <option value="{{ $pptk->id }}">{{ $pptk->nama }} - {{ $pptk->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="edit_bendahara_checkbox" name="edit_bendahara_checkbox">
                        <label class="custom-control-label font-weight-bold" for="edit_bendahara_checkbox">Sertakan Bendahara Barang</label>
                    </div>
                    <div id="edit_bendahara_info" class="alert alert-info py-2" style="display:none;">
                        <i class="fas fa-user mr-1"></i><strong>Bendahara Barang:</strong> <span id="edit_display_bendahara_nama"></span>
                    </div>
                    <input type="hidden" id="edit_nama_bendahara_barang" name="nama_bendahara_barang">
                    <input type="hidden" id="edit_nip_bendahara_barang" name="nip_bendahara_barang">

                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal" style="border-radius:8px;">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button class="btn btn-info" type="button" id="btnSimpanPerubahan" onclick="updateEditFormBefore(event)" style="border-radius:8px;" disabled>
                        <i class="fas fa-save mr-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Datalist for Kode Objek Pajak -->
<datalist id="kodeObjekPajakList">
    @foreach($kodeObjekPajaks as $kop)
        <option value="{{ $kop->kode }}">{{ $kop->kode }} - {{ $kop->nama }} ({{ $kop->tarif }}%)</option>
    @endforeach
</datalist>

<datalist id="penerimaList">
    @foreach($rekanans as $rekanan)
        <option value="{{ $rekanan->nama_perusahaan }} (Rekanan)" data-type="rekanan" data-id="{{ $rekanan->id }}" data-name="{{ $rekanan->nama_perusahaan }}" label="{{ $rekanan->npwp ? 'NPWP: '.$rekanan->npwp : 'Rekanan' }}"></option>
    @endforeach
    @foreach($staffs as $staff)
        <option value="{{ $staff->nama }} (Staff)" data-type="staff" data-id="{{ $staff->id }}" data-name="{{ $staff->nama }}" label="{{ $staff->nip ? 'NIP: '.$staff->nip : 'Staff' }}"></option>
    @endforeach
</datalist>

<datalist id="editPenerimaList">
    @foreach($rekanans as $rekanan)
        <option value="{{ $rekanan->nama_perusahaan }} (Rekanan)" data-type="rekanan" data-id="{{ $rekanan->id }}" data-name="{{ $rekanan->nama_perusahaan }}" label="{{ $rekanan->npwp ? 'NPWP: '.$rekanan->npwp : 'Rekanan' }}"></option>
    @endforeach
    @foreach($staffs as $staff)
        <option value="{{ $staff->nama }} (Staff)" data-type="staff" data-id="{{ $staff->id }}" data-name="{{ $staff->nama }}" label="{{ $staff->nip ? 'NIP: '.$staff->nip : 'Staff' }}"></option>
    @endforeach
</datalist>

@endsection

@push('scripts')
    <script src="{{ asset('js/kuitansi-form.js') }}"></script>
    
    <script>
    let itemCounter = 0;
    let editItemCounter = 0;
    let selectedRekeningData = { id_akun: null, kode_akun: null, nama_akun: null };
    const bendaharaBarangNama = @json($bendaharaBarang->nama ?? null);
    const bendaharaBarangNip  = @json($bendaharaBarang->nip  ?? null);

    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#dataTable').DataTable({
            destroy: true,
            responsive: false,
            autoWidth: false,
            dom: 'lrtip',
            order: [[2, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 8] },
                { searchable: false, targets: [0, 8] },
            ],
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                emptyTable: "Tidak ada data Kuitansi.",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        });

        // Function to renumber after sort/filter
        function renumberRows() {
            let counter = 1;
            $('#dataTable tbody tr:visible').each(function() {
                $(this).find('td:eq(1)').text(counter);
                counter++;
            });
        }

        // Renumber on initial load
        renumberRows();

        // Renumber whenever table is redrawn (sort, filter, pagination)
        table.on('draw', function() {
            renumberRows();
            updateExportButton();  // Update button visibility after redraw
        });

        function normalizeText(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function extractText(cellHtml) {
            return normalizeText($('<div>').html(cellHtml || '').text());
        }

        function parseDdMmYyyy(dateText) {
            if (!dateText) return null;
            const m = dateText.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!m) return null;
            const d = new Date(Number(m[3]), Number(m[2]) - 1, Number(m[1]));
            d.setHours(0, 0, 0, 0);
            return d;
        }

        function parseYyyyMmDd(dateText) {
            if (!dateText) return null;
            const d = new Date(dateText);
            if (isNaN(d.getTime())) return null;
            d.setHours(0, 0, 0, 0);
            return d;
        }

        // Track all row data for filtering
        const allRows = {};
        $('#dataTable tbody tr').each(function(idx) {
            const tanggal = $(this).attr('data-tanggal');
            allRows[idx] = { tanggal: tanggal };
        });

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'dataTable') return true;

            const noBukuFilter = normalizeText($('#filter_no_buku').val());
            const rekeningFilter = normalizeText($('#filter_rekening').val());
            const penerimaFilter = normalizeText($('#filter_penerima').val());
            const pembayaranFilter = normalizeText($('#filter_pembayaran').val());

            const start = $('#filter_tanggal_mulai').val();
            const end = $('#filter_tanggal_selesai').val();

            // Kolom: 0 checkbox, 1 no, 2 no_buku, 3 rekening, 4 pembayaran, 5 total, 6 penerima, 7 pajak, 8 aksi
            const rowNoBuku = extractText(data[2]);
            const rowRekening = extractText(data[3]);
            const rowPembayaran = extractText(data[4]);
            const rowPenerima = extractText(data[6]);

            if (noBukuFilter && !rowNoBuku.includes(noBukuFilter)) return false;
            if (rekeningFilter && !rowRekening.includes(rekeningFilter)) return false;
            if (penerimaFilter && !rowPenerima.includes(penerimaFilter)) return false;
            if (pembayaranFilter && !rowPembayaran.includes(pembayaranFilter)) return false;

            if (!start && !end) return true;
            
            // Get date from tracked row data
            const rowDateText = allRows[dataIndex] ? allRows[dataIndex].tanggal : '';
            const rowDate = parseYyyyMmDd(rowDateText);
            if (!rowDate) return false;

            const startDate = parseYyyyMmDd(start);
            const endDate = parseYyyyMmDd(end);

            if (startDate && rowDate < startDate) return false;
            if (endDate && rowDate > endDate) return false;
            return true;
        });

        function applyFilters() {
            table.draw();
        }

        $('#applyFilterBtn').on('click', function () {
            applyFilters();
        });

        $('#filter_no_buku, #filter_rekening, #filter_pembayaran, #filter_penerima').on('keyup', function (e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        $('#filter_tanggal_mulai, #filter_tanggal_selesai').on('change', function () {
            table.draw();
        });

        $('#resetFilterBtn').on('click', function () {
            $('#filter_no_buku, #filter_rekening, #filter_penerima, #filter_pembayaran, #filter_tanggal_mulai, #filter_tanggal_selesai').val('');
            $('#selectAllCheckbox').prop('checked', false);  // Reset select all
            $('.kuitansi-checkbox').prop('checked', false);   // Uncheck all rows
            table.draw();
            updateExportButton();  // Update button visibility
        });

        // Select all checkboxes
        $(document).on('change', '#selectAllCheckbox', function(e) {
            const isChecked = this.checked;
            document.querySelectorAll('.kuitansi-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            updateExportButton();
        });

        // Individual checkbox change (using event delegation)
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('kuitansi-checkbox')) {
                updateExportButton();
            }
        }, true);  // Use capture phase

        // Initial button state
        updateExportButton();

        // Export XML button
        $('#exportXmlBtn').on('click', function() {
            const selectedIds = [];
            $('.kuitansi-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal 1 kuitansi', 'warning');
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membuat file XML...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('kuitansi_ids', JSON.stringify(selectedIds));

            fetch('{{ route("kuitansi.exportBupotXmlSelected") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error ?? 'Terjadi kesalahan saat membuat XML.');
                    }).catch(jsonErr => {
                        if (jsonErr.message !== 'Terjadi kesalahan saat membuat XML.' && jsonErr.constructor.name !== 'SyntaxError') {
                            throw jsonErr;
                        }
                        throw new Error('Terjadi kesalahan: HTTP ' + response.status);
                    });
                }
                return response.blob();
            })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'BuPot_PPh_' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '.xml';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                Swal.fire('Berhasil!', 'File XML berhasil didownload.', 'success');
            })
            .catch(err => {
                Swal.fire('Gagal', err.message, 'error');
            });
        });
    });

    function updateExportButton() {
        // Find all visible checkboxes that are checked
        const allCheckboxes = document.querySelectorAll('.kuitansi-checkbox');
        const checkedCount = Array.from(allCheckboxes).filter(cb => cb.checked).length;
        
        // Update count display
        document.getElementById('selectedCount').textContent = checkedCount;
        
        // Show/hide button
        const btn = document.getElementById('exportXmlBtn');
        if (checkedCount > 0) {
            btn.style.display = '';  // Show
        } else {
            btn.style.display = 'none';  // Hide
        }
    }

    function truncateText(text, maxLength = 50) {
        return text && text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }

    function syncPenerimaFromInput(inputSelector, listSelector, hiddenRekananSelector, hiddenStaffSelector, hiddenTypeSelector) {
        const inputValue = $(inputSelector).val();
        const option = $(`${listSelector} option`).filter(function () {
            return $(this).val() === inputValue;
        }).first();

        if (!option.length) {
            $(hiddenRekananSelector).val('');
            $(hiddenStaffSelector).val('');
            $(hiddenTypeSelector).val('');
            return;
        }

        const type = option.data('type');
        const id = option.data('id');

        if (type === 'rekanan') {
            $(hiddenRekananSelector).val(id);
            $(hiddenStaffSelector).val('');
            $(hiddenTypeSelector).val('rekanan');
            return;
        }

        if (type === 'staff') {
            $(hiddenRekananSelector).val('');
            $(hiddenStaffSelector).val(id);
            $(hiddenTypeSelector).val('staff');
            return;
        }

        $(hiddenRekananSelector).val('');
        $(hiddenStaffSelector).val('');
        $(hiddenTypeSelector).val('');
    }

    function setPenerimaInputFromData(inputSelector, listSelector, hiddenRekananSelector, hiddenStaffSelector, hiddenTypeSelector, rekananId, namaPenerima) {
        let option = null;
        if (rekananId) {
            option = $(`${listSelector} option[data-type="rekanan"][data-id="${rekananId}"]`).first();
        } else if (namaPenerima) {
            const normalizedNama = String(namaPenerima).trim().toLowerCase();
            option = $(`${listSelector} option[data-type="staff"]`).filter(function () {
                const optionName = String($(this).data('name') || '').trim().toLowerCase();
                return optionName === normalizedNama;
            }).first();
        }

        if (option && option.length) {
            $(inputSelector).val(option.val());
            syncPenerimaFromInput(inputSelector, listSelector, hiddenRekananSelector, hiddenStaffSelector, hiddenTypeSelector);
            return;
        }

        $(inputSelector).val('');
        $(hiddenRekananSelector).val('');
        $(hiddenStaffSelector).val('');
        $(hiddenTypeSelector).val('');
    }

    $('#penerima_lookup').on('input change', function() {
        syncPenerimaFromInput('#penerima_lookup', '#penerimaList', '#rekanan_id', '#staff_id', '#penerima_type');
    });

    $('#edit_penerima_lookup').on('input change', function() {
        syncPenerimaFromInput('#edit_penerima_lookup', '#editPenerimaList', '#edit_rekanan_id', '#edit_staff_id', '#edit_penerima_type');
    });

    // Bendahara Barang checkbox — Add form
    $('#bendahara_checkbox').on('change', function() {
        if (this.checked && bendaharaBarangNama) {
            $('#nama_bendahara_barang').val(bendaharaBarangNama);
            $('#nip_bendahara_barang').val(bendaharaBarangNip || '');
            $('#display_bendahara_nama').text(bendaharaBarangNama);
            $('#bendahara_info').show();
        } else {
            $('#nama_bendahara_barang').val('');
            $('#nip_bendahara_barang').val('');
            $('#bendahara_info').hide();
        }
    });

    // Bendahara Barang checkbox — Edit form
    $('#edit_bendahara_checkbox').on('change', function() {
        if (this.checked && bendaharaBarangNama) {
            $('#edit_nama_bendahara_barang').val(bendaharaBarangNama);
            $('#edit_nip_bendahara_barang').val(bendaharaBarangNip || '');
            $('#edit_display_bendahara_nama').text(bendaharaBarangNama);
            $('#edit_bendahara_info').show();
        } else {
            $('#edit_nama_bendahara_barang').val('');
            $('#edit_nip_bendahara_barang').val('');
            $('#edit_bendahara_info').hide();
        }
    });

    $('#selectRekeningModal').on('shown.bs.modal', loadKegiatan);

    let selectedKodeGiat = '';
    let selectedKodeSubGiat = '';

    function loadKegiatan() {
        $.ajax({
            url: '{{ route("api.kegiatan") }}',
            success: function(data) {
                let options = '<option value="">-- Pilih Kegiatan --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}" data-kode="${item.kode_giat}">${item.kode} - ${item.nama}</option>`;
                });
                $('#select_kegiatan').html(options);
            }
        });
    }

    $('#select_kegiatan').on('change', function() {
        const idGiat = $(this).val();
        const selectedOption = $(this).find('option:selected');
        selectedKodeGiat = selectedOption.data('kode') || '';
        $('#select_sub_kegiatan').prop('disabled', !idGiat).html('<option value="">-- Pilih Sub Kegiatan --</option>');
        $('#select_kode_rekening').prop('disabled', true).html('<option value="">-- Pilih Kode Rekening --</option>');
        if (idGiat) loadSubKegiatan(idGiat);
    });

    function loadSubKegiatan(idGiat) {
        $.ajax({
            url: '{{ route("api.subKegiatan") }}',
            data: { id_giat: idGiat },
            success: function(data) {
                let options = '<option value="">-- Pilih Sub Kegiatan --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}" data-kode="${item.kode_sub_giat}">${item.kode} - ${item.nama}</option>`;
                });
                $('#select_sub_kegiatan').html(options);
            }
        });
    }

    $('#select_sub_kegiatan').on('change', function() {
        const idSubGiat = $(this).val();
        const selectedOption = $(this).find('option:selected');
        selectedKodeSubGiat = selectedOption.data('kode') || '';
        if (idSubGiat) loadKodeRekening(idSubGiat);
    });

    function loadKodeRekening(idSubGiat) {
        $.ajax({
            url: '{{ route("api.kodeRekening") }}',
            data: { id_sub_giat: idSubGiat },
            success: function(data) {
                let options = '<option value="">-- Pilih Kode Rekening --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}" data-kode="${item.kode}" data-id-akun="${item.id_akun}" data-nama="${item.nama}">${item.kode} - ${item.nama}</option>`;
                });
                $('#select_kode_rekening').prop('disabled', false).html(options);
            }
        });
    }

    // Helper function to extract last part after last dot
    function extractLastPart(code) {
        if (!code) return '';
        const parts = String(code).split('.');
        return parts[parts.length - 1];
    }

    // Helper function to format nomor_rekening as XX.YYYY.kode_akun
    function formatFormattedNomorRekening(kodeGiat, kodeSubGiat, kodeAkun) {
        const xx = extractLastPart(kodeGiat);
        const yyyy = extractLastPart(kodeSubGiat);
        return `${xx}.${yyyy}.${kodeAkun}`;
    }

    $('#select_kode_rekening').on('change', function() {
        const selected = $(this).find('option:selected');
        const idKodeRekening = $(this).val();
        if (idKodeRekening) {
            const kodeAkun = selected.data('kode');
            const idAkun = selected.data('id-akun');
            const formattedNomorRekening = formatFormattedNomorRekening(selectedKodeGiat, selectedKodeSubGiat, kodeAkun);
            selectedRekeningData = {
                id_kode_rekening: idKodeRekening,
                id_akun: idAkun,
                kode_akun: kodeAkun,
                nama_akun: selected.data('nama'),
                formatted_nomor_rekening: formattedNomorRekening
            };
            $('#info_kode_akun').text(formattedNomorRekening);
            $('#info_nama_akun').text(selectedRekeningData.nama_akun);
            $('#selected_rekening_info').fadeIn();
            $('#btnLanjutKeForm').prop('disabled', false);
        }
    });

    $('#btnLanjutKeForm').on('click', function() {
        if (selectedRekeningData.kode_akun) {
            $('#nomor_rekening').val(selectedRekeningData.kode_akun);
            $('#selected_id_kode_rekening').val(selectedRekeningData.id_kode_rekening);
            $('#selected_id_akun').val(selectedRekeningData.id_akun);
            $('#display_kode_rekening').text(truncateText(selectedRekeningData.formatted_nomor_rekening + ' - ' + selectedRekeningData.nama_akun));
            $('#selectRekeningModal').modal('hide');
            setTimeout(() => $('#addkuitansiModal').modal('show'), 300);
        }
    });

    $('#btnGantiRekening').on('click', function() {
        $('#addkuitansiModal').modal('hide');
        setTimeout(() => $('#selectRekeningModal').modal('show'), 300);
    });

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const rowId = 'item_' + itemCounter++;
        const row = document.createElement('tr');
        row.id = rowId;
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm item-name" placeholder="Nama item"></td>
            <td><input type="number" class="form-control form-control-sm item-qty" placeholder="Jumlah (opsional)" min="1" step="1"></td>
            <td><input type="text" class="form-control form-control-sm item-unit" placeholder="Contoh: pcs"></td>
            <td><input type="number" class="form-control form-control-sm item-price" placeholder="Harga satuan" min="0" step="0.01"></td>
            <td class="jasa-col text-center"><input type="checkbox" class="item-jasa"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
    }

    function addEditItemRow() {
        addEditItemRowWithData('', '', '', '', false);
    }

    function addEditItemRowWithData(nama, jumlah, satuan, harga, isJasa) {
        const tbody = document.getElementById('editItemsBody');
        const rowId = 'edit_item_' + editItemCounter++;
        const row = document.createElement('tr');
        row.id = rowId;
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm item-name" placeholder="Nama item" value="${nama}"></td>
            <td><input type="number" class="form-control form-control-sm item-qty" placeholder="Jumlah (opsional)" min="1" step="1" value="${jumlah}"></td>
            <td><input type="text" class="form-control form-control-sm item-unit" placeholder="Contoh: pcs" value="${satuan}"></td>
            <td><input type="number" class="form-control form-control-sm item-price" placeholder="Harga satuan" min="0" step="0.01" value="${harga}"></td>
            <td class="jasa-col text-center"><input type="checkbox" class="item-jasa" ${isJasa ? 'checked' : ''}></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
    }

    function removeItemRow(rowId) {
        document.getElementById(rowId).remove();
    }

    function updateItemsJson() {
        const items = [];
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const name = row.querySelector('.item-name').value;
            const qtyRaw = row.querySelector('.item-qty').value;
            const unit = row.querySelector('.item-unit').value;
            const price = row.querySelector('.item-price').value;
            if (name && price) {
                const isJasa = row.querySelector('.item-jasa')?.checked || false;
                const qty = qtyRaw ? parseInt(qtyRaw, 10) : 1;
                items.push({ nama: name, jumlah: qty, satuan: unit, harga_satuan: parseFloat(price), is_jasa: isJasa });
            }
        });
        document.getElementById('rincian_item_json').value = JSON.stringify(items);
    }

    function updateAddFormBefore(event) {
        event.preventDefault();
        syncPenerimaFromInput('#penerima_lookup', '#penerimaList', '#rekanan_id', '#staff_id', '#penerima_type');
        if (!document.getElementById('penerima_type').value) {
            Swal.fire('Peringatan', 'Pilih penerima dari daftar yang tersedia.', 'warning');
            return;
        }
        if (!document.getElementById('bendahara_checkbox').checked) {
            document.getElementById('nama_bendahara_barang').value = '';
            document.getElementById('nip_bendahara_barang').value = '';
        }
        updateItemsJson();
        document.querySelector('#addkuitansiModal form').submit();
    }

    function updateEditFormBefore(event) {
        event.preventDefault();
        syncPenerimaFromInput('#edit_penerima_lookup', '#editPenerimaList', '#edit_rekanan_id', '#edit_staff_id', '#edit_penerima_type');
        if (!document.getElementById('edit_penerima_type').value) {
            Swal.fire('Peringatan', 'Pilih penerima dari daftar yang tersedia.', 'warning');
            return;
        }
        if (!document.getElementById('edit_bendahara_checkbox').checked) {
            document.getElementById('edit_nama_bendahara_barang').value = '';
            document.getElementById('edit_nip_bendahara_barang').value = '';
        }
        updateEditItemsJson();
        document.getElementById('editForm').submit();
    }

    function updateEditItemsJson() {
        const items = [];
        document.querySelectorAll('#editItemsBody tr').forEach(row => {
            const name = row.querySelector('.item-name').value;
            const qtyRaw = row.querySelector('.item-qty').value;
            const unit = row.querySelector('.item-unit').value;
            const price = row.querySelector('.item-price').value;
            if (name && price) {
                const isJasa = row.querySelector('.item-jasa')?.checked || false;
                const qty = qtyRaw ? parseInt(qtyRaw, 10) : 1;
                items.push({ nama: name, jumlah: qty, satuan: unit, harga_satuan: parseFloat(price), is_jasa: isJasa });
            }
        });
        document.getElementById('edit_rincian_item_json').value = JSON.stringify(items);
    }

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get('/kuitansi/' + id + '/edit', function(data) {
            $('#edit_nomor_urut').val(String(data.nomor_urut).padStart(3, '0'));
            $('#edit_periode_lengkap').val(formatPeriodeLengkap(data.periode_type, data.periode_number)).trigger('change');
            setPenerimaInputFromData('#edit_penerima_lookup', '#editPenerimaList', '#edit_rekanan_id', '#edit_staff_id', '#edit_penerima_type', data.rekanan_id, data.nama_penerima);
            $('#edit_tanggal_kuitansi').val(data.tanggal_kuitansi);
            $('#edit_untuk_pembayaran').val(data.untuk_pembayaran);
            $('#edit_pptk_1_id').val(data.pptk_1_id).trigger('change');
            $('#edit_nomor_rekening').val(data.nomor_rekening);
            $('#edit_id_kode_rekening').val(data.id_kode_rekening);
            $('#edit_id_akun').val(data.id_akun);
            $('#edit_display_kode_rekening').text(data.formatted_nomor_rekening || data.nomor_rekening || '-');
            
            // Populate PPH 22 kode
            if (data.kode_objek_pajak) {
                const kode22 = data.kode_objek_pajak;
                const opt22 = $(`#kodeObjekPajakList option[value="${kode22}"]`);
                $('#edit_kode_objek_pajak_22').val(opt22.length > 0 ? opt22.text() : kode22);
                $('#edit_tarif_pajak').val(data.tarif_pajak || '0');
            } else {
                $('#edit_kode_objek_pajak_22').val('');
                $('#edit_tarif_pajak').val('0');
            }
            
            // Populate PPH 23 kode
            if (data.kode_objek_pajak_23) {
                const kode23 = data.kode_objek_pajak_23;
                const opt23 = $(`#kodeObjekPajakList option[value="${kode23}"]`);
                $('#edit_kode_objek_pajak_23').val(opt23.length > 0 ? opt23.text() : kode23);
                $('#edit_tarif_pajak_23').val(data.tarif_pajak_23 || '0');
            } else {
                $('#edit_kode_objek_pajak_23').val('');
                $('#edit_tarif_pajak_23').val('0');
            }
            
            $('#editForm').attr('action', '/kuitansi/' + id);

            // Populate item rows
            $('#editItemsBody').empty();
            editItemCounter = 0;
            if (data.rincian_item && Array.isArray(data.rincian_item)) {
                data.rincian_item.forEach(function(item) {
                    addEditItemRowWithData(item.nama, item.jumlah || '', item.satuan || '', item.harga_satuan, item.is_jasa || false);
                });
            }
            // Restore PPN checkbox
            const hasPPN = parseInt(data.ppn || 0) > 0;
            $('#edit_ppn_checkbox').prop('checked', hasPPN);
            calculateEditPPH();
            calculateEditTotalAkhir();

            // Restore bendahara barang state
            const hasBB = !!data.nama_bendahara_barang;
            $('#edit_bendahara_checkbox').prop('checked', hasBB);
            $('#edit_nama_bendahara_barang').val(data.nama_bendahara_barang || '');
            $('#edit_nip_bendahara_barang').val(data.nip_bendahara_barang || '');
            if (hasBB) {
                $('#edit_display_bendahara_nama').text(data.nama_bendahara_barang);
                $('#edit_bendahara_info').show();
            } else {
                $('#edit_bendahara_info').hide();
            }
        });
    });

    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Kuitansi?',
            text: nama,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }

    function formatPeriodeLengkap(type, number) {
        // If number is null, 0, or undefined, return only type
        return number ? `${type}-${number}` : type;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // FORM VALIDATION - Disabled Submit Button Until All Required Fields Filled
    // ═══════════════════════════════════════════════════════════════════════

    function validateAddForm() {
        // Check all required fields
        const tanggalKuitansi = document.getElementById('tanggal_kuitansi').value.trim();
        const penerima = document.getElementById('penerima_lookup').value.trim();
        const penerimaType = document.getElementById('penerima_type').value;
        const untukPembayaran = document.getElementById('untuk_pembayaran').value.trim();
        const pptk = document.getElementById('pptk_1_id').value;
        const nomorRekening = document.getElementById('nomor_rekening').value;

        // Check if at least 1 item exists
        const itemRows = document.querySelectorAll('#itemsBody tr');
        const hasItems = itemRows.length > 0;

        // All required fields must be filled AND at least 1 item must exist
        const isValid = tanggalKuitansi && penerima && penerimaType && untukPembayaran && pptk && nomorRekening && hasItems;
        
        const btn = document.getElementById('btnSimpanKuitansi');
        if (btn) {
            if (isValid) {
                btn.removeAttribute('disabled');
                btn.style.cursor = 'pointer';
            } else {
                btn.setAttribute('disabled', 'disabled');
                btn.style.cursor = 'not-allowed';
            }
        }
    }

    function validateEditForm() {
        // Check all required fields
        const tanggalKuitansi = document.getElementById('edit_tanggal_kuitansi').value.trim();
        const penerima = document.getElementById('edit_penerima_lookup').value.trim();
        const penerimaType = document.getElementById('edit_penerima_type').value;
        const untukPembayaran = document.getElementById('edit_untuk_pembayaran').value.trim();
        const pptk = document.getElementById('edit_pptk_1_id').value;

        // Check if at least 1 item exists
        const itemRows = document.querySelectorAll('#editItemsBody tr');
        const hasItems = itemRows.length > 0;

        // All required fields must be filled AND at least 1 item must exist
        const isValid = tanggalKuitansi && penerima && penerimaType && untukPembayaran && pptk && hasItems;
        
        const btn = document.getElementById('btnSimpanPerubahan');
        if (btn) {
            if (isValid) {
                btn.removeAttribute('disabled');
                btn.style.cursor = 'pointer';
            } else {
                btn.setAttribute('disabled', 'disabled');
                btn.style.cursor = 'not-allowed';
            }
        }
    }

    // Attach validation listeners when modal opens
    $('#addkuitansiModal').on('show.bs.modal', function() {
        // Add validation to required fields
        document.getElementById('tanggal_kuitansi').addEventListener('change', validateAddForm);
        document.getElementById('penerima_lookup').addEventListener('input', validateAddForm);
        document.getElementById('penerima_lookup').addEventListener('change', validateAddForm);
        document.getElementById('untuk_pembayaran').addEventListener('input', validateAddForm);
        document.getElementById('pptk_1_id').addEventListener('change', validateAddForm);

        // Listen for datalist selection (when user picks from dropdown)
        document.getElementById('penerima_lookup').addEventListener('change', function() {
            setTimeout(validateAddForm, 100); // Delay to ensure syncPenerima completes
        });

        // Validate when items table changes
        const itemsBody = document.getElementById('itemsBody');
        const observer = new MutationObserver(validateAddForm);
        observer.observe(itemsBody, { childList: true, subtree: true });

        // Validate when item data changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-name') || e.target.classList.contains('item-price') || e.target.classList.contains('item-qty') || e.target.classList.contains('item-unit')) {
                validateAddForm();
            }
        });

        // Initial validation
        validateAddForm();
    });

    $('#editkuitansiModal').on('show.bs.modal', function() {
        // Add validation to required fields
        document.getElementById('edit_tanggal_kuitansi').addEventListener('change', validateEditForm);
        document.getElementById('edit_penerima_lookup').addEventListener('input', validateEditForm);
        document.getElementById('edit_penerima_lookup').addEventListener('change', validateEditForm);
        document.getElementById('edit_untuk_pembayaran').addEventListener('input', validateEditForm);
        document.getElementById('edit_pptk_1_id').addEventListener('change', validateEditForm);

        // Listen for datalist selection (when user picks from dropdown)
        document.getElementById('edit_penerima_lookup').addEventListener('change', function() {
            setTimeout(validateEditForm, 100); // Delay to ensure syncPenerima completes
        });

        // Validate when items table changes
        const editItemsBody = document.getElementById('editItemsBody');
        const observer = new MutationObserver(validateEditForm);
        observer.observe(editItemsBody, { childList: true, subtree: true });

        // Validate when item data changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-name') || e.target.classList.contains('item-price') || e.target.classList.contains('item-qty') || e.target.classList.contains('item-unit')) {
                validateEditForm();
            }
        });

        // Initial validation
        validateEditForm();
    });

    // Filter panel chevron toggle
    $('#filterInner').on('show.bs.collapse', function () {
        $('#filterInnerChevron').removeClass('rotated');
    }).on('hide.bs.collapse', function () {
        $('#filterInnerChevron').addClass('rotated');
    });

    // ═══════════════════════════════════════════════════════════════════════
    // TEXTAREA AUTOCOMPLETE
    // ═══════════════════════════════════════════════════════════════════════

    // Get recent "untuk_pembayaran" values from PHP and clean them
    let recentPembayaran = @json(\DB::table('kuitansis')->select('untuk_pembayaran', \DB::raw('MAX(created_at) as last_used'))->whereNotNull('untuk_pembayaran')->groupBy('untuk_pembayaran')->orderBy('last_used', 'desc')->limit(50)->pluck('untuk_pembayaran')->toArray());
    
    // Filter out null/empty values and trim each item (but keep original for display)
    recentPembayaran = recentPembayaran.filter(item => item && String(item).trim().length > 0)
                                       .map(item => String(item).trim());

    function normalizeForSearch(text) {
        // Normalize text for searching: lowercase, trim, normalize spaces
        return String(text || '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g, ' ');  // Replace multiple spaces with single space
    }

    function setupTextareaAutocomplete(textareaSelector, suggestionsSelector) {
        const textarea = document.querySelector(textareaSelector);
        const suggestionsDiv = document.querySelector(suggestionsSelector);

        if (!textarea || !suggestionsDiv) return;

        textarea.addEventListener('input', function() {
            const inputValue = normalizeForSearch(this.value);
            
            if (inputValue.length === 0) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            // Filter suggestions - match if any word in input matches any word in item
            const inputWords = inputValue.split(' ').filter(w => w.length > 0);
            
            const filtered = recentPembayaran.filter(item => {
                if (!item) return false;
                const normalizedItem = normalizeForSearch(item);
                // Match if ANY input word is found in the item
                return inputWords.some(word => normalizedItem.includes(word));
            });

            if (filtered.length === 0) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            // Build suggestions HTML - show up to 15 results (instead of 10)
            let html = '';
            const maxResults = Math.min(15, filtered.length);
            
            for (let i = 0; i < maxResults; i++) {
                const item = filtered[i];
                html += `<div class="autocomplete-item" style="padding:8px 12px; cursor:pointer; border-bottom:1px solid #eee; white-space: normal; word-wrap: break-word; max-height: 60px; overflow: hidden;">${escapeHtml(item)}</div>`;
            }
            
            // Show count if there are more results
            if (filtered.length > maxResults) {
                html += `<div style="padding:8px 12px; background:#f9f9f9; border-top:1px solid #ddd; font-size:12px; color:#666;">+${filtered.length - maxResults} more results</div>`;
            }
            
            suggestionsDiv.innerHTML = html;
            suggestionsDiv.style.display = 'block';
            suggestionsDiv.style.maxHeight = '300px';  // Show more visible items

            // Add click handlers to suggestions
            suggestionsDiv.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    textarea.value = this.textContent;
                    suggestionsDiv.style.display = 'none';
                    textarea.dispatchEvent(new Event('input'));
                    textarea.dispatchEvent(new Event('change'));
                    validateAddForm();  // Trigger validation
                    validateEditForm(); // Trigger validation
                });
                item.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#f5f5f5';
                });
                item.addEventListener('mouseout', function() {
                    this.style.backgroundColor = 'transparent';
                });
            });
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target !== textarea && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.style.display = 'none';
            }
        });

        textarea.addEventListener('focus', function() {
            if (this.value.length > 0 && recentPembayaran.length > 0) {
                // Trigger input event to show filtered suggestions
                this.dispatchEvent(new Event('input'));
            }
        });

        textarea.addEventListener('blur', function() {
            // Delay hiding to allow click on suggestion
            setTimeout(() => {
                if (document.activeElement !== suggestionsDiv) {
                    suggestionsDiv.style.display = 'none';
                }
            }, 200);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize autocomplete for both textareas when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        setupTextareaAutocomplete('#untuk_pembayaran', '#untukPembayaranSuggestions');
        setupTextareaAutocomplete('#edit_untuk_pembayaran', '#editUntukPembayaranSuggestions');
    });
    </script>
@endpush

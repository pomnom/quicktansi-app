@extends('layouts.app')

@section('title', 'Master Rekening')


@section('content')

@php
    $totalKegiatan    = $kegiatans->count();
    $totalSubKegiatan = $subKegiatans->count();
    $totalRekening    = $kodeRekenings->count();
    $totalBlokir      = $kodeRekenings->where('is_blokir', true)->count();
@endphp

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Master Rekening
                @if(!auth()->user()->is_superadmin)
                    &nbsp;·&nbsp; {{ auth()->user()->instansi }}
                @endif
            </div>
            <div class="hero-title">Kegiatan &amp; Kode Rekening</div>
            <p class="hero-sub">Kelola hierarki Kegiatan → Sub Kegiatan → Kode Rekening.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-sitemap"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-tasks hs-icon"></i>
            <div>
                <div class="hs-label">Kegiatan</div>
                <div class="hs-value">{{ $totalKegiatan }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-list-ul hs-icon"></i>
            <div>
                <div class="hs-label">Sub Kegiatan</div>
                <div class="hs-value">{{ $totalSubKegiatan }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-calculator hs-icon"></i>
            <div>
                <div class="hs-label">Kode Rekening</div>
                <div class="hs-value">{{ $totalRekening }}</div>
            </div>
        </div>
        @if($totalBlokir > 0)
        <div class="hs-item">
            <i class="fas fa-ban hs-icon"></i>
            <div>
                <div class="hs-label">Diblokir</div>
                <div class="hs-value">{{ $totalBlokir }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-2">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary h-100">
            <div class="card-body">
                <div class="stat-label">Total Kegiatan</div>
                <div class="stat-value">{{ $totalKegiatan }}</div>
                <i class="fas fa-tasks stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Program kerja terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success h-100">
            <div class="card-body">
                <div class="stat-label">Total Sub Kegiatan</div>
                <div class="stat-value">{{ $totalSubKegiatan }}</div>
                <i class="fas fa-list-ul stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-check-circle"></i>Turunan dari kegiatan</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-warning h-100">
            <div class="card-body">
                <div class="stat-label">Total Kode Rekening</div>
                <div class="stat-value">{{ $totalRekening }}</div>
                <i class="fas fa-calculator stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-ban"></i>{{ $totalBlokir }} diblokir</div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<!-- Section quick-jump nav -->
<div class="mr-nav">
    <a href="#sectionKegiatan" class="kegiatan"><i class="fas fa-tasks"></i> Kegiatan <span class="ml-1" style="opacity:.7;">({{ $totalKegiatan }})</span></a>
    <a href="#sectionSubKegiatan" class="sub"><i class="fas fa-list-ul"></i> Sub Kegiatan <span class="ml-1" style="opacity:.7;">({{ $totalSubKegiatan }})</span></a>
    <a href="#sectionRekening" class="rekening"><i class="fas fa-calculator"></i> Kode Rekening <span class="ml-1" style="opacity:.7;">({{ $totalRekening }})</span></a>
</div>

<!-- ═══════════════ KEGIATAN ═══════════════ -->
<div id="sectionKegiatan">
    <div class="section-action-bar">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addKegiatanModal">
            <i class="fas fa-plus mr-2"></i>Tambah Kegiatan
        </button>
        <span class="text-muted small ml-auto align-self-center">
            <i class="fas fa-info-circle mr-1"></i>{{ $totalKegiatan }} kegiatan terdaftar
        </span>
    </div>
    <div class="card recent-card mb-4">
        <div class="card-header">
            <div class="header-icon" style="background:rgba(78,115,223,.15);color:#4e73df;"><i class="fas fa-tasks"></i></div>
            <h6>Data Kegiatan</h6>
            <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalKegiatan }} data</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 dt-table" id="dataTableKegiatan" data-custom-dt="1">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">#</th>
                            @if(auth()->user()->is_superadmin)
                            <th>Instansi</th>
                            @endif
                            <th style="width:90px;">ID Giat</th>
                            <th style="width:140px;">Kode</th>
                            <th>Nama Kegiatan</th>
                            <th style="width:100px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $index => $kegiatan)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            @if(auth()->user()->is_superadmin)
                            <td><small>{{ $kegiatan->instansi }}</small></td>
                            @endif
                            <td><span class="kode-badge">{{ $kegiatan->id_giat }}</span></td>
                            <td><span class="kode-badge">{{ $kegiatan->kode_giat }}</span></td>
                            <td><strong>{{ $kegiatan->nama_giat }}</strong></td>
                            <td class="text-center">
                                <div class="d-inline-flex" style="gap:4px;">
                                    <button type="button" class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit"
                                        onclick="openEditKegiatan({{ $kegiatan->id }}, '{{ $kegiatan->kode_giat }}', '{{ addslashes($kegiatan->nama_giat) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('master-rekening.kegiatan.destroy', $kegiatan->id) }}" class="d-inline" onsubmit="return confirmHapus(event, 'kegiatan ini');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="{{ auth()->user()->is_superadmin ? 6 : 5 }}" class="text-center text-muted">Belum ada data kegiatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ SUB KEGIATAN ═══════════════ -->
<div id="sectionSubKegiatan">
    <div class="section-action-bar">
        <button class="btn btn-success" data-toggle="modal" data-target="#addSubKegiatanModal">
            <i class="fas fa-plus mr-2"></i>Tambah Sub Kegiatan
        </button>
        <span class="text-muted small ml-auto align-self-center">
            <i class="fas fa-info-circle mr-1"></i>{{ $totalSubKegiatan }} sub kegiatan terdaftar
        </span>
    </div>
    <div class="card recent-card mb-4">
        <div class="card-header">
            <div class="header-icon" style="background:rgba(28,200,138,.15);color:#13855c;"><i class="fas fa-list-ul"></i></div>
            <h6>Data Sub Kegiatan</h6>
            <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalSubKegiatan }} data</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 dt-table" id="dataTableSubKegiatan" data-custom-dt="1">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">#</th>
                            @if(auth()->user()->is_superadmin)
                            <th>Instansi</th>
                            @endif
                            <th style="width:100px;">ID Sub Giat</th>
                            <th style="width:150px;">Kode</th>
                            <th>Nama Sub Kegiatan</th>
                            <th>Kegiatan Induk</th>
                            <th style="width:100px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subKegiatans as $index => $subKegiatan)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            @if(auth()->user()->is_superadmin)
                            <td><small>{{ $subKegiatan->instansi }}</small></td>
                            @endif
                            <td><span class="kode-badge">{{ $subKegiatan->id_sub_giat }}</span></td>
                            <td><span class="kode-badge">{{ $subKegiatan->kode_sub_giat }}</span></td>
                            <td><strong>{{ $subKegiatan->nama_sub_giat }}</strong></td>
                            <td><small class="text-muted">{{ $subKegiatan->kegiatan?->kode_giat }} — {{ $subKegiatan->kegiatan?->nama_giat }}</small></td>
                            <td class="text-center">
                                <div class="d-inline-flex" style="gap:4px;">
                                    <button type="button" class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit"
                                        onclick="openEditSubKegiatan({{ $subKegiatan->id }}, '{{ $subKegiatan->id_giat }}', '{{ $subKegiatan->kode_sub_giat }}', '{{ addslashes($subKegiatan->nama_sub_giat) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('master-rekening.sub-kegiatan.destroy', $subKegiatan->id) }}" class="d-inline" onsubmit="return confirmHapus(event, 'sub kegiatan ini');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="{{ auth()->user()->is_superadmin ? 7 : 6 }}" class="text-center text-muted">Belum ada data sub kegiatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ KODE REKENING ═══════════════ -->
<div id="sectionRekening">
    <div class="section-action-bar">
        <button class="btn btn-warning text-white" data-toggle="modal" data-target="#addKodeRekeningModal">
            <i class="fas fa-plus mr-2"></i>Tambah Kode Rekening
        </button>
        <span class="text-muted small ml-auto align-self-center">
            <i class="fas fa-info-circle mr-1"></i>{{ $totalRekening }} kode rekening terdaftar
        </span>
    </div>
    <div class="card recent-card mb-4">
        <div class="card-header">
            <div class="header-icon" style="background:rgba(246,194,62,.2);color:#a07800;"><i class="fas fa-calculator"></i></div>
            <h6>Data Kode Rekening</h6>
            <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalRekening }} data</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 dt-table" id="dataTableKodeRekening" data-custom-dt="1">
                    <thead>
                        <tr>
                            <th style="width:50px;" class="text-center">#</th>
                            @if(auth()->user()->is_superadmin)
                            <th>Instansi</th>
                            @endif
                            <th style="width:90px;">ID Akun</th>
                            <th style="width:150px;">Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Sub Kegiatan</th>
                            <th style="width:90px;" class="text-center">Blokir</th>
                            <th style="width:100px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kodeRekenings as $index => $rekening)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            @if(auth()->user()->is_superadmin)
                            <td><small>{{ $rekening->instansi }}</small></td>
                            @endif
                            <td><span class="kode-badge">{{ $rekening->id_akun }}</span></td>
                            <td><span class="kode-badge">{{ $rekening->kode_akun }}</span></td>
                            <td><strong>{{ $rekening->nama_akun }}</strong></td>
                            <td><small class="text-muted">{{ $rekening->subKegiatan?->kode_sub_giat }} — {{ $rekening->subKegiatan?->nama_sub_giat }}</small></td>
                            <td class="text-center">
                                @if($rekening->is_blokir)
                                    <span class="blokir-badge-yes"><i class="fas fa-ban mr-1"></i>Blokir</span>
                                @else
                                    <span class="blokir-badge-no"><i class="fas fa-check mr-1"></i>Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex" style="gap:4px;">
                                    <button type="button" class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit"
                                        onclick="openEditKodeRekening({{ $rekening->id }}, '{{ $rekening->id_sub_giat }}', '{{ $rekening->kode_akun }}', '{{ addslashes($rekening->nama_akun) }}', {{ $rekening->is_blokir ? 'true' : 'false' }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('master-rekening.kode-rekening.destroy', $rekening->id) }}" class="d-inline" onsubmit="return confirmHapus(event, 'kode rekening ini');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="{{ auth()->user()->is_superadmin ? 8 : 7 }}" class="text-center text-muted">Belum ada data kode rekening.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ─── MODALS ─── -->
<div class="modal fade" id="addKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('master-rekening.kegiatan.store') }}" class="modal-content border-left-primary">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Kode Giat</label>
                    <input type="text" name="kode_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nama Kegiatan</label>
                    <input type="text" name="nama_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" id="editKegiatanForm" class="modal-content border-left-info">
            @csrf @method('PUT')
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Kode Giat</label>
                    <input type="text" name="kode_giat" id="edit_kegiatan_kode_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nama Kegiatan</label>
                    <input type="text" name="nama_giat" id="edit_kegiatan_nama_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addSubKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('master-rekening.sub-kegiatan.store') }}" class="modal-content border-left-success">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Sub Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Kegiatan Induk</label>
                    <select name="id_giat" class="form-control" required>
                        <option value="">Pilih Kegiatan</option>
                        @foreach($kegiatans as $kegiatan)
                        <option value="{{ $kegiatan->id_giat }}">{{ $kegiatan->kode_giat }} — {{ $kegiatan->nama_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Kode Sub Giat</label>
                    <input type="text" name="kode_sub_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nama Sub Kegiatan</label>
                    <input type="text" name="nama_sub_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSubKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" id="editSubKegiatanForm" class="modal-content border-left-info">
            @csrf @method('PUT')
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Sub Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Kegiatan Induk</label>
                    <select name="id_giat" id="edit_sub_kegiatan_id_giat" class="form-control" required>
                        <option value="">Pilih Kegiatan</option>
                        @foreach($kegiatans as $kegiatan)
                        <option value="{{ $kegiatan->id_giat }}">{{ $kegiatan->kode_giat }} — {{ $kegiatan->nama_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Kode Sub Giat</label>
                    <input type="text" name="kode_sub_giat" id="edit_sub_kegiatan_kode_sub_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nama Sub Kegiatan</label>
                    <input type="text" name="nama_sub_giat" id="edit_sub_kegiatan_nama_sub_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addKodeRekeningModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('master-rekening.kode-rekening.store') }}" class="modal-content border-left-warning">
            @csrf
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Kode Rekening</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Sub Kegiatan</label>
                    <select name="id_sub_giat" class="form-control" required>
                        <option value="">Pilih Sub Kegiatan</option>
                        @foreach($subKegiatans as $subKegiatan)
                        <option value="{{ $subKegiatan->id_sub_giat }}">{{ $subKegiatan->kode_sub_giat }} — {{ $subKegiatan->nama_sub_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Kode Akun</label>
                    <input type="text" name="kode_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nama Akun</label>
                    <input type="text" name="nama_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="is_blokir" value="1" id="add_is_blokir">
                        <label class="custom-control-label" for="add_is_blokir">
                            <i class="fas fa-ban text-danger mr-1"></i>Blokir akun ini
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editKodeRekeningModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" id="editKodeRekeningForm" class="modal-content border-left-info">
            @csrf @method('PUT')
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Kode Rekening</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Sub Kegiatan</label>
                    <select name="id_sub_giat" id="edit_kode_rekening_id_sub_giat" class="form-control" required>
                        <option value="">Pilih Sub Kegiatan</option>
                        @foreach($subKegiatans as $subKegiatan)
                        <option value="{{ $subKegiatan->id_sub_giat }}">{{ $subKegiatan->kode_sub_giat }} — {{ $subKegiatan->nama_sub_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Kode Akun</label>
                    <input type="text" name="kode_akun" id="edit_kode_rekening_kode_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Nama Akun</label>
                    <input type="text" name="nama_akun" id="edit_kode_rekening_nama_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="is_blokir" value="1" id="edit_kode_rekening_is_blokir">
                        <label class="custom-control-label" for="edit_kode_rekening_is_blokir">
                            <i class="fas fa-ban text-danger mr-1"></i>Blokir akun ini
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmHapus(event, label) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus data?',
        text: 'Data ' + label + ' akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => { if (result.isConfirmed) event.target.submit(); });
    return false;
}

function openEditKegiatan(id, kodeGiat, namaGiat) {
    document.getElementById('editKegiatanForm').action = `/master-rekening/kegiatan/${id}`;
    document.getElementById('edit_kegiatan_kode_giat').value = kodeGiat;
    document.getElementById('edit_kegiatan_nama_giat').value = namaGiat;
    $('#editKegiatanModal').modal('show');
}

function openEditSubKegiatan(id, idGiat, kodeSubGiat, namaSubGiat) {
    document.getElementById('editSubKegiatanForm').action = `/master-rekening/sub-kegiatan/${id}`;
    document.getElementById('edit_sub_kegiatan_id_giat').value = idGiat;
    document.getElementById('edit_sub_kegiatan_kode_sub_giat').value = kodeSubGiat;
    document.getElementById('edit_sub_kegiatan_nama_sub_giat').value = namaSubGiat;
    $('#editSubKegiatanModal').modal('show');
}

function openEditKodeRekening(id, idSubGiat, kodeAkun, namaAkun, isBlokir) {
    document.getElementById('editKodeRekeningForm').action = `/master-rekening/kode-rekening/${id}`;
    document.getElementById('edit_kode_rekening_id_sub_giat').value = idSubGiat;
    document.getElementById('edit_kode_rekening_kode_akun').value = kodeAkun;
    document.getElementById('edit_kode_rekening_nama_akun').value = namaAkun;
    document.getElementById('edit_kode_rekening_is_blokir').checked = !!isBlokir;
    $('#editKodeRekeningModal').modal('show');
}

$(document).ready(function() {
    const isSuperadmin = {{ auth()->user()->is_superadmin ? 'true' : 'false' }};
    const dtConfig = {
        destroy: true,
        responsive: true,
        autoWidth: false,
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok",
            emptyTable: "Tidak ada data.",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    };

    if (!$.fn.DataTable.isDataTable('#dataTableKegiatan')) {
        const actionColKegiatan = isSuperadmin ? 5 : 4;
        $('#dataTableKegiatan').DataTable({
            ...dtConfig,
            columnDefs: [
                { orderable: false, targets: [actionColKegiatan] },
                { searchable: false, targets: [0, actionColKegiatan] }
            ]
        });
    }

    if (!$.fn.DataTable.isDataTable('#dataTableSubKegiatan')) {
        const actionColSubKegiatan = isSuperadmin ? 6 : 5;
        $('#dataTableSubKegiatan').DataTable({
            ...dtConfig,
            columnDefs: [
                { orderable: false, targets: [actionColSubKegiatan] },
                { searchable: false, targets: [0, actionColSubKegiatan] }
            ]
        });
    }

    if (!$.fn.DataTable.isDataTable('#dataTableKodeRekening')) {
        const actionColKodeRekening = isSuperadmin ? 7 : 6;
        $('#dataTableKodeRekening').DataTable({
            ...dtConfig,
            columnDefs: [
                { orderable: false, targets: [actionColKodeRekening] },
                { searchable: false, targets: [0, actionColKodeRekening] }
            ]
        });
    }
});
</script>
@endpush

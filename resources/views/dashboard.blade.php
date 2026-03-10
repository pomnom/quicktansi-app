@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-custom.css') }}">
@endpush

@section('content')

{{-- ── Hero Banner ── --}}
<div class="dashboard-hero">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i>
                {{ $isSuperadmin ? 'SuperAdmin' : 'Operator' }}
            </div>
            <div class="hero-title">Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!</div>
            <p class="hero-sub">
                {{ $isSuperadmin ? 'Anda melihat ringkasan data seluruh instansi.' : 'Anda melihat data instansi: <strong>' . auth()->user()->instansi . '</strong>' }}
            </p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-receipt"></i>
        </div>
    </div>
</div>

{{-- ── Main Stat Cards ── --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary">
            <div class="card-body">
                <div class="stat-label">Total Kuitansi</div>
                <div class="stat-value">{{ number_format($totalKuitansi, 0, ',', '.') }}</div>
                <i class="fas fa-receipt stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua periode</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-warning">
            <div class="card-body">
                <div class="stat-label">Kuitansi Bulan Ini</div>
                <div class="stat-value">{{ number_format($kuitansiBulanIni, 0, ',', '.') }}</div>
                <i class="fas fa-calendar-check stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-clock"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success">
            <div class="card-body">
                <div class="stat-label">Nominal Bulan Ini</div>
                <div class="stat-value small-value">Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}</div>
                <i class="fas fa-money-bill-wave stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-clock"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-info">
            <div class="card-body">
                <div class="stat-label">Total Nominal</div>
                <div class="stat-value small-value">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                <i class="fas fa-wallet stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua periode</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Secondary Info Cards ── --}}
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card info-card">
            <div class="card-body">
                <div class="icon-wrap bg-primary-soft">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <div class="info-label">Total Rekanan</div>
                    <div class="info-value">{{ number_format($totalRekanan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card info-card">
            <div class="card-body">
                <div class="icon-wrap bg-success-soft">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <div class="info-label">Total Staff</div>
                    <div class="info-value">{{ number_format($totalStaff, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card info-card">
            <div class="card-body">
                <div class="icon-wrap bg-warning-soft">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="info-label">{{ $isSuperadmin ? 'Total User Sistem' : 'Total User Instansi' }}</div>
                    <div class="info-value">{{ number_format($totalUser, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Kuitansi Table ── --}}
<div class="card recent-card mb-4">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-history"></i></div>
        <h6>Kuitansi Terbaru</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No Buku</th>
                        <th>Tanggal</th>
                        <th>Penerima</th>
                        <th>Rekanan</th>
                        <th class="text-right">Total Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kuitansiTerbaru as $k)
                    <tr>
                        <td><span class="no-buku-badge">{{ $k->no_buku ?? '-' }}</span></td>
                        <td>{{ $k->tanggal_kuitansi ? \Carbon\Carbon::parse($k->tanggal_kuitansi)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $k->nama_penerima ?? '-' }}</td>
                        <td>{{ $k->rekanan->nama_perusahaan ?? '-' }}</td>
                        <td class="text-right"><span class="badge-nominal">Rp {{ number_format($k->total_akhir ?? 0, 0, ',', '.') }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x d-block mb-2" style="color:#d1d3e2;"></i>
                            Belum ada data kuitansi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="background:#f8f9fc;border-top:1px solid #e3e6f0;padding:12px 22px;">
        <a href="{{ route('kuitansi.index') }}" class="btn btn-sm btn-primary" style="border-radius:8px;font-weight:600;">
            <i class="fas fa-list mr-1"></i>Lihat Semua Kuitansi
        </a>
    </div>
</div>

@endsection
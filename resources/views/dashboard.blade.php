@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Dashboard</h1>
        <p class="mb-0 text-muted">
            {{ $isSuperadmin ? 'Ringkasan seluruh instansi' : 'Ringkasan data instansi Anda' }}
        </p>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kuitansi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalKuitansi, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Nominal Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Nominal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kuitansi Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($kuitansiBulanIni, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-handshake fa-2x text-primary"></i>
                </div>
                <div>
                    <div class="text-xs text-uppercase text-muted mb-1">Total Rekanan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRekanan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-user-tie fa-2x text-success"></i>
                </div>
                <div>
                    <div class="text-xs text-uppercase text-muted mb-1">Total Staff</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalStaff, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-users fa-2x text-warning"></i>
                </div>
                <div>
                    <div class="text-xs text-uppercase text-muted mb-1">{{ $isSuperadmin ? 'Total User Sistem' : 'Total User Instansi' }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUser, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Kuitansi Terbaru</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th>No Buku</th>
                        <th>Tanggal Kuitansi</th>
                        <th>Penerima</th>
                        <th>Rekanan</th>
                        <th class="text-right">Total Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kuitansiTerbaru as $kuitansi)
                    <tr>
                        <td>{{ $kuitansi->no_buku ?? '-' }}</td>
                        <td>{{ $kuitansi->tanggal_kuitansi ? \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $kuitansi->nama_penerima ?? '-' }}</td>
                        <td>{{ $kuitansi->rekanan->nama_perusahaan ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($kuitansi->total_akhir ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data kuitansi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@extends('layouts.app')

@section('title', 'Instansi')


@section('content')

@php
    $totalInstansi = count($instansis);
    $withWebsite   = collect($instansis)->filter(fn($i) => !empty($i->website))->count();
@endphp

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Manajemen Instansi
            </div>
            <div class="hero-title">Data Instansi</div>
            <p class="hero-sub">Kelola daftar instansi yang terdaftar dalam sistem.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-city"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-building hs-icon"></i>
            <div>
                <div class="hs-label">Total Instansi</div>
                <div class="hs-value">{{ $totalInstansi }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-globe hs-icon"></i>
            <div>
                <div class="hs-label">Punya Website</div>
                <div class="hs-value">{{ $withWebsite }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Card -->
<div class="row mb-2">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary h-100">
            <div class="card-body">
                <div class="stat-label">Total Instansi</div>
                <div class="stat-value">{{ $totalInstansi }}</div>
                <i class="fas fa-building stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua instansi terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success h-100">
            <div class="card-body">
                <div class="stat-label">Memiliki Website</div>
                <div class="stat-value">{{ $withWebsite }}</div>
                <i class="fas fa-globe stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-check-circle"></i>Instansi dengan website</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-info h-100">
            <div class="card-body">
                <div class="stat-label">Instansi Terbaru</div>
                <div class="stat-value" style="font-size:15px;">{{ collect($instansis)->last()?->nama ? \Illuminate\Support\Str::limit(collect($instansis)->last()->nama, 22) : '-' }}</div>
                <i class="fas fa-city stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-clock"></i>Ditambahkan terakhir</div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-1"></i> {{ $message }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
</div>
@endif
@if($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
</div>
@endif

<!-- Action Bar -->
<div class="instansi-action-bar">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addInstansiModal">
        <i class="fas fa-plus mr-2"></i>Tambah Instansi
    </button>
    <span class="text-muted small ml-auto align-self-center">
        <i class="fas fa-info-circle mr-1"></i>{{ $totalInstansi }} instansi terdaftar
    </span>
</div>

<!-- DataTable Card -->
<div class="card recent-card mb-4">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-building"></i></div>
        <h6>Data Instansi</h6>
        <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalInstansi }} data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Nama Instansi</th>
                        <th>Alamat</th>
                        <th style="width: 130px;">No. Telp</th>
                        <th style="width: 200px;">Email</th>
                        <th style="width: 140px;">Website</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instansis as $index => $instansi)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td><strong>{{ $instansi->nama }}</strong></td>
                        <td><small class="text-muted">{{ $instansi->alamat ?? '-' }}</small></td>
                        <td><small>{{ $instansi->no_telp ?? '-' }}</small></td>
                        <td><small>{{ $instansi->email ?? '-' }}</small></td>
                        <td>
                            @if($instansi->website)
                                <a href="{{ $instansi->website }}" target="_blank" class="website-badge">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka
                                </a>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex" style="gap:4px;">
                                <button class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit" onclick='editInstansi(@json($instansi))'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('instansi.destroy', $instansi->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event, {{ $instansi->id }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;" title="Hapus">
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

<!-- Add Instansi Modal -->
<div class="modal fade" id="addInstansiModal" tabindex="-1" role="dialog" aria-labelledby="addInstansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addInstansiModalLabel">
                    <i class="fas fa-building mr-2"></i>Tambah Instansi Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('instansi.store') }}" method="POST" id="addInstansiForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama" class="font-weight-bold">Nama Instansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" required placeholder="Contoh: Dinas Keuangan">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="alamat" class="font-weight-bold">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap instansi"></textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_telp" class="font-weight-bold">No. Telepon</label>
                                <input type="text" class="form-control @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" placeholder="021-1234567" maxlength="20">
                                @error('no_telp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="info@instansi.go.id">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="website" class="font-weight-bold">Website</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            </div>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" placeholder="https://www.instansi.go.id">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Format URL harus lengkap, contoh: https://www.instansi.go.id
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Instansi Modal -->
<div class="modal fade" id="editInstansiModal" tabindex="-1" role="dialog" aria-labelledby="editInstansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-left-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editInstansiModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Instansi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="editInstansiForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama" class="font-weight-bold">Nama Instansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_alamat" class="font-weight-bold">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_no_telp" class="font-weight-bold">No. Telepon</label>
                                <input type="text" class="form-control" id="edit_no_telp" name="no_telp" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email" class="font-weight-bold">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_website" class="font-weight-bold">Website</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            </div>
                            <input type="url" class="form-control" id="edit_website" name="website">
                        </div>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Format URL harus lengkap, contoh: https://www.instansi.go.id
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save mr-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function editInstansi(instansi) {
        document.getElementById('editInstansiForm').action = '{{ route("instansi.update", ":id") }}'.replace(':id', instansi.id);
        document.getElementById('edit_nama').value = instansi.nama;
        document.getElementById('edit_alamat').value = instansi.alamat || '';
        document.getElementById('edit_no_telp').value = instansi.no_telp || '';
        document.getElementById('edit_email').value = instansi.email || '';
        document.getElementById('edit_website').value = instansi.website || '';
        $('#editInstansiModal').modal('show');
    }

    function confirmDelete(event, instansiId) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus Instansi?',
            text: 'Data instansi akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
        return false;
    }

    $('#addInstansiModal').on('hidden.bs.modal', function () {
        $('#addInstansiForm')[0].reset();
    });

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                destroy: true,
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: false, targets: [6] },
                    { searchable: false, targets: [0, 6] }
                ],
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ instansi",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 instansi",
                    infoFiltered: "(difilter dari _MAX_ total instansi)",
                    zeroRecords: "Tidak ada instansi yang cocok",
                    emptyTable: "Tidak ada data instansi.",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[1, 'asc']]
            });
        }
    });
</script>
@endpush

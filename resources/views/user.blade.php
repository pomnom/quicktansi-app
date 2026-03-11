@extends('layouts.app')

@section('title', 'Manajemen User')


@section('content')

@php
    $totalUser      = count($users);
    $totalSuperadmin = collect($users)->where('is_superadmin', true)->count();
    $totalOperator  = $totalUser - $totalSuperadmin;
    $instansiList   = collect($users)->pluck('instansi')->filter()->unique()->count();
@endphp

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Manajemen User
            </div>
            <div class="hero-title">Kelola Akun Pengguna</div>
            <p class="hero-sub">Atur akses dan hak pengguna di seluruh instansi.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-users-cog"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-users hs-icon"></i>
            <div>
                <div class="hs-label">Total User</div>
                <div class="hs-value">{{ $totalUser }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-crown hs-icon"></i>
            <div>
                <div class="hs-label">Superadmin</div>
                <div class="hs-value">{{ $totalSuperadmin }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-user hs-icon"></i>
            <div>
                <div class="hs-label">Operator</div>
                <div class="hs-value">{{ $totalOperator }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-building hs-icon"></i>
            <div>
                <div class="hs-label">Instansi</div>
                <div class="hs-value">{{ $instansiList }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-2">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary h-100">
            <div class="card-body">
                <div class="stat-label">Total User</div>
                <div class="stat-value">{{ $totalUser }}</div>
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Seluruh akun terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-warning h-100">
            <div class="card-body">
                <div class="stat-label">Superadmin</div>
                <div class="stat-value">{{ $totalSuperadmin }}</div>
                <i class="fas fa-crown stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-shield-alt"></i>Akses penuh sistem</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success h-100">
            <div class="card-body">
                <div class="stat-label">Operator</div>
                <div class="stat-value">{{ $totalOperator }}</div>
                <i class="fas fa-user stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-edit"></i>Akses per instansi</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-info h-100">
            <div class="card-body">
                <div class="stat-label">Instansi Terdaftar</div>
                <div class="stat-value">{{ $instansiList }}</div>
                <i class="fas fa-building stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-check-circle"></i>Instansi aktif</div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-1"></i> {{ $message }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif
@if($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<!-- Action Bar -->
<div class="user-action-bar">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
        <i class="fas fa-user-plus mr-2"></i>Tambah User
    </button>
    <span class="text-muted small ml-auto align-self-center">
        <i class="fas fa-info-circle mr-1"></i>{{ $totalUser }} akun terdaftar
    </span>
</div>

<!-- DataTable Card -->
<div class="card recent-card mb-4">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-users"></i></div>
        <h6>Data User</h6>
        <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalUser }} data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="dataTable" data-custom-dt="1">
                <thead>
                    <tr>
                        <th style="width:50px;" class="text-center">#</th>
                        <th style="width:160px;">NIP</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th style="width:130px;">No. Telp</th>
                        <th>Instansi</th>
                        <th style="width:130px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td>
                            <span class="nip-text">{{ $user->nip }}</span>
                            @if($user->id == auth()->id())
                                <br><span class="me-badge mt-1"><i class="fas fa-user-check mr-1"></i>Anda</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            <br>
                            @if($user->is_superadmin)
                                <span class="role-badge-super"><i class="fas fa-crown mr-1"></i>Superadmin</span>
                            @else
                                <span class="role-badge-user"><i class="fas fa-user mr-1"></i>Operator</span>
                            @endif
                        </td>
                        <td><small>{{ $user->email }}</small></td>
                        <td><small>{{ $user->no_telp ?? '-' }}</small></td>
                        <td><span class="instansi-text">{{ $user->instansi ?? '-' }}</span></td>
                        <td class="text-center">
                            <div class="d-inline-flex" style="gap:4px;">
                                <button class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit" onclick='editUser(@json($user))'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" style="border-radius:8px;" title="Reset Password ke NIP" onclick="resetPassword({{ $user->id }}, '{{ $user->nip }}')">
                                    <i class="fas fa-key"></i>
                                </button>
                                @if($user->id != auth()->id())
                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirmHapusUser(event)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius:8px;" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm" style="border-radius:8px;" disabled title="Tidak dapat menghapus akun sendiri">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Tambah User Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.store') }}" method="POST" id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle mr-1"></i>
                        <small><strong>Catatan:</strong> Username dan Password default adalah <strong>NIP</strong> yang Anda masukkan</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip" class="font-weight-bold">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" required placeholder="199001012020011001" maxlength="18">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> NIP akan menjadi username & password default
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required placeholder="contoh@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_telp" class="font-weight-bold">No. Telepon</label>
                                <input type="text" class="form-control @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" placeholder="081234567890" maxlength="20">
                                @error('no_telp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="instansi" class="font-weight-bold">Instansi</label>
                        <select class="form-control @error('instansi') is-invalid @enderror" id="instansi" name="instansi">
                            <option value="">-- Pilih Instansi --</option>
                            @foreach($instansis as $inst)
                                <option value="{{ $inst->nama }}">{{ $inst->nama }}</option>
                            @endforeach
                        </select>
                        @error('instansi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    @if(auth()->user()->is_superadmin)
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_superadmin" name="is_superadmin" value="1">
                            <label class="custom-control-label" for="is_superadmin">
                                <i class="fas fa-crown text-warning"></i> <strong>Jadikan Superadmin</strong>
                                <small class="d-block text-muted">Superadmin dapat mengelola semua user dari semua instansi</small>
                            </label>
                        </div>
                    </div>
                    @endif
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-left-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit mr-2"></i>Edit User
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="editUserForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <small><strong>Perhatian:</strong> Untuk mereset password ke NIP, gunakan tombol <strong>"Reset Password"</strong> pada tabel</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nip" class="font-weight-bold">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nip" name="nip" required readonly style="background-color: #e9ecef;">
                                <small class="form-text text-muted">
                                    <i class="fas fa-lock"></i> NIP tidak dapat diubah
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_name" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_no_telp" class="font-weight-bold">No. Telepon</label>
                                <input type="text" class="form-control" id="edit_no_telp" name="no_telp" maxlength="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_instansi" class="font-weight-bold">Instansi</label>
                        <select class="form-control" id="edit_instansi" name="instansi">
                            <option value="">-- Pilih Instansi --</option>
                            @foreach($instansis as $inst)
                                <option value="{{ $inst->nama }}">{{ $inst->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if(auth()->user()->is_superadmin)
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="edit_is_superadmin" name="is_superadmin" value="1">
                            <label class="custom-control-label" for="edit_is_superadmin">
                                <i class="fas fa-crown text-warning"></i> <strong>Jadikan Superadmin</strong>
                                <small class="d-block text-muted">Superadmin dapat mengelola semua user dari semua instansi</small>
                            </label>
                        </div>
                    </div>
                    @endif
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
    function editUser(user) {
        document.getElementById('editUserForm').action = '{{ route("user.update", ":id") }}'.replace(':id', user.id);
        document.getElementById('edit_nip').value = user.nip;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_no_telp').value = user.no_telp || '';
        document.getElementById('edit_instansi').value = user.instansi || '';
        const superadminCheckbox = document.getElementById('edit_is_superadmin');
        if (superadminCheckbox) superadminCheckbox.checked = !!user.is_superadmin;
        $('#editUserModal').modal('show');
    }

    function confirmHapusUser(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus User?',
            text: 'Akun user akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) event.target.submit(); });
        return false;
    }

    function resetPassword(userId, nip) {
        Swal.fire({
            title: 'Reset Password?',
            html: `Password user akan direset ke:<br><strong class="text-primary">${nip}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f6c23e',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-key"></i> Ya, Reset Password',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("user.resetPassword", ":id") }}'.replace(':id', userId);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Clear form when adding new user
    $('#addUserModal').on('hidden.bs.modal', function () {
        $('#addUserForm')[0].reset();
    });

    // Initialize DataTable with Indonesian language
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
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ user",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 user",
                    infoFiltered: "(difilter dari _MAX_ total user)",
                    zeroRecords: "Tidak ada user yang cocok",
                    emptyTable: "Tidak ada data user.",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[1, 'asc']] // Order by NIP
            });
        }
    });
</script>
@endpush

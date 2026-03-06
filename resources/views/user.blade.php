@extends('layouts.app')

@section('title', 'User')

@section('content')
<!-- Page Heading -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-users-cog text-primary mr-2"></i>Manajemen User
        </h1>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </button>
    </div>
</div>

<!-- Alert Messages -->
@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ $message }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ $message }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- DataTable Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-table mr-2"></i>Data User
        </h6>
        <span class="badge badge-light">{{ count($users) }} user</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th style="width: 150px;"><i class="fas fa-id-badge text-primary mr-1"></i>NIP</th>
                        <th><i class="fas fa-user text-primary mr-1"></i>Nama</th>
                        <th><i class="fas fa-envelope text-primary mr-1"></i>Email</th>
                        <th><i class="fas fa-phone text-primary mr-1"></i>No. Telp</th>
                        <th><i class="fas fa-building text-primary mr-1"></i>Instansi</th>
                        <th style="width: 150px;" class="text-center"><i class="fas fa-cogs text-primary mr-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <small class="font-weight-bold">{{ $user->nip }}</small>
                            @if($user->id == auth()->id())
                                <br><span class="badge badge-primary badge-sm mt-1">
                                    <i class="fas fa-user-check"></i> Anda
                                </span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->is_superadmin)
                                <br><span class="badge badge-danger badge-sm mt-1">
                                    <i class="fas fa-crown"></i> Superadmin
                                </span>
                            @endif
                        </td>
                        <td><small>{{ $user->email }}</small></td>
                        <td><small>{{ $user->no_telp ?? '-' }}</small></td>
                        <td><small>{{ $user->instansi ?? '-' }}</small></td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm" title="Edit" onclick="editUser({{ $user }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" title="Reset Password ke NIP" onclick="resetPassword({{ $user->id }}, '{{ $user->nip }}')">
                                <i class="fas fa-key"></i>
                            </button>
                            @if($user->id != auth()->id())
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled title="Tidak dapat menghapus user yang sedang login">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); border: none;">
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
        
        // Set selected instansi
        const instansiSelect = document.getElementById('edit_instansi');
        instansiSelect.value = user.instansi || '';
        
        // Set is_superadmin checkbox
        const superadminCheckbox = document.getElementById('edit_is_superadmin');
        if (superadminCheckbox) {
            superadminCheckbox.checked = user.is_superadmin ? true : false;
        }
        
        $('#editUserModal').modal('show');
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
                // Create form and submit
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

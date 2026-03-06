@extends('layouts.app')

@section('title', 'Instansi')

@section('content')
<!-- Page Heading -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-building text-primary mr-2"></i>Manajemen Instansi
        </h1>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addInstansiModal">
            <i class="fas fa-plus mr-2"></i>Tambah Instansi
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
            <i class="fas fa-table mr-2"></i>Data Instansi
        </h6>
        <span class="badge badge-light">{{ count($instansis) }} instansi</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th><i class="fas fa-building text-primary mr-1"></i>Nama Instansi</th>
                        <th><i class="fas fa-map-marker-alt text-primary mr-1"></i>Alamat</th>
                        <th style="width: 130px;"><i class="fas fa-phone text-primary mr-1"></i>No. Telp</th>
                        <th style="width: 200px;"><i class="fas fa-envelope text-primary mr-1"></i>Email</th>
                        <th style="width: 150px;"><i class="fas fa-globe text-primary mr-1"></i>Website</th>
                        <th style="width: 120px;" class="text-center"><i class="fas fa-cogs text-primary mr-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instansis as $index => $instansi)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $instansi->nama }}</strong></td>
                        <td><small>{{ $instansi->alamat ?? '-' }}</small></td>
                        <td><small>{{ $instansi->no_telp ?? '-' }}</small></td>
                        <td><small>{{ $instansi->email ?? '-' }}</small></td>
                        <td>
                            @if($instansi->website)
                                <a href="{{ $instansi->website }}" target="_blank" class="text-primary">
                                    <small><i class="fas fa-external-link-alt"></i> Link</small>
                                </a>
                            @else
                                <small>-</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm" title="Edit" onclick="editInstansi({{ $instansi }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('instansi.destroy', $instansi->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event, {{ $instansi->id }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
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
                        <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" placeholder="https://www.instansi.go.id">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); border: none;">
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
                        <input type="url" class="form-control" id="edit_website" name="website">
                        <small class="form-text text-muted">
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

    // Clear form when adding new instansi
    $('#addInstansiModal').on('hidden.bs.modal', function () {
        $('#addInstansiForm')[0].reset();
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
                order: [[1, 'asc']] // Order by Nama Instansi
            });
        }
    });
</script>
@endpush

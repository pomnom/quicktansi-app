@extends('layouts.app')

@section('title', 'Staff')

@section('content')
<!-- Page Heading -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-users text-primary mr-2"></i>Staff
        </h1>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addStaffModal">
            <i class="fas fa-plus mr-2"></i>Tambah Staff
        </button>
    </div>
</div>

<!-- DataTable Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-table mr-2"></i>Data Staff
        </h6>
        <span class="badge badge-light">{{ count($staff) }} data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th><i class="fas fa-id-badge text-primary mr-1"></i>NIP</th>
                        <th><i class="fas fa-user text-primary mr-1"></i>Nama</th>
                        <th><i class="fas fa-briefcase text-primary mr-1"></i>Jabatan</th>
                        <th><i class="fas fa-award text-primary mr-1"></i>Golongan</th>
                        <th><i class="fas fa-info-circle text-primary mr-1"></i>Status</th>
                        <th style="width: 100px;" class="text-center"><i class="fas fa-cogs text-primary mr-1"></i>Aksi</th>
                    </tr>
                </thead>
                    <tbody>
                        @foreach($staff as $index => $s)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $s->nip }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->jabatan }}</td>
                            <td>{{ $s->golongan }}</td>
                            <td>
                                @if($s->status)
                                    <span class="badge badge-info">{{ $s->status }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm mr-1" title="Edit" onclick="editStaff({{ $s }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('staff.destroy', $s->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
                <h5 class="modal-title" id="addStaffModalLabel">
                    <i class="fas fa-plus mr-2"></i>Tambah Staff Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('staff.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nip">NIP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" required placeholder="19700101 199901 1 001">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" required placeholder="Contoh: Kepala Bidang">
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="golongan">Golongan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('golongan') is-invalid @enderror" id="golongan" name="golongan" required placeholder="Contoh: III/d, IV/a">
                        @error('golongan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="">-- Tanpa Status --</option>
                            <option value="Pengguna Anggaran">Pengguna Anggaran</option>
                            <option value="PPK">PPK</option>
                            <option value="PPTK">PPTK</option>
                            <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                            <option value="Bendahara Barang">Bendahara Barang</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); border: none;">
                <h5 class="modal-title" id="editStaffModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Staff
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editStaffForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nip">NIP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nip" name="nip" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_jabatan">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_jabatan" name="jabatan" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_golongan">Golongan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_golongan" name="golongan" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select class="form-control" id="edit_status" name="status">
                            <option value="">-- Tanpa Status --</option>
                            <option value="Pengguna Anggaran">Pengguna Anggaran</option>
                            <option value="PPK">PPK</option>
                            <option value="PPTK">PPTK</option>
                            <option value="Bendahara Pengeluaran">Bendahara Pengeluaran</option>
                            <option value="Bendahara Barang">Bendahara Barang</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editStaff(staff) {
        $('#editStaffForm').attr('action', '/staff/' + staff.id);
        $('#edit_nip').val(staff.nip);
        $('#edit_nama').val(staff.nama);
        $('#edit_jabatan').val(staff.jabatan);
        $('#edit_golongan').val(staff.golongan);
        $('#edit_status').val(staff.status);
        $('#editStaffModal').modal('show');
    }

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                destroy: true,
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: false, targets: [6] },
                    { searchable: false, targets: [6] }
                ],
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang cocok",
                    emptyTable: "Tidak ada data Staff.",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });
        }
    });
</script>
@endpush
@endsection

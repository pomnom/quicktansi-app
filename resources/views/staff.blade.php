@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Staff</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addStaffModal">
            <i class="fas fa-plus fa-sm"></i> Tambah Staff
        </button>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Staff</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Golongan</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editStaff({{ $s }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('staff.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus staff ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStaffModalLabel">Tambah Staff Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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
</script>
@endpush
@endsection

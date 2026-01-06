@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Rekanan</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addRekananModal">
            <i class="fas fa-plus fa-sm"></i> Tambah Rekanan
        </button>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Rekanan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NPWP</th>
                            <th>Nama Perusahaan</th>
                            <th>No. Rekening</th>
                            <th>Bank</th>
                            <th>Nama Pemilik Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekanans as $index => $rekanan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $rekanan->npwp }}</td>
                            <td>{{ $rekanan->nama_perusahaan }}</td>
                            <td>{{ $rekanan->nomor_rekening }}</td>
                            <td>{{ $rekanan->bank }}</td>
                            <td>{{ $rekanan->nama_pemilik_rekening }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editRekanan({{ $rekanan }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('rekanan.destroy', $rekanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus rekanan ini?')">
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

<!-- Add Rekanan Modal -->
<div class="modal fade" id="addRekananModal" tabindex="-1" role="dialog" aria-labelledby="addRekananModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRekananModalLabel">Tambah Rekanan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('rekanan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="npwp">NPWP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp" required placeholder="00.000.000.0-000.000">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_perusahaan">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaan" name="nama_perusahaan" required>
                        @error('nama_perusahaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="nomor_rekening">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nomor_rekening') is-invalid @enderror" id="nomor_rekening" name="nomor_rekening" required>
                        @error('nomor_rekening')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="bank">Bank <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('bank') is-invalid @enderror" id="bank" name="bank" required placeholder="Contoh: BRI, BCA, Mandiri">
                        @error('bank')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_pemilik_rekening">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pemilik_rekening') is-invalid @enderror" id="nama_pemilik_rekening" name="nama_pemilik_rekening" required>
                        @error('nama_pemilik_rekening')
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

<!-- Edit Rekanan Modal -->
<div class="modal fade" id="editRekananModal" tabindex="-1" role="dialog" aria-labelledby="editRekananModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRekananModalLabel">Edit Rekanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editRekananForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_npwp">NPWP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_npwp" name="npwp" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_perusahaan">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_perusahaan" name="nama_perusahaan" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nomor_rekening">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nomor_rekening" name="nomor_rekening" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_bank">Bank <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_bank" name="bank" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_pemilik_rekening">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_pemilik_rekening" name="nama_pemilik_rekening" required>
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
    function editRekanan(rekanan) {
        $('#edit_npwp').val(rekanan.npwp);
        $('#edit_nama_perusahaan').val(rekanan.nama_perusahaan);
        $('#edit_nomor_rekening').val(rekanan.nomor_rekening);
        $('#edit_bank').val(rekanan.bank);
        $('#edit_nama_pemilik_rekening').val(rekanan.nama_pemilik_rekening);
        
        const formAction = "{{ route('rekanan.update', ':id') }}".replace(':id', rekanan.id);
        $('#editRekananForm').attr('action', formAction);
        
        $('#editRekananModal').modal('show');
    }

    $(document).ready(function() {
        // Initialize DataTable only if not already initialized
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                }
            });
        }
    });
</script>
@endpush
@endsection

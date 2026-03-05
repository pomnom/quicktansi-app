@extends('layouts.app')

@section('title', 'Rekanan')

@section('content')
<!-- Page Heading -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-building text-primary mr-2"></i>Rekanan
        </h1>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addRekananModal">
            <i class="fas fa-plus mr-2"></i>Tambah Rekanan
        </button>
    </div>
</div>

<!-- DataTable Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-table mr-2"></i>Data Rekanan
        </h6>
        <span class="badge badge-light">{{ count($rekanans) }} data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th><i class="fas fa-id-card text-primary mr-1"></i>NPWP</th>
                        <th><i class="fas fa-building text-primary mr-1"></i>Nama Perusahaan</th>
                        <th><i class="fas fa-credit-card text-primary mr-1"></i>No. Rekening</th>
                        <th><i class="fas fa-university text-primary mr-1"></i>Bank</th>
                        <th><i class="fas fa-user text-primary mr-1"></i>Nama Pemilik Rekening</th>
                        <th style="width: 100px;" class="text-center"><i class="fas fa-cogs text-primary mr-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekanans as $index => $rekanan)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $rekanan->npwp }}</td>
                        <td>{{ $rekanan->nama_perusahaan }}</td>
                        <td>{{ $rekanan->nomor_rekening }}</td>
                        <td>{{ $rekanan->bank }}</td>
                        <td>{{ $rekanan->nama_pemilik_rekening }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-info btn-sm" title="Edit" onclick='editRekanan(@json($rekanan))'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('rekanan.destroy', $rekanan->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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

<!-- Add Rekanan Modal -->
<div class="modal fade" id="addRekananModal" tabindex="-1" role="dialog" aria-labelledby="addRekananModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addRekananModalLabel">
                    <i class="fas fa-plus mr-2"></i>Tambah Rekanan Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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
                <div class="modal-footer bg-light">
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
        <div class="modal-content border-left-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editRekananModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Rekanan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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
                    emptyTable: "Tidak ada data Rekanan.",
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

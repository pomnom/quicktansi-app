@extends('layouts.app')

@section('title', 'Rekanan')


@section('content')

@php
    $totalRekanan = $rekanans->count();
    $banks = $rekanans->pluck('bank')->filter()->unique()->count();
@endphp

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Manajemen Rekanan
            </div>
            <div class="hero-title">Data Rekanan</div>
            <p class="hero-sub">Kelola rekanan dan informasi rekening bank secara terpusat.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-handshake"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-building hs-icon"></i>
            <div>
                <div class="hs-label">Total Rekanan</div>
                <div class="hs-value">{{ number_format($totalRekanan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-university hs-icon"></i>
            <div>
                <div class="hs-label">Bank Terdaftar</div>
                <div class="hs-value">{{ $banks }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-2">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary h-100">
            <div class="card-body">
                <div class="stat-label">Total Rekanan</div>
                <div class="stat-value">{{ number_format($totalRekanan, 0, ',', '.') }}</div>
                <i class="fas fa-building stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua rekanan terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success h-100">
            <div class="card-body">
                <div class="stat-label">Bank Terdaftar</div>
                <div class="stat-value">{{ $banks }}</div>
                <i class="fas fa-university stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-check-circle"></i>Varian bank unik</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card card-gradient-info h-100">
            <div class="card-body">
                <div class="stat-label">Rekanan Terbaru</div>
                <div class="stat-value" style="font-size:15px;">{{ $rekanans->first()?->nama_perusahaan ? \Illuminate\Support\Str::limit($rekanans->first()->nama_perusahaan, 20) : '-' }}</div>
                <i class="fas fa-user-tie stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-clock"></i>Ditambahkan terakhir</div>
            </div>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="rekanan-action-bar">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addRekananModal">
        <i class="fas fa-plus mr-2"></i>Tambah Rekanan
    </button>
    <span class="text-muted small ml-auto align-self-center">
        <i class="fas fa-info-circle mr-1"></i>{{ $totalRekanan }} rekanan terdaftar
    </span>
</div>

<!-- DataTable Card -->
<div class="card recent-card mb-4">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-table"></i></div>
        <h6>Data Rekanan</h6>
        <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalRekanan }} data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>NPWP</th>
                        <th>Nama Perusahaan</th>
                        <th>No. Rekening</th>
                        <th>Bank</th>
                        <th>Nama Pemilik Rekening</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekanans as $index => $rekanan)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td><span class="npwp-text">{{ $rekanan->npwp }}</span></td>
                        <td><strong>{{ $rekanan->nama_perusahaan }}</strong></td>
                        <td><code style="font-size:12px;color:#5a5c69;">{{ $rekanan->nomor_rekening }}</code></td>
                        <td><span class="bank-badge">{{ $rekanan->bank }}</span></td>
                        <td>{{ $rekanan->nama_pemilik_rekening }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex" style="gap:4px;">
                                <button class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit" onclick='editRekanan(@json($rekanan))'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('rekanan.destroy', $rekanan->id) }}" method="POST" style="display:inline;" id="deleteRekananForm{{ $rekanan->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" style="border-radius:8px;" title="Hapus" onclick="confirmHapusRekanan({{ $rekanan->id }}, '{{ addslashes($rekanan->nama_pemilik_rekening) }}')">
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
                        <label for="npwp">NPWP <small class="text-muted">(opsional)</small></label>
                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp" placeholder="00.000.000.0-000.000">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:10px;"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:10px;"><i class="fas fa-save mr-1"></i>Simpan</button>
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
                        <label for="edit_npwp">NPWP <small class="text-muted">(opsional)</small></label>
                        <input type="text" class="form-control" id="edit_npwp" name="npwp">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:10px;"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-info" style="border-radius:10px;"><i class="fas fa-save mr-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmHapusRekanan(id, nama) {
        Swal.fire({
            title: 'Hapus Rekanan?',
            text: nama,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) document.getElementById('deleteRekananForm' + id).submit(); });
    }

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

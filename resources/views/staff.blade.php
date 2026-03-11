@extends('layouts.app')

@section('title', 'Staff')


@section('content')

@php
    $totalStaff  = $staff->count();
    $totalPptk   = $staff->where('status', 'PPTK')->count();
    $totalBendahara = $staff->whereIn('status', ['Bendahara Pengeluaran','Bendahara Barang'])->count();
    $totalBerStatus = $staff->whereNotNull('status')->where('status','!=','')->count();
@endphp

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Manajemen Staff
            </div>
            <div class="hero-title">Data Staff</div>
            <p class="hero-sub">Kelola daftar staff, jabatan, golongan, dan peran dalam pengelolaan anggaran.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-id-card-alt hs-icon"></i>
            <div>
                <div class="hs-label">Total Staff</div>
                <div class="hs-value">{{ $totalStaff }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-user-tie hs-icon"></i>
            <div>
                <div class="hs-label">PPTK</div>
                <div class="hs-value">{{ $totalPptk }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-coins hs-icon"></i>
            <div>
                <div class="hs-label">Bendahara</div>
                <div class="hs-value">{{ $totalBendahara }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-2">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-primary h-100">
            <div class="card-body">
                <div class="stat-label">Total Staff</div>
                <div class="stat-value">{{ $totalStaff }}</div>
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-folder-open"></i>Semua staff terdaftar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-info h-100">
            <div class="card-body">
                <div class="stat-label">PPTK</div>
                <div class="stat-value">{{ $totalPptk }}</div>
                <i class="fas fa-user-tie stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-briefcase"></i>Pejabat Pelaksana Teknis</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-warning h-100">
            <div class="card-body">
                <div class="stat-label">Bendahara</div>
                <div class="stat-value">{{ $totalBendahara }}</div>
                <i class="fas fa-coins stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-wallet"></i>Pengeluaran &amp; Barang</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card card-gradient-success h-100">
            <div class="card-body">
                <div class="stat-label">Punya Jabatan Fungsional</div>
                <div class="stat-value">{{ $totalBerStatus }}</div>
                <i class="fas fa-award stat-icon"></i>
                <div class="stat-footer"><i class="fas fa-check-circle"></i>Dari total {{ $totalStaff }} staff</div>
            </div>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="staff-action-bar">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addStaffModal">
        <i class="fas fa-plus mr-2"></i>Tambah Staff
    </button>
    <span class="text-muted small ml-auto align-self-center">
        <i class="fas fa-info-circle mr-1"></i>{{ $totalStaff }} staff terdaftar
    </span>
</div>

<!-- DataTable Card -->
<div class="card recent-card mb-4">
    <div class="card-header">
        <div class="header-icon"><i class="fas fa-table"></i></div>
        <h6>Data Staff</h6>
        <span class="badge badge-light ml-2" style="color:#5a5c69;">{{ $totalStaff }} data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Golongan</th>
                        <th>Status</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $index => $s)
                    @php
                        $statusClass = match($s->status) {
                            'Pengguna Anggaran'    => 'status-pa',
                            'PPK'                  => 'status-ppk',
                            'PPTK'                 => 'status-pptk',
                            'Bendahara Pengeluaran'=> 'status-bpng',
                            'Bendahara Barang'     => 'status-bbrg',
                            default                => 'status-none',
                        };
                        $statusIcon = match($s->status) {
                            'Pengguna Anggaran'    => 'fa-star',
                            'PPK'                  => 'fa-shield-alt',
                            'PPTK'                 => 'fa-user-tie',
                            'Bendahara Pengeluaran'=> 'fa-coins',
                            'Bendahara Barang'     => 'fa-boxes',
                            default                => 'fa-minus',
                        };
                    @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td><span class="nip-text">{{ $s->nip }}</span></td>
                        <td><strong>{{ $s->nama }}</strong></td>
                        <td>{{ $s->jabatan }}</td>
                        <td><span class="golongan-badge">{{ $s->golongan }}</span></td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                <i class="fas {{ $statusIcon }} mr-1"></i>{{ $s->status ?: 'Tanpa Status' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex" style="gap:4px;">
                                <button class="btn btn-info btn-sm" style="border-radius:8px;" title="Edit" onclick="editStaff({{ $s }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('staff.destroy', $s->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus staff ini?')">
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

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:10px;"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:10px;"><i class="fas fa-save mr-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-left-info">
            <div class="modal-header bg-info text-white">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:10px;"><i class="fas fa-times mr-1"></i>Batal</button>
                    <button type="submit" class="btn btn-info" style="border-radius:10px;"><i class="fas fa-save mr-1"></i>Update</button>
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

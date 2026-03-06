@extends('layouts.app')

@section('title', 'Master Rekening')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-folder-tree text-primary mr-2"></i>Master Rekening
        </h1>
        <p class="text-muted mt-2">Manajemen Kegiatan → Sub Kegiatan → Kode Rekening
        @if(!auth()->user()->is_superadmin)
            <span class="badge badge-primary ml-2">{{ auth()->user()->instansi }}</span>
        @endif
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-tasks mr-2"></i>Data Kegiatan
                </h6>
                <button class="btn btn-light btn-sm shadow-sm" data-toggle="modal" data-target="#addKegiatanModal">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableKegiatan" data-custom-dt="1" style="font-size: 0.9rem;">
                        <thead style="background-color: #f8f9fc;">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                @if(auth()->user()->is_superadmin)
                                <th><i class="fas fa-building text-primary mr-1"></i>Instansi</th>
                                @endif
                                <th><i class="fas fa-hashtag text-primary mr-1"></i>ID Giat</th>
                                <th><i class="fas fa-barcode text-primary mr-1"></i>Kode</th>
                                <th><i class="fas fa-file-text text-primary mr-1"></i>Nama</th>
                                <th style="width: 120px;" class="text-center"><i class="fas fa-cogs text-primary mr-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kegiatans as $index => $kegiatan)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                @if(auth()->user()->is_superadmin)
                                <td>{{ $kegiatan->instansi }}</td>
                                @endif
                                <td>{{ $kegiatan->id_giat }}</td>
                                <td>{{ $kegiatan->kode_giat }}</td>
                                <td>{{ $kegiatan->nama_giat }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button
                                            type="button"
                                            class="btn btn-info btn-sm"
                                            title="Edit"
                                            onclick="openEditKegiatan({{ $kegiatan->id }}, '{{ $kegiatan->id_giat }}', '{{ $kegiatan->kode_giat }}', '{{ addslashes($kegiatan->nama_giat) }}')"
                                        ><i class="fas fa-edit"></i></button>
                                        <form method="POST" action="{{ route('master-rekening.kegiatan.destroy', $kegiatan->id) }}" class="d-inline" onsubmit="return confirm('Hapus kegiatan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ auth()->user()->is_superadmin ? 6 : 5 }}" class="text-center">Belum ada data kegiatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-list-ul mr-2"></i>Data Sub Kegiatan
                </h6>
                <button class="btn btn-light btn-sm shadow-sm" data-toggle="modal" data-target="#addSubKegiatanModal">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableSubKegiatan" data-custom-dt="1" style="font-size: 0.9rem;">
                        <thead style="background-color: #f8f9fc;">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                @if(auth()->user()->is_superadmin)
                                <th><i class="fas fa-building text-success mr-1"></i>Instansi</th>
                                @endif
                                <th><i class="fas fa-hashtag text-success mr-1"></i>ID Sub Giat</th>
                                <th><i class="fas fa-barcode text-success mr-1"></i>Kode</th>
                                <th><i class="fas fa-file-text text-success mr-1"></i>Nama</th>
                                <th><i class="fas fa-tasks text-success mr-1"></i>Kegiatan</th>
                                <th style="width: 120px;" class="text-center"><i class="fas fa-cogs text-success mr-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subKegiatans as $index => $subKegiatan)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                @if(auth()->user()->is_superadmin)
                                <td>{{ $subKegiatan->instansi }}</td>
                                @endif
                                <td>{{ $subKegiatan->id_sub_giat }}</td>
                                <td>{{ $subKegiatan->kode_sub_giat }}</td>
                                <td>{{ $subKegiatan->nama_sub_giat }}</td>
                                <td>{{ $subKegiatan->kegiatan?->kode_giat }} - {{ $subKegiatan->kegiatan?->nama_giat }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button
                                            type="button"
                                            class="btn btn-info btn-sm"
                                            title="Edit"
                                            onclick="openEditSubKegiatan({{ $subKegiatan->id }}, '{{ $subKegiatan->id_giat }}', '{{ $subKegiatan->id_sub_giat }}', '{{ $subKegiatan->kode_sub_giat }}', '{{ addslashes($subKegiatan->nama_sub_giat) }}')"
                                        ><i class="fas fa-edit"></i></button>
                                        <form method="POST" action="{{ route('master-rekening.sub-kegiatan.destroy', $subKegiatan->id) }}" class="d-inline" onsubmit="return confirm('Hapus sub kegiatan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ auth()->user()->is_superadmin ? 7 : 6 }}" class="text-center">Belum ada data sub kegiatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); border: none;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-calculator mr-2"></i>Data Kode Rekening
                </h6>
                <button class="btn btn-light btn-sm shadow-sm" data-toggle="modal" data-target="#addKodeRekeningModal">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableKodeRekening" data-custom-dt="1" style="font-size: 0.9rem;">
                        <thead style="background-color: #f8f9fc;">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                @if(auth()->user()->is_superadmin)
                                <th><i class="fas fa-building text-warning mr-1"></i>Instansi</th>
                                @endif
                                <th><i class="fas fa-hashtag text-warning mr-1"></i>ID Akun</th>
                                <th><i class="fas fa-barcode text-warning mr-1"></i>Kode Akun</th>
                                <th><i class="fas fa-file-invoice text-warning mr-1"></i>Nama Akun</th>
                                <th><i class="fas fa-list-ul text-warning mr-1"></i>Sub Kegiatan</th>
                                <th style="width: 80px;" class="text-center"><i class="fas fa-ban text-warning mr-1"></i>Blokir</th>
                                <th style="width: 120px;" class="text-center"><i class="fas fa-cogs text-warning mr-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kodeRekenings as $index => $rekening)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                @if(auth()->user()->is_superadmin)
                                <td>{{ $rekening->instansi }}</td>
                                @endif
                                <td>{{ $rekening->id_akun }}</td>
                                <td>{{ $rekening->kode_akun }}</td>
                                <td>{{ $rekening->nama_akun }}</td>
                                <td>{{ $rekening->subKegiatan?->kode_sub_giat }} - {{ $rekening->subKegiatan?->nama_sub_giat }}</td>
                                <td class="text-center">
                                    @if($rekening->is_blokir)
                                        <span class="badge badge-danger">Ya</span>
                                    @else
                                        <span class="badge badge-success">Tidak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button
                                            type="button"
                                            class="btn btn-info btn-sm"
                                            title="Edit"
                                            onclick="openEditKodeRekening({{ $rekening->id }}, '{{ $rekening->id_sub_giat }}', '{{ $rekening->id_akun }}', '{{ $rekening->kode_akun }}', '{{ addslashes($rekening->nama_akun) }}', {{ $rekening->is_blokir ? 'true' : 'false' }})"
                                        ><i class="fas fa-edit"></i></button>
                                        <form method="POST" action="{{ route('master-rekening.kode-rekening.destroy', $rekening->id) }}" class="d-inline" onsubmit="return confirm('Hapus kode rekening ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ auth()->user()->is_superadmin ? 8 : 7 }}" class="text-center">Belum ada data kode rekening.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('master-rekening.kegiatan.store') }}" class="modal-content border-left-primary">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-primary mr-1"></i>ID Giat</label>
                    <input type="number" name="id_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-barcode text-primary mr-1"></i>Kode Giat</label>
                    <input type="text" name="kode_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-text text-primary mr-1"></i>Nama Giat</label>
                    <input type="text" name="nama_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editKegiatanForm" class="modal-content border-left-info">
            @csrf
            @method('PUT')
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-info mr-1"></i>ID Giat</label>
                    <input type="number" name="id_giat" id="edit_kegiatan_id_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-barcode text-info mr-1"></i>Kode Giat</label>
                    <input type="text" name="kode_giat" id="edit_kegiatan_kode_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-text text-info mr-1"></i>Nama Giat</label>
                    <input type="text" name="nama_giat" id="edit_kegiatan_nama_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addSubKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('master-rekening.sub-kegiatan.store') }}" class="modal-content border-left-success">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Sub Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-tasks text-success mr-1"></i>Kegiatan</label>
                    <select name="id_giat" class="form-control" required>
                        <option value="">Pilih Kegiatan</option>
                        @foreach($kegiatans as $kegiatan)
                        <option value="{{ $kegiatan->id_giat }}">{{ $kegiatan->kode_giat }} - {{ $kegiatan->nama_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-success mr-1"></i>ID Sub Giat</label>
                    <input type="number" name="id_sub_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-barcode text-success mr-1"></i>Kode Sub Giat</label>
                    <input type="text" name="kode_sub_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-text text-success mr-1"></i>Nama Sub Giat</label>
                    <input type="text" name="nama_sub_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSubKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editSubKegiatanForm" class="modal-content border-left-info">
            @csrf
            @method('PUT')
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Sub Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-tasks text-info mr-1"></i>Kegiatan</label>
                    <select name="id_giat" id="edit_sub_kegiatan_id_giat" class="form-control" required>
                        <option value="">Pilih Kegiatan</option>
                        @foreach($kegiatans as $kegiatan)
                        <option value="{{ $kegiatan->id_giat }}">{{ $kegiatan->kode_giat }} - {{ $kegiatan->nama_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-info mr-1"></i>ID Sub Giat</label>
                    <input type="number" name="id_sub_giat" id="edit_sub_kegiatan_id_sub_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-barcode text-info mr-1"></i>Kode Sub Giat</label>
                    <input type="text" name="kode_sub_giat" id="edit_sub_kegiatan_kode_sub_giat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-text text-info mr-1"></i>Nama Sub Giat</label>
                    <input type="text" name="nama_sub_giat" id="edit_sub_kegiatan_nama_sub_giat" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addKodeRekeningModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('master-rekening.kode-rekening.store') }}" class="modal-content border-left-warning">
            @csrf
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Kode Rekening</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-list-ul text-warning mr-1"></i>Sub Kegiatan</label>
                    <select name="id_sub_giat" class="form-control" required>
                        <option value="">Pilih Sub Kegiatan</option>
                        @foreach($subKegiatans as $subKegiatan)
                        <option value="{{ $subKegiatan->id_sub_giat }}">{{ $subKegiatan->kode_sub_giat }} - {{ $subKegiatan->nama_sub_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-warning mr-1"></i>ID Akun</label>
                    <input type="number" name="id_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-barcode text-warning mr-1"></i>Kode Akun</label>
                    <input type="text" name="kode_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-invoice text-warning mr-1"></i>Nama Akun</label>
                    <input type="text" name="nama_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="is_blokir" value="1" id="add_is_blokir">
                        <label class="custom-control-label" for="add_is_blokir">
                            <i class="fas fa-ban text-danger mr-1"></i>Blokir
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editKodeRekeningModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editKodeRekeningForm" class="modal-content border-left-info">
            @csrf
            @method('PUT')
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Kode Rekening</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-list-ul text-info mr-1"></i>Sub Kegiatan</label>
                    <select name="id_sub_giat" id="edit_kode_rekening_id_sub_giat" class="form-control" required>
                        <option value="">Pilih Sub Kegiatan</option>
                        @foreach($subKegiatans as $subKegiatan)
                        <option value="{{ $subKegiatan->id_sub_giat }}">{{ $subKegiatan->kode_sub_giat }} - {{ $subKegiatan->nama_sub_giat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-hashtag text-info mr-1"></i>ID Akun</label>
                    <input type="number" name="id_akun" id="edit_kode_rekening_id_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-barcode text-info mr-1"></i>Kode Akun</label>
                    <input type="text" name="kode_akun" id="edit_kode_rekening_kode_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-file-invoice text-info mr-1"></i>Nama Akun</label>
                    <input type="text" name="nama_akun" id="edit_kode_rekening_nama_akun" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="is_blokir" value="1" id="edit_kode_rekening_is_blokir">
                        <label class="custom-control-label" for="edit_kode_rekening_is_blokir">
                            <i class="fas fa-ban text-danger mr-1"></i>Blokir
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditKegiatan(id, idGiat, kodeGiat, namaGiat) {
    document.getElementById('editKegiatanForm').action = `/master-rekening/kegiatan/${id}`;
    document.getElementById('edit_kegiatan_id_giat').value = idGiat;
    document.getElementById('edit_kegiatan_kode_giat').value = kodeGiat;
    document.getElementById('edit_kegiatan_nama_giat').value = namaGiat;
    $('#editKegiatanModal').modal('show');
}

function openEditSubKegiatan(id, idGiat, idSubGiat, kodeSubGiat, namaSubGiat) {
    document.getElementById('editSubKegiatanForm').action = `/master-rekening/sub-kegiatan/${id}`;
    document.getElementById('edit_sub_kegiatan_id_giat').value = idGiat;
    document.getElementById('edit_sub_kegiatan_id_sub_giat').value = idSubGiat;
    document.getElementById('edit_sub_kegiatan_kode_sub_giat').value = kodeSubGiat;
    document.getElementById('edit_sub_kegiatan_nama_sub_giat').value = namaSubGiat;
    $('#editSubKegiatanModal').modal('show');
}

function openEditKodeRekening(id, idSubGiat, idAkun, kodeAkun, namaAkun, isBlokir) {
    document.getElementById('editKodeRekeningForm').action = `/master-rekening/kode-rekening/${id}`;
    document.getElementById('edit_kode_rekening_id_sub_giat').value = idSubGiat;
    document.getElementById('edit_kode_rekening_id_akun').value = idAkun;
    document.getElementById('edit_kode_rekening_kode_akun').value = kodeAkun;
    document.getElementById('edit_kode_rekening_nama_akun').value = namaAkun;
    document.getElementById('edit_kode_rekening_is_blokir').checked = !!isBlokir;
    $('#editKodeRekeningModal').modal('show');
}

$(document).ready(function() {
    const isSuperadmin = {{ auth()->user()->is_superadmin ? 'true' : 'false' }};
    const dtConfig = {
        destroy: true,
        responsive: true,
        autoWidth: false,
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok",
            emptyTable: "Tidak ada data.",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    };

    if (!$.fn.DataTable.isDataTable('#dataTableKegiatan')) {
        const actionColKegiatan = isSuperadmin ? 5 : 4;
        $('#dataTableKegiatan').DataTable({
            ...dtConfig,
            columnDefs: [
                { orderable: false, targets: [actionColKegiatan] },
                { searchable: false, targets: [0, actionColKegiatan] }
            ]
        });
    }

    if (!$.fn.DataTable.isDataTable('#dataTableSubKegiatan')) {
        const actionColSubKegiatan = isSuperadmin ? 6 : 5;
        $('#dataTableSubKegiatan').DataTable({
            ...dtConfig,
            columnDefs: [
                { orderable: false, targets: [actionColSubKegiatan] },
                { searchable: false, targets: [0, actionColSubKegiatan] }
            ]
        });
    }

    if (!$.fn.DataTable.isDataTable('#dataTableKodeRekening')) {
        const actionColKodeRekening = isSuperadmin ? 7 : 6;
        $('#dataTableKodeRekening').DataTable({
            ...dtConfig,
            columnDefs: [
                { orderable: false, targets: [actionColKodeRekening] },
                { searchable: false, targets: [0, actionColKodeRekening] }
            ]
        });
    }
});
</script>
@endpush

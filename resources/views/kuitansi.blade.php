@extends('layouts.app')

@section('title', 'Kuitansi')

@section('content')
@push('styles')
    <style>
        th, td {
            vertical-align: middle !important;
        }
        .filter-panel {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: .35rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .kuitansi-checkbox,
        #selectAllCheckbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
            margin: 0;
            position: static;
        }
        .aksi-buttons .btn {
            min-width: 32px;
        }
    </style>
@endpush

<!-- Page Heading -->
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-receipt text-primary mr-2"></i>Kuitansi
        </h1>
    </div>
    <div class="col-md-6 text-right">
        <button class="btn btn-primary" data-toggle="modal" data-target="#selectRekeningModal">
            <i class="fas fa-plus mr-2"></i>Tambah Kuitansi
        </button>
    </div>
</div>

<!-- DataTable Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none;">
        <div class="d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-white mr-3">
                <i class="fas fa-table mr-2"></i>Data Kuitansi
            </h6>
            <span class="badge badge-light">{{ count($kuitansis) }} data</span>
        </div>
        <button class="btn btn-sm btn-success" id="exportXmlBtn" style="display:none;" title="Export XML dari kuitansi yang dipilih">
            <i class="fas fa-download mr-1"></i>Export XML (<span id="selectedCount">0</span>)
        </button>
    </div>
    <div class="card-body">
        <div class="filter-panel">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter mr-1"></i>Filter Data
                </h6>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="filter_no_buku" class="small font-weight-bold">No. Buku</label>
                    <input type="text" class="form-control" id="filter_no_buku" placeholder="Contoh: TU-1-001">
                </div>
                <div class="form-group col-md-3">
                    <label for="filter_rekening" class="small font-weight-bold">Nomor Rekening</label>
                    <input type="text" class="form-control" id="filter_rekening" placeholder="Cari rekening">
                </div>
                <div class="form-group col-md-3">
                    <label for="filter_penerima" class="small font-weight-bold">Nama Penerima</label>
                    <input type="text" class="form-control" id="filter_penerima" placeholder="Cari penerima">
                </div>
                <div class="form-group col-md-3">
                    <label for="filter_pembayaran" class="small font-weight-bold">Untuk Pembayaran</label>
                    <input type="text" class="form-control" id="filter_pembayaran" placeholder="Cari pembayaran">
                </div>
            </div>
            <div class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label for="filter_tanggal_mulai" class="small font-weight-bold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="filter_tanggal_mulai">
                </div>
                <div class="form-group col-md-3">
                    <label for="filter_tanggal_selesai" class="small font-weight-bold">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="filter_tanggal_selesai">
                </div>
                <div class="form-group col-md-6 text-md-right">
                    <button type="button" class="btn btn-outline-secondary mr-2" id="resetFilterBtn">
                        <i class="fas fa-undo mr-1"></i>Reset Filter
                    </button>
                    <button type="button" class="btn btn-primary" id="applyFilterBtn">
                        <i class="fas fa-check mr-1"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" data-custom-dt="1" style="font-size: 0.9rem;">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th width="40px" class="text-center">
                            <input type="checkbox" id="selectAllCheckbox" title="Pilih semua">
                        </th>
                        <th>No</th>
                        <th>No. Buku</th>
                        <th>Nomor Rekening</th>
                        <th>Untuk Pembayaran</th>
                        <th>Grand Total</th>
                        <th>Nama Penerima</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kuitansis as $kuitansi)
                    <tr class="kuitansi-row"
                        data-no-buku="{{ strtolower($kuitansi->no_buku ?? '') }}"
                        data-rekening="{{ strtolower($kuitansi->nomor_rekening ?? '') }}"
                        data-penerima="{{ strtolower($kuitansi->nama_penerima ?? '') }}"
                        data-pembayaran="{{ strtolower($kuitansi->untuk_pembayaran ?? '') }}"
                        data-tanggal="{{ $kuitansi->tanggal_kuitansi }}">
                        <td class="text-center">
                            <input type="checkbox" class="kuitansi-checkbox" value="{{ $kuitansi->id }}" title="Pilih kuitansi ini">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td data-search="{{ $kuitansi->no_buku }}">
                            <span class="badge badge-primary">{{ $kuitansi->no_buku }}</span>
                        </td>
                        <td data-search="{{ $kuitansi->nomor_rekening }}">
                            <small class="text-muted">{{ $kuitansi->nomor_rekening }}</small>
                        </td>
                        <td data-search="{{ $kuitansi->untuk_pembayaran }}">
                            <small>{{ \Illuminate\Support\Str::limit($kuitansi->untuk_pembayaran, 80) }}</small>
                        </td>
                        <td data-search="{{ (int)($kuitansi->total_akhir ?? 0) }}">
                            <strong class="text-success">Rp {{ number_format((int)($kuitansi->total_akhir ?? 0), 0, ',', '.') }}</strong>
                        </td>
                        <td data-search="{{ $kuitansi->nama_penerima }}">{{ $kuitansi->nama_penerima }}</td>
                        <td data-search="{{ $kuitansi->tanggal_kuitansi }} {{ $kuitansi->tanggal_kuitansi ? \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('d/m/Y') : '' }}" data-raw-date="{{ $kuitansi->tanggal_kuitansi }}">
                            <small>{{ $kuitansi->tanggal_kuitansi ? \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('d/m/Y') : '-' }}</small>
                        </td>
                        <td class="text-center">
                            <div class="aksi-buttons d-inline-flex">
                                <button class="btn btn-info edit-btn" 
                                        title="Edit"
                                        data-id="{{ $kuitansi->id }}" 
                                        data-rekening="{{ $kuitansi->nomor_rekening }}" 
                                        data-periode_type="{{ $kuitansi->periode_type }}"
                                        data-periode_number="{{ $kuitansi->periode_number }}"
                                        data-rekanan_id="{{ $kuitansi->rekanan_id }}" 
                                        data-jenis_pph="{{ $kuitansi->jenis_pph }}"
                                        data-untuk_pembayaran="{{ $kuitansi->untuk_pembayaran }}"
                                        data-toggle="modal" 
                                        data-target="#editkuitansiModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('kuitansi.preview', $kuitansi->id) }}" class="btn btn-warning" target="_blank" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('kuitansi.destroy', $kuitansi->id) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit" title="Hapus">
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

<!-- Select Rekening Modal -->
<div class="modal fade" id="selectRekeningModal" tabindex="-1" role="dialog" aria-labelledby="selectRekeningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="selectRekeningModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>Pilih Kode Rekening
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Langkah 1:</strong> Pilih Kegiatan, Sub Kegiatan, dan Kode Rekening terlebih dahulu
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div class="form-group">
                    <label for="select_kegiatan" class="font-weight-bold">Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_kegiatan" required>
                        <option value="">-- Pilih Kegiatan --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_sub_kegiatan" class="font-weight-bold">Sub Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_sub_kegiatan" required disabled>
                        <option value="">-- Pilih Sub Kegiatan --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_kode_rekening" class="font-weight-bold">Kode Rekening <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_kode_rekening" required disabled>
                        <option value="">-- Pilih Kode Rekening --</option>
                    </select>
                    <small class="form-text text-muted">Kode akun akan diisi otomatis ke form kuitansi</small>
                </div>

                <div id="selected_rekening_info" class="alert alert-success mt-3" style="display:none;">
                    <strong>✓ Rekening Terpilih:</strong>
                    <p class="mb-1"><strong>Kode:</strong> <span id="info_kode_akun"></span></p>
                    <p class="mb-0"><strong>Nama:</strong> <span id="info_nama_akun"></span></p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button class="btn btn-primary" type="button" id="btnLanjutKeForm" disabled>
                    <i class="fas fa-arrow-right mr-1"></i>Lanjut ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Kuitansi Modal -->
<div class="modal fade" id="addkuitansiModal" tabindex="-1" role="dialog" aria-labelledby="addkuitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addkuitansiModalLabel">
                    <i class="fas fa-file-invoice mr-2"></i>Form Kuitansi
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('kuitansi.store') }}">
                @csrf
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="alert alert-warning" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Kode Rekening:</strong> <span id="display_kode_rekening" class="badge badge-warning"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-dark" id="btnGantiRekening">
                                <i class="fas fa-edit mr-1"></i>Ganti
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" id="nomor_rekening" name="nomor_rekening" required>
                    <input type="hidden" id="selected_id_akun" name="id_akun">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_kuitansi" class="font-weight-bold">Tanggal Kuitansi</label>
                                <input type="date" class="form-control" id="tanggal_kuitansi" name="tanggal_kuitansi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="periode_lengkap" class="font-weight-bold">Periode <span class="text-danger">*</span></label>
                                <select class="form-control" id="periode_lengkap" name="periode_lengkap" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @php $periodes = ['TU', 'GU']; @endphp
                                    @foreach($periodes as $tipe)
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $tipe }}-{{ $i }}">{{ $tipe }} {{ $i }}</option>
                                        @endfor
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_urut" class="font-weight-bold">Nomor Kuitansi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_urut" name="nomor_urut" placeholder="001" maxlength="3" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_pph" class="font-weight-bold">Jenis PPH</label>
                                <select class="form-control" id="jenis_pph" name="jenis_pph">
                                    <option value="">-- Pilih Jenis PPH --</option>
                                    <option value="22">PPH 22</option>
                                    <option value="23">PPH 23</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rekanan_id" class="font-weight-bold">Penerima (Rekanan) <span class="text-danger">*</span></label>
                        <select class="form-control" id="rekanan_id" name="rekanan_id" required>
                            <option value="">-- Pilih Rekanan --</option>
                            @foreach($rekanans as $rekanan)
                                <option value="{{ $rekanan->id }}" data-npwp="{{ $rekanan->npwp }}">
                                    {{ $rekanan->nama_perusahaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kode_objek_pajak" class="font-weight-bold">Kode Objek Pajak</label>
                        <input type="text" class="form-control" id="kode_objek_pajak" name="kode_objek_pajak" list="kodeObjekPajakList" placeholder="Cari kode objek pajak..." autocomplete="off">
                        <small class="form-text text-muted">Hanya perlu diisi jika belanja ≥ 2.000.000</small>
                    </div>
                    <input type="hidden" id="tarif_pajak" name="tarif_pajak" value="0">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dpp_display" class="font-weight-bold">DPP (Dasar Pengenaan Pajak)</label>
                                <input type="text" class="form-control" id="dpp_display" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pph_nominal" class="font-weight-bold">PPH yang Dipotong</label>
                                <input type="text" class="form-control" id="pph_nominal" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ppn_checkbox" class="font-weight-bold">
                            <input type="checkbox" id="ppn_checkbox" name="ppn_checkbox"> 
                            Tambahkan PPN 11%
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="total_akhir_display" class="font-weight-bold">Total Akhir</label>
                        <input type="text" class="form-control form-control-lg" id="total_akhir_display" value="Rp 0" readonly style="font-weight: bold; background-color: #e8f4f8;">
                    </div>

                    <div class="form-group">
                        <label for="untuk_pembayaran" class="font-weight-bold">Untuk Pembayaran <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="untuk_pembayaran" name="untuk_pembayaran" rows="3" placeholder="Jelaskan tujuan pembayaran..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="pptk_1_id" class="font-weight-bold">PPTK <span class="text-danger">*</span></label>
                        <select class="form-control" id="pptk_1_id" name="pptk_1_id" required>
                            <option value="">-- Pilih PPTK --</option>
                            @foreach($pptks as $pptk)
                                <option value="{{ $pptk->id }}">{{ $pptk->nama }} - {{ $pptk->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bendahara_checkbox">
                            <input type="checkbox" id="bendahara_checkbox" name="bendahara_checkbox"> 
                            <strong>Tambahkan Nama Bendahara Barang</strong>
                        </label>
                    </div>
                    <div id="bendahara_info" class="alert alert-info" style="display:none;">
                        <strong>Bendahara Barang:</strong> <span id="display_bendahara_nama"></span>
                    </div>
                    <input type="hidden" id="nama_bendahara_barang" name="nama_bendahara_barang">
                    <input type="hidden" id="nip_bendahara_barang" name="nip_bendahara_barang">

                    <div class="form-group">
                        <label class="font-weight-bold">Item Barang</label>
                        <table class="table table-sm table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">
                            <i class="fas fa-plus mr-1"></i>Tambah Item
                        </button>
                    </div>
                    <input type="hidden" id="rincian_item_json" name="rincian_item_json" value="[]">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="button" onclick="updateAddFormBefore(event)">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Kuitansi Modal -->
<div class="modal fade" id="editkuitansiModal" tabindex="-1" role="dialog" aria-labelledby="editkuitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-left-info">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editkuitansiModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Kuitansi
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tanggal_kuitansi" class="font-weight-bold">Tanggal Kuitansi</label>
                                <input type="date" class="form-control" id="edit_tanggal_kuitansi" name="tanggal_kuitansi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_periode_lengkap" class="font-weight-bold">Periode <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_periode_lengkap" name="periode_lengkap" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @php $periodes = ['TU', 'GU']; @endphp
                                    @foreach($periodes as $tipe)
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $tipe }}-{{ $i }}">{{ $tipe }} {{ $i }}</option>
                                        @endfor
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nomor_urut" class="font-weight-bold">Nomor Kuitansi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nomor_urut" name="nomor_urut" placeholder="001" maxlength="3" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_jenis_pph" class="font-weight-bold">Jenis PPH</label>
                                <select class="form-control" id="edit_jenis_pph" name="jenis_pph">
                                    <option value="">-- Pilih Jenis PPH --</option>
                                    <option value="22">PPH 22</option>
                                    <option value="23">PPH 23</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_rekanan_id" class="font-weight-bold">Penerima (Rekanan) <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_rekanan_id" name="rekanan_id" required>
                            <option value="">-- Pilih Rekanan --</option>
                            @foreach($rekanans as $rekanan)
                                <option value="{{ $rekanan->id }}" data-npwp="{{ $rekanan->npwp }}">
                                    {{ $rekanan->nama_perusahaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_kode_objek_pajak" class="font-weight-bold">Kode Objek Pajak</label>
                        <input type="text" class="form-control" id="edit_kode_objek_pajak" name="kode_objek_pajak" list="kodeObjekPajakList" placeholder="Cari kode objek pajak..." autocomplete="off">
                        <small class="form-text text-muted">Hanya perlu diisi jika belanja ≥ 2.000.000</small>
                    </div>
                    <input type="hidden" id="edit_tarif_pajak" name="tarif_pajak" value="0">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_dpp_display" class="font-weight-bold">DPP</label>
                                <input type="text" class="form-control" id="edit_dpp_display" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_pph_nominal" class="font-weight-bold">PPH yang Dipotong</label>
                                <input type="text" class="form-control" id="edit_pph_nominal" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_ppn_checkbox">
                            <input type="checkbox" id="edit_ppn_checkbox" name="edit_ppn_checkbox"> 
                            <strong>Tambahkan PPN 11%</strong>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="edit_total_akhir_display" class="font-weight-bold">Total Akhir</label>
                        <input type="text" class="form-control form-control-lg" id="edit_total_akhir_display" value="Rp 0" readonly style="font-weight: bold; background-color: #e8f4f8;">
                    </div>

                    <div class="form-group">
                        <label for="edit_untuk_pembayaran" class="font-weight-bold">Untuk Pembayaran <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_untuk_pembayaran" name="untuk_pembayaran" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_pptk_1_id" class="font-weight-bold">PPTK <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_pptk_1_id" name="pptk_1_id" required>
                            <option value="">-- Pilih PPTK --</option>
                            @foreach($pptks as $pptk)
                                <option value="{{ $pptk->id }}">{{ $pptk->nama }} - {{ $pptk->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_bendahara_checkbox">
                            <input type="checkbox" id="edit_bendahara_checkbox" name="edit_bendahara_checkbox"> 
                            <strong>Tambahkan Nama Bendahara Barang</strong>
                        </label>
                    </div>
                    <div id="edit_bendahara_info" class="alert alert-info" style="display:none;">
                        <strong>Bendahara Barang:</strong> <span id="edit_display_bendahara_nama"></span>
                    </div>
                    <input type="hidden" id="edit_nama_bendahara_barang" name="nama_bendahara_barang">
                    <input type="hidden" id="edit_nip_bendahara_barang" name="nip_bendahara_barang">

                    <div class="form-group">
                        <label class="font-weight-bold">Item Barang</label>
                        <table class="table table-sm table-bordered" id="editItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="editItemsBody"></tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addEditItemRow()">
                            <i class="fas fa-plus mr-1"></i>Tambah Item
                        </button>
                    </div>
                    <input type="hidden" id="edit_rincian_item_json" name="rincian_item_json" value="[]">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-info" type="button" onclick="updateEditFormBefore(event)">
                        <i class="fas fa-save mr-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Datalist for Kode Objek Pajak -->
<datalist id="kodeObjekPajakList">
    @foreach($kodeObjekPajaks as $kop)
        <option value="{{ $kop->kode }}">{{ $kop->kode }} - {{ $kop->nama }} ({{ $kop->tarif }}%)</option>
    @endforeach
</datalist>

@endsection

@push('scripts')
    <script src="{{ asset('js/kuitansi-form.js') }}"></script>
    
    <script>
    let itemCounter = 0;
    let editItemCounter = 0;
    let selectedRekeningData = { id_akun: null, kode_akun: null, nama_akun: null };

    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#dataTable').DataTable({
            destroy: true,
            responsive: true,
            autoWidth: false,
            dom: 'lrtip',
            columnDefs: [
                { orderable: false, targets: [0, 8] },
                { searchable: false, targets: [0, 8] },
            ],
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                emptyTable: "Tidak ada data Kuitansi.",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        });

        function normalizeText(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function extractText(cellHtml) {
            return normalizeText($('<div>').html(cellHtml || '').text());
        }

        function parseDdMmYyyy(dateText) {
            if (!dateText) return null;
            const m = dateText.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!m) return null;
            const d = new Date(Number(m[3]), Number(m[2]) - 1, Number(m[1]));
            d.setHours(0, 0, 0, 0);
            return d;
        }

        function parseYyyyMmDd(dateText) {
            if (!dateText) return null;
            const d = new Date(dateText);
            if (isNaN(d.getTime())) return null;
            d.setHours(0, 0, 0, 0);
            return d;
        }

        $.fn.dataTable.ext.search.push(function (settings, data) {
            if (settings.nTable.id !== 'dataTable') return true;

            const noBukuFilter = normalizeText($('#filter_no_buku').val());
            const rekeningFilter = normalizeText($('#filter_rekening').val());
            const penerimaFilter = normalizeText($('#filter_penerima').val());
            const pembayaranFilter = normalizeText($('#filter_pembayaran').val());

            const start = $('#filter_tanggal_mulai').val();
            const end = $('#filter_tanggal_selesai').val();

            // Kolom: 0 checkbox, 1 no, 2 no_buku, 3 rekening, 4 pembayaran, 5 total, 6 penerima, 7 tanggal, 8 aksi
            const rowNoBuku = extractText(data[2]);
            const rowRekening = extractText(data[3]);
            const rowPembayaran = extractText(data[4]);
            const rowPenerima = extractText(data[6]);
            const rowDateText = extractText(data[7]);

            if (noBukuFilter && !rowNoBuku.includes(noBukuFilter)) return false;
            if (rekeningFilter && !rowRekening.includes(rekeningFilter)) return false;
            if (penerimaFilter && !rowPenerima.includes(penerimaFilter)) return false;
            if (pembayaranFilter && !rowPembayaran.includes(pembayaranFilter)) return false;

            if (!start && !end) return true;
            const rowDate = parseDdMmYyyy(rowDateText);
            if (!rowDate) return false;

            const startDate = parseYyyyMmDd(start);
            const endDate = parseYyyyMmDd(end);

            if (startDate && rowDate < startDate) return false;
            if (endDate && rowDate > endDate) return false;
            return true;
        });

        function applyFilters() {
            table.draw();
        }

        $('#applyFilterBtn').on('click', function () {
            applyFilters();
        });

        $('#filter_no_buku, #filter_rekening, #filter_pembayaran, #filter_penerima').on('keyup', function (e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        $('#filter_tanggal_mulai, #filter_tanggal_selesai').on('change', function () {
            table.draw();
        });

        $('#resetFilterBtn').on('click', function () {
            $('#filter_no_buku, #filter_rekening, #filter_penerima, #filter_pembayaran, #filter_tanggal_mulai, #filter_tanggal_selesai').val('');
            table.draw();
        });

        // Select all checkboxes
        $('#selectAllCheckbox').on('change', function() {
            $('.kuitansi-checkbox').prop('checked', $(this).prop('checked'));
            updateExportButton();
        });

        // Individual checkbox change
        $('.kuitansi-checkbox').on('change', function() {
            updateExportButton();
        });

        // Export XML button
        $('#exportXmlBtn').on('click', function() {
            const selectedIds = [];
            $('.kuitansi-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal 1 kuitansi', 'warning');
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang membuat file XML...',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("kuitansi.exportBupotXmlSelected") }}';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="kuitansi_ids" value='${JSON.stringify(selectedIds)}'>
            `;
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            setTimeout(() => {
                Swal.fire('Berhasil!', 'File XML telah didownload', 'success');
            }, 1000);
        });
    });

    function updateExportButton() {
        const selectedCount = $('.kuitansi-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        $('#exportXmlBtn').toggle(selectedCount > 0);
    }

    function truncateText(text, maxLength = 50) {
        return text && text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }

    $('#selectRekeningModal').on('shown.bs.modal', loadKegiatan);

    function loadKegiatan() {
        $.ajax({
            url: '{{ route("api.kegiatan") }}',
            success: function(data) {
                let options = '<option value="">-- Pilih Kegiatan --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.kode} - ${item.nama}</option>`;
                });
                $('#select_kegiatan').html(options);
            }
        });
    }

    $('#select_kegiatan').on('change', function() {
        const idGiat = $(this).val();
        $('#select_sub_kegiatan').prop('disabled', !idGiat).html('<option value="">-- Pilih Sub Kegiatan --</option>');
        $('#select_kode_rekening').prop('disabled', true).html('<option value="">-- Pilih Kode Rekening --</option>');
        if (idGiat) loadSubKegiatan(idGiat);
    });

    function loadSubKegiatan(idGiat) {
        $.ajax({
            url: '{{ route("api.subKegiatan") }}',
            data: { id_giat: idGiat },
            success: function(data) {
                let options = '<option value="">-- Pilih Sub Kegiatan --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.kode} - ${item.nama}</option>`;
                });
                $('#select_sub_kegiatan').html(options);
            }
        });
    }

    $('#select_sub_kegiatan').on('change', function() {
        const idSubGiat = $(this).val();
        if (idSubGiat) loadKodeRekening(idSubGiat);
    });

    function loadKodeRekening(idSubGiat) {
        $.ajax({
            url: '{{ route("api.kodeRekening") }}',
            data: { id_sub_giat: idSubGiat },
            success: function(data) {
                let options = '<option value="">-- Pilih Kode Rekening --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}" data-kode="${item.kode}" data-nama="${item.nama}">${item.kode} - ${item.nama}</option>`;
                });
                $('#select_kode_rekening').prop('disabled', false).html(options);
            }
        });
    }

    $('#select_kode_rekening').on('change', function() {
        const selected = $(this).find('option:selected');
        const idAkun = $(this).val();
        if (idAkun) {
            selectedRekeningData = {
                id_akun: idAkun,
                kode_akun: selected.data('kode'),
                nama_akun: selected.data('nama')
            };
            $('#info_kode_akun').text(selectedRekeningData.kode_akun);
            $('#info_nama_akun').text(selectedRekeningData.nama_akun);
            $('#selected_rekening_info').fadeIn();
            $('#btnLanjutKeForm').prop('disabled', false);
        }
    });

    $('#btnLanjutKeForm').on('click', function() {
        if (selectedRekeningData.kode_akun) {
            $('#nomor_rekening').val(selectedRekeningData.kode_akun);
            $('#selected_id_akun').val(selectedRekeningData.id_akun);
            $('#display_kode_rekening').text(truncateText(selectedRekeningData.kode_akun + ' - ' + selectedRekeningData.nama_akun));
            $('#selectRekeningModal').modal('hide');
            setTimeout(() => $('#addkuitansiModal').modal('show'), 300);
        }
    });

    $('#btnGantiRekening').on('click', function() {
        $('#addkuitansiModal').modal('hide');
        setTimeout(() => $('#selectRekeningModal').modal('show'), 300);
    });

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const rowId = 'item_' + itemCounter++;
        const row = document.createElement('tr');
        row.id = rowId;
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm item-name" placeholder="Nama item"></td>
            <td><input type="number" class="form-control form-control-sm item-qty" placeholder="Jumlah" min="1" step="1"></td>
            <td><input type="number" class="form-control form-control-sm item-price" placeholder="Harga satuan" min="0" step="0.01"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
    }

    function addEditItemRow() {
        const tbody = document.getElementById('editItemsBody');
        const rowId = 'edit_item_' + editItemCounter++;
        const row = document.createElement('tr');
        row.id = rowId;
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm item-name" placeholder="Nama item"></td>
            <td><input type="number" class="form-control form-control-sm item-qty" placeholder="Jumlah" min="1" step="1"></td>
            <td><input type="number" class="form-control form-control-sm item-price" placeholder="Harga satuan" min="0" step="0.01"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(row);
    }

    function removeItemRow(rowId) {
        document.getElementById(rowId).remove();
    }

    function updateItemsJson() {
        const items = [];
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const name = row.querySelector('.item-name').value;
            const qty = row.querySelector('.item-qty').value;
            const price = row.querySelector('.item-price').value;
            if (name && qty && price) {
                items.push({ nama: name, jumlah: parseInt(qty), harga_satuan: parseFloat(price) });
            }
        });
        document.getElementById('rincian_item_json').value = JSON.stringify(items);
    }

    function updateAddFormBefore(event) {
        event.preventDefault();
        if (!document.getElementById('bendahara_checkbox').checked) {
            document.getElementById('nama_bendahara_barang').value = '';
            document.getElementById('nip_bendahara_barang').value = '';
        }
        updateItemsJson();
        document.querySelector('#addkuitansiModal form').submit();
    }

    function updateEditFormBefore(event) {
        event.preventDefault();
        if (!document.getElementById('edit_bendahara_checkbox').checked) {
            document.getElementById('edit_nama_bendahara_barang').value = '';
            document.getElementById('edit_nip_bendahara_barang').value = '';
        }
        updateEditItemsJson();
        document.getElementById('editForm').submit();
    }

    function updateEditItemsJson() {
        const items = [];
        document.querySelectorAll('#editItemsBody tr').forEach(row => {
            const name = row.querySelector('.item-name').value;
            const qty = row.querySelector('.item-qty').value;
            const price = row.querySelector('.item-price').value;
            if (name && qty && price) {
                items.push({ nama: name, jumlah: parseInt(qty), harga_satuan: parseFloat(price) });
            }
        });
        document.getElementById('edit_rincian_item_json').value = JSON.stringify(items);
    }

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.get('/kuitansi/' + id + '/edit', function(data) {
            $('#edit_nomor_urut').val(String(data.nomor_urut).padStart(3, '0'));
            $('#edit_periode_lengkap').val(formatPeriodeLengkap(data.periode_type, data.periode_number)).trigger('change');
            $('#edit_rekanan_id').val(data.rekanan_id).trigger('change');
            $('#edit_tanggal_kuitansi').val(data.tanggal_kuitansi);
            $('#edit_jenis_pph').val(data.jenis_pph || '').trigger('change');
            $('#edit_untuk_pembayaran').val(data.untuk_pembayaran);
            $('#edit_pptk_1_id').val(data.pptk_1_id).trigger('change');
            
            // Populate kode_objek_pajak with full formatted text
            if (data.kode_objek_pajak) {
                const kode = data.kode_objek_pajak;
                const option = $(`#kodeObjekPajakList option[value="${kode}"]`);
                
                if (option.length > 0) {
                    // Set full text format: "kode - nama (tarif%)"
                    $('#edit_kode_objek_pajak').val(option.text());
                } else {
                    // Fallback: just set the code
                    $('#edit_kode_objek_pajak').val(kode);
                }
                
                // Set hidden tarif field
                if (data.tarif_pajak) {
                    $('#edit_tarif_pajak').val(data.tarif_pajak);
                }
            } else {
                $('#edit_kode_objek_pajak').val('');
                $('#edit_tarif_pajak').val('0');
            }
            
            $('#editForm').attr('action', '/kuitansi/' + id);
        });
    });

    function formatPeriodeLengkap(type, number) {
        return `${type}-${number}`;
    }
    </script>
@endpush

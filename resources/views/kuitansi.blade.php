@extends('layouts.app')

@section('title', 'Kuitansi')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kuitansi</h1>
    <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#selectRekeningModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kuitansi
    </button>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Kuitansi</h6>
        <button class="btn btn-sm btn-success" id="exportXmlBtn" style="display:none;" title="Export XML dari kuitansi yang dipilih">
            <i class="fas fa-download fa-sm"></i> Export XML (<span id="selectedCount">0</span>)
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="40px"><input type="checkbox" id="selectAllCheckbox" title="Pilih semua"></th>
                        <th>No</th>
                        <th>No. Buku</th>
                        <th>Nomor Rekening</th>
                        <th>Untuk Pembayaran</th>
                        <th>Grand Total</th>
                        <th>Nama Penerima</th>
                        <th>Tanggal Kuitansi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th width="40px"><input type="checkbox" id="selectAllCheckbox2" title="Pilih semua"></th>
                        <th>No</th>
                        <th>No. Buku</th>
                        <th>Nomor Rekening</th>
                        <th>Untuk Pembayaran</th>
                        <th>Grand Total</th>
                        <th>Nama Penerima</th>
                        <th>Tanggal Kuitansi</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($kuitansis as $kuitansi)
                    <tr class="kuitansi-row">
                        <td><input type="checkbox" class="kuitansi-checkbox" value="{{ $kuitansi->id }}" title="Pilih kuitansi ini"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $kuitansi->no_buku }}</strong></td>
                        <td>{{ $kuitansi->nomor_rekening }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($kuitansi->untuk_pembayaran, 100) }}</td>
                        <td>Rp {{ number_format((int)($kuitansi->total_akhir ?? 0), 0, ',', '.') }}</td>
                        <td>{{ $kuitansi->nama_penerima }}</td>
                        <td>{{ $kuitansi->tanggal_kuitansi ? \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('d-m-Y') : '-' }}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" 
                                    title="Edit"
                                    data-id="{{ $kuitansi->id }}" 
                                    data-rekening="{{ $kuitansi->nomor_rekening }}" 
                                    data-periode_type="{{ $kuitansi->periode_type }}"
                                    data-periode_number="{{ $kuitansi->periode_number }}"
                                    data-rekanan_id="{{ $kuitansi->rekanan_id }}" 
                                    data-jenis_pph="{{ $kuitansi->jenis_pph }}"
                                    data-untuk_pembayaran="{{ $kuitansi->untuk_pembayaran }}"
                                    data-toggle="modal" 
                                    data-target="#editkuitansiModal"><i class="fas fa-edit"></i></button>
                            <a href="{{ route('kuitansi.preview', $kuitansi->id) }}" class="btn btn-sm btn-warning" target="_blank" title="Preview"><i class="fas fa-eye"></i></a>
                            <form method="POST" action="{{ route('kuitansi.destroy', $kuitansi->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Select Rekening Modal (Tingkat 1) -->
<div class="modal fade" id="selectRekeningModal" tabindex="-1" role="dialog" aria-labelledby="selectRekeningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="selectRekeningModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>Pilih Kode Rekening
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Langkah 1:</strong> Pilih Kegiatan, Sub Kegiatan, dan Kode Rekening terlebih dahulu
                </div>
                
                <div class="form-group">
                    <label for="select_kegiatan">Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_kegiatan" required>
                        <option value="">-- Pilih Kegiatan --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_sub_kegiatan">Sub Kegiatan <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_sub_kegiatan" required disabled>
                        <option value="">-- Pilih Sub Kegiatan --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="select_kode_rekening">Kode Rekening <span class="text-danger">*</span></label>
                    <select class="form-control" id="select_kode_rekening" required disabled>
                        <option value="">-- Pilih Kode Rekening --</option>
                    </select>
                    <small class="form-text text-muted">Kode akun akan diisi otomatis ke form kuitansi</small>
                </div>

                <div id="selected_rekening_info" class="alert alert-success mt-3" style="display:none;">
                    <h6 class="font-weight-bold">Rekening Terpilih:</h6>
                    <p class="mb-1"><strong>Kode:</strong> <span id="info_kode_akun"></span></p>
                    <p class="mb-0"><strong>Nama:</strong> <span id="info_nama_akun"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button class="btn btn-primary" type="button" id="btnLanjutKeForm" disabled>
                    <i class="fas fa-arrow-right mr-1"></i>Lanjut ke Form Kuitansi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add kuitansi Modal (Tingkat 2) -->
<div class="modal fade" id="addkuitansiModal" tabindex="-1" role="dialog" aria-labelledby="addkuitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addkuitansiModalLabel">
                    <i class="fas fa-file-invoice mr-2"></i>Form Kuitansi
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('kuitansi.store') }}">
                @csrf
                <div class="modal-body" style="max-height: 550px; overflow-y: auto;">
                    <div class="alert alert-warning d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Kode Rekening:</strong> <span id="display_kode_rekening" class="font-weight-bold" style="color: #333 !important;"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="btnGantiRekening">
                            <i class="fas fa-edit"></i> Ganti Rekening
                        </button>
                    </div>
                    
                    <input type="hidden" id="nomor_rekening" name="nomor_rekening" required>
                    <input type="hidden" id="selected_id_akun" name="id_akun">
                    
                    <div class="form-group">
                        <label for="tanggal_kuitansi">Tanggal Kuitansi</label>
                        <input type="date" class="form-control" id="tanggal_kuitansi" name="tanggal_kuitansi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="periode_lengkap">Periode <span class="text-danger">*</span></label>
                                <select class="form-control" id="periode_lengkap" name="periode_lengkap" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @php
                                        $periodes = ['TU', 'GU'];
                                    @endphp
                                    @foreach($periodes as $tipe)
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $tipe }}-{{ $i }}">{{ $tipe }} {{ $i }}</option>
                                        @endfor
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Contoh: TU 1, GU 2</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_urut">Nomor Kuitansi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_urut" name="nomor_urut" placeholder="001" maxlength="3" required>
                                <small class="form-text text-muted">Urut: 001, 002, 003, dst</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rekanan_id">Penerima (Rekanan)</label>
                        <select class="form-control" id="rekanan_id" name="rekanan_id" required>
                            <option value="">-- Pilih Rekanan --</option>
                            @foreach($rekanans as $rekanan)
                                <option value="{{ $rekanan->id }}" 
                                        data-perusahaan="{{ $rekanan->nama_perusahaan }}"
                                        data-npwp="{{ $rekanan->npwp }}"
                                        data-bank="{{ $rekanan->bank }}"
                                        data-rekening="{{ $rekanan->nomor_rekening }}">
                                    {{ $rekanan->nama_perusahaan }} ({{ $rekanan->npwp }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Pilih rekanan sebagai penerima kuitansi</small>
                    </div>
                    <div class="form-group">
                        <label for="jenis_pph">Jenis PPH</label>
                        <select class="form-control" id="jenis_pph" name="jenis_pph">
                            <option value="">-- Pilih Jenis PPH --</option>
                            <option value="22">PPH 22</option>
                            <option value="23">PPH 23</option>
                        </select>
                        <small class="form-text text-muted">PPH dihitung otomatis sesuai NPWP rekanan</small>
                    </div>
                    <div class="form-group">
                        <label for="kode_objek_pajak">Kode Objek Pajak</label>
                        <input type="text" 
                               class="form-control" 
                               id="kode_objek_pajak" 
                               name="kode_objek_pajak" 
                               placeholder="Cari kode objek pajak..."
                               list="kodeObjekPajakList"
                               autocomplete="off">
                        <datalist id="kodeObjekPajakList">
                            @php
                                $kodeObjekPajaks = \DB::table('kode_objek_pajaks')->orderBy('kode')->get();
                            @endphp
                            @foreach($kodeObjekPajaks as $kop)
                                <option value="{{ $kop->kode }}" label="{{ $kop->nama }} ({{ $kop->tarif }}%)">
                            @endforeach
                        </datalist>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Hanya perlu diisi jika belanja ≥ 2.000.000 (untuk membuat BuPot)
                        </small>
                    </div>
                    <!-- DPP Display -->
                    <div class="form-group">
                        <label for="dpp_display">DPP (Dasar Pengenaan Pajak)</label>
                        <input type="text" 
                               class="form-control" 
                               id="dpp_display" 
                               name="dpp_display" 
                               value="Rp 0" 
                               readonly>
                        <small class="form-text text-muted">Jumlah dari semua item</small>
                    </div>

                    <!-- PPN Checkbox dan Input -->
                    <div class="form-group">
                        <label for="ppn_checkbox">
                            <input type="checkbox" id="ppn_checkbox" name="ppn_checkbox"> 
                            Tambahkan PPN 11% (jika rekanan adalah PKP)
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="ppn_nominal">PPN yang ditambahkan (Rp)</label>
                        <input type="text" 
                               class="form-control" 
                               id="ppn_nominal" 
                               name="ppn_display" 
                               value="0" 
                               readonly>
                        <small class="form-text text-muted">Otomatis: DPP × 11% (jika PPN dicentang)</small>
                    </div>

                    <!-- Hidden field untuk tarif pajak - auto-fill only -->
                    <input type="hidden" id="tarif_pajak" name="tarif_pajak">
                    <!-- Display PPH nominal yang akan dipotong -->
                    <div class="form-group">
                        <label for="pph_nominal">PPH yang akan dipotong (Rp)</label>
                        <input type="text" 
                               class="form-control" 
                               id="pph_nominal" 
                               name="pph_nominal_display" 
                               value="0" 
                               readonly>
                        <small class="form-text text-muted">Otomatis: DPP × Tarif Pajak (jika ada)</small>
                    </div>

                    <!-- Total Akhir -->
                    <div class="form-group">
                        <label for="total_akhir_display"><strong>Total Akhir (Rp)</strong></label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="total_akhir_display" 
                               name="total_akhir_display" 
                               value="0" 
                               readonly
                               style="font-weight: bold; font-size: 1.1em; background-color: #e8f4f8;">
                        <small class="form-text text-muted">Formula: DPP + PPN - PPH</small>
                    </div>
                    <div class="form-group">
                        <label for="untuk_pembayaran">Untuk Pembayaran</label>
                        <textarea class="form-control" id="untuk_pembayaran" name="untuk_pembayaran" rows="3" placeholder="Jelaskan tujuan pembayaran..." required></textarea>
                        <small class="form-text text-muted">Deskripsi rinci tentang keperluan pembayaran</small>
                    </div>
                    <div class="form-group">
                        <label for="pptk_1_id">PPTK <span class="text-danger">*</span></label>
                        <select class="form-control" id="pptk_1_id" name="pptk_1_id" required>
                            <option value="">-- Pilih PPTK --</option>
                            @foreach($pptks as $pptk)
                                <option value="{{ $pptk->id }}">{{ $pptk->nama }} - {{ $pptk->jabatan }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Pilih PPTK untuk menandatangani kuitansi</small>
                    </div>
                    <!-- Items Section -->
                    <div class="form-group">
                        <label>Item Barang</label>
                        <table class="table table-sm table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Items will be added here -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">+ Tambah Item</button>
                    </div>
                    <input type="hidden" id="rincian_item_json" name="rincian_item_json" value="[]">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" onclick="updateItemsJson()">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit kuitansi Modal -->
<div class="modal fade" id="editkuitansiModal" tabindex="-1" role="dialog" aria-labelledby="editkuitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editkuitansiModalLabel">Edit Kuitansi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <div class="form-group">
                        <label for="edit_nomor_rekening">Nomor Rekening</label>
                        <input type="text" class="form-control" id="edit_nomor_rekening" name="nomor_rekening" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_periode_lengkap">Periode <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_periode_lengkap" name="periode_lengkap" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @php
                                        $periodes = ['TU', 'GU'];
                                    @endphp
                                    @foreach($periodes as $tipe)
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $tipe }}-{{ $i }}">{{ $tipe }} {{ $i }}</option>
                                        @endfor
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Contoh: TU 1, GU 2</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nomor_urut">Nomor Kuitansi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nomor_urut" name="nomor_urut" placeholder="001" maxlength="3" required>
                                <small class="form-text text-muted">Urut: 001, 002, 003, dst</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_tanggal_kuitansi">Tanggal Kuitansi</label>
                        <input type="date" class="form-control" id="edit_tanggal_kuitansi" name="tanggal_kuitansi" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_rekanan_id">Penerima (Rekanan)</label>
                        <select class="form-control" id="edit_rekanan_id" name="rekanan_id" required>
                            <option value="">-- Pilih Rekanan --</option>
                            @foreach($rekanans as $rekanan)
                                <option value="{{ $rekanan->id }}" 
                                        data-perusahaan="{{ $rekanan->nama_perusahaan }}"
                                        data-npwp="{{ $rekanan->npwp }}"
                                        data-bank="{{ $rekanan->bank }}"
                                        data-rekening="{{ $rekanan->nomor_rekening }}">
                                    {{ $rekanan->nama_perusahaan }} ({{ $rekanan->npwp }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Pilih rekanan sebagai penerima kuitansi</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_jenis_pph">Jenis PPH</label>
                        <select class="form-control" id="edit_jenis_pph" name="jenis_pph">
                            <option value="">-- Pilih Jenis PPH --</option>
                            <option value="22">PPH 22</option>
                            <option value="23">PPH 23</option>
                        </select>
                        <small class="form-text text-muted">PPH dihitung otomatis sesuai NPWP rekanan</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_kode_objek_pajak">Kode Objek Pajak</label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_kode_objek_pajak" 
                               name="kode_objek_pajak" 
                               placeholder="Cari kode objek pajak..."
                               list="editKodeObjekPajakList"
                               autocomplete="off">
                        <datalist id="editKodeObjekPajakList">
                            @foreach($kodeObjekPajaks as $kop)
                                <option value="{{ $kop->kode }}" label="{{ $kop->nama }} ({{ $kop->tarif }}%)">
                            @endforeach
                        </datalist>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Hanya perlu diisi jika belanja ≥ 2.000.000 (untuk membuat BuPot)
                        </small>
                    </div>
                    <!-- DPP Display -->
                    <div class="form-group">
                        <label for="edit_dpp_display">DPP (Dasar Pengenaan Pajak)</label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_dpp_display" 
                               name="edit_dpp_display" 
                               value="Rp 0" 
                               readonly>
                        <small class="form-text text-muted">Jumlah dari semua item</small>
                    </div>

                    <!-- PPN Checkbox dan Input -->
                    <div class="form-group">
                        <label for="edit_ppn_checkbox">
                            <input type="checkbox" id="edit_ppn_checkbox" name="edit_ppn_checkbox"> 
                            Tambahkan PPN 11% (jika rekanan adalah PKP)
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="edit_ppn_nominal">PPN yang ditambahkan (Rp)</label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_ppn_nominal" 
                               name="edit_ppn_display" 
                               value="0" 
                               readonly>
                        <small class="form-text text-muted">Otomatis: DPP × 11% (jika PPN dicentang)</small>
                    </div>

                    <!-- Hidden field untuk tarif pajak - auto-fill only -->
                    <input type="hidden" id="edit_tarif_pajak" name="tarif_pajak">
                    <!-- Display PPH nominal yang akan dipotong -->
                    <div class="form-group">
                        <label for="edit_pph_nominal">PPH yang akan dipotong (Rp)</label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_pph_nominal" 
                               name="edit_pph_nominal_display" 
                               value="0" 
                               readonly>
                        <small class="form-text text-muted">Otomatis: DPP × Tarif Pajak (jika ada)</small>
                    </div>

                    <!-- Total Akhir -->
                    <div class="form-group">
                        <label for="edit_total_akhir_display"><strong>Total Akhir (Rp)</strong></label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="edit_total_akhir_display" 
                               name="edit_total_akhir_display" 
                               value="0" 
                               readonly
                               style="font-weight: bold; font-size: 1.1em; background-color: #e8f4f8;">
                        <small class="form-text text-muted">Formula: DPP + PPN - PPH</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_untuk_pembayaran">Untuk Pembayaran</label>
                        <textarea class="form-control" id="edit_untuk_pembayaran" name="untuk_pembayaran" rows="3" placeholder="Jelaskan tujuan pembayaran..." required></textarea>
                        <small class="form-text text-muted">Deskripsi rinci tentang keperluan pembayaran</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_pptk_1_id">PPTK <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_pptk_1_id" name="pptk_1_id" required>
                            <option value="">-- Pilih PPTK --</option>
                            @foreach($pptks as $pptk)
                                <option value="{{ $pptk->id }}">{{ $pptk->nama }} - {{ $pptk->jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nama_pptk">Nama PPTK (Snapshot)</label>
                                <input type="text" class="form-control" id="edit_nama_pptk" readonly>
                                <small class="form-text text-muted">Snapshot nama PPTK saat kuitansi dibuat</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nip_pptk">NIP PPTK (Snapshot)</label>
                                <input type="text" class="form-control" id="edit_nip_pptk" readonly>
                                <small class="form-text text-muted">Snapshot NIP PPTK saat kuitansi dibuat</small>
                            </div>
                        </div>
                    </div>
                    <!-- Items Section -->
                    <div class="form-group">
                        <label>Item Barang</label>
                        <table class="table table-sm table-bordered" id="editItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="editItemsBody">
                                <!-- Items will be added here -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addEditItemRow()">+ Tambah Item</button>
                    </div>
                    <input type="hidden" id="edit_rincian_item_json" name="rincian_item_json" value="[]">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" onclick="updateEditItemsJson()">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<!-- Kuitansi Custom CSS -->
<link href="{{ asset('css/kuitansi.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/kuitansi-form.js') }}"></script>
<script>
let itemCounter = 0;
let editItemCounter = 0;
let selectedRekeningData = {
    id_akun: null,
    kode_akun: null,
    nama_akun: null
};

// Fungsi untuk membatasi teks ke 50 karakter
function truncateText(text, maxLength = 50) {
    if (text && text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
}

// Load Kegiatan on modal open
$('#selectRekeningModal').on('shown.bs.modal', function() {
    loadKegiatan();
});

// Load Kegiatan
function loadKegiatan() {
    $.ajax({
        url: '{{ route("api.kegiatan") }}',
        type: 'GET',
        success: function(data) {
            let options = '<option value="">-- Pilih Kegiatan --</option>';
            data.forEach(function(item) {
                options += `<option value="${item.id_giat}" data-kode="${item.kode_giat}">${item.kode_giat} - ${item.nama_giat}</option>`;
            });
            $('#select_kegiatan').html(options);
        },
        error: function() {
            alert('Gagal memuat data kegiatan');
        }
    });
}

// Handle Kegiatan change
$('#select_kegiatan').on('change', function() {
    const idGiat = $(this).val();
    $('#select_sub_kegiatan').prop('disabled', false).html('<option value="">-- Pilih Sub Kegiatan --</option>');
    $('#select_kode_rekening').prop('disabled', true).html('<option value="">-- Pilih Kode Rekening --</option>');
    $('#selected_rekening_info').hide();
    $('#btnLanjutKeForm').prop('disabled', true);
    
    if (idGiat) {
        loadSubKegiatan(idGiat);
    } else {
        $('#select_sub_kegiatan').prop('disabled', true);
    }
});

// Load Sub Kegiatan
function loadSubKegiatan(idGiat) {
    $.ajax({
        url: '{{ route("api.subKegiatan") }}',
        type: 'GET',
        data: { id_giat: idGiat },
        success: function(data) {
            let options = '<option value="">-- Pilih Sub Kegiatan --</option>';
            data.forEach(function(item) {
                options += `<option value="${item.id_sub_giat}" data-kode="${item.kode_sub_giat}">${item.kode_sub_giat} - ${item.nama_sub_giat}</option>`;
            });
            $('#select_sub_kegiatan').html(options);
        },
        error: function() {
            alert('Gagal memuat data sub kegiatan');
        }
    });
}

// Handle Sub Kegiatan change
$('#select_sub_kegiatan').on('change', function() {
    const idSubGiat = $(this).val();
    $('#select_kode_rekening').prop('disabled', false).html('<option value="">-- Pilih Kode Rekening --</option>');
    $('#selected_rekening_info').hide();
    $('#btnLanjutKeForm').prop('disabled', true);
    
    if (idSubGiat) {
        loadKodeRekening(idSubGiat);
    } else {
        $('#select_kode_rekening').prop('disabled', true);
    }
});

// Load Kode Rekening
function loadKodeRekening(idSubGiat) {
    $.ajax({
        url: '{{ route("api.kodeRekening") }}',
        type: 'GET',
        data: { id_sub_giat: idSubGiat },
        success: function(data) {
            let options = '<option value="">-- Pilih Kode Rekening --</option>';
            data.forEach(function(item) {
                const anggaran = new Intl.NumberFormat('id-ID').format(item.nilai_anggaran);
                options += `<option value="${item.id_akun}" data-kode="${item.kode_akun}" data-nama="${item.nama_akun}">${item.kode_akun} - ${item.nama_akun} (Rp ${anggaran})</option>`;
            });
            $('#select_kode_rekening').html(options);
        },
        error: function() {
            alert('Gagal memuat data kode rekening');
        }
    });
}

// Handle Kode Rekening change
$('#select_kode_rekening').on('change', function() {
    const selectedOption = $(this).find('option:selected');
    const idAkun = $(this).val();
    const kodeAkun = selectedOption.data('kode');
    const namaAkun = selectedOption.data('nama');
    
    if (idAkun) {
        selectedRekeningData = {
            id_akun: idAkun,
            kode_akun: kodeAkun,
            nama_akun: namaAkun
        };
        
        $('#info_kode_akun').text(kodeAkun);
        $('#info_nama_akun').text(namaAkun);
        $('#selected_rekening_info').fadeIn();
        $('#btnLanjutKeForm').prop('disabled', false);
    } else {
        $('#selected_rekening_info').hide();
        $('#btnLanjutKeForm').prop('disabled', true);
    }
});

// Lanjut ke Form Kuitansi (Tingkat 2)
$('#btnLanjutKeForm').on('click', function() {
    if (selectedRekeningData.kode_akun) {
        // Set nomor rekening ke form
        $('#nomor_rekening').val(selectedRekeningData.kode_akun);
        $('#selected_id_akun').val(selectedRekeningData.id_akun);
        const displayText = selectedRekeningData.kode_akun + ' - ' + selectedRekeningData.nama_akun;
        $('#display_kode_rekening').text(truncateText(displayText));
        
        // Tutup modal tingkat 1, buka modal tingkat 2
        $('#selectRekeningModal').modal('hide');
        setTimeout(function() {
            $('#addkuitansiModal').modal('show');
        }, 300);
    }
});

// Tombol Ganti Rekening (kembali ke tingkat 1)
$('#btnGantiRekening').on('click', function() {
    $('#addkuitansiModal').modal('hide');
    setTimeout(function() {
        $('#selectRekeningModal').modal('show');
    }, 300);
});

// Reset saat modal ditutup
$('#selectRekeningModal').on('hidden.bs.modal', function() {
    $('#select_kegiatan').val('').trigger('change');
    $('#select_sub_kegiatan').html('<option value="">-- Pilih Sub Kegiatan --</option>').prop('disabled', true);
    $('#select_kode_rekening').html('<option value="">-- Pilih Kode Rekening --</option>').prop('disabled', true);
    $('#selected_rekening_info').hide();
    $('#btnLanjutKeForm').prop('disabled', true);
});

$('#addkuitansiModal').on('hidden.bs.modal', function() {
    // Reset form jika user cancel dari tingkat 2
    if (!$('#selectRekeningModal').hasClass('show')) {
        selectedRekeningData = { id_akun: null, kode_akun: null, nama_akun: null };
    }
});

// Parse periode_lengkap (format: "TU-1" -> periode_type: "TU", periode_number: 1)
function parsePeriodeLengkap(value) {
    const parts = value.split('-');
    return {
        periode_type: parts[0],
        periode_number: parseInt(parts[1])
    };
}

// Format periode_lengkap back to "TU-1" format
function formatPeriodeLengkap(periodeType, periodeNumber) {
    return `${periodeType}-${periodeNumber}`;
}

// Auto-fill next periode number when periode_type changes
function updatePeriodeNumber(elementId, editMode = false) {
    const periodeTypeInput = editMode ? '#edit_periode_type' : '#periode_type';
    const periodeNumberInput = editMode ? '#edit_periode_number' : '#periode_number';
    
    const periodeType = $(periodeTypeInput).val();
    
    if (periodeType) {
        $.ajax({
            url: '{{ route("kuitansi.getNextPeriode") }}',
            type: 'GET',
            data: { periode_type: periodeType },
            success: function(response) {
                $(periodeNumberInput).val(response.next_number);
            },
            error: function() {
                console.error('Failed to get next periode number');
            }
        });
    }
}

// Add item row to add modal
function addItemRow() {
    const tbody = document.getElementById('itemsBody');
    const rowId = 'item_' + itemCounter++;
    const row = document.createElement('tr');
    row.id = rowId;
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm item-name" placeholder="Nama item"></td>
        <td><input type="number" class="form-control form-control-sm item-qty" placeholder="Jumlah" min="1" step="1"></td>
        <td><input type="number" class="form-control form-control-sm item-price" placeholder="Harga satuan" min="0" step="0.01"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')">Hapus</button></td>
    `;
    tbody.appendChild(row);
}

// Add item row to edit modal
function addEditItemRow() {
    const tbody = document.getElementById('editItemsBody');
    const rowId = 'edit_item_' + editItemCounter++;
    const row = document.createElement('tr');
    row.id = rowId;
    row.innerHTML = `
        <td><input type="text" class="form-control form-control-sm item-name" placeholder="Nama item"></td>
        <td><input type="number" class="form-control form-control-sm item-qty" placeholder="Jumlah" min="1" step="1"></td>
        <td><input type="number" class="form-control form-control-sm item-price" placeholder="Harga satuan" min="0" step="0.01"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow('${rowId}')">Hapus</button></td>
    `;
    tbody.appendChild(row);
}

// Remove item row
function removeItemRow(rowId) {
    document.getElementById(rowId).remove();
}

// Update items JSON before submit (add modal)
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

// Update items JSON before submit (edit modal)
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

$(document).ready(function() {
    // Handle periode_lengkap change for both add and edit modals
    $('#periode_lengkap, #edit_periode_lengkap').on('change', function() {
        // This is now handled directly in the form, no need for auto-update
    });

    // Initial calculation on modal show
    $('#addkuitansiModal').on('shown.bs.modal', function() {
        calculateTotalAkhir();
    });
    
    $('#editkuitansiModal').on('shown.bs.modal', function() {
        calculateEditTotalAkhir();
    });

    // Initialize Select2 for rekanan dropdowns
    $('#rekanan_id, #edit_rekanan_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Rekanan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#rekanan_id').closest('.modal').length ? $('#rekanan_id').closest('.modal') : $('body'),
        language: {
            noResults: function() {
                return "Rekanan tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            },
            inputTooShort: function() {
                return "Ketik untuk mencari rekanan";
            }
        }
    });

    // Set correct dropdownParent for edit modal
    $('#editkuitansiModal').on('shown.bs.modal', function () {
        $('#edit_rekanan_id').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Rekanan --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#editkuitansiModal'),
            language: {
                noResults: function() {
                    return "Rekanan tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
    });

    // Set correct dropdownParent for add modal
    $('#addkuitansiModal').on('shown.bs.modal', function () {
        $('#rekanan_id').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Rekanan --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addkuitansiModal'),
            language: {
                noResults: function() {
                    return "Rekanan tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
    });

    // Handle edit button click
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        itemCounter = 0;
        editItemCounter = 0;
        $('#editItemsBody').html(''); // Clear existing items
        
        // Fetch kuitansi data including items
        $.get('/kuitansi/' + id + '/edit', function(data) {
            
            $('#edit_nomor_rekening').val(data.nomor_rekening);
            
            // Set periode_lengkap - IMPORTANT: formatPeriodeLengkap dari data
            const periodeLengkap = formatPeriodeLengkap(data.periode_type, data.periode_number);
            $('#edit_periode_lengkap').val(periodeLengkap).trigger('change');
            
            // Set nomor_urut
            $('#edit_nomor_urut').val(String(data.nomor_urut).padStart(3, '0'));
            
            // Set rekanan, tanggal, jenis_pph, untuk_pembayaran, pptk
            $('#edit_rekanan_id').val(data.rekanan_id).trigger('change');
            $('#edit_tanggal_kuitansi').val(data.tanggal_kuitansi);
            $('#edit_jenis_pph').val(data.jenis_pph || '').trigger('change');
            $('#edit_untuk_pembayaran').val(data.untuk_pembayaran);
            $('#edit_pptk_1_id').val(data.pptk_1_id).trigger('change');
            
            // Set snapshot fields (read-only)
            document.getElementById('edit_nama_pptk').value = data.nama_pptk || '';
            document.getElementById('edit_nip_pptk').value = data.nip_pptk || '';
            
            // Set kode_objek_pajak if exists
            if (data.kode_objek_pajak) {
                $('#edit_kode_objek_pajak').val(data.kode_objek_pajak);
            }
            
            // Load items if they exist
            if (data.rincian_item && Array.isArray(data.rincian_item)) {
                data.rincian_item.forEach(item => {
                    addEditItemRow();
                    const lastRow = document.querySelector('#editItemsBody tr:last-child');
                    lastRow.querySelector('.item-name').value = item.nama;
                    lastRow.querySelector('.item-qty').value = item.jumlah;
                    lastRow.querySelector('.item-price').value = item.harga_satuan;
                });
            }
            
            // Set PPN checkbox if ppn > 0
            if (data.ppn > 0) {
                document.getElementById('edit_ppn_checkbox').checked = true;
            } else {
                document.getElementById('edit_ppn_checkbox').checked = false;
            }
            
            // Set tarif_pajak if exists
            if (data.tarif_pajak) {
                $('#edit_tarif_pajak').val(data.tarif_pajak);
            }
            
            // Recalculate display values
            calculateEditTotalAkhir();
            
            $('#editForm').attr('action', '/kuitansi/' + id);
        }).fail(function(error) {
            console.error('Error fetching kuitansi:', error);
        });
    });

    // Reset Select2 and items when modal is closed
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('select.select2-hidden-accessible').select2('destroy');
        if ($(this).attr('id') === 'addkuitansiModal') {
            document.getElementById('itemsBody').innerHTML = '';
            itemCounter = 0;
        }
    });

    // ===== Checkbox dan Export XML =====
    // Select all checkboxes
    $('#selectAllCheckbox, #selectAllCheckbox2').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.kuitansi-checkbox').prop('checked', isChecked);
        updateExportButton();
    });

    // Individual checkbox change
    $('.kuitansi-checkbox').on('change', function() {
        updateExportButton();
    });

    function updateExportButton() {
        const selectedCount = $('.kuitansi-checkbox:checked').length;
        const $btn = $('#exportXmlBtn');
        const $count = $('#selectedCount');
        
        $count.text(selectedCount);
        
        if (selectedCount > 0) {
            $btn.show();
        } else {
            $btn.hide();
        }
    }

    // Export XML button click
    $('#exportXmlBtn').on('click', function() {
        const selectedIds = [];
        $('.kuitansi-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal 1 kuitansi untuk export',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang membuat file XML...',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: (toast) => {
                Swal.showLoading()
            }
        });

        // Send POST request to export XML
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("kuitansi.exportBupotXmlSelected") }}';
        
        // Add CSRF token
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '{{ csrf_token() }}';
        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = '_token';
        input1.value = csrfToken;
        form.appendChild(input1);

        // Add selected IDs
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'kuitansi_ids';
        input2.value = JSON.stringify(selectedIds);
        form.appendChild(input2);

        // Listen for download and show success
        const beforeUnload = function() {
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: 'File XML telah berhasil didownload<br><small>{{ session("success") ? session("success") : "Data kuitansi diekspor ke format XML" }}</small>',
                    confirmButtonText: 'OK'
                });
            }, 500);
            
            // Cleanup
            window.removeEventListener('beforeunload', beforeUnload);
        };
        
        window.addEventListener('beforeunload', beforeUnload);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
});
</script>
@endpush
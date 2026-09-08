<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KodeRekening;
use App\Models\Kuitansi;
use App\Models\Rekanan;
use App\Models\Staff;
use App\Models\SubKegiatan;
use App\Services\KuitansiTaxCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KuitansiController extends Controller
{
    public function index()
    {
        // Filter data berdasarkan instansi user yang sedang login
        $userInstansi = auth()->user()->instansi;

        // Statistik dihitung lewat agregat DB, bukan menarik semua baris kuitansi
        // ke memori — tabel datanya sendiri dimuat terpisah lewat data() (server-side DataTables).
        $baseQuery = Kuitansi::where('instansi', $userInstansi);
        $totalKuitansi = (clone $baseQuery)->count();
        $totalNominal = (clone $baseQuery)->sum('total_akhir');
        $bulanIniQuery = (clone $baseQuery)->whereYear('tanggal_kuitansi', now()->year)->whereMonth('tanggal_kuitansi', now()->month);
        $countBulanIni = (clone $bulanIniQuery)->count();
        $nominalBulanIni = (clone $bulanIniQuery)->sum('total_akhir');

        $rekanans = Rekanan::where('instansi', $userInstansi)->get();
        $pptks = Staff::where('status', 'PPTK')->where('instansi', $userInstansi)->get();
        $staffs = Staff::where('instansi', $userInstansi)->orderBy('nama')->get();
        $bendaharaBarang = Staff::where('status', 'Bendahara Barang')->where('instansi', $userInstansi)->first();
        $kodeObjekPajaks = DB::table('kode_objek_pajaks')->orderBy('kode')->get();

        return view('kuitansi', compact(
            'totalKuitansi',
            'totalNominal',
            'countBulanIni',
            'nominalBulanIni',
            'rekanans',
            'staffs',
            'pptks',
            'bendaharaBarang',
            'kodeObjekPajaks'
        ));
    }

    /**
     * AJAX data source for the server-side DataTable on the kuitansi list page.
     * Expects DataTables' standard server-side params (draw, start, length, order)
     * plus the custom filter panel fields (filter_no_buku, filter_rekening, etc).
     */
    public function data(Request $request)
    {
        $userInstansi = auth()->user()->instansi;

        $query = Kuitansi::with('kodeRekening.subKegiatan.kegiatan')
            ->where('instansi', $userInstansi);

        if ($request->filled('filter_no_buku')) {
            $query->where('no_buku', 'like', '%' . $request->input('filter_no_buku') . '%');
        }
        if ($request->filled('filter_rekening')) {
            $query->where('nomor_rekening', 'like', '%' . $request->input('filter_rekening') . '%');
        }
        if ($request->filled('filter_penerima')) {
            $query->where('nama_penerima', 'like', '%' . $request->input('filter_penerima') . '%');
        }
        if ($request->filled('filter_pembayaran')) {
            $query->where('untuk_pembayaran', 'like', '%' . $request->input('filter_pembayaran') . '%');
        }
        if ($request->filled('filter_tanggal_mulai')) {
            $query->whereDate('tanggal_kuitansi', '>=', $request->input('filter_tanggal_mulai'));
        }
        if ($request->filled('filter_tanggal_selesai')) {
            $query->whereDate('tanggal_kuitansi', '<=', $request->input('filter_tanggal_selesai'));
        }

        $recordsFiltered = (clone $query)->count();
        $recordsTotal = Kuitansi::where('instansi', $userInstansi)->count();

        $sortMap = [
            2 => 'no_buku',
            3 => 'nomor_rekening',
            4 => 'untuk_pembayaran',
            5 => 'total_akhir',
            6 => 'nama_penerima',
        ];
        $orderColumnIndex = (int) $request->input('order.0.column', 2);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sortColumn = $sortMap[$orderColumnIndex] ?? 'no_buku';
        $query->orderBy($sortColumn, $orderDir);

        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $kuitansis = $query->get();

        $data = $kuitansis->map(function (Kuitansi $k) {
            return [
                'id' => $k->id,
                'no_buku' => $k->no_buku,
                'periode_type' => $k->periode_type,
                'periode_number' => $k->periode_number,
                'nomor_urut' => $k->nomor_urut,
                'nomor_rekening' => $k->nomor_rekening,
                'formatted_nomor_rekening' => $k->formatted_nomor_rekening,
                'untuk_pembayaran' => $k->untuk_pembayaran,
                'total_akhir' => (int) ($k->total_akhir ?? 0),
                'nama_penerima' => $k->nama_penerima,
                'pph_22' => $k->pph_22 ? (float) $k->pph_22 : null,
                'pph_23' => $k->pph_23 ? (float) $k->pph_23 : null,
                'ppn' => $k->ppn ? (float) $k->ppn : null,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function resolvePenerima(Request $request): array
    {
        $userInstansi = auth()->user()->instansi;
        $penerimaType = $request->input('penerima_type');

        if ($penerimaType === 'rekanan') {
            $rekanan = Rekanan::where('id', $request->rekanan_id)
                ->where('instansi', $userInstansi)
                ->firstOrFail();

            return [
                'rekanan_id' => $rekanan->id,
                'nama_penerima' => $rekanan->nama_perusahaan,
            ];
        }

        $staff = Staff::where('id', $request->staff_id)
            ->where('instansi', $userInstansi)
            ->firstOrFail();

        return [
            'rekanan_id' => null,
            'nama_penerima' => $staff->nama,
        ];
    }

    public function getNextPeriodeNumber(Request $request)
    {
        $periodeType = $request->query('periode_type');

        if (!in_array($periodeType, ['TU', 'GU'])) {
            return response()->json(['error' => 'Invalid periode type'], 400);
        }

        // Filter berdasarkan instansi user yang sedang login
        $userInstansi = auth()->user()->instansi;

        // Get the highest periode_number for this periode_type
        // This is to show user what the last periode number was, not for auto-numbering kuitansi
        $lastKuitansi = Kuitansi::where('periode_type', $periodeType)
            ->where('instansi', $userInstansi)
            ->orderBy('periode_number', 'desc')
            ->orderBy('nomor_urut', 'desc')
            ->first();

        // Return info about last periode
        if ($lastKuitansi) {
            $nextPeriodeNum = $lastKuitansi->periode_number;
            $lastNomorUrut = $lastKuitansi->nomor_urut;
            return response()->json([
                'current_periode_number' => $nextPeriodeNum,
                'last_nomor_urut' => $lastNomorUrut
            ]);
        }

        return response()->json([
            'current_periode_number' => 1,
            'last_nomor_urut' => 0
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('periode_lengkap')) {
            $request->merge([
                'periode_lengkap' => strtoupper($request->periode_lengkap),
            ]);
        }

        $userInstansi = auth()->user()->instansi;

        $request->validate([
            'nomor_rekening' => 'required|string|max:255',
            'periode_lengkap' => 'nullable|string|max:50',
            'nomor_urut' => 'nullable|string|max:3',
            'penerima_type' => 'required|in:rekanan,staff',
            'rekanan_id' => 'nullable|integer',
            'staff_id' => 'nullable|exists:staff,id',
            'tanggal_kuitansi' => 'required|date',
            'rincian_item_json' => 'nullable|json',
            'kode_objek_pajak_23' => 'nullable|string|max:255',
            'tarif_pajak_23' => 'nullable|numeric',
            'pptk_1_id' => [
                'required',
                Rule::exists('staff', 'id')->where('instansi', $userInstansi),
            ],
            'id_kode_rekening' => [
                'nullable',
                Rule::exists('kode_rekening', 'id')->where('instansi', $userInstansi),
            ],
        ]);

        if ($request->penerima_type === 'rekanan' && !$request->rekanan_id) {
            return back()->withErrors(['rekanan_id' => 'Penerima rekanan wajib dipilih.'])->withInput();
        }
        if ($request->penerima_type === 'staff' && !$request->staff_id) {
            return back()->withErrors(['staff_id' => 'Penerima staff wajib dipilih.'])->withInput();
        }

        // Parse periode_lengkap (supports "TU-1" or "UP 1" format)
        $periodeType = null;
        $periodeNumber = null;
        $nomorUrut = null;
        $noBuku = null;
        if ($request->filled('periode_lengkap')) {
            $periodeParts = preg_split('/[\s\-]+/', $request->periode_lengkap, 2);
            $periodeType = $periodeParts[0] ?? $request->periode_lengkap;
            $periodeNumber = isset($periodeParts[1]) && is_numeric($periodeParts[1]) ? (int) $periodeParts[1] : null;
        }
        if ($request->filled('nomor_urut')) {
            $nomorUrut = (int) $request->nomor_urut;
        }
        if ($periodeType !== null && $nomorUrut !== null) {
            // Format noBuku: "TYPE NUMBER / XXX" or "TYPE / XXX" if no number
            $noBuku = $periodeNumber !== null
                ? $periodeType . ' ' . $periodeNumber . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT)
                : $periodeType . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
        }

        $penerima = $this->resolvePenerima($request);

        $rincianItem = null;
        if ($request->rincian_item_json) {
            $rincianItem = json_decode($request->rincian_item_json, true);
        }

        $tax = KuitansiTaxCalculator::calculate(
            $rincianItem,
            $request->has('ppn_checkbox') && $request->ppn_checkbox,
            (float) ($request->tarif_pajak ?? 0),
            (float) ($request->tarif_pajak_23 ?? 0)
        );
        $dpp = $tax['dpp'];
        $ppnAmount = $tax['ppn'];
        $pph22Amount = $tax['pph_22'];
        $pph23Amount = $tax['pph_23'];
        $pphAmount = $tax['pph'];
        $jenisPph = $tax['jenis_pph'];

        // Total Akhir = DPP
        $totalAkhir = $dpp;

        // Get staff for snapshot (filter by instansi)
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->where('instansi', $userInstansi)->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->where('instansi', $userInstansi)->first();
        $pptk = Staff::where('instansi', $userInstansi)->findOrFail($request->pptk_1_id);

        // Handle nama_bendahara_barang from form input (if provided)
        $namaBendaharaBarang = null;
        $nipBendaharaBarang = null;
        if ($request->filled('nama_bendahara_barang')) {
            $namaBendaharaBarang = $request->nama_bendahara_barang;
            $nipBendaharaBarang = $request->nip_bendahara_barang;
        }

        Kuitansi::create([
            'id_kode_rekening' => $request->id_kode_rekening,
            'id_akun' => $request->id_akun,
            'nomor_rekening' => $request->nomor_rekening,
            'periode_type' => $periodeType,
            'periode_number' => $periodeNumber,
            'nomor_urut' => $nomorUrut,
            'no_buku' => $noBuku,
            'rekanan_id' => $penerima['rekanan_id'],
            'nama_penerima' => $penerima['nama_penerima'],
            'tanggal_kuitansi' => $request->tanggal_kuitansi,
            'ppn' => $ppnAmount,
            'pph' => $pphAmount,
            'pph_22' => $pph22Amount,
            'pph_23' => $pph23Amount,
            'jenis_pph' => $jenisPph,
            'untuk_pembayaran' => $request->untuk_pembayaran,
            'total_akhir' => $totalAkhir,
            'rincian_item' => $rincianItem,
            'kode_objek_pajak' => $request->kode_objek_pajak ? trim(explode(' - ', $request->kode_objek_pajak)[0]) : null,
            'tarif_pajak' => $request->tarif_pajak ?: null,
            'kode_objek_pajak_23' => $request->kode_objek_pajak_23 ? trim(explode(' - ', $request->kode_objek_pajak_23)[0]) : null,
            'tarif_pajak_23' => $request->tarif_pajak_23 ?: null,
            'dpp' => $dpp,
            'jenis_dokumen' => $request->jenis_dokumen ?? 'PaymentProof',
            'tanggal_pemotongan' => $request->tanggal_pemotongan ?? $request->tanggal_kuitansi,
            'pptk_1_id' => $request->pptk_1_id,
            'nama_pengguna_anggaran' => $penggunaAnggaran->nama ?? null,
            'nip_pengguna_anggaran' => $penggunaAnggaran->nip ?? null,
            'nama_bendahara_pengeluaran' => $bendaharaPengeluaran->nama ?? null,
            'nip_bendahara_pengeluaran' => $bendaharaPengeluaran->nip ?? null,
            'nama_bendahara_barang' => $namaBendaharaBarang,
            'nip_bendahara_barang' => $nipBendaharaBarang,
            'nama_pptk' => $pptk->nama,
            'nip_pptk' => $pptk->nip,
            'instansi' => $userInstansi, // Auto-assign instansi
        ]);

        return redirect()->route('kuitansi.index')->with('success', 'kuitansi berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $userInstansi = auth()->user()->instansi;
        $kuitansi = Kuitansi::with(['rekanan', 'pptk', 'kodeRekening.subKegiatan.kegiatan'])
            ->where('instansi', $userInstansi)
            ->findOrFail($id);
        return response()->json($kuitansi);
    }

    public function update(Request $request, string $id)
    {
        if ($request->filled('periode_lengkap')) {
            $request->merge([
                'periode_lengkap' => strtoupper($request->periode_lengkap),
            ]);
        }

        $userInstansi = auth()->user()->instansi;

        $kuitansi = Kuitansi::where('instansi', $userInstansi)->findOrFail($id);

        $request->validate([
            'nomor_rekening' => 'required|string|max:255',
            'periode_lengkap' => 'nullable|string|max:50',
            'nomor_urut' => 'nullable|string|max:3',
            'penerima_type' => 'required|in:rekanan,staff',
            'rekanan_id' => 'nullable|integer',
            'staff_id' => 'nullable|exists:staff,id',
            'tanggal_kuitansi' => 'required|date',
            'jenis_pph' => 'nullable|string|max:5',
            'rincian_item_json' => 'nullable|json',
            'kode_objek_pajak_23' => 'nullable|string|max:255',
            'tarif_pajak_23' => 'nullable|numeric',
            'pptk_1_id' => [
                'required',
                Rule::exists('staff', 'id')->where('instansi', $userInstansi),
            ],
            'id_kode_rekening' => [
                'nullable',
                Rule::exists('kode_rekening', 'id')->where('instansi', $userInstansi),
            ],
        ]);

        if ($request->penerima_type === 'rekanan' && !$request->rekanan_id) {
            return back()->withErrors(['rekanan_id' => 'Penerima rekanan wajib dipilih.'])->withInput();
        }
        if ($request->penerima_type === 'staff' && !$request->staff_id) {
            return back()->withErrors(['staff_id' => 'Penerima staff wajib dipilih.'])->withInput();
        }

        // Parse periode_lengkap (supports "TU-1" or "UP 1" format)
        $periodeType = null;
        $periodeNumber = null;
        $nomorUrut = null;
        $noBuku = null;
        if ($request->filled('periode_lengkap')) {
            $periodeParts = preg_split('/[\s\-]+/', $request->periode_lengkap, 2);
            $periodeType = $periodeParts[0] ?? $request->periode_lengkap;
            $periodeNumber = isset($periodeParts[1]) && is_numeric($periodeParts[1]) ? (int) $periodeParts[1] : null;
        }
        if ($request->filled('nomor_urut')) {
            $nomorUrut = (int) $request->nomor_urut;
        }
        if ($periodeType !== null && $nomorUrut !== null) {
            // Format noBuku: "TYPE NUMBER / XXX" or "TYPE / XXX" if no number
            $noBuku = $periodeNumber !== null
                ? $periodeType . ' ' . $periodeNumber . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT)
                : $periodeType . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
        }

        $penerima = $this->resolvePenerima($request);

        $rincianItem = null;
        if ($request->rincian_item_json) {
            $rincianItem = json_decode($request->rincian_item_json, true);
        }

        $tax = KuitansiTaxCalculator::calculate(
            $rincianItem,
            $request->has('ppn_checkbox') && $request->ppn_checkbox,
            (float) ($request->tarif_pajak ?? 0),
            (float) ($request->tarif_pajak_23 ?? 0)
        );
        $dpp = $tax['dpp'];
        $ppnAmount = $tax['ppn'];
        $pph22Amount = $tax['pph_22'];
        $pph23Amount = $tax['pph_23'];
        $pphAmount = $tax['pph'];
        $jenisPph = $tax['jenis_pph'];

        // Total Akhir = DPP
        $totalAkhir = $dpp;

        // Get staff for snapshot (filter by instansi)
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->where('instansi', $userInstansi)->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->where('instansi', $userInstansi)->first();
        $pptk = Staff::where('instansi', $userInstansi)->findOrFail($request->pptk_1_id);

        // Handle nama_bendahara_barang from form input (if provided)
        $namaBendaharaBarang = null;
        $nipBendaharaBarang = null;
        if ($request->filled('nama_bendahara_barang')) {
            $namaBendaharaBarang = $request->nama_bendahara_barang;
            $nipBendaharaBarang = $request->nip_bendahara_barang;
        }

        $kuitansi->update([
            'id_kode_rekening' => $request->id_kode_rekening,
            'id_akun' => $request->id_akun,
            'nomor_rekening' => $request->nomor_rekening,
            'periode_type' => $periodeType,
            'periode_number' => $periodeNumber,
            'nomor_urut' => $nomorUrut,
            'no_buku' => $noBuku,
            'rekanan_id' => $penerima['rekanan_id'],
            'nama_penerima' => $penerima['nama_penerima'],
            'tanggal_kuitansi' => $request->tanggal_kuitansi,
            'ppn' => $ppnAmount,
            'pph' => $pphAmount,
            'pph_22' => $pph22Amount,
            'pph_23' => $pph23Amount,
            'jenis_pph' => $jenisPph,
            'untuk_pembayaran' => $request->untuk_pembayaran,
            'total_akhir' => $totalAkhir,
            'rincian_item' => $rincianItem,
            'kode_objek_pajak' => $request->kode_objek_pajak ? trim(explode(' - ', $request->kode_objek_pajak)[0]) : null,
            'tarif_pajak' => $request->tarif_pajak ?: null,
            'kode_objek_pajak_23' => $request->kode_objek_pajak_23 ? trim(explode(' - ', $request->kode_objek_pajak_23)[0]) : null,
            'tarif_pajak_23' => $request->tarif_pajak_23 ?: null,
            'dpp' => $dpp,
            'jenis_dokumen' => $request->jenis_dokumen ?? 'PaymentProof',
            'tanggal_pemotongan' => $request->tanggal_pemotongan ?? $request->tanggal_kuitansi,
            'pptk_1_id' => $request->pptk_1_id,
            'nama_pengguna_anggaran' => $penggunaAnggaran->nama ?? null,
            'nip_pengguna_anggaran' => $penggunaAnggaran->nip ?? null,
            'nama_bendahara_pengeluaran' => $bendaharaPengeluaran->nama ?? null,
            'nip_bendahara_pengeluaran' => $bendaharaPengeluaran->nip ?? null,
            'nama_bendahara_barang' => $namaBendaharaBarang,
            'nip_bendahara_barang' => $nipBendaharaBarang,
            'nama_pptk' => $pptk->nama,
            'nip_pptk' => $pptk->nip,
            'instansi' => $kuitansi->instansi ?? $userInstansi, // Keep instansi unchanged
        ]);

        return redirect()->route('kuitansi.index')->with('success', 'kuitansi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $userInstansi = auth()->user()->instansi;
        $kuitansi = Kuitansi::where('instansi', $userInstansi)->findOrFail($id);
        $kuitansi->deleted_by = auth()->user()->id;
        $kuitansi->save();
        $kuitansi->delete();

        return redirect()->route('kuitansi.index')->with('success', 'kuitansi berhasil dihapus.');
    }

    public function preview(string $id)
    {
        $userInstansi = auth()->user()->instansi;
        $kuitansi = Kuitansi::with(['rekanan', 'pptk', 'kodeRekening.subKegiatan.kegiatan'])
            ->where('instansi', $userInstansi)
            ->findOrFail($id);

        // Get fixed staff (filter by instansi milik kuitansi)
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->where('instansi', $kuitansi->instansi)->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->where('instansi', $kuitansi->instansi)->first();

        // Get instansi data for kop
        $instansiData = \App\Models\Instansi::where('nama', $kuitansi->instansi)->first();

        return view('preview.kuitansi', compact('kuitansi', 'penggunaAnggaran', 'bendaharaPengeluaran', 'instansiData'));
    }

    // API endpoints for cascading selects
    public function getKegiatan()
    {
        $userInstansi = auth()->user()->instansi;

        $kegiatan = Kegiatan::selectRaw('id_giat as id, id_giat, kode_giat as kode, kode_giat, nama_giat as nama, nama_giat')
            ->where('instansi', $userInstansi)
            ->distinct()
            ->orderBy('kode')
            ->get();

        return response()->json($kegiatan);
    }

    public function getSubKegiatan(Request $request)
    {
        $userInstansi = auth()->user()->instansi;
        $idGiat = $request->query('id_giat') ?? $request->query('id');

        $subKegiatan = SubKegiatan::selectRaw('id_sub_giat as id, id_sub_giat, id_giat, kode_sub_giat as kode, kode_sub_giat, nama_sub_giat as nama, nama_sub_giat')
            ->where('id_giat', $idGiat)
            ->where('instansi', $userInstansi)
            ->distinct()
            ->orderBy('kode')
            ->get();

        return response()->json($subKegiatan);
    }

    public function getKodeRekening(Request $request)
    {
        $userInstansi = auth()->user()->instansi;
        $idSubGiat = $request->query('id_sub_giat') ?? $request->query('id');

        $kodeRekening = KodeRekening::selectRaw('id, id_akun, id_sub_giat, kode_akun as kode, kode_akun, nama_akun as nama, nama_akun')
            ->where('id_sub_giat', $idSubGiat)
            ->where('instansi', $userInstansi)
            ->orderBy('kode')
            ->get();

        return response()->json($kodeRekening);
    }

    public function getTarifPajak(string $kode)
    {
        $kodeObjekPajak = DB::table('kode_objek_pajaks')
            ->where('kode', $kode)
            ->first();

        if ($kodeObjekPajak) {
            return response()->json(['tarif' => $kodeObjekPajak->tarif]);
        }

        return response()->json(['error' => 'Kode pajak tidak ditemukan'], 404);
    }

    private function resolveTaxBaseForExport($kuitansi): int
    {
        $rincianItem = $kuitansi->rincian_item;
        if (!is_array($rincianItem)) {
            return (int) round((float) ($kuitansi->dpp ?? 0));
        }

        $dppResult = KuitansiTaxCalculator::computeDpp($rincianItem);
        $dpp = $dppResult['dpp'];
        $dppBarang = $dppResult['dpp_barang'];
        $dppJasa = $dppResult['dpp_jasa'];

        if (!empty($kuitansi->kode_objek_pajak_23)) {
            return $dppJasa;
        }

        if (!empty($kuitansi->kode_objek_pajak)) {
            return $dppBarang > 0 ? $dppBarang : $dpp;
        }

        return $dpp;
    }

    public function exportBupotXml(Request $request)
    {
        $bulan = $request->query('bulan', date('n')); // Default bulan sekarang
        $tahun = $request->query('tahun', date('Y')); // Default tahun sekarang

        // NPWP Pemotong - ambil dari instansi user yang login
        $userInstansi = auth()->user()->instansi;

        // Ambil data kuitansi berdasarkan bulan dan tahun dari tanggal_pemotongan
        // Punya kode_objek_pajak (PPh 22) ATAU kode_objek_pajak_23 (PPh 23)
        $kuitansis = Kuitansi::with('rekanan')
            ->where('instansi', $userInstansi)
            ->where(function ($q) {
                $q->whereNotNull('kode_objek_pajak')
                    ->orWhereNotNull('kode_objek_pajak_23');
            })
            ->whereNotNull('dpp')
            ->where('dpp', '>', 0)
            ->whereYear('tanggal_pemotongan', $tahun)
            ->whereMonth('tanggal_pemotongan', $bulan)
            ->orderBy('tanggal_pemotongan')
            ->get();

        if ($kuitansis->isEmpty()) {
            return back()->with('error', 'Tidak ada data kuitansi dengan data BuPot lengkap untuk periode tersebut.');
        }

        $instansiData = \App\Models\Instansi::where('nama', $userInstansi)->first();
        $npwpPemotong = $instansiData?->npwp ?? '';

        if (empty($npwpPemotong)) {
            return back()->with('error', 'NPWP instansi belum diisi. Silakan lengkapi NPWP instansi terlebih dahulu di menu Instansi.');
        }

        $idTkuPemotong = $npwpPemotong . '000000';

        // Build XML menggunakan SimpleXMLElement
        $xmlObj = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><BpuBulk xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></BpuBulk>');

        $xmlObj->addChild('TIN', $npwpPemotong);
        $listOfBpu = $xmlObj->addChild('ListOfBpu');

        foreach ($kuitansis as $kuitansi) {
            $bpu = $listOfBpu->addChild('Bpu');
            $bpu->addChild('TaxPeriodMonth', $bulan);
            $bpu->addChild('TaxPeriodYear', $tahun);
            $bpu->addChild('CounterpartTin', $kuitansi->rekanan?->npwp ?? '9990000000999000');
            $bpu->addChild('IDPlaceOfBusinessActivityOfIncomeRecipient', ($kuitansi->rekanan?->npwp ?? '9990000000999000') . '000000');
            $kodeObjekPajak = $kuitansi->kode_objek_pajak ?? $kuitansi->kode_objek_pajak_23;
            $tarifPajak = $kuitansi->tarif_pajak ?? $kuitansi->tarif_pajak_23;
            $taxBase = $this->resolveTaxBaseForExport($kuitansi);
            $bpu->addChild('TaxCertificate', 'N/A');
            $bpu->addChild('TaxObjectCode', $kodeObjekPajak);
            $bpu->addChild('TaxBase', number_format($taxBase, 0, '', ''));
            $bpu->addChild('Rate', $tarifPajak);
            $bpu->addChild('Document', $kuitansi->jenis_dokumen);
            $bpu->addChild('DocumentNumber', $kuitansi->nomor_urut . '/' . $kuitansi->periode_type . ' ' . $kuitansi->periode_number);
            $bpu->addChild('DocumentDate', \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('Y-m-d'));
            $bpu->addChild('IDPlaceOfBusinessActivity', $idTkuPemotong);
            $bpu->addChild('GovTreasurerOpt', 'N/A');
            $sp2d = $bpu->addChild('SP2DNumber');
            $sp2d->addAttribute('xsi:nil', 'true', 'http://www.w3.org/2001/XMLSchema-instance');
            $bpu->addChild('WithholdingDate', \Carbon\Carbon::parse($kuitansi->tanggal_pemotongan)->format('Y-m-d'));
        }

        $xmlString = $xmlObj->asXML();
        $filename = "BuPot_PPh_{$tahun}_{$bulan}_" . date('YmdHis') . ".xml";

        return response($xmlString, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportBupotXmlSelected(Request $request)
    {
        $kuitansiIds = json_decode($request->input('kuitansi_ids'), true);

        if (!is_array($kuitansiIds) || empty($kuitansiIds)) {
            return response()->json(['error' => 'Pilih minimal 1 kuitansi untuk export XML.'], 422);
        }

        // NPWP Pemotong - ambil dari instansi user yang login
        $userInstansi = auth()->user()->instansi;

        // Ambil semua kuitansi yang dipilih, dibatasi pada instansi user yang sedang login
        $allSelected = Kuitansi::with('rekanan')
            ->whereIn('id', $kuitansiIds)
            ->where('instansi', $userInstansi)
            ->orderBy('tanggal_pemotongan')
            ->get();

        // Filter hanya yang punya kode_objek_pajak (PPh 22) ATAU kode_objek_pajak_23 (PPh 23), dan dpp > 0
        $validKuitansis = $allSelected->filter(function ($k) {
            $hasKode = !is_null($k->kode_objek_pajak) || !is_null($k->kode_objek_pajak_23);
            return $hasKode && !is_null($k->dpp) && (int) $k->dpp > 0;
        })->values();

        if ($validKuitansis->isEmpty()) {
            return response()->json(['error' => 'Tidak ada kuitansi dengan data BuPot lengkap di antara pilihan Anda. Pastikan kode objek pajak dan DPP sudah terisi.'], 422);
        }

        $instansiData = \App\Models\Instansi::where('nama', $userInstansi)->first();
        $npwpPemotong = $instansiData?->npwp ?? '';

        if (empty($npwpPemotong)) {
            return response()->json(['error' => 'NPWP instansi belum diisi. Silakan lengkapi NPWP instansi terlebih dahulu di menu Instansi.'], 422);
        }

        $idTkuPemotong = $npwpPemotong . '000000';

        // Build XML menggunakan SimpleXMLElement
        $xmlObj = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><BpuBulk xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></BpuBulk>');

        $xmlObj->addChild('TIN', $npwpPemotong);
        $listOfBpu = $xmlObj->addChild('ListOfBpu');

        foreach ($validKuitansis as $kuitansi) {
            $bpu = $listOfBpu->addChild('Bpu');
            $bpu->addChild('TaxPeriodMonth', date('n', strtotime($kuitansi->tanggal_pemotongan)));
            $bpu->addChild('TaxPeriodYear', date('Y', strtotime($kuitansi->tanggal_pemotongan)));
            $bpu->addChild('CounterpartTin', $kuitansi->rekanan?->npwp ?? '9990000000999000');
            $bpu->addChild('IDPlaceOfBusinessActivityOfIncomeRecipient', ($kuitansi->rekanan?->npwp ?? '9990000000999000') . '000000');
            $kodeObjekPajak = $kuitansi->kode_objek_pajak ?? $kuitansi->kode_objek_pajak_23;
            $tarifPajak = $kuitansi->tarif_pajak ?? $kuitansi->tarif_pajak_23;
            $taxBase = $this->resolveTaxBaseForExport($kuitansi);
            $bpu->addChild('TaxCertificate', 'N/A');
            $bpu->addChild('TaxObjectCode', $kodeObjekPajak);
            $bpu->addChild('TaxBase', number_format($taxBase, 0, '', ''));
            $bpu->addChild('Rate', $tarifPajak);
            $bpu->addChild('Document', $kuitansi->jenis_dokumen);
            $bpu->addChild('DocumentNumber', $kuitansi->nomor_urut . '/' . $kuitansi->periode_type . ' ' . $kuitansi->periode_number);
            $bpu->addChild('DocumentDate', \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('Y-m-d'));
            $bpu->addChild('IDPlaceOfBusinessActivity', $idTkuPemotong);
            $bpu->addChild('GovTreasurerOpt', 'N/A');
            $sp2d = $bpu->addChild('SP2DNumber');
            $sp2d->addAttribute('xsi:nil', 'true', 'http://www.w3.org/2001/XMLSchema-instance');
            $bpu->addChild('WithholdingDate', \Carbon\Carbon::parse($kuitansi->tanggal_pemotongan)->format('Y-m-d'));
        }

        $xmlString = $xmlObj->asXML();
        $filename = "BuPot_PPh_Selected_" . date('YmdHis') . ".xml";

        return response($xmlString, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KodeRekening;
use App\Models\Kuitansi;
use App\Models\Rekanan;
use App\Models\Staff;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuitansiController extends Controller
{
    public function index()
    {
        // Filter data berdasarkan instansi user yang sedang login
        $userInstansi = auth()->user()->instansi;

        $kuitansis = Kuitansi::with('rekanan', 'kodeRekening.subKegiatan.kegiatan')
            ->where('instansi', $userInstansi)
            ->get();
        $rekanans = Rekanan::where('instansi', $userInstansi)->get();
        $pptks = Staff::where('status', 'PPTK')->where('instansi', $userInstansi)->get();
        $staffs = Staff::where('instansi', $userInstansi)->orderBy('nama')->get();
        $bendaharaBarang = Staff::where('status', 'Bendahara Barang')->where('instansi', $userInstansi)->first();
        $kodeObjekPajaks = DB::table('kode_objek_pajaks')->orderBy('kode')->get();
        return view('kuitansi', compact('kuitansis', 'rekanans', 'staffs', 'pptks', 'bendaharaBarang', 'kodeObjekPajaks'));
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
            'pptk_1_id' => 'required|exists:staff,id',
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

        // Calculate DPP total, DPP Barang (non-jasa), DPP Jasa
        $dpp = 0;
        $dppBarang = 0;
        $dppJasa = 0;
        if (is_array($rincianItem)) {
            foreach ($rincianItem as $item) {
                $jumlahRaw = $item['jumlah'] ?? null;
                $jumlah = (is_numeric($jumlahRaw) && (float) $jumlahRaw > 0) ? (int) $jumlahRaw : 1;
                $harga = (float) ($item['harga_satuan'] ?? 0);
                $subtotal = $jumlah * $harga;
                $dpp += $subtotal;
                if (!empty($item['is_jasa'])) {
                    $dppJasa += $subtotal;
                } else {
                    $dppBarang += $subtotal;
                }
            }
        }
        $dpp = (int) round($dpp);
        $dppBarang = (int) round($dppBarang);
        $dppJasa = (int) round($dppJasa);

        // Calculate PPN (jika checkbox ppn_checkbox dicentang)
        $ppnAmount = 0;
        if ($request->has('ppn_checkbox') && $request->ppn_checkbox) {
            $ppnAmount = (int) round($dpp * 0.11);
        }

        // PPH 22: dari item barang, threshold > 2jt
        $pph22Amount = 0;
        $tarifPajak22 = (float) ($request->tarif_pajak ?? 0);
        if ($tarifPajak22 > 0 && $dppBarang > 2000000) {
            $pph22Amount = (int) round($dppBarang * $tarifPajak22 / 100);
        }

        // PPH 23: dari item jasa saja
        $pph23Amount = 0;
        $tarifPajak23 = (float) ($request->tarif_pajak_23 ?? 0);
        if ($tarifPajak23 > 0 && $dppJasa > 0) {
            $pph23Amount = (int) round($dppJasa * $tarifPajak23 / 100);
        }

        $pphAmount = $pph22Amount + $pph23Amount;

        // Derive jenis_pph
        if ($pph22Amount > 0 && $pph23Amount > 0)
            $jenisPph = '22,23';
        elseif ($pph22Amount > 0)
            $jenisPph = '22';
        elseif ($pph23Amount > 0)
            $jenisPph = '23';
        else
            $jenisPph = '';

        // Total Akhir = DPP
        $totalAkhir = $dpp;

        // Get staff for snapshot (filter by instansi)
        $userInstansi = auth()->user()->instansi;
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->where('instansi', $userInstansi)->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->where('instansi', $userInstansi)->first();
        $pptk = Staff::findOrFail($request->pptk_1_id);

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
        $kuitansi = Kuitansi::with(['rekanan', 'pptk', 'kodeRekening.subKegiatan.kegiatan'])->findOrFail($id);
        return response()->json($kuitansi);
    }

    public function update(Request $request, string $id)
    {
        if ($request->filled('periode_lengkap')) {
            $request->merge([
                'periode_lengkap' => strtoupper($request->periode_lengkap),
            ]);
        }

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
            'pptk_1_id' => 'required|exists:staff,id',
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

        $kuitansi = Kuitansi::findOrFail($id);
        $penerima = $this->resolvePenerima($request);

        $rincianItem = null;
        if ($request->rincian_item_json) {
            $rincianItem = json_decode($request->rincian_item_json, true);
        }

        // Calculate DPP total, DPP Barang (non-jasa), DPP Jasa
        $dpp = 0;
        $dppBarang = 0;
        $dppJasa = 0;
        if (is_array($rincianItem)) {
            foreach ($rincianItem as $item) {
                $jumlahRaw = $item['jumlah'] ?? null;
                $jumlah = (is_numeric($jumlahRaw) && (float) $jumlahRaw > 0) ? (int) $jumlahRaw : 1;
                $harga = (float) ($item['harga_satuan'] ?? 0);
                $subtotal = $jumlah * $harga;
                $dpp += $subtotal;
                if (!empty($item['is_jasa'])) {
                    $dppJasa += $subtotal;
                } else {
                    $dppBarang += $subtotal;
                }
            }
        }
        $dpp = (int) round($dpp);
        $dppBarang = (int) round($dppBarang);
        $dppJasa = (int) round($dppJasa);

        // Calculate PPN (jika checkbox ppn_checkbox dicentang)
        $ppnAmount = 0;
        if ($request->has('ppn_checkbox') && $request->ppn_checkbox) {
            $ppnAmount = (int) round($dpp * 0.11);
        }

        // PPH 22: dari item barang, threshold > 2jt
        $pph22Amount = 0;
        $tarifPajak22 = (float) ($request->tarif_pajak ?? 0);
        if ($tarifPajak22 > 0 && $dppBarang > 2000000) {
            $pph22Amount = (int) round($dppBarang * $tarifPajak22 / 100);
        }

        // PPH 23: dari item jasa saja
        $pph23Amount = 0;
        $tarifPajak23 = (float) ($request->tarif_pajak_23 ?? 0);
        if ($tarifPajak23 > 0 && $dppJasa > 0) {
            $pph23Amount = (int) round($dppJasa * $tarifPajak23 / 100);
        }

        $pphAmount = $pph22Amount + $pph23Amount;

        // Derive jenis_pph
        if ($pph22Amount > 0 && $pph23Amount > 0)
            $jenisPph = '22,23';
        elseif ($pph22Amount > 0)
            $jenisPph = '22';
        elseif ($pph23Amount > 0)
            $jenisPph = '23';
        else
            $jenisPph = '';

        // Total Akhir = DPP
        $totalAkhir = $dpp;

        // Get staff for snapshot (filter by instansi)
        $userInstansi = auth()->user()->instansi;
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->where('instansi', $userInstansi)->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->where('instansi', $userInstansi)->first();
        $pptk = Staff::findOrFail($request->pptk_1_id);

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
        $kuitansi = Kuitansi::findOrFail($id);
        $kuitansi->delete();

        return redirect()->route('kuitansi.index')->with('success', 'kuitansi berhasil dihapus.');
    }

    public function preview(string $id)
    {
        $kuitansi = Kuitansi::with(['rekanan', 'pptk', 'kodeRekening.subKegiatan.kegiatan'])->findOrFail($id);

        // Get fixed staff
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->first();

        // Get instansi data for kop
        $instansiData = \App\Models\Instansi::where('nama', $kuitansi->instansi)->first();

        return view('preview.kuitansi', compact('kuitansi', 'penggunaAnggaran', 'bendaharaPengeluaran', 'instansiData'));
    }

    // API endpoints for cascading selects
    public function getKegiatan()
    {
        $kegiatan = Kegiatan::selectRaw('id_giat as id, id_giat, kode_giat as kode, kode_giat, nama_giat as nama, nama_giat')
            ->distinct()
            ->orderBy('kode')
            ->get();

        return response()->json($kegiatan);
    }

    public function getSubKegiatan(Request $request)
    {
        $idGiat = $request->query('id_giat') ?? $request->query('id');

        $subKegiatan = SubKegiatan::selectRaw('id_sub_giat as id, id_sub_giat, id_giat, kode_sub_giat as kode, kode_sub_giat, nama_sub_giat as nama, nama_sub_giat')
            ->where('id_giat', $idGiat)
            ->distinct()
            ->orderBy('kode')
            ->get();

        return response()->json($subKegiatan);
    }

    public function getKodeRekening(Request $request)
    {
        $idSubGiat = $request->query('id_sub_giat') ?? $request->query('id');

        $kodeRekening = KodeRekening::selectRaw('id, id_akun, id_sub_giat, kode_akun as kode, kode_akun, nama_akun as nama, nama_akun')
            ->where('id_sub_giat', $idSubGiat)
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

        $dpp = 0;
        $dppBarang = 0;
        $dppJasa = 0;

        foreach ($rincianItem as $item) {
            $jumlahRaw = $item['jumlah'] ?? null;
            $jumlah = (is_numeric($jumlahRaw) && (float) $jumlahRaw > 0) ? (int) $jumlahRaw : 1;
            $harga = (float) ($item['harga_satuan'] ?? 0);
            $subtotal = $jumlah * $harga;

            $dpp += $subtotal;
            if (!empty($item['is_jasa'])) {
                $dppJasa += $subtotal;
            } else {
                $dppBarang += $subtotal;
            }
        }

        if (!empty($kuitansi->kode_objek_pajak_23)) {
            return (int) round($dppJasa);
        }

        if (!empty($kuitansi->kode_objek_pajak)) {
            return (int) round($dppBarang > 0 ? $dppBarang : $dpp);
        }

        return (int) round($dpp);
    }

    public function exportBupotXml(Request $request)
    {
        $bulan = $request->query('bulan', date('n')); // Default bulan sekarang
        $tahun = $request->query('tahun', date('Y')); // Default tahun sekarang

        // Ambil data kuitansi berdasarkan bulan dan tahun dari tanggal_pemotongan
        // Punya kode_objek_pajak (PPh 22) ATAU kode_objek_pajak_23 (PPh 23)
        $kuitansis = Kuitansi::with('rekanan')
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

        // NPWP Pemotong - ambil dari instansi user yang login
        $userInstansi = auth()->user()->instansi;
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

        // Ambil semua kuitansi yang dipilih
        $allSelected = Kuitansi::with('rekanan')
            ->whereIn('id', $kuitansiIds)
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

        // NPWP Pemotong - ambil dari instansi user yang login
        $userInstansi = auth()->user()->instansi;
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
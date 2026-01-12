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
        $kuitansis = Kuitansi::with('rekanan')->get();
        $rekanans = Rekanan::all();
        $pptks = Staff::where('status', 'PPTK')->get();
        $bendaharaBarang = Staff::where('status', 'Bendahara Barang')->first();
        $kodeObjekPajaks = DB::table('kode_objek_pajaks')->orderBy('kode')->get();
        return view('kuitansi', compact('kuitansis', 'rekanans', 'pptks', 'bendaharaBarang', 'kodeObjekPajaks'));
    }

    public function getNextPeriodeNumber(Request $request)
    {
        $periodeType = $request->query('periode_type');
        
        if (!in_array($periodeType, ['TU', 'GU'])) {
            return response()->json(['error' => 'Invalid periode type'], 400);
        }

        // Get the highest periode_number for this periode_type
        // This is to show user what the last periode number was, not for auto-numbering kuitansi
        $lastKuitansi = Kuitansi::where('periode_type', $periodeType)
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
        $request->validate([
            'nomor_rekening' => 'required|string|max:255',
            'periode_lengkap' => ['required', 'string', 'regex:/^(TU|GU)-\d+$/'],
            'nomor_urut' => 'required|string|max:3',
            'rekanan_id' => 'required|exists:rekanans,id',
            'tanggal_kuitansi' => 'required|date',
            'jenis_pph' => 'nullable|in:22,23',
            'rincian_item_json' => 'nullable|json',
            'pptk_1_id' => 'required|exists:staff,id',
        ]);

        // Parse periode_lengkap format "TU-1" into type and number
        list($periodeType, $periodeNumber) = explode('-', $request->periode_lengkap);
        $periodeNumber = (int) $periodeNumber;
        $nomorUrut = (int) $request->nomor_urut;

        $rekanan = Rekanan::findOrFail($request->rekanan_id);
        
        $rincianItem = null;
        if ($request->rincian_item_json) {
            $rincianItem = json_decode($request->rincian_item_json, true);
        }

        // Calculate DPP from items
        $dpp = 0;
        if (is_array($rincianItem)) {
            foreach ($rincianItem as $item) {
                $jumlah = isset($item['jumlah']) ? (int) $item['jumlah'] : 0;
                $harga = isset($item['harga_satuan']) ? (float) $item['harga_satuan'] : 0;
                $dpp += $jumlah * $harga;
            }
        }
        $dpp = (int) round($dpp);

        // Calculate PPN (jika checkbox ppn_checkbox dicentang)
        $ppnAmount = 0;
        if ($request->has('ppn_checkbox') && $request->ppn_checkbox) {
            $ppnAmount = (int) round($dpp * 0.11); // PPN 11%
        }
        
        // Calculate PPH based on tarif_pajak from kode_objek_pajak
        // PPH 22: hanya untuk belanja > 2.000.000
        // PPH 23: berlaku untuk semua belanja
        $pphAmount = 0;
        if (!empty($request->tarif_pajak)) {
            $tarifPajak = (float) $request->tarif_pajak;
            $jenisPph = $request->jenis_pph ?? '';
            
            // Check if PPH 22 dengan amount <= 2M, maka tidak kena pajak
            if ($jenisPph === '22' && $dpp <= 2000000) {
                $pphAmount = 0;
            } else {
                // PPH 23 atau PPH 22 dengan belanja > 2M
                $pphAmount = (int) round($dpp * $tarifPajak / 100);
            }
        }
        
        // Total Akhir = DPP + PPN - PPH
        $totalAkhir = $dpp + $ppnAmount - $pphAmount;
        
        // Get staff for snapshot
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->first();
        $pptk = Staff::findOrFail($request->pptk_1_id);
        
        // Handle nama_bendahara_barang from form input (if provided)
        $namaBendaharaBarang = null;
        $nipBendaharaBarang = null;
        if ($request->filled('nama_bendahara_barang')) {
            $namaBendaharaBarang = $request->nama_bendahara_barang;
            $nipBendaharaBarang = $request->nip_bendahara_barang;
        }
        
        $noBuku = $periodeType . ' ' . $periodeNumber . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
        
        Kuitansi::create([
            'nomor_rekening' => $request->nomor_rekening,
            'periode_type' => $periodeType,
            'periode_number' => $periodeNumber,
            'nomor_urut' => $nomorUrut,
            'no_buku' => $noBuku,
            'rekanan_id' => $request->rekanan_id,
            'nama_penerima' => $rekanan->nama_perusahaan,
            'tanggal_kuitansi' => $request->tanggal_kuitansi,
            'ppn' => $ppnAmount,
            'pph' => $pphAmount,
            'jenis_pph' => $request->jenis_pph,
            'untuk_pembayaran' => $request->untuk_pembayaran,
            'total_akhir' => $totalAkhir,
            'rincian_item' => $rincianItem,
            'kode_objek_pajak' => $request->kode_objek_pajak,
            'tarif_pajak' => $request->tarif_pajak,
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
        ]);

        return redirect()->route('kuitansi.index')->with('success', 'kuitansi berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $kuitansi = Kuitansi::with(['rekanan', 'pptk'])->findOrFail($id);
        return response()->json($kuitansi);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nomor_rekening' => 'required|string|max:255',
            'periode_lengkap' => ['required', 'string', 'regex:/^(TU|GU)-\d+$/'],
            'nomor_urut' => 'required|string|max:3',
            'rekanan_id' => 'required|exists:rekanans,id',
            'tanggal_kuitansi' => 'required|date',
            'jenis_pph' => 'nullable|in:22,23',
            'rincian_item_json' => 'nullable|json',
            'pptk_1_id' => 'required|exists:staff,id',
        ]);

        // Parse periode_lengkap format "TU-1" into type and number
        list($periodeType, $periodeNumber) = explode('-', $request->periode_lengkap);
        $periodeNumber = (int) $periodeNumber;
        $nomorUrut = (int) $request->nomor_urut;

        $kuitansi = Kuitansi::findOrFail($id);
        $rekanan = Rekanan::findOrFail($request->rekanan_id);
        
        $rincianItem = null;
        if ($request->rincian_item_json) {
            $rincianItem = json_decode($request->rincian_item_json, true);
        }

        // Calculate DPP from items
        $dpp = 0;
        if (is_array($rincianItem)) {
            foreach ($rincianItem as $item) {
                $jumlah = isset($item['jumlah']) ? (int) $item['jumlah'] : 0;
                $harga = isset($item['harga_satuan']) ? (float) $item['harga_satuan'] : 0;
                $dpp += $jumlah * $harga;
            }
        }
        $dpp = (int) round($dpp);

        // Calculate PPN (jika checkbox ppn_checkbox dicentang)
        $ppnAmount = 0;
        if ($request->has('ppn_checkbox') && $request->ppn_checkbox) {
            $ppnAmount = (int) round($dpp * 0.11); // PPN 11%
        }
        
        // Calculate PPH based on tarif_pajak from kode_objek_pajak
        // PPH 22: hanya untuk belanja > 2.000.000
        // PPH 23: berlaku untuk semua belanja
        $pphAmount = 0;
        if (!empty($request->tarif_pajak)) {
            $tarifPajak = (float) $request->tarif_pajak;
            $jenisPph = $request->jenis_pph ?? '';
            
            // Check if PPH 22 dengan amount <= 2M, maka tidak kena pajak
            if ($jenisPph === '22' && $dpp <= 2000000) {
                $pphAmount = 0;
            } else {
                // PPH 23 atau PPH 22 dengan belanja > 2M
                $pphAmount = (int) round($dpp * $tarifPajak / 100);
            }
        }
        
        // Total Akhir = DPP + PPN - PPH
        $totalAkhir = $dpp + $ppnAmount - $pphAmount;
        
        // Get staff for snapshot
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->first();
        $pptk = Staff::findOrFail($request->pptk_1_id);
        
        // Handle nama_bendahara_barang from form input (if provided)
        $namaBendaharaBarang = null;
        $nipBendaharaBarang = null;
        if ($request->filled('nama_bendahara_barang')) {
            $namaBendaharaBarang = $request->nama_bendahara_barang;
            $nipBendaharaBarang = $request->nip_bendahara_barang;
        }
        
        // Generate noBuku dengan periode dan nomor_urut yang baru diinput
        $noBuku = $periodeType . ' ' . $periodeNumber . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
        
        $kuitansi->update([
            'nomor_rekening' => $request->nomor_rekening,
            'periode_type' => $periodeType,
            'periode_number' => $periodeNumber,
            'nomor_urut' => $nomorUrut,
            'no_buku' => $noBuku,
            'rekanan_id' => $request->rekanan_id,
            'nama_penerima' => $rekanan->nama_perusahaan,
            'tanggal_kuitansi' => $request->tanggal_kuitansi,
            'ppn' => $ppnAmount,
            'pph' => $pphAmount,
            'jenis_pph' => $request->jenis_pph,
            'untuk_pembayaran' => $request->untuk_pembayaran,
            'total_akhir' => $totalAkhir,
            'rincian_item' => $rincianItem,
            'kode_objek_pajak' => $request->kode_objek_pajak,
            'tarif_pajak' => $request->tarif_pajak,
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
        $kuitansi = Kuitansi::with(['rekanan', 'pptk'])->findOrFail($id);
        
        // Get fixed staff
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->first();
        
        return view('preview.kuitansi', compact('kuitansi', 'penggunaAnggaran', 'bendaharaPengeluaran'));
    }

    // API endpoints for cascading selects
    public function getKegiatan()
    {
        $kegiatan = Kegiatan::select('id_giat', 'kode_giat', 'nama_giat')
            ->groupBy('id_giat', 'kode_giat', 'nama_giat')
            ->orderBy('kode_giat')
            ->get();
        
        return response()->json($kegiatan);
    }

    public function getSubKegiatan(Request $request)
    {
        $idGiat = $request->query('id_giat');
        
        $subKegiatan = SubKegiatan::select('id_sub_giat', 'kode_sub_giat', 'nama_sub_giat')
            ->where('id_giat', $idGiat)
            ->groupBy('id_sub_giat', 'kode_sub_giat', 'nama_sub_giat')
            ->orderBy('kode_sub_giat')
            ->get();
        
        return response()->json($subKegiatan);
    }

    public function getKodeRekening(Request $request)
    {
        $idSubGiat = $request->query('id_sub_giat');
        
        $kodeRekening = KodeRekening::select('id_akun', 'kode_akun', 'nama_akun', 'nilai_anggaran')
            ->where('id_sub_giat', $idSubGiat)
            ->orderBy('kode_akun')
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

    public function exportBupotXml(Request $request)
    {
        $bulan = $request->query('bulan', date('n')); // Default bulan sekarang
        $tahun = $request->query('tahun', date('Y')); // Default tahun sekarang
        
        // Ambil data kuitansi berdasarkan bulan dan tahun dari tanggal_pemotongan
        // Hanya yang belanja >= 2M dan punya data BuPot lengkap
        $kuitansis = Kuitansi::with('rekanan')
            ->whereNotNull('kode_objek_pajak')
            ->whereNotNull('dpp')
            ->whereYear('tanggal_pemotongan', $tahun)
            ->whereMonth('tanggal_pemotongan', $bulan)
            ->where('dpp', '>=', 2000000) // Hanya belanja >= 2M
            ->orderBy('tanggal_pemotongan')
            ->get();

        if ($kuitansis->isEmpty()) {
            return back()->with('error', 'Tidak ada data kuitansi dengan belanja ≥ 2.000.000 untuk periode tersebut. Hanya kuitansi dengan belanja ≥ 2.000.000 yang perlu dibuatkan BuPot.');
        }

        // NPWP Pemotong - ambil dari config atau database
        $npwpPemotong = env('NPWP_INSTANSI', '0002928463912000');
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
            $bpu->addChild('TaxCertificate', 'N/A');
            $bpu->addChild('TaxObjectCode', $kuitansi->kode_objek_pajak);
            $bpu->addChild('TaxBase', number_format($kuitansi->dpp, 0, '', ''));
            $bpu->addChild('Rate', $kuitansi->tarif_pajak);
            $bpu->addChild('Document', $kuitansi->jenis_dokumen);
            $bpu->addChild('DocumentNumber', $kuitansi->nomor_urut . '/' . $kuitansi->periode_type . ' ' . $kuitansi->periode_number);
            $bpu->addChild('DocumentDate', \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('Y-m-d'));
            $bpu->addChild('IDPlaceOfBusinessActivity', $idTkuPemotong);
            $bpu->addChild('GovTreasurerOpt', 'N/A');
            $sp2d = $bpu->addChild('SP2DNumber');
            $sp2d->addAttribute('xsi:nil', 'true');
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
            return back()->with('error', 'Pilih minimal 1 kuitansi untuk export XML.');
        }

        // Ambil semua kuitansi yang dipilih
        $allSelected = Kuitansi::with('rekanan')
            ->whereIn('id', $kuitansiIds)
            ->orderBy('tanggal_pemotongan')
            ->get();

        // Filter hanya yang punya belanja >= 2M dan kode_objek_pajak + dpp lengkap
        $validKuitansis = $allSelected->filter(function($k) {
            $dpp = (int)$k->dpp;
            // Hanya include jika belanja >= 2M dan punya data BuPot lengkap
            return $dpp >= 2000000 && !is_null($k->kode_objek_pajak) && !is_null($k->dpp);
        })->values();

        if ($validKuitansis->isEmpty()) {
            return back()->with('error', 'Tidak ada kuitansi dengan belanja ≥ 2.000.000 dan data BuPot lengkap di antara pilihan Anda. Hanya kuitansi dengan belanja ≥ 2.000.000 yang perlu dibuatkan BuPot.');
        }

        // NPWP Pemotong - ambil dari config atau database
        $npwpPemotong = env('NPWP_INSTANSI', '0002928463912000');
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
            $bpu->addChild('TaxCertificate', 'N/A');
            $bpu->addChild('TaxObjectCode', $kuitansi->kode_objek_pajak);
            $bpu->addChild('TaxBase', number_format($kuitansi->dpp, 0, '', ''));
            $bpu->addChild('Rate', $kuitansi->tarif_pajak);
            $bpu->addChild('Document', $kuitansi->jenis_dokumen);
            $bpu->addChild('DocumentNumber', $kuitansi->nomor_urut . '/' . $kuitansi->periode_type . ' ' . $kuitansi->periode_number);
            $bpu->addChild('DocumentDate', \Carbon\Carbon::parse($kuitansi->tanggal_kuitansi)->format('Y-m-d'));
            $bpu->addChild('IDPlaceOfBusinessActivity', $idTkuPemotong);
            $bpu->addChild('GovTreasurerOpt', 'N/A');
            $sp2d = $bpu->addChild('SP2DNumber');
            $sp2d->addAttribute('xsi:nil', 'true');
            $bpu->addChild('WithholdingDate', \Carbon\Carbon::parse($kuitansi->tanggal_pemotongan)->format('Y-m-d'));
        }

        $xmlString = $xmlObj->asXML();
        $filename = "BuPot_PPh_Selected_" . date('YmdHis') . ".xml";

        return response($xmlString, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
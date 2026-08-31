<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kuitansi;
use App\Models\Rekanan;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KuitansiApiController extends Controller
{
    /**
     * GET /api/v1/kuitansi
     * List kuitansi. Superadmin sees all; others filtered by instansi.
     * Optional query params: periode_type, periode_number, rekanan_id
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $query = Kuitansi::with('rekanan');

        if (!$user->is_superadmin) {
            $query->where('instansi', $user->instansi);
        }

        if ($request->filled('periode_type')) {
            $query->where('periode_type', $request->periode_type);
        }

        if ($request->filled('periode_number')) {
            $query->where('periode_number', (int) $request->periode_number);
        }

        if ($request->filled('rekanan_id')) {
            $query->where('rekanan_id', $request->rekanan_id);
        }

        $kuitansis = $query->orderBy('created_at', 'desc')->get();

        return response()->json($kuitansis);
    }

    /**
     * POST /api/v1/kuitansi
     * Create a new kuitansi record.
     *
     * Required body fields:
     *   nomor_rekening, periode_lengkap (e.g. "TU-1"), nomor_urut,
     *   rekanan_id, tanggal_kuitansi, pptk_1_id
     *
     * Optional body fields:
     *   rincian_item (array), ppn_checkbox (bool), tarif_pajak (numeric),
     *   tarif_pajak_23 (numeric), kode_objek_pajak, kode_objek_pajak_23,
     *   untuk_pembayaran, jenis_dokumen, tanggal_pemotongan,
     *   nama_bendahara_barang, nip_bendahara_barang
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_rekening' => 'required|string|max:255',
            'periode_lengkap' => ['required', 'string', 'regex:/^(TU|GU)-\d+$/'],
            'nomor_urut' => 'required|integer|min:1',
            'rekanan_id' => 'required|exists:rekanans,id',
            'tanggal_kuitansi' => 'required|date',
            'pptk_1_id' => 'required|exists:staff,id',
            'rincian_item' => 'nullable|array',
            'ppn_checkbox' => 'nullable|boolean',
            'tarif_pajak' => 'nullable|numeric|min:0',
            'tarif_pajak_23' => 'nullable|numeric|min:0',
            'kode_objek_pajak' => 'nullable|string|max:255',
            'kode_objek_pajak_23' => 'nullable|string|max:255',
            'untuk_pembayaran' => 'nullable|string',
            'jenis_dokumen' => 'nullable|string|max:100',
            'tanggal_pemotongan' => 'nullable|date',
            'nama_bendahara_barang' => 'nullable|string|max:255',
            'nip_bendahara_barang' => 'nullable|string|max:50',
        ]);

        $user = Auth::guard('api')->user();

        [$periodeType, $periodeNumber] = explode('-', $validated['periode_lengkap']);
        $periodeNumber = (int) $periodeNumber;
        $nomorUrut = (int) $validated['nomor_urut'];

        $rekanan = Rekanan::findOrFail($validated['rekanan_id']);

        // Calculate DPP, DPP Barang, DPP Jasa from rincian_item array
        $dpp = 0;
        $dppBarang = 0;
        $dppJasa = 0;
        $rincianItem = $validated['rincian_item'] ?? null;

        if (is_array($rincianItem)) {
            foreach ($rincianItem as $item) {
                $jumlah = (int) ($item['jumlah'] ?? 0);
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

        // PPN
        $ppnAmount = 0;
        if (!empty($validated['ppn_checkbox'])) {
            $ppnAmount = (int) round($dpp * 0.11);
        }

        // PPH 22 (barang, threshold > 2 jt)
        $pph22Amount = 0;
        $tarifPajak22 = (float) ($validated['tarif_pajak'] ?? 0);
        if ($tarifPajak22 > 0 && $dppBarang > 2000000) {
            $pph22Amount = (int) round($dppBarang * $tarifPajak22 / 100);
        }

        // PPH 23 (jasa)
        $pph23Amount = 0;
        $tarifPajak23 = (float) ($validated['tarif_pajak_23'] ?? 0);
        if ($tarifPajak23 > 0 && $dppJasa > 0) {
            $pph23Amount = (int) round($dppJasa * $tarifPajak23 / 100);
        }

        $pphAmount = $pph22Amount + $pph23Amount;

        if ($pph22Amount > 0 && $pph23Amount > 0)
            $jenisPph = '22,23';
        elseif ($pph22Amount > 0)
            $jenisPph = '22';
        elseif ($pph23Amount > 0)
            $jenisPph = '23';
        else
            $jenisPph = '';

        $totalAkhir = $dpp + $ppnAmount - $pphAmount;

        // Fetch snapshot staff data
        $userInstansi = $user->instansi;
        $penggunaAnggaran = Staff::where('status', 'Pengguna Anggaran')->where('instansi', $userInstansi)->first();
        $bendaharaPengeluaran = Staff::where('status', 'Bendahara Pengeluaran')->where('instansi', $userInstansi)->first();
        $pptk = Staff::findOrFail($validated['pptk_1_id']);

        $noBuku = $periodeType . ' ' . $periodeNumber . ' / ' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        $kode23 = isset($validated['kode_objek_pajak_23'])
            ? trim(explode(' - ', $validated['kode_objek_pajak_23'])[0])
            : null;
        $kode22 = isset($validated['kode_objek_pajak'])
            ? trim(explode(' - ', $validated['kode_objek_pajak'])[0])
            : null;

        $kuitansi = Kuitansi::create([
            'nomor_rekening' => $validated['nomor_rekening'],
            'periode_type' => $periodeType,
            'periode_number' => $periodeNumber,
            'nomor_urut' => $nomorUrut,
            'no_buku' => $noBuku,
            'rekanan_id' => $validated['rekanan_id'],
            'nama_penerima' => $rekanan->nama_perusahaan,
            'tanggal_kuitansi' => $validated['tanggal_kuitansi'],
            'tanggal_pemotongan' => $validated['tanggal_pemotongan'] ?? $validated['tanggal_kuitansi'],
            'ppn' => $ppnAmount,
            'pph' => $pphAmount,
            'pph_22' => $pph22Amount,
            'pph_23' => $pph23Amount,
            'jenis_pph' => $jenisPph,
            'untuk_pembayaran' => $validated['untuk_pembayaran'] ?? null,
            'total_akhir' => $totalAkhir,
            'rincian_item' => $rincianItem,
            'kode_objek_pajak' => $kode22,
            'tarif_pajak' => $validated['tarif_pajak'] ?: null,
            'kode_objek_pajak_23' => $kode23,
            'tarif_pajak_23' => $validated['tarif_pajak_23'] ?: null,
            'dpp' => $dpp,
            'jenis_dokumen' => $validated['jenis_dokumen'] ?? 'PaymentProof',
            'pptk_1_id' => $validated['pptk_1_id'],
            'nama_pengguna_anggaran' => $penggunaAnggaran->nama ?? null,
            'nip_pengguna_anggaran' => $penggunaAnggaran->nip ?? null,
            'nama_bendahara_pengeluaran' => $bendaharaPengeluaran->nama ?? null,
            'nip_bendahara_pengeluaran' => $bendaharaPengeluaran->nip ?? null,
            'nama_bendahara_barang' => $validated['nama_bendahara_barang'] ?? null,
            'nip_bendahara_barang' => $validated['nip_bendahara_barang'] ?? null,
            'nama_pptk' => $pptk->nama,
            'nip_pptk' => $pptk->nip,
            'instansi' => $userInstansi,
        ]);

        return response()->json([
            'message' => 'Kuitansi berhasil ditambahkan.',
            'kuitansi' => $kuitansi->load('rekanan'),
        ], 201);
    }
}

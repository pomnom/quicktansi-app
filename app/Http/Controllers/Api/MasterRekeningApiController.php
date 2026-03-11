<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\KodeRekening;
use App\Models\SubKegiatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterRekeningApiController extends Controller
{
    // KEGIATAN

    public function indexKegiatan(): JsonResponse
    {
        $user  = Auth::guard('api')->user();
        $query = Kegiatan::orderBy('kode_giat');

        if (! $user->is_superadmin) {
            $query->where('instansi', $user->instansi);
        }

        return response()->json($query->get());
    }

    public function storeKegiatan(Request $request): JsonResponse
    {
        $user     = Auth::guard('api')->user();
        $instansi = $user->instansi;

        $validated = $request->validate([
            'id_giat'   => [
                'required', 'integer',
                function ($attr, $value, $fail) use ($instansi) {
                    if (Kegiatan::where('id_giat', $value)->where('instansi', $instansi)->exists()) {
                        $fail('ID Giat sudah digunakan untuk instansi ini.');
                    }
                },
            ],
            'kode_giat' => 'required|string|max:50',
            'nama_giat' => 'required|string|max:255',
        ]);

        $validated['instansi'] = $instansi;
        $kegiatan = Kegiatan::create($validated);

        return response()->json([
            'message'  => 'Kegiatan berhasil ditambahkan.',
            'kegiatan' => $kegiatan,
        ], 201);
    }

    // SUB KEGIATAN

    public function indexSubKegiatan(Request $request): JsonResponse
    {
        $user  = Auth::guard('api')->user();
        $query = SubKegiatan::with('kegiatan')->orderBy('kode_sub_giat');

        if (! $user->is_superadmin) {
            $query->where('instansi', $user->instansi);
        }

        if ($request->filled('id_giat')) {
            $query->where('id_giat', $request->id_giat);
        }

        return response()->json($query->get());
    }

    public function storeSubKegiatan(Request $request): JsonResponse
    {
        $user     = Auth::guard('api')->user();
        $instansi = $user->instansi;

        $validated = $request->validate([
            'id_giat' => [
                'required', 'integer',
                function ($attr, $value, $fail) use ($instansi) {
                    if (! Kegiatan::where('id_giat', $value)->where('instansi', $instansi)->exists()) {
                        $fail('Kegiatan tidak ditemukan untuk instansi ini.');
                    }
                },
            ],
            'id_sub_giat' => [
                'required', 'integer',
                function ($attr, $value, $fail) use ($instansi) {
                    if (SubKegiatan::where('id_sub_giat', $value)->where('instansi', $instansi)->exists()) {
                        $fail('ID Sub Giat sudah digunakan untuk instansi ini.');
                    }
                },
            ],
            'kode_sub_giat' => 'required|string|max:50',
            'nama_sub_giat' => 'required|string|max:255',
        ]);

        $validated['instansi'] = $instansi;
        $subKegiatan = SubKegiatan::create($validated);

        return response()->json([
            'message'      => 'Sub Kegiatan berhasil ditambahkan.',
            'sub_kegiatan' => $subKegiatan,
        ], 201);
    }

    // KODE REKENING

    public function indexKodeRekening(Request $request): JsonResponse
    {
        $user  = Auth::guard('api')->user();
        $query = KodeRekening::with('subKegiatan')->orderBy('kode_akun');

        if (! $user->is_superadmin) {
            $query->where('instansi', $user->instansi);
        }

        if ($request->filled('id_sub_giat')) {
            $query->where('id_sub_giat', $request->id_sub_giat);
        }

        return response()->json($query->get());
    }

    public function storeKodeRekening(Request $request): JsonResponse
    {
        $user     = Auth::guard('api')->user();
        $instansi = $user->instansi;

        $validated = $request->validate([
            'id_sub_giat' => [
                'required', 'integer',
                function ($attr, $value, $fail) use ($instansi) {
                    if (! SubKegiatan::where('id_sub_giat', $value)->where('instansi', $instansi)->exists()) {
                        $fail('Sub Kegiatan tidak ditemukan untuk instansi ini.');
                    }
                },
            ],
            'id_akun' => [
                'required', 'integer',
                function ($attr, $value, $fail) use ($instansi) {
                    if (KodeRekening::where('id_akun', $value)->where('instansi', $instansi)->exists()) {
                        $fail('ID Akun sudah digunakan untuk instansi ini.');
                    }
                },
            ],
            'kode_akun' => 'required|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'is_blokir' => 'nullable|boolean',
        ]);

        $validated['is_blokir'] = $request->boolean('is_blokir');
        $validated['instansi']  = $instansi;
        $kodeRekening = KodeRekening::create($validated);

        return response()->json([
            'message'       => 'Kode Rekening berhasil ditambahkan.',
            'kode_rekening' => $kodeRekening,
        ], 201);
    }
}

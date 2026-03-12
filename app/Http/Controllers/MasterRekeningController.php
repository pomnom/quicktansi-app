<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KodeRekening;
use App\Models\Kuitansi;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class MasterRekeningController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $instansi = $user->instansi;

        if ($user->is_superadmin) {
            $kegiatans = Kegiatan::orderBy('kode_giat')->get();
            $subKegiatans = SubKegiatan::with('kegiatan')->orderBy('kode_sub_giat')->get();
            $kodeRekenings = KodeRekening::with('subKegiatan')->orderBy('kode_akun')->get();
        } else {
            $kegiatans = Kegiatan::where('instansi', $instansi)->orderBy('kode_giat')->get();
            $subKegiatans = SubKegiatan::where('instansi', $instansi)->with('kegiatan')->orderBy('kode_sub_giat')->get();
            $kodeRekenings = KodeRekening::where('instansi', $instansi)->with('subKegiatan')->orderBy('kode_akun')->get();
        }

        return view('master-rekening', compact('kegiatans', 'subKegiatans', 'kodeRekenings'));
    }

    public function storeKegiatan(Request $request)
    {
        $user = auth()->user();
        $instansi = $user->instansi;

        $validated = $request->validate([
            'kode_giat' => 'required|string|max:50',
            'nama_giat' => 'required|string|max:255',
        ]);

        $validated['id_giat'] = (Kegiatan::where('instansi', $instansi)->max('id_giat') ?? 0) + 1;
        $validated['instansi'] = $instansi;

        Kegiatan::create($validated);

        return redirect()->route('master-rekening.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function updateKegiatan(Request $request, string $id)
    {
        $user = auth()->user();
        $instansi = $user->instansi;
        $kegiatan = Kegiatan::findOrFail($id);

        if (!$user->is_superadmin && $kegiatan->instansi !== $instansi) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'kode_giat' => 'required|string|max:50',
            'nama_giat' => 'required|string|max:255',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('master-rekening.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroyKegiatan(string $id)
    {
        $user = auth()->user();
        $kegiatan = Kegiatan::findOrFail($id);

        if (!$user->is_superadmin && $kegiatan->instansi !== $user->instansi) {
            abort(403, 'Unauthorized');
        }

        $rekeningIds = KodeRekening::whereIn(
            'id_sub_giat',
            SubKegiatan::where('id_giat', $kegiatan->id_giat)
                ->where('instansi', $kegiatan->instansi)
                ->pluck('id_sub_giat')
        )->pluck('id_akun');

        if ($rekeningIds->isNotEmpty() && Kuitansi::whereIn('id_akun', $rekeningIds)->exists()) {
            return redirect()->route('master-rekening.index')->with('error', 'Kegiatan tidak bisa dihapus karena sudah dipakai pada data kuitansi.');
        }

        $kegiatan->delete();

        return redirect()->route('master-rekening.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function storeSubKegiatan(Request $request)
    {
        $user = auth()->user();
        $instansi = $user->instansi;

        $validated = $request->validate([
            'id_giat' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($instansi) {
                    $exists = Kegiatan::where('id_giat', $value)
                        ->where('instansi', $instansi)
                        ->exists();
                    if (!$exists) {
                        $fail('Kegiatan tidak ditemukan untuk instansi ini.');
                    }
                },
            ],
            'kode_sub_giat' => 'required|string|max:50',
            'nama_sub_giat' => 'required|string|max:255',
        ]);

        $validated['id_sub_giat'] = (SubKegiatan::where('instansi', $instansi)->max('id_sub_giat') ?? 0) + 1;
        $validated['instansi'] = $instansi;

        SubKegiatan::create($validated);

        return redirect()->route('master-rekening.index')->with('success', 'Sub Kegiatan berhasil ditambahkan.');
    }

    public function updateSubKegiatan(Request $request, string $id)
    {
        $user = auth()->user();
        $instansi = $user->instansi;
        $subKegiatan = SubKegiatan::findOrFail($id);

        if (!$user->is_superadmin && $subKegiatan->instansi !== $instansi) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'id_giat' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($instansi) {
                    $exists = Kegiatan::where('id_giat', $value)
                        ->where('instansi', $instansi)
                        ->exists();
                    if (!$exists) {
                        $fail('Kegiatan tidak ditemukan untuk instansi ini.');
                    }
                },
            ],
            'kode_sub_giat' => 'required|string|max:50',
            'nama_sub_giat' => 'required|string|max:255',
        ]);

        $subKegiatan->update($validated);

        return redirect()->route('master-rekening.index')->with('success', 'Sub Kegiatan berhasil diperbarui.');
    }

    public function destroySubKegiatan(string $id)
    {
        $user = auth()->user();
        $subKegiatan = SubKegiatan::findOrFail($id);

        if (!$user->is_superadmin && $subKegiatan->instansi !== $user->instansi) {
            abort(403, 'Unauthorized');
        }

        $rekeningIds = KodeRekening::where('id_sub_giat', $subKegiatan->id_sub_giat)
            ->where('instansi', $subKegiatan->instansi)
            ->pluck('id_akun');

        if ($rekeningIds->isNotEmpty() && Kuitansi::whereIn('id_akun', $rekeningIds)->exists()) {
            return redirect()->route('master-rekening.index')->with('error', 'Sub Kegiatan tidak bisa dihapus karena sudah dipakai pada data kuitansi.');
        }

        $subKegiatan->delete();

        return redirect()->route('master-rekening.index')->with('success', 'Sub Kegiatan berhasil dihapus.');
    }

    public function storeKodeRekening(Request $request)
    {
        $user = auth()->user();
        $instansi = $user->instansi;

        $validated = $request->validate([
            'id_sub_giat' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($instansi) {
                    $exists = SubKegiatan::where('id_sub_giat', $value)
                        ->where('instansi', $instansi)
                        ->exists();
                    if (!$exists) {
                        $fail('Sub Kegiatan tidak ditemukan untuk instansi ini.');
                    }
                },
            ],
            'kode_akun' => 'required|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'is_blokir' => 'nullable|boolean',
        ]);

        $validated['id_akun'] = (KodeRekening::where('instansi', $instansi)->max('id_akun') ?? 0) + 1;
        $validated['is_blokir'] = $request->boolean('is_blokir');
        $validated['instansi'] = $instansi;

        KodeRekening::create($validated);

        return redirect()->route('master-rekening.index')->with('success', 'Kode Rekening berhasil ditambahkan.');
    }

    public function updateKodeRekening(Request $request, string $id)
    {
        $user = auth()->user();
        $instansi = $user->instansi;
        $kodeRekening = KodeRekening::findOrFail($id);

        if (!$user->is_superadmin && $kodeRekening->instansi !== $instansi) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'id_sub_giat' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($instansi) {
                    $exists = SubKegiatan::where('id_sub_giat', $value)
                        ->where('instansi', $instansi)
                        ->exists();
                    if (!$exists) {
                        $fail('Sub Kegiatan tidak ditemukan untuk instansi ini.');
                    }
                },
            ],
            'kode_akun' => 'required|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'is_blokir' => 'nullable|boolean',
        ]);

        $validated['is_blokir'] = $request->boolean('is_blokir');

        $kodeRekening->update($validated);

        return redirect()->route('master-rekening.index')->with('success', 'Kode Rekening berhasil diperbarui.');
    }

    public function destroyKodeRekening(string $id)
    {
        $user = auth()->user();
        $kodeRekening = KodeRekening::findOrFail($id);

        if (!$user->is_superadmin && $kodeRekening->instansi !== $user->instansi) {
            abort(403, 'Unauthorized');
        }

        if (Kuitansi::where('id_akun', $kodeRekening->id_akun)->exists()) {
            return redirect()->route('master-rekening.index')->with('error', 'Kode Rekening tidak bisa dihapus karena sudah dipakai pada data kuitansi.');
        }

        $kodeRekening->delete();

        return redirect()->route('master-rekening.index')->with('success', 'Kode Rekening berhasil dihapus.');
    }
}

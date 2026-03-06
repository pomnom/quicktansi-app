<?php

namespace App\Http\Controllers;

use App\Models\Rekanan;
use Illuminate\Http\Request;

class RekananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filter rekanan berdasarkan instansi user yang sedang login
        $userInstansi = auth()->user()->instansi;
        $rekanans = Rekanan::where('instansi', $userInstansi)->latest()->get();
        return view('rekanan', compact('rekanans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'npwp' => 'required|string|max:20|unique:rekanans,npwp',
            'nama_perusahaan' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'nama_pemilik_rekening' => 'required|string|max:255',
        ]);

        // Auto-assign instansi dari user yang sedang login
        $validated['instansi'] = auth()->user()->instansi;

        Rekanan::create($validated);

        return redirect()->route('rekanan.index')->with('success', 'Rekanan berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rekanan $rekanan)
    {
        $validated = $request->validate([
            'npwp' => 'required|string|max:20|unique:rekanans,npwp,' . $rekanan->id,
            'nama_perusahaan' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'bank' => 'required|string|max:255',
            'nama_pemilik_rekening' => 'required|string|max:255',
        ]);

        // Keep instansi unchanged (or assign if missing)
        $validated['instansi'] = $rekanan->instansi ?? auth()->user()->instansi;

        $rekanan->update($validated);

        return redirect()->route('rekanan.index')->with('success', 'Rekanan berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rekanan $rekanan)
    {
        $rekanan->delete();
        return redirect()->route('rekanan.index')->with('success', 'Rekanan berhasil dihapus!');
    }
}

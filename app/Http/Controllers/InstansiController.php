<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instansis = Instansi::orderBy('nama', 'asc')->get();
        return view('instansi', compact('instansis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        Instansi::create($request->all());

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $instansi = Instansi::findOrFail($id);
        return response()->json($instansi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $instansi = Instansi::findOrFail($id);
        $instansi->update($request->all());

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $instansi = Instansi::findOrFail($id);
        
        // Check if instansi is used by users
        $usersCount = \App\Models\User::where('instansi', $instansi->nama)->count();
        
        if ($usersCount > 0) {
            return redirect()->route('instansi.index')->with('error', 'Tidak dapat menghapus instansi yang masih digunakan oleh ' . $usersCount . ' user.');
        }
        
        $instansi->delete();

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil dihapus.');
    }
}

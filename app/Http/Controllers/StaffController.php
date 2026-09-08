<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filter staff berdasarkan instansi user yang sedang login
        $userInstansi = auth()->user()->instansi;
        $staff = Staff::where('instansi', $userInstansi)->get();
        return view('staff', compact('staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:staff,nip|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'golongan' => 'required|string|max:50',
            'status' => 'nullable|in:Pengguna Anggaran,PPK,PPTK,Bendahara Pengeluaran,Bendahara Barang',
        ]);

        // Auto-assign instansi dari user yang sedang login
        $validated['instansi'] = auth()->user()->instansi;

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $userInstansi = auth()->user()->instansi;
        $staff = Staff::where('instansi', $userInstansi)->findOrFail($id);
        return response()->json($staff);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:255|unique:staff,nip,' . $id,
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'golongan' => 'required|string|max:50',
            'status' => 'nullable|in:Pengguna Anggaran,PPK,PPTK,Bendahara Pengeluaran,Bendahara Barang',
        ]);

        $userInstansi = auth()->user()->instansi;
        $staff = Staff::where('instansi', $userInstansi)->findOrFail($id);

        // Keep instansi unchanged
        $validated['instansi'] = $staff->instansi;

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userInstansi = auth()->user()->instansi;
        $staff = Staff::where('instansi', $userInstansi)->findOrFail($id);
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus.');
    }
}

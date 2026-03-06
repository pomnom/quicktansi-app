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
        $request->validate([
            'nip' => 'required|string|unique:staff,nip|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'golongan' => 'required|string|max:50',
            'status' => 'nullable|in:Pengguna Anggaran,PPK,PPTK,Bendahara Pengeluaran,Bendahara Barang',
        ]);

        // Auto-assign instansi dari user yang sedang login
        $data = $request->all();
        $data['instansi'] = auth()->user()->instansi;

        Staff::create($data);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $staff = Staff::findOrFail($id);
        return response()->json($staff);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nip' => 'required|string|max:255|unique:staff,nip,' . $id,
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'golongan' => 'required|string|max:50',
            'status' => 'nullable|in:Pengguna Anggaran,PPK,PPTK,Bendahara Pengeluaran,Bendahara Barang',
        ]);

        $staff = Staff::findOrFail($id);
        
        // Keep instansi unchanged (or assign if missing)
        $data = $request->all();
        $data['instansi'] = $staff->instansi ?? auth()->user()->instansi;
        
        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff berhasil dihapus.');
    }
}

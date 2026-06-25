<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    public function __construct()
    {
        // Hanya superadmin yang bisa mengakses instansi
        $this->middleware(function ($request, $next) {
            if (!auth()->user()?->is_superadmin) {
                abort(403, 'Unauthorized - Hanya superadmin yang dapat mengelola instansi.');
            }
            return $next($request);
        });
    }

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
            'nama_pemerintah' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->only(['nama', 'nama_pemerintah', 'npwp', 'alamat', 'no_telp', 'email', 'website']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            try {
                // Ensure directory exists
                $uploadPath = public_path('images/logos');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $file->move($uploadPath, $filename);
                $data['logo'] = $filename;
            } catch (\Exception $e) {
                \Log::error('Logo upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal upload logo: ' . $e->getMessage());
            }
        }

        Instansi::create($data);

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
            'nama_pemerintah' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $instansi = Instansi::findOrFail($id);
        $data = $request->only(['nama', 'nama_pemerintah', 'npwp', 'alamat', 'no_telp', 'email', 'website']);

        if ($request->hasFile('logo')) {
            try {
                // Hapus logo lama jika ada
                if ($instansi->logo && file_exists(public_path('images/logos/' . $instansi->logo))) {
                    unlink(public_path('images/logos/' . $instansi->logo));
                }
                $file = $request->file('logo');
                $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadPath = public_path('images/logos');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $file->move($uploadPath, $filename);
                $data['logo'] = $filename;
            } catch (\Exception $e) {
                \Log::error('Logo upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal upload logo: ' . $e->getMessage());
            }
        }

        $instansi->update($data);

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $instansi = Instansi::findOrFail($id);

        $usersCount = \App\Models\User::where('instansi', $instansi->nama)->count();
        $staffCount = \App\Models\Staff::where('instansi', $instansi->nama)->count();
        $rekananCount = \App\Models\Rekanan::where('instansi', $instansi->nama)->count();
        $kuitansiCount = \App\Models\Kuitansi::where('instansi', $instansi->nama)->count();

        $total = $usersCount + $staffCount + $rekananCount + $kuitansiCount;

        if ($total > 0) {
            $details = [];
            if ($usersCount)
                $details[] = "{$usersCount} user";
            if ($staffCount)
                $details[] = "{$staffCount} staff";
            if ($rekananCount)
                $details[] = "{$rekananCount} rekanan";
            if ($kuitansiCount)
                $details[] = "{$kuitansiCount} kuitansi";

            return redirect()->route('instansi.index')
                ->with('error', 'Tidak dapat menghapus instansi yang masih memiliki data: ' . implode(', ', $details) . '.');
        }

        $instansi->delete();

        return redirect()->route('instansi.index')->with('success', 'Instansi berhasil dihapus.');
    }
}

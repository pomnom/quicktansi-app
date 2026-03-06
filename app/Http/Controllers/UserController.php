<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * UserController constructor.
     * Hanya superadmin yang dapat mengakses manajemen user.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_superadmin) {
                abort(403, 'Unauthorized. Only superadmin can access user management.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Superadmin dapat melihat semua user
        $users = User::all();
        $instansis = Instansi::orderBy('nama')->get();
        return view('user', compact('users', 'instansis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:255|unique:users,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'no_telp' => 'nullable|string|max:20',
            'instansi' => 'nullable|string|max:255',
            'is_superadmin' => 'nullable|boolean',
        ]);

        // Only superadmin can create other superadmins
        $isSuperadmin = false;
        if (auth()->user()->is_superadmin && $request->has('is_superadmin')) {
            $isSuperadmin = true;
        }

        User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'instansi' => $request->instansi,
            'is_superadmin' => $isSuperadmin,
            'password' => Hash::make($request->nip), // Default password = NIP
            'email_verified_at' => now(),
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan dengan password default NIP.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nip' => 'required|string|max:255|unique:users,nip,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'no_telp' => 'nullable|string|max:20',
            'instansi' => 'nullable|string|max:255',
            'is_superadmin' => 'nullable|boolean',
        ]);

        $user = User::findOrFail($id);
        
        // Only superadmin can modify is_superadmin flag
        $isSuperadmin = $user->is_superadmin;
        if (auth()->user()->is_superadmin && $request->has('is_superadmin')) {
            $isSuperadmin = true;
        } elseif (auth()->user()->is_superadmin && !$request->has('is_superadmin')) {
            // Superadmin can uncheck the is_superadmin flag
            $isSuperadmin = false;
        }
        
        $user->update([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'instansi' => $request->instansi,
            'is_superadmin' => $isSuperadmin,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Reset password user ke default (NIP).
     */
    public function resetPassword(string $id)
    {
        $user = User::findOrFail($id);
        
        // Reset password ke NIP
        $user->update([
            'password' => Hash::make($user->nip),
        ]);

        return redirect()->route('user.index')->with('success', 'Password user berhasil direset ke NIP: ' . $user->nip);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Cegah penghapusan user sendiri
        if ($id == auth()->id()) {
            return redirect()->route('user.index')->with('error', 'Tidak dapat menghapus user yang sedang login.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kuitansi;
use App\Models\Rekanan;
use App\Models\Staff;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->is_superadmin;

        if ($isSuperadmin) {
            // Superadmin melihat semua data
            $totalKuitansi = Kuitansi::count();
            $kuitansiBulanIni = Kuitansi::whereMonth('tanggal_kuitansi', date('m'))
                ->whereYear('tanggal_kuitansi', date('Y'))
                ->count();
            $totalRekanan = Rekanan::count();
            $totalStaff = Staff::count();
            $totalUser = User::count();
            $totalNominal = Kuitansi::sum('total_akhir');
            $nominalBulanIni = Kuitansi::whereMonth('tanggal_kuitansi', date('m'))
                ->whereYear('tanggal_kuitansi', date('Y'))
                ->sum('total_akhir');
        } else {
            // User biasa hanya melihat data instansi mereka
            $totalKuitansi = Kuitansi::where('instansi', $user->instansi)->count();
            $kuitansiBulanIni = Kuitansi::where('instansi', $user->instansi)
                ->whereMonth('tanggal_kuitansi', date('m'))
                ->whereYear('tanggal_kuitansi', date('Y'))
                ->count();
            $totalRekanan = Rekanan::where('instansi', $user->instansi)->count();
            $totalStaff = Staff::where('instansi', $user->instansi)->count();
            $totalUser = User::where('instansi', $user->instansi)->count();
            $totalNominal = Kuitansi::where('instansi', $user->instansi)->sum('total_akhir');
            $nominalBulanIni = Kuitansi::where('instansi', $user->instansi)
                ->whereMonth('tanggal_kuitansi', date('m'))
                ->whereYear('tanggal_kuitansi', date('Y'))
                ->sum('total_akhir');
        }

        // Kuitansi terbaru (5 terakhir)
        if ($isSuperadmin) {
            $kuitansiTerbaru = Kuitansi::with('rekanan')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            $kuitansiTerbaru = Kuitansi::with('rekanan')
                ->where('instansi', $user->instansi)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact(
            'totalKuitansi',
            'kuitansiBulanIni',
            'totalRekanan',
            'totalStaff',
            'totalUser',
            'totalNominal',
            'nominalBulanIni',
            'kuitansiTerbaru',
            'isSuperadmin'
        ));
    }
}

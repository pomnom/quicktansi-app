<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\RekananController;
use App\Http\Controllers\StaffController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->name('auth.login')->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard & Main Routes (Protected by auth middleware)
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('user', UserController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::resource('kuitansi', KuitansiController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('kuitansi/get-next-periode', [KuitansiController::class, 'getNextPeriodeNumber'])->name('kuitansi.getNextPeriode');
    Route::get('kuitansi/{id}/preview', [KuitansiController::class, 'preview'])->name('kuitansi.preview');
    Route::get('kuitansi/export/bupot-xml', [KuitansiController::class, 'exportBupotXml'])->name('kuitansi.exportBupotXml');
    Route::post('kuitansi/export/bupot-xml-selected', [KuitansiController::class, 'exportBupotXmlSelected'])->name('kuitansi.exportBupotXmlSelected');

    // API routes for cascading selects
    Route::get('api/kegiatan', [KuitansiController::class, 'getKegiatan'])->name('api.kegiatan');
    Route::get('api/sub-kegiatan', [KuitansiController::class, 'getSubKegiatan'])->name('api.subKegiatan');
    Route::get('api/kode-rekening', [KuitansiController::class, 'getKodeRekening'])->name('api.kodeRekening');
    Route::get('api/tarif-pajak/{kode}', [KuitansiController::class, 'getTarifPajak'])->name('api.tarifPajak');

    Route::resource('rekanan', RekananController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('staff', StaffController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
});

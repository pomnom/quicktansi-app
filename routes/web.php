<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\RekananController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\MasterRekeningController;

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
Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('user', UserController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::post('user/{id}/reset-password', [UserController::class, 'resetPassword'])->name('user.resetPassword');

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
    
    Route::resource('instansi', InstansiController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

    Route::get('master-rekening', [MasterRekeningController::class, 'index'])->name('master-rekening.index');

    Route::post('master-rekening/kegiatan', [MasterRekeningController::class, 'storeKegiatan'])->name('master-rekening.kegiatan.store');
    Route::put('master-rekening/kegiatan/{id}', [MasterRekeningController::class, 'updateKegiatan'])->name('master-rekening.kegiatan.update');
    Route::delete('master-rekening/kegiatan/{id}', [MasterRekeningController::class, 'destroyKegiatan'])->name('master-rekening.kegiatan.destroy');

    Route::post('master-rekening/sub-kegiatan', [MasterRekeningController::class, 'storeSubKegiatan'])->name('master-rekening.sub-kegiatan.store');
    Route::put('master-rekening/sub-kegiatan/{id}', [MasterRekeningController::class, 'updateSubKegiatan'])->name('master-rekening.sub-kegiatan.update');
    Route::delete('master-rekening/sub-kegiatan/{id}', [MasterRekeningController::class, 'destroySubKegiatan'])->name('master-rekening.sub-kegiatan.destroy');

    Route::post('master-rekening/kode-rekening', [MasterRekeningController::class, 'storeKodeRekening'])->name('master-rekening.kode-rekening.store');
    Route::put('master-rekening/kode-rekening/{id}', [MasterRekeningController::class, 'updateKodeRekening'])->name('master-rekening.kode-rekening.update');
    Route::delete('master-rekening/kode-rekening/{id}', [MasterRekeningController::class, 'destroyKodeRekening'])->name('master-rekening.kode-rekening.destroy');
});

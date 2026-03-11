<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\KuitansiApiController;
use App\Http\Controllers\Api\MasterRekeningApiController;

/*
|--------------------------------------------------------------------------
| API Routes — JWT Auth
|--------------------------------------------------------------------------
| All routes below are stateless (no session/cookie).
| Protected routes require:  Authorization: Bearer <token>
|
| Base URL prefix:  /api/v1/
*/

Route::prefix('v1')->group(function () {

    // ── Public ────────────────────────────────────────────────────────────────
    Route::middleware('throttle:login')->prefix('auth')->group(function () {
        Route::post('login', [AuthApiController::class, 'login']);
    });

    // ── Protected ─────────────────────────────────────────────────────────────
    Route::middleware(['auth:api', 'throttle:api'])->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout',  [AuthApiController::class, 'logout']);
            Route::post('refresh', [AuthApiController::class, 'refresh']);
            Route::get('me',       [AuthApiController::class, 'me']);
        });

        // Master Rekening
        Route::get('kegiatan',      [MasterRekeningApiController::class, 'indexKegiatan']);
        Route::post('kegiatan',     [MasterRekeningApiController::class, 'storeKegiatan']);

        Route::get('sub-kegiatan',  [MasterRekeningApiController::class, 'indexSubKegiatan']);
        Route::post('sub-kegiatan', [MasterRekeningApiController::class, 'storeSubKegiatan']);

        Route::get('kode-rekening',  [MasterRekeningApiController::class, 'indexKodeRekening']);
        Route::post('kode-rekening', [MasterRekeningApiController::class, 'storeKodeRekening']);

        // Kuitansi
        Route::get('kuitansi',  [KuitansiApiController::class, 'index']);
        Route::post('kuitansi', [KuitansiApiController::class, 'store']);
    });
});

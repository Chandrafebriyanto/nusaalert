<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BencanaApiController;
use App\Http\Controllers\Api\LokasiApiController;
use App\Http\Controllers\Api\AlertApiController;
use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\Api\AdminApiController;

/*
|--------------------------------------------------------------------------
| API Routes — NusaAlert v1
|--------------------------------------------------------------------------
|
| Auth methods supported:
| 1. JWT Bearer Token (Authorization: Bearer <token>)
| 2. API Key (X-API-Key: <key>)
| 3. HTTP Basic Auth (email:password)
|
*/

Route::prefix('v1')->group(function () {

    // ──────────────────────────────────────────────
    // Public Endpoints (no auth required, 60 req/min)
    // ──────────────────────────────────────────────
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/bencana', [BencanaApiController::class, 'index']);
        Route::get('/bencana/nearby', [BencanaApiController::class, 'nearby']);
        Route::get('/bencana/{bencana}', [BencanaApiController::class, 'show']);
        Route::get('/gempa/terkini', [BencanaApiController::class, 'gempaTerkini']);
        Route::get('/peta/bencana-aktif', [BencanaApiController::class, 'bencanaAktif']);
    });

    // ──────────────────────────────────────────────
    // Auth Endpoints (public, for login/register)
    // ──────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/login', [AuthApiController::class, 'login']);
    });

    // ──────────────────────────────────────────────
    // JWT Protected Endpoints (auth:api guard)
    // ──────────────────────────────────────────────
    Route::middleware('auth:api')->group(function () {

        // Auth management
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthApiController::class, 'logout']);
            Route::post('/refresh', [AuthApiController::class, 'refresh']);
            Route::get('/me', [AuthApiController::class, 'me']);
            Route::post('/api-key/generate', [AuthApiController::class, 'generateApiKey']);
        });

        // Lokasi CRUD
        Route::apiResource('lokasi', LokasiApiController::class);
        Route::patch('/lokasi/{lokasi}/toggle', [LokasiApiController::class, 'toggleActive']);

        // Alerts
        Route::get('/alerts', [AlertApiController::class, 'index']);
        Route::get('/alerts/unread', [AlertApiController::class, 'unread']);
        Route::patch('/alerts/{alert}/read', [AlertApiController::class, 'markAsRead']);
        Route::patch('/alerts/mark-all-read', [AlertApiController::class, 'markAllRead']);

        // Laporan Komunitas
        Route::get('/laporan', [LaporanApiController::class, 'index']);
        Route::post('/laporan', [LaporanApiController::class, 'store']);
        Route::get('/laporan/{laporan}', [LaporanApiController::class, 'show']);

        // Admin Endpoints (JWT + admin role check inside controller)
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminApiController::class, 'dashboard']);
            Route::get('/users', [AdminApiController::class, 'users']);
            Route::patch('/users/{user}/role', [AdminApiController::class, 'updateRole']);
            Route::patch('/laporan/{laporan}/verify', [AdminApiController::class, 'verifyLaporan']);
            Route::delete('/laporan/{laporan}/reject', [AdminApiController::class, 'rejectLaporan']);
            Route::delete('/laporan/{laporan}', [AdminApiController::class, 'destroyLaporan']);
            Route::post('/bencana', [AdminApiController::class, 'storeBencana']);
            Route::delete('/bencana/{bencana}', [AdminApiController::class, 'destroyBencana']);
        });
    });

    // ──────────────────────────────────────────────
    // API Key Protected Endpoints (200 req/min)
    // ──────────────────────────────────────────────
    Route::middleware(['auth.apikey', 'throttle:200,1'])->prefix('key')->group(function () {
        Route::get('/bencana', [BencanaApiController::class, 'index']);
        Route::get('/bencana/nearby', [BencanaApiController::class, 'nearby']);
        Route::get('/bencana/{bencana}', [BencanaApiController::class, 'show']);
        Route::get('/gempa/terkini', [BencanaApiController::class, 'gempaTerkini']);
        Route::get('/peta/bencana-aktif', [BencanaApiController::class, 'bencanaAktif']);
        Route::get('/lokasi', [LokasiApiController::class, 'index']);
        Route::get('/alerts', [AlertApiController::class, 'index']);
        Route::get('/alerts/unread', [AlertApiController::class, 'unread']);
    });

    // ──────────────────────────────────────────────
    // Basic Auth Protected Endpoints
    // ──────────────────────────────────────────────
    Route::middleware(['auth.basic.api', 'throttle:60,1'])->prefix('basic')->group(function () {
        Route::get('/bencana', [BencanaApiController::class, 'index']);
        Route::get('/bencana/nearby', [BencanaApiController::class, 'nearby']);
        Route::get('/gempa/terkini', [BencanaApiController::class, 'gempaTerkini']);
        Route::get('/peta/bencana-aktif', [BencanaApiController::class, 'bencanaAktif']);
        Route::get('/lokasi', [LokasiApiController::class, 'index']);
        Route::get('/alerts', [AlertApiController::class, 'index']);
    });
});

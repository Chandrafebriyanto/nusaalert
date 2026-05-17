<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BencanaApiController;

// Public API Endpoints with rate limiting (60 requests per minute)
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/bencana', [BencanaApiController::class, 'index']);
    Route::get('/bencana/nearby', [BencanaApiController::class, 'nearby']);
    Route::get('/bencana/{bencana}', [BencanaApiController::class, 'show']);
    Route::get('/gempa/terkini', [BencanaApiController::class, 'gempaTerkini']);
    Route::get('/peta/bencana-aktif', [BencanaApiController::class, 'bencanaAktif']);
});

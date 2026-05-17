<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminController;

// Public Routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/pelajari-sistem', [LandingController::class, 'learnSystem'])->name('learn-system');
Route::get('/peta', [LandingController::class, 'fullMap'])->name('peta');
Route::get('/panduan-keselamatan', [LandingController::class, 'panduanKeselamatan'])->name('panduan-keselamatan');
Route::get('/kebijakan-privasi', [LandingController::class, 'kebijakanPrivasi'])->name('kebijakan-privasi');
Route::get('/kontak-darurat', [LandingController::class, 'kontakDarurat'])->name('kontak-darurat');

// Auth Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes (Member)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lokasi Management
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::put('/lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::patch('/lokasi/{lokasi}/toggle', [LokasiController::class, 'toggleActive'])->name('lokasi.toggle');
    Route::delete('/lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');

    // Alert History
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])->name('alerts.read');
    Route::patch('/alerts/mark-all-read', [AlertController::class, 'markAllRead'])->name('alerts.markAllRead');

    // Laporan Komunitas
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');

    // User Alert API (for notification polling)
    Route::get('/api/user/check-proximity', [DashboardController::class, 'checkProximity'])->name('user.check-proximity');
    Route::get('/api/user/alerts', [AlertController::class, 'latestAlerts'])->name('user.alerts');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::patch('/laporan/{laporan}/verify', [AdminController::class, 'verifyLaporan'])->name('admin.laporan.verify');
    Route::delete('/laporan/{laporan}/reject', [AdminController::class, 'rejectLaporan'])->name('admin.laporan.reject');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.role');
    Route::post('/bencana', [AdminController::class, 'storeBencana'])->name('admin.bencana.store');
});

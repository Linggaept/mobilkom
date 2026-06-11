<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Pelapor;
use App\Http\Controllers\Teknisi;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Pimpinan;

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Notifications (all authenticated)
Route::middleware('auth')->prefix('notifikasi')->name('notifications.')->group(function () {
    Route::get('/unread',    [NotificationController::class, 'getUnread'])->name('unread');
    Route::post('/read',     [NotificationController::class, 'markRead'])->name('read');
    Route::get('/',          [NotificationController::class, 'index'])->name('index');
});

// ── PELAPOR ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pelapor'])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('/dashboard', [Pelapor\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',              [Pelapor\LaporanController::class, 'index'])->name('index');
        Route::get('/buat',          [Pelapor\LaporanController::class, 'create'])->name('create');
        Route::post('/',             [Pelapor\LaporanController::class, 'store'])->name('store');
        Route::get('/{laporan}',     [Pelapor\LaporanController::class, 'show'])->name('show');
        Route::get('/{laporan}/edit',[Pelapor\LaporanController::class, 'edit'])->name('edit');
        Route::put('/{laporan}',     [Pelapor\LaporanController::class, 'update'])->name('update');
        Route::delete('/{laporan}',  [Pelapor\LaporanController::class, 'destroy'])->name('destroy');
    });
});

// ── TEKNISI ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [Teknisi\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',                         [Teknisi\LaporanController::class, 'index'])->name('index');
        Route::get('/{laporan}',                [Teknisi\LaporanController::class, 'show'])->name('show');
        Route::post('/{laporan}/update-status', [Teknisi\LaporanController::class, 'updateStatus'])->name('updateStatus');
    });
});

// ── ADMIN ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',                        [Admin\LaporanController::class, 'index'])->name('index');
        Route::get('/{laporan}',               [Admin\LaporanController::class, 'show'])->name('show');
        Route::post('/{laporan}/verifikasi',   [Admin\LaporanController::class, 'verifikasi'])->name('verifikasi');
        Route::get('/{laporan}/cetak',         [Admin\LaporanController::class, 'cetakSatu'])->name('cetak');
        Route::post('/cetak-bulk',             [Admin\LaporanController::class, 'cetakBulk'])->name('cetakBulk');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',               [Admin\UserController::class, 'index'])->name('index');
        Route::get('/buat',           [Admin\UserController::class, 'create'])->name('create');
        Route::post('/',              [Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit',    [Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',         [Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}',      [Admin\UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle', [Admin\UserController::class, 'toggleActive'])->name('toggle');
    });
});

// ── PIMPINAN ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [Pimpinan\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',                [Pimpinan\LaporanController::class, 'index'])->name('index');
        Route::get('/{laporan}',       [Pimpinan\LaporanController::class, 'show'])->name('show');
        Route::get('/{laporan}/cetak', [Pimpinan\LaporanController::class, 'cetakSatu'])->name('cetak');
        Route::post('/cetak-bulk',     [Pimpinan\LaporanController::class, 'cetakBulk'])->name('cetakBulk');
    });
});
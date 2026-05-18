<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ReservasiController;
use Illuminate\Support\Facades\Route;

// Public Routes (Bisa diakses siapa saja)
Route::view('/', 'pages.user.index')->name('home');

// Guest Routes (Hanya untuk pengguna yang belum login)
// Route::middleware('guest')->group(function () {
    Route::view('/login', 'pages.user.login')->name('login');
    Route::view('/register', 'pages.user.register')->name('register');

    // Memperbaiki sedikit typo di URL proses-rergister menjadi proses-register
    Route::post('/proses-register', [AuthController::class, 'prosesRegister'])->name('proses-register');
    Route::post('/proses-login', [AuthController::class, 'prosesLogin'])->name('proses-login');
// });

// Auth Routes (Hanya untuk pengguna yang SUDAH login)
Route::middleware('auth')->group(function () {

    // --- GROUP RESERVASI USER ---
    Route::prefix('reservasi')->name('reservasi.')->controller(ReservasiController::class)->group(function () {
        // Halaman Reservasi dan Riwayat Booking
        Route::get('/', 'tampilRiwayatBooking')->name('riwayat');

        // Proses Reservasi
        Route::get('/cek-jadwal', 'cekJadwalTersedia')->name('cek-jadwal');
        Route::post('/', 'buatPesanan')->name('buat');
        Route::post('/{reservasi}/bayar', 'prosesPembayaran')->name('bayar');
        Route::post('/{reservasi}/batal', 'batalkanPesanan')->name('batal');
    });

    // --- GROUP ADMIN ---
    Route::prefix('admin')->middleware('is_admin')->group(function () {
        Route::view('/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');

        // Jadwal Routes
        Route::controller(JadwalController::class)->group(function () {
            Route::get('/jadwal', 'index')->name('jadwal');
            Route::get('/jadwal/form', 'tampilForm')->name('jadwal.form');
            Route::post('/buat-jadwal', 'buatJadwal')->name('buat-jadwal');
        });

        // Halaman Admin Reservasi
        Route::controller(\App\Http\Controllers\Admin\ReservasiController::class)->group(function () {
            Route::get('/reservasi', 'index')->name('admin.reservasi');
            Route::post('/reservasi/{id}/terima', 'terimaPembayaran')->name('admin.reservasi.terima');
            Route::post('/reservasi/{id}/tolak', 'tolakPembayaran')->name('admin.reservasi.tolak');
            Route::post('/reservasi/{id}/selesai', 'selesaikanReservasi')->name('admin.reservasi.selesai');
        });

        // Halaman Admin Lainnya
        Route::view('/laporan', 'pages.admin.laporan')->name('admin.laporan');
        Route::view('/membership', 'pages.admin.membership')->name('admin.membership');
        Route::view('/membership/form', 'pages.admin.membership-form')->name('admin.membership.form');
        Route::view('/pelanggan', 'pages.admin.pelanggan')->name('admin.pelanggan');
        Route::view('/pelanggan/form', 'pages.admin.pelanggan-form')->name('admin.pelanggan.form');
    });
});

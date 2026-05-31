<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MembershipController;
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
        Route::get('/', 'tampilRiwayatBooking')->name('index'); // diubah menjadi index untuk pemesanan
        Route::get('/riwayat', 'riwayatLengkap')->name('riwayat-lengkap'); // Halaman tabel riwayat lengkap

        // Proses Reservasi
        Route::get('/cek-jadwal', 'cekJadwalTersedia')->name('cek-jadwal');
        Route::post('/', 'buatPesanan')->name('buat');
        Route::post('/{reservasi}/bayar', 'prosesPembayaran')->name('bayar');
        Route::post('/{reservasi}/batal', 'batalkanPesanan')->name('batal');
        Route::get('/{reservasi}/cetak', 'cetakTiket')->name('cetak');
    });

    // --- GROUP MEMBERSHIP & PROFILE ---
    Route::post('/membership/register', [MembershipController::class, 'register'])->name('membership.register');
    Route::get('/profile', [MembershipController::class, 'profile'])->name('profile');

    // --- GROUP ADMIN ---
    Route::prefix('admin')->middleware('is_admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        // Halaman Admin Reservasi
        Route::controller(\App\Http\Controllers\Admin\ReservasiController::class)->group(function () {
            Route::get('/reservasi', 'index')->name('admin.reservasi');
            Route::post('/reservasi/{id}/terima', 'terimaPembayaran')->name('admin.reservasi.terima');
            Route::post('/reservasi/{id}/tolak', 'tolakPembayaran')->name('admin.reservasi.tolak');
            Route::post('/reservasi/{id}/selesai', 'selesaikanReservasi')->name('admin.reservasi.selesai');
        });

        // Halaman Admin Lainnya
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('admin.laporan');

        Route::controller(\App\Http\Controllers\Admin\MembershipController::class)->group(function () {
            Route::get('/membership', 'index')->name('admin.membership');
            Route::post('/membership/{id}/terima', 'terima')->name('admin.membership.terima');
            Route::post('/membership/{id}/tolak', 'tolak')->name('admin.membership.tolak');
        });

        Route::view('/membership/form', 'pages.admin.membership-form')->name('admin.membership.form');
        Route::controller(\App\Http\Controllers\Admin\PelangganController::class)->group(function () {
            Route::get('/pelanggan', 'index')->name('admin.pelanggan');
            Route::get('/pelanggan/form', 'create')->name('admin.pelanggan.form');
            Route::post('/pelanggan', 'store')->name('admin.pelanggan.store');
            Route::get('/pelanggan/{id}/edit', 'edit')->name('admin.pelanggan.edit');
            Route::put('/pelanggan/{id}', 'update')->name('admin.pelanggan.update');
            Route::delete('/pelanggan/{id}', 'destroy')->name('admin.pelanggan.destroy');
        });
    });
});

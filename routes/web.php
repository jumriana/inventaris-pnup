<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ReportController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\ActivationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama & Auth
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]); // Pendaftaran mandiri dimatikan demi keamanan

// 2. Rute yang HANYA bisa diakses setelah Login
Route::middleware(['auth'])->group(function () {
    
    // --- AKSES UMUM (Admin & User/Peminjam) ---
    
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Fitur Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Fitur Peminjaman (Resource standard untuk semua user)
    Route::resource('peminjaman', PeminjamanController::class);

    // Fitur Request Pengembalian oleh User
    Route::put('/peminjaman/request-kembali/{id}', [PeminjamanController::class, 'requestPengembalian'])->name('peminjaman.requestKembali');

    // Fitur Informasi Aset (Daftar Ruangan, Kendaraan, dan Informasi Barang)
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

    // Fitur Panduan Penggunaan Sistem (Bisa diakses oleh semua role)
    Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan.index');

    // --- KHUSUS ADMIN (Master Data, Approval, Report, & Verifikasi User) ---
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        
        // Resource Barang, Ruangan, & Kendaraan untuk Admin (Aksi CRUD selain index)
        Route::resource('barang', BarangController::class)->except(['index']);
        Route::resource('ruangan', RuanganController::class)->except(['index']);
        Route::resource('kendaraan', KendaraanController::class)->except(['index']);

        // Aksi Persetujuan Peminjaman oleh Admin
        Route::put('/peminjaman/setujui/{id}', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::put('/peminjaman/tolak/{id}', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
        Route::put('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

        // Fitur Report
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/pdf', [ReportController::class, 'exportPDF'])->name('report.pdf');

        // FITUR VERIFIKASI AKUN SISI ADMIN
        Route::get('/verifikasi-akun', [ActivationController::class, 'adminIndex'])->name('admin.verifikasi.index');
        Route::post('/verifikasi-akun/{id}/setujui', [ActivationController::class, 'approveActivation'])->name('admin.verifikasi.approve');
        Route::post('/verifikasi-akun/{id}/tolak', [ActivationController::class, 'rejectActivation'])->name('admin.verifikasi.tolak');
    });
});

// 3. FITUR AKTIVASI AKUN & NOTIFIKASI WHATSAPP CIVITAS (DI LUAR MIDDLEWARE AUTH)
Route::get('/activation', [ActivationController::class, 'showForm'])->name('activation.form');
Route::post('/activation', [ActivationController::class, 'requestActivation'])->name('activation.request');

// 4. REDIRECT & COMPATIBILITY
Route::redirect('/home', '/dashboard');

// 5. RUTE PEMBUAT KODE ENKRIPSI PASSWORD MANUAL (DARURAT)
Route::get('/cek-password', function () {
    return Hash::make('Pnup123');
});
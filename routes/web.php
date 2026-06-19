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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama & Auth
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]); // Sesuai kesepakatan, pendaftaran mandiri dimatikan demi keamanan

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

    // FITUR BARU: Aksi Request Pengembalian yang bisa dilakukan oleh User dari halaman index peminjaman
    Route::put('/peminjaman/request-kembali/{id}', [PeminjamanController::class, 'requestPengembalian'])->name('peminjaman.requestKembali');

    // Fitur Informasi Aset (Daftar Ruangan, Kendaraan, dan Inventaris Barang)
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    
    /** * PERBAIKAN: Fungsi index Barang dibuka untuk umum 
     * User melihat daftar inventaris yang sudah terurut berdasarkan kondisi di Controller
     */
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');


    // --- KHUSUS ADMIN (Master Data, Approval, & Report) ---
    Route::middleware(['role:admin'])->group(function () {
        
        /** * Resource Barang untuk Admin 
         * Mengelola tambah, edit, dan hapus inventaris.
         */
        Route::resource('barang', BarangController::class)->except(['index']);

        // Resource Ruangan & Kendaraan (Hanya untuk aksi CRUD selain index)
        Route::resource('ruangan', RuanganController::class)->except(['index']);
        Route::resource('kendaraan', KendaraanController::class)->except(['index']);

        // Aksi Persetujuan Peminjaman oleh Admin
        Route::put('/peminjaman/setujui/{id}', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::put('/peminjaman/tolak/{id}', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
        Route::put('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

        // Fitur Report
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/pdf', [ReportController::class, 'exportPDF'])->name('report.pdf');
    });
});

// 3. REDIRECT & COMPATIBILITY
Route::redirect('/home', '/dashboard');

// 4. RUTE PEMBUAT KODE ENKRIPSI PASSWORD MANUAL (DARURAT)
Route::get('/cek-password', function () {
    return Hash::make('Mhs42522024');
});
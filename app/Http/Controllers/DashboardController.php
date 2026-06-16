<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kendaraan; 
use App\Models\Ruangan;  
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Memastikan hanya user yang sudah login yang bisa mengakses dashboard.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan ringkasan informasi di Dashboard.
     */
    public function index()
    {
        // 1. Statistik Inventaris Gabungan (Dihitung secara global)
        $totalBarang = Barang::count();
        $totalKendaraan = Kendaraan::count();
        $totalRuangan = Ruangan::count();
        $totalInventaris = $totalBarang + $totalKendaraan + $totalRuangan;

        // 2. Logika Pemisahan Data Berdasarkan Role (Penting untuk Privasi Data)
        if (Auth::user()->role == 'admin') {
            // JIKA ADMIN: Melihat seluruh riwayat dari semua user
            $totalRiwayat = Peminjaman::count();
            
            // Mengambil 5 aktivitas terbaru dari seluruh sistem
            $notifikasiPeminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // JIKA USER (Contoh: Jumriana): Hanya melihat data miliknya sendiri
            // Variabel disamakan namanya ($totalRiwayat) agar sinkron dengan file Blade
            $totalRiwayat = Peminjaman::where('user_id', Auth::id())->count();
            
            // Mengambil 5 aktivitas terbaru khusus milik user yang login
            $notifikasiPeminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])
                ->where('user_id', Auth::id())
                ->latest()
                ->take(5)
                ->get();
        }

        // 3. Status Barang (Menghitung barang berdasarkan stok)
        $barangTersedia = Barang::where('jumlah_stok', '>', 0)->count();
        $barangDipinjam = Peminjaman::whereIn('status', ['disetujui', 'pending'])->count();

        // 4. Mengirim data ke View Dashboard
        return view('dashboard', compact(
            'totalInventaris',
            'totalRiwayat',
            'barangTersedia',
            'barangDipinjam',
            'notifikasiPeminjaman',
            'totalBarang',
            'totalKendaraan',
            'totalRuangan'
        ));
    }
}
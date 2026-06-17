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
        
        // Menghitung ragam macam item aset yang terdaftar di sistem
        $totalInventaris = $totalBarang + $totalKendaraan + $totalRuangan;

        // Barang Tersedia dihitung secara global berdasarkan item yang stoknya di atas 0
        $barangTersedia = Barang::where('jumlah_stok', '>', 0)->count();

        // 2. Logika Pemisahan Data Berdasarkan Role (Penting untuk Privasi Data)
        if (Auth::user()->role == 'admin') {
            // JIKA ADMIN: Melihat seluruh riwayat dari semua user di kampus
            $totalRiwayat = Peminjaman::count();
            
            // Menghitung seluruh aset barang yang sedang dipinjam secara global
            $barangDipinjam = Peminjaman::whereNotNull('barang_id')
                ->whereIn('status', ['disetujui', 'pending'])
                ->count();
            
            // Mengambil 5 aktivitas terbaru dari seluruh sistem PNUP
            $notifikasiPeminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // JIKA USER: Hanya melihat ringkasan data milik dirinya sendiri
            $totalRiwayat = Peminjaman::where('user_id', Auth::id())->count();
            
            // Menghitung hanya barang milik user yang bersangkutan yang sedang aktif dipinjam
            $barangDipinjam = Peminjaman::where('user_id', Auth::id())
                ->whereNotNull('barang_id')
                ->whereIn('status', ['disetujui', 'pending'])
                ->count();
            
            // Mengambil 5 aktivitas terbaru khusus milik user yang sedang aktif login
            $notifikasiPeminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])
                ->where('user_id', Auth::id())
                ->latest()
                ->take(5)
                ->get();
        }

        // 3. Mengirim seluruh data dinamis ke View Dashboard
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
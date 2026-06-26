<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan; // Wajib agar controller kenal model Kendaraan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KendaraanController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware auth.
     * Memastikan hanya user yang login yang bisa mengakses controller ini.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 1. Menampilkan daftar kendaraan beserta estimasi waktu pemakaian (Eager Loading).
     * Terbuka untuk semua user (Admin dan Staff/Mahasiswa).
     */
    public function index()
    {
        // MODIFIKASI: Memuat relasi peminjamanAktif agar data tgl_kembali bisa terbaca di Blade View
        $kendaraans = Kendaraan::with('peminjamanAktif')->get(); 
        return view('kendaraan.index', compact('kendaraans'));
    }

    /**
     * 2. Menampilkan form untuk menambah kendaraan.
     * PROTEKSI: Khusus Admin.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return view('kendaraan.create');
    }

    /**
     * 3. Menyimpan data kendaraan baru ke database.
     * PROTEKSI: Khusus Admin.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Validasi ketat untuk menghindari data sampah masuk ke database
        $request->validate([
            'nama_kendaraan'  => 'required|string|max:255',
            'plat_nomor'       => 'required|string|max:20|unique:kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|in:Mobil,Motor,Mobil Tangki,Gerobak Tarik,Bus',
            'kondisi'         => 'nullable|in:Baik,Rusak Ringan,Rusak',
            'keterangan'      => 'nullable|string'
        ]);

        // Mengambil input yang sudah tervalidasi agar aman dari Mass Assignment injection
        $data = $request->only(['nama_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'keterangan']);
        
        // Setting nilai bawaan (default)
        $data['status'] = 'Tersedia';
        $data['kondisi'] = $request->input('kondisi', 'Baik');

        Kendaraan::create($data);

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan form untuk mengedit kendaraan.
     * PROTEKSI: Khusus Admin.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $kendaraan = Kendaraan::findOrFail($id);
        return view('kendaraan.edit', compact('kendaraan'));
    }

    /**
     * 5. Memperbarui data kendaraan di database.
     * PROTEKSI: Khusus Admin.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'nama_kendaraan'  => 'required|string|max:255',
            'plat_nomor'       => 'required|string|max:20|unique:kendaraan,plat_nomor,' . $id,
            'jenis_kendaraan' => 'required|in:Mobil,Motor,Mobil Tangki,Gerobak Tarik,Bus',
            'kondisi'         => 'required|in:Baik,Rusak Ringan,Rusak',
            'status'          => 'required|in:Tersedia,Dipinjam',
            'keterangan'      => 'nullable|string'
        ]);

        // Mengambil field yang diizinkan untuk diubah
        $data = $request->only(['nama_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'kondisi', 'status', 'keterangan']);
        
        // Update data ke database
        $kendaraan->update($data);

        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan berhasil diperbarui!');
    }

    /**
     * 6. Menghapus kendaraan dari database.
     * PROTEKSI: Khusus Admin.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->delete();

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil dihapus!');
    }
}
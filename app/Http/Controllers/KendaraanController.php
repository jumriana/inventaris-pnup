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
     * 1. Menampilkan daftar kendaraan dengan urutan kondisi kustom (Eager Loading).
     * Diperbarui dengan fitur pencarian teks (search) dan filter dropdown (jenis_kendaraan).
     * Terbuka untuk semua user (Admin dan Staff/Mahasiswa).
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi query dasar beserta Eager Loading relasi
        $query = Kendaraan::with('peminjamanAktif');

        // 2. Logika Opsi 1: Filter Dropdown Jenis Kendaraan
        if ($request->has('jenis_kendaraan') && $request->jenis_kendaraan != '') {
            $query->where('jenis_kendaraan', $request->jenis_kendaraan);
        }

        // 3. Logika Opsi 2: Pencarian Kata Kunci Teks (Merek/Nama Kendaraan atau Plat Nomor)
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_kendaraan', 'like', '%' . $request->search . '%')
                  ->orWhere('plat_nomor', 'like', '%' . $request->search . '%');
            });
        }

        // 4. Pengurutan Kustom berdasarkan kondisi fisik aset kendaraan
        $kendaraans = $query->orderByRaw("FIELD(kondisi, 'Baik', 'Rusak Ringan', 'Servis', 'Rusak Berat')")
                            ->get(); 
            
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
            'jenis_kendaraan' => 'required|in:Mobil,Motor,Mobil Tangki,Gerobak Tarik,Bus,Bus / Elf',
            'kondisi'         => 'nullable|in:Baik,Rusak Ringan,Rusak Berat,Servis',
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

        // Validasi data perubahan dari form edit
        $request->validate([
            'nama_kendaraan'  => 'required|string|max:255',
            'plat_nomor'       => 'required|string|max:20|unique:kendaraan,plat_nomor,' . $id,
            'jenis_kendaraan' => 'required|in:Mobil,Motor,Mobil Tangki,Gerobak Tarik,Bus,Bus / Elf',
            'kondisi'         => 'required|in:Baik,Rusak Ringan,Rusak Berat,Servis', 
            'status'          => 'nullable|in:Tersedia,Dipinjam',
            'keterangan'      => 'nullable|string'
        ]);

        // Mengambil field yang diizinkan untuk diubah dari form
        $data = $request->only(['nama_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'kondisi', 'keterangan']);
        
        // JIKA form tidak mengirim nilai status, gunakan nilai status lama yang ada di database
        $data['status'] = $request->input('status', $kendaraan->status);

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
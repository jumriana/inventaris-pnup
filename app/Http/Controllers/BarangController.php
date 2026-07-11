<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    /**
     * Penerapan middleware auth secara global untuk mengamankan controller.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 1. Menampilkan daftar inventaris barang diurutkan berdasarkan Abjad A-Z.
     * Ditambahkan fitur pencarian cepat dan penyaringan kategori aset.
     * Terbuka untuk seluruh pengguna yang telah terautentikasi.
     */
    public function index(Request $request)
    {
        $query = Barang::query();

        // FITUR TAMBAHAN: Logika Penyaringan Berdasarkan Pilihan Kategori Dropdown
        if ($request->filled('kategori')) {
            // Catatan: Pastikan kolom 'kategori' tersedia pada tabel barang di database Anda
            $query->where('kategori', $request->kategori);
        }

        // FITUR TAMBAHAN: Logika Pencarian Cepat Berdasarkan Nama atau Kode Inventaris
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_barang', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('kode_inventaris', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Mengurutkan data inventaris barang berdasarkan nama_barang (A ke Z)
        $barangs = $query->orderBy('nama_barang', 'asc')->get();
        
        return view('barang.index', compact('barangs'));
    }

    /**
     * 2. Menampilkan form tambah barang.
     * PROTEKSI: Khusus Admin.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return view('barang.create');
    }

    /**
     * 3. Menyimpan data barang baru ke database (Mendukung 5 Field Form BMN).
     * PROTEKSI: Khusus Admin.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Validasi input dari form BMN
        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barang,kode_inventaris',
            'nup'         => 'required|numeric|min:1',
            'nama_barang' => 'required|string|max:255',
            'merk'        => 'required|string|max:255',
            'kondisi'     => 'required|in:Baik,Rusak Ringan,Rusak',
            'keterangan'  => 'nullable|string',
        ]);

        // Mapping data dari form ke kolom database asli Anda
        $barang = new Barang();
        $barang->kode_inventaris = $request->kode_barang; // Kode Barang ke kode_inventaris
        $barang->nama_barang     = $request->nama_barang; // Nama Barang ke nama_barang
        $barang->kondisi         = $request->kondisi;     // Kondisi ke kondisi
        $barang->jumlah_stok     = $request->nup;         // NUP ke jumlah_stok (Sebagai Jumlah)
        
        // Menggabungkan Merk dan Keterangan Tambahan ke dalam kolom ruangan_id agar semua info aman tersimpan
        $barang->ruangan_id      = 'Merk: ' . $request->merk . ' | ' . ($request->keterangan ?? 'Tanpa Keterangan');
        
        // Menyimpan kategori barang jika form input tambah barang juga menyediakan field kategori
        if ($request->has('kategori')) {
            $barang->kategori    = $request->kategori;
        }

        $barang->status          = 'Tersedia';            // Set status default bawaan sistem Anda
        $barang->tanggal_regis   = now()->format('Y-m-d');
        
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Barang Berhasil Disimpan!');
    }

    /**
     * 4. Menampilkan form edit barang.
     * PROTEKSI: Khusus Admin.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    /**
     * 5. Memperbarui data barang yang sudah ada.
     * PROTEKSI: Khusus Admin.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $barang = Barang::findOrFail($id);

        // Validasi input edit untuk form BMN
        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barang,kode_inventaris,' . $id,
            'nup'         => 'required|numeric|min:1',
            'nama_barang' => 'required|string|max:255',
            'merk'        => 'required|string|max:255',
            'kondisi'     => 'required|in:Baik,Rusak Ringan,Rusak',
            'keterangan'  => 'nullable|string',
        ]);

        // Mapping update langsung ke kolom database asli Anda
        $barang->kode_inventaris = $request->kode_barang;
        $barang->nama_barang     = $request->nama_barang;
        $barang->kondisi         = $request->kondisi;
        $barang->jumlah_stok     = $request->nup;
        $barang->ruangan_id      = 'Merk: ' . $request->merk . ' | ' . ($request->keterangan ?? 'Tanpa Keterangan');
        
        if ($request->has('kategori')) {
            $barang->kategori    = $request->kategori;
        }
        
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    /**
     * 6. Menghapus data barang.
     * PROTEKSI: Khusus Admin.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }
}
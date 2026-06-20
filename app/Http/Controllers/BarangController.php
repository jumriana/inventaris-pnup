<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
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
     * 1. Menampilkan daftar inventaris barang.
     * Terbuka untuk seluruh pengguna yang telah terautentikasi.
     */
    public function index()
    {
        $barangs = Barang::all();
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

        $kategoris = Kategori::orderBy('kategori_induk', 'asc')->get();
        return view('barang.create', compact('kategoris'));
    }

    /**
     * 3. Menyimpan data barang baru ke database (Mendukung 6 Field Form BMN).
     * PROTEKSI: Khusus Admin.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Validasi input dari 6 field form baru Anda (Tabel sudah diarahkan ke 'barang')
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
        $barang->kategori_id     = $request->nup;         // NUP ke kategori_id (Sebagai arsip NUP)
        
        // Menggabungkan Merk dan Keterangan Tambahan ke dalam kolom ruangan_id agar semua info aman tersimpan
        $barang->ruangan_id      = 'Merk: ' . $request->merk . ' | ' . ($request->keterangan ?? 'Tanpa Keterangan');
        
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
        $kategoris = Kategori::orderBy('kategori_induk', 'asc')->get();

        return view('barang.edit', compact('barang', 'kategoris'));
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

        // Validasi input edit untuk 6 field form baru Anda (Tabel sudah diarahkan ke 'barang')
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
        $barang->kategori_id     = $request->nup;
        $barang->ruangan_id      = 'Merk: ' . $request->merk . ' | ' . ($request->keterangan ?? 'Tanpa Keterangan');
        
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
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
     * 1. Menampilkan daftar inventaris barang diurutkan berdasarkan Abjad A-Z & Pagination.
     */
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_barang', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('kode_inventaris', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $barangs = $query->orderBy('nama_barang', 'asc')
                         ->paginate(10)
                         ->withQueryString();
        
        return view('barang.index', compact('barangs'));
    }

    /**
     * 2. Menampilkan form tambah barang.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return view('barang.create');
    }

    /**
     * 3. Menyimpan data barang baru ke database (dengan Max Hari Peminjaman).
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barang,kode_inventaris',
            'nup'         => 'required|numeric|min:1',
            'nama_barang' => 'required|string|max:255',
            'merk'        => 'required|string|max:255',
            'kondisi'     => 'required|in:Baik,Rusak Ringan,Rusak',
            'max_hari'    => 'nullable|integer|min:1|max:7', // Batas maksimal 1 - 7 hari
            'keterangan'  => 'nullable|string',
        ]);

        $barang = new Barang();
        $barang->kode_inventaris = $request->kode_barang;
        $barang->nama_barang     = $request->nama_barang;
        $barang->kondisi         = $request->kondisi;
        $barang->jumlah_stok     = $request->nup;
        $barang->max_hari        = $request->max_hari ?? 7; // Default 7 hari jika dikosongkan

        $barang->ruangan_id      = 'Merk: ' . $request->merk . ' | ' . ($request->keterangan ?? 'Tanpa Keterangan');
        
        if ($request->has('kategori')) {
            $barang->kategori    = $request->kategori;
        }

        $barang->status          = 'Tersedia';
        $barang->tanggal_regis   = now()->format('Y-m-d');
        
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Barang Berhasil Disimpan!');
    }

    /**
     * 4. Menampilkan form edit barang.
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
     * 5. Memperbarui data barang.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $barang = Barang::findOrFail($id);

        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barang,kode_inventaris,' . $id,
            'nup'         => 'required|numeric|min:1',
            'nama_barang' => 'required|string|max:255',
            'merk'        => 'required|string|max:255',
            'kondisi'     => 'required|in:Baik,Rusak Ringan,Rusak',
            'max_hari'    => 'nullable|integer|min:1|max:7', // Batas maksimal 1 - 7 hari
            'keterangan'  => 'nullable|string',
        ]);

        $barang->kode_inventaris = $request->kode_barang;
        $barang->nama_barang     = $request->nama_barang;
        $barang->kondisi         = $request->kondisi;
        $barang->jumlah_stok     = $request->nup;
        $barang->max_hari        = $request->max_hari ?? 7; // Default 7 hari jika dikosongkan

        $barang->ruangan_id      = 'Merk: ' . $request->merk . ' | ' . ($request->keterangan ?? 'Tanpa Keterangan');
        
        if ($request->has('kategori')) {
            $barang->kategori    = $request->kategori;
        }
        
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    /**
     * 6. Menghapus data barang.
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
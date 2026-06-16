<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * 1. Menampilkan daftar folder utama atau isi folder.
     */
    public function index(Request $request)
    {
        // Daftar kategori induk tetap (Folder Utama)
        $daftar_induk = [
            'Elektronik', 'Furnitur', 'Peralatan Kantor', 'Peralatan IT / Jaringan', 
            'Multimedia / Acara', 'Laboratorium', 'Kendaraan', 
            'Peralatan Kebersihan', 'Peralatan Olahraga'
        ];

        // Jika ada parameter ?view= (Berarti sedang membuka isi folder)
        if ($request->has('view')) {
            $kategori_aktif = $request->view;
            $kategoris = Kategori::where('kategori_induk', $kategori_aktif)->get();
            
            return view('kategori.index', compact('kategoris', 'kategori_aktif', 'daftar_induk'));
        }

        // Jika tidak ada parameter, tampilkan halaman 9 folder utama
        return view('kategori.folders', compact('daftar_induk'));
    }

    /**
     * 2. Menampilkan form tambah kategori.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * 3. Menyimpan data ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kategori_induk' => 'required',
            'kode_jenis'     => 'required|unique:kategori,kode_jenis|max:50',
            'nama_jenis'     => 'required|string|max:255',
            'keterangan'     => 'nullable',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // Logika upload gambar
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_gambar = time() . "_" . $file->getClientOriginalName();
            
            // Simpan file ke public/img/kategori
            $file->move(public_path('img/kategori'), $nama_gambar);
            
            // Masukkan ke array $data untuk disimpan
            $data['gambar'] = $nama_gambar; 
        }

        // Eksekusi simpan ke database
        Kategori::create($data);

        // Redirect otomatis masuk ke folder yang baru saja dipilih
        return redirect()->route('kategori.index', ['view' => $request->kategori_induk])
            ->with('success', 'Data berhasil ditambahkan ke folder ' . $request->kategori_induk);
    }

    /**
     * 4. Menghapus data kategori.
     */
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $induk_asal = $kategori->kategori_induk;

        // Hapus file gambar secara fisik dari folder jika ada
        if ($kategori->gambar && file_exists(public_path('img/kategori/' . $kategori->gambar))) {
            unlink(public_path('img/kategori/' . $kategori->gambar));
        }

        $kategori->delete();

        // Redirect kembali ke folder asal biar tidak bingung
        return redirect()->route('kategori.index', ['view' => $induk_asal])
            ->with('success', 'Kategori Berhasil Dihapus!');
    }
}
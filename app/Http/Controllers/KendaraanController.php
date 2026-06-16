<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan; // Wajib agar controller kenal model Kendaraan
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    /**
     * 1. Menampilkan daftar kendaraan.
     */
    public function index()
    {
        $kendaraans = Kendaraan::all(); 
        return view('kendaraan.index', compact('kendaraans'));
    }

    /**
     * 2. Menampilkan form untuk menambah kendaraan.
     */
    public function create()
    {
        return view('kendaraan.create');
    }

    /**
     * 3. Menyimpan data kendaraan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kendaraan' => 'required',
            'plat_nomor'     => 'required|unique:kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required',
        ]);

        $data = $request->all();
        
        // Setting default
        $data['status'] = 'Tersedia';
        $data['kondisi'] = $data['kondisi'] ?? 'Baik';

        Kendaraan::create($data);

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan form untuk mengedit kendaraan.
     */
    public function edit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        return view('kendaraan.edit', compact('kendaraan'));
    }

    /**
     * 5. Memperbarui data kendaraan di database.
     */
    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'nama_kendaraan' => 'required',
            'plat_nomor'     => 'required|unique:kendaraan,plat_nomor,' . $id,
            'jenis_kendaraan' => 'required',
        ]);

        $data = $request->all();
        
        // Update data
        $kendaraan->update($data);

        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan berhasil diperbarui!');
    }

    /**
     * 6. Menghapus kendaraan dari database.
     */
    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        
        // Perbaikan: gunakan -> bukan .
        $kendaraan->delete();

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil dihapus!');
    }
}
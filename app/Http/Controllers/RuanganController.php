<?php

namespace App\Http\Controllers;

use App\Models\Ruangan; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 

class RuanganController extends Controller
{
    /**
     * 1. Menampilkan daftar semua ruangan.
     */
    public function index()
    {
        $ruangans = Ruangan::all();
        return view('ruangan.index', compact('ruangans'));
    }

    /**
     * 2. Menampilkan form untuk menambah ruangan baru.
     */
    public function create()
    {
        return view('ruangan.create');
    }

    /**
     * 3. Menyimpan data ruangan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan',
            'nama_ruangan' => 'required',
            'lokasi'       => 'required',
            'kapasitas'    => 'required|numeric',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // LOGIKA OTOMATIS SURAT IZIN
        $nama = strtolower($request->nama_ruangan);
        if (str_contains($nama, 'aula') || str_contains($nama, 'auditorium') || str_contains($nama, 'teater')) {
            $data['butuh_surat'] = 1; 
        } else {
            $data['butuh_surat'] = 0; 
        }

        $data['status'] = 'Tersedia';

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('img/ruangan'), $nama_file);
            $data['gambar'] = $nama_file;
        }

        Ruangan::create($data);

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan form edit.
     */
    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    /**
     * 5. Memperbarui data ruangan di database.
     */
    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        $request->validate([
            'nama_ruangan' => 'required',
            'lokasi'       => 'required',
            'kapasitas'    => 'required|numeric',
        ]);

        $data = $request->all();

        // Logika otomatis surat izin tetap berjalan saat edit nama
        $nama = strtolower($request->nama_ruangan);
        if (str_contains($nama, 'aula') || str_contains($nama, 'auditorium') || str_contains($nama, 'teater')) {
            $data['butuh_surat'] = 1;
        } else {
            $data['butuh_surat'] = 0;
        }

        // Logika Ganti Gambar (Opsional)
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($ruangan->gambar && File::exists(public_path('img/ruangan/' . $ruangan->gambar))) {
                File::delete(public_path('img/ruangan/' . $ruangan->gambar));
            }
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('img/ruangan'), $nama_file);
            $data['gambar'] = $nama_file;
        }

        $ruangan->update($data);

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
    }

    /**
     * 6. Menghapus data ruangan.
     */
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        if ($ruangan->gambar) {
            $path = public_path('img/ruangan/' . $ruangan->gambar);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $ruangan->delete();

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus!');
    }
}
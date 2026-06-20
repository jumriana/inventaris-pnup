<?php

namespace App\Http\Controllers;

use App\Models\Ruangan; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Auth;

class RuanganController extends Controller
{
    /**
     * Penerapan middleware auth secara global dalam controller ini.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 1. Menampilkan daftar semua ruangan.
     * Dapat diakses oleh Admin maupun User umum.
     */
    public function index()
    {
        $ruangans = Ruangan::all();
        return view('ruangan.index', compact('ruangans'));
    }

    /**
     * 2. Menampilkan form untuk menambah ruangan baru.
     * PROTEKSI: Khusus Admin.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return view('ruangan.create');
    }

    /**
     * 3. Menyimpan data ruangan baru ke database.
     * PROTEKSI: Khusus Admin.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'kode_ruangan' => 'required|string|max:50|unique:ruangan,kode_ruangan',
            'nama_ruangan' => 'required|string|max:255',
            'lokasi'       => 'required|string|max:255',
            'kapasitas'    => 'required|numeric|min:1',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Proteksi mass assignment dengan membatasi field input yang diambil
        $data = $request->only(['kode_ruangan', 'nama_ruangan', 'lokasi', 'kapasitas', 'keterangan']);

        // LOGIKA OTOMATIS SURAT IZIN
        $nama = strtolower($request->nama_ruangan);
        if (str_contains($nama, 'aula') || str_contains($nama, 'auditorium') || str_contains($nama, 'teater')) {
            $data['butuh_surat'] = 1; 
        } else {
            $data['butuh_surat'] = 0; 
        }

        $data['status'] = 'Tersedia';

        // Proses Unggah Gambar
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
     * PROTEKSI: Khusus Admin.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    /**
     * 5. Memperbarui data ruangan di database.
     * PROTEKSI: Khusus Admin.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $ruangan = Ruangan::findOrFail($id);
        
        // PERBAIKAN: Menambahkan validasi unik kode_ruangan dengan pengecualian ID saat ini (. $id)
        $request->validate([
            'kode_ruangan' => 'required|string|max:50|unique:ruangan,kode_ruangan,' . $id,
            'nama_ruangan' => 'required|string|max:255',
            'lokasi'       => 'required|string|max:255',
            'kapasitas'    => 'required|numeric|min:1',
            'status'       => 'required|in:Tersedia,Dipakai,Perbaikan',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only(['kode_ruangan', 'nama_ruangan', 'lokasi', 'kapasitas', 'status', 'keterangan']);

        // Logika otomatis surat izin tetap berjalan jika nama_ruangan ikut diubah
        $nama = strtolower($request->nama_ruangan);
        if (str_contains($nama, 'aula') || str_contains($nama, 'auditorium') || str_contains($nama, 'teater')) {
            $data['butuh_surat'] = 1;
        } else {
            $data['butuh_surat'] = 0;
        }

        // Logika Ganti Gambar & Hapus Berkas Lama
        if ($request->hasFile('gambar')) {
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
     * 6. Menghapus data ruangan beserta berkas gambarnya.
     * PROTEKSI: Khusus Admin.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $ruangan = Ruangan::findOrFail($id);

        // Hapus file gambar secara fisik di storage sebelum record database dihapus
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
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
     * 1. Menampilkan daftar semua ruangan dengan urutan status kustom (Eager Loading).
     * Diperbarui dengan fitur pencarian (search) dan filter cepat (status).
     * Dapat diakses oleh Admin maupun User umum.
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi query dasar beserta Eager Loading relasi
        $query = Ruangan::with(['peminjamans', 'peminjamanAktif']);

        // 2. Logika Pencarian Kata Kunci (Berdasarkan Nama Ruangan atau Lokasi)
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_ruangan', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Logika Filter Cepat Status (Tersedia, Dipakai, Perbaikan)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // 4. Pengurutan Kustom: Tersedia -> Dipakai -> Perbaikan
        $ruangans = $query->orderByRaw("FIELD(status, 'Tersedia', 'Dipakai', 'Perbaikan')")
                          ->get();

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
            'status'       => 'required|in:Tersedia,Dipakai,Perbaikan',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only(['kode_ruangan', 'nama_ruangan', 'lokasi', 'kapasitas', 'status', 'keterangan']);

        // Set SEMUA ruangan baru wajib menggunakan surat izin (1)
        $data['butuh_surat'] = 1;

        // Fallback jika input status kosong (meski sudah diproteksi required)
        if (!$request->filled('status')) {
            $data['status'] = 'Tersedia';
        }

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
        
        // Validasi data input form edit dengan aturan status 'required'
        $request->validate([
            'kode_ruangan' => 'required|string|max:50|unique:ruangan,kode_ruangan,' . $id,
            'nama_ruangan' => 'required|string|max:255',
            'lokasi'       => 'required|string|max:255',
            'kapasitas'    => 'required|numeric|min:1',
            'status'       => 'required|in:Tersedia,Dipakai,Perbaikan',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only(['kode_ruangan', 'nama_ruangan', 'lokasi', 'kapasitas', 'status', 'keterangan']);

        // Memastikan saat di-update, status butuh_surat tetap bernilai 1 (Wajib) untuk semua ruangan
        $data['butuh_surat'] = 1;

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
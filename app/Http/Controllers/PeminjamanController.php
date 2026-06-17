<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\Kendaraan;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsappService; // Import Service WhatsApp Fonnte

class PeminjamanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar transaksi berdasarkan role.
     */
    public function index()
    {
        $query = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->latest();

        // Menyesuaikan pengecekan relasi berdasarkan identity_number (NIM/NIP)
        if (Auth::user()->role == 'admin') {
            $peminjamans = $query->get();
        } else {
            $peminjamans = $query->where('user_id', Auth::user()->identity_number)
                                 ->orWhere('user_id', Auth::id())
                                 ->get();
        }

        // Jalur view disesuaikan dengan folder asli Anda
        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Menampilkan form buat pinjaman baru dengan opsi parameter dinamis.
     */
    public function create(Request $request)
    {
        // Menangkap parameter dari tombol "Pinjam" di halaman Informasi Ruangan/Kendaraan
        $selected_item_id = $request->query('item_id');
        $kategori_pilihan = $request->query('kategori');

        // Mengambil data aset yang siap dipinjam
        $barangs = Barang::where('jumlah_stok', '>', 0)->get();
        $kendaraans = Kendaraan::where('status', 'Tersedia')->get();
        $ruangans = Ruangan::where('status', 'Tersedia')->get();

        return view('peminjaman.create', compact('barangs', 'kendaraans', 'ruangans', 'selected_item_id', 'kategori_pilihan'));
    }
    
    /**
     * Memproses penyimpanan data permohonan peminjaman baru beserta upload PDF.
     */
    public function store(Request $request)
    {
        // 1. VALIDASI INPUT FORM UTAMA & PEMBATASAN BERKAS PDF
        $request->validate([
            'tgl_pinjam'   => 'required|date|after_or_equal:today',
            'tgl_kembali'  => 'required|date|after_or_equal:tgl_pinjam',
            'nomor_wa'     => 'required|string',
            'keperluan'    => 'required|string',
            'kategori'     => 'required|in:barang,kendaraan,ruangan',
            'surat_izin'   => 'required_if:kategori,ruangan|required_if:kategori,kendaraan|nullable|mimes:pdf|max:2048',
        ]);

        // 2. PROSES HANDLE UPLOAD FILE SURAT IZIN KE STORAGE LARAGON
        $pathSuratIzin = null;
        if ($request->hasFile('surat_izin')) {
            $file = $request->file('surat_izin');
            // Membuat nama file unik berdasarkan timestamp waktu
            $namaFile = time() . '_' . $file->getClientOriginalName();
            // Disimpan ke dalam folder: storage/app/public/surat_izin
            $pathSuratIzin = $file->storeAs('surat_izin', $namaFile, 'public');
        }

        // Ambil ID Otentikasi terbaik (jika string NIM/NIP, gunakan identity_number)
        $userIdTersimpan = Auth::user()->identity_number ?? Auth::id();

        // 3. LOGIKA PENYIMPANAN DATA BERDASARKAN KATEGORI ASET
        if ($request->kategori === 'barang') {
            if ($request->has('barang_id')) {
                foreach ($request->barang_id as $key => $id) {
                    if ($id) {
                        Peminjaman::create([
                            'user_id'     => $userIdTersimpan,
                            'barang_id'   => $id,
                            'jumlah_item' => $request->jumlah[$key] ?? 1,
                            'tgl_pinjam'  => $request->tgl_pinjam,
                            'tgl_kembali' => $request->tgl_kembali,
                            'keperluan'   => $request->keperluan,
                            'nomor_wa'    => $request->nomor_wa,
                            'surat_izin'  => $pathSuratIzin, // Menyimpan jalur file PDF
                            'status'      => 'pending'
                        ]);
                    }
                }
            }
        } elseif ($request->kategori === 'kendaraan') {
            Peminjaman::create([
                'user_id'      => $userIdTersimpan,
                'kendaraan_id' => $request->kendaraan_id,
                'jumlah_item'  => 1,
                'tgl_pinjam'   => $request->tgl_pinjam,
                'tgl_kembali'  => $request->tgl_kembali,
                'keperluan'    => $request->keperluan,
                'nomor_wa'     => $request->nomor_wa,
                'surat_izin'   => $pathSuratIzin, // Menyimpan jalur file PDF
                'status'       => 'pending'
            ]);
        } elseif ($request->kategori === 'ruangan') {
            Peminjaman::create([
                'user_id'     => $userIdTersimpan,
                'ruangan_id' => $request->ruangan_id,
                'jumlah_item' => 1,
                'tgl_pinjam'  => $request->tgl_pinjam,
                'tgl_kembali' => $request->tgl_kembali,
                'keperluan'   => $request->keperluan,
                'nomor_wa'    => $request->nomor_wa,
                'surat_izin'  => $pathSuratIzin, // Menyimpan jalur file PDF
                'status'      => 'pending'
            ]);
        }

        return redirect()->route('peminjaman.index')->with('success', 'Permohonan peminjaman berhasil diajukan! Menunggu validasi berkas oleh Admin.');
    }

    /**
     * Menyetujui peminjaman aset, memotong stok/status, dan mengirim notifikasi WhatsApp.
     */
    public function setujui($id) 
    {
        $peminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->findOrFail($id);
        
        // 1. Logika Pengurangan Stok untuk Barang
        if ($peminjaman->barang_id) {
            $barang = Barang::find($peminjaman->barang_id);
            if (!$barang || $barang->jumlah_stok < $peminjaman->jumlah_item) {
                return redirect()->back()->with('error', 'Gagal setuju: Stok barang tidak mencukupi atau barang tidak ditemukan.');
            }
            $barang->decrement('jumlah_stok', $peminjaman->jumlah_item);
        }

        // 2. Logika Update Status untuk Kendaraan
        if ($peminjaman->kendaraan_id) {
            Kendaraan::where('id', $peminjaman->kendaraan_id)->update(['status' => 'Dipinjam']);
        }

        // 3. Logika Update Status untuk Ruangan
        if ($peminjaman->ruangan_id) {
            Ruangan::where('id', $peminjaman->ruangan_id)->update(['status' => 'Dipakai']);
        }

        // Jalankan update status transaksi peminjaman
        $peminjaman->update(['status' => 'disetujui']);

        // --- PROSES KIRIM NOTIFIKASI WHATSAPP VIA FONNTE ---
        if ($peminjaman->nomor_wa) {
            $namaAset = '';
            $detailJumlah = '1 Unit';

            if ($peminjaman->barang_id) {
                $namaAset = $peminjaman->barang->nama_barang ?? 'Barang Inventaris';
                $detailJumlah = $peminjaman->jumlah_item . ' Unit';
            } elseif ($peminjaman->kendaraan_id) {
                $namaAset = ($peminjaman->kendaraan->nama_kendaraan ?? 'Kendaraan') . ' [' . ($peminjaman->kendaraan->plat_nomor ?? '-') . ']';
            } elseif ($peminjaman->ruangan_id) {
                $namaAset = $peminjaman->ruangan->nama_ruangan ?? 'Ruangan/Aula';
            }

            // Susun template pesan teks rapi dengan fallback name jika objek null
            $namaPeminjam = $peminjaman->user->name ?? 'Civitas PNUP';

            $pesan = "Halo *" . $namaPeminjam . "*,\n\n"
                   . "Pengajuan peminjaman Anda telah *DISETUJUI* oleh Admin Divisi Rumah Tangga PNUP.\n\n"
                   . "📌 *Detail Aset :* " . $namaAset . "\n"
                   . "🔢 *Jumlah :* " . $detailJumlah . "\n"
                   . "📅 *Mulai Pinjam :* " . date('d M Y', strtotime($peminjaman->tgl_pinjam)) . "\n"
                   . "📅 *Batas Kembali :* " . date('d M Y', strtotime($peminjaman->tgl_kembali)) . "\n"
                   . "💡 *Keperluan :* " . ($peminjaman->keperluan ?? '-') . "\n\n"
                   . "Silakan gunakan/ambil aset sesuai ketentuan jadwal di atas.\n"
                   . "Terima kasih!\n\n"
                   . "_- Sistem Pinjam-INV PNUP -_";

            // Eksekusi kirim via Fonnte service
            if (class_exists('App\Services\WhatsappService')) {
                WhatsappService::sendMessage($peminjaman->nomor_wa, $pesan);
            }
        }

        return redirect()->back()->with('success', 'Peminjaman disetujui dan notifikasi WA berhasil dikirim.');
    }

    /**
     * Memproses pemulihan/pengembalian aset menjadi tersedia kembali.
     */
    public function kembalikan($id) 
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $statusCurrent = strtolower($peminjaman->status);

        if ($statusCurrent == 'disetujui') {
            
            // 1. Kembalikan Stok Barang
            if ($peminjaman->barang_id) {
                $peminjaman->barang()->increment('jumlah_stok', $peminjaman->jumlah_item);
            }

            // 2. Kembalikan Status Kendaraan menjadi Tersedia
            if ($peminjaman->kendaraan_id) {
                Kendaraan::where('id', $peminjaman->kendaraan_id)->update(['status' => 'Tersedia']);
            }

            // 3. Kembalikan Status Ruangan menjadi Tersedia
            if ($peminjaman->ruangan_id) {
                Ruangan::where('id', $peminjaman->ruangan_id)->update(['status' => 'Tersedia']);
            }

            $peminjaman->update(['status' => 'dikembalikan']);
            
            return redirect()->back()->with('success', 'Aset telah dikembalikan dan status dipulihkan menjadi Tersedia.');
        }

        return redirect()->back()->with('error', 'Gagal memproses pengembalian.');
    }

    /**
     * Menolak permintaan peminjaman dan mengabari user lewat notifikasi otomatis WhatsApp.
     */
    public function tolak($id)
    {
        $peminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->findOrFail($id);
        
        $peminjaman->update(['status' => 'ditolak']);

        // --- PROSES KIRIM NOTIFIKASI WHATSAPP UNTUK STATUS DITOLAK ---
        if ($peminjaman->nomor_wa) {
            $namaAset = '';
            if ($peminjaman->barang_id) {
                $namaAset = $peminjaman->barang->nama_barang ?? 'Barang Inventaris';
            } elseif ($peminjaman->kendaraan_id) {
                $namaAset = ($peminjaman->kendaraan->nama_kendaraan ?? 'Kendaraan') . ' [' . ($peminjaman->kendaraan->plat_nomor ?? '-') . ']';
            } elseif ($peminjaman->ruangan_id) {
                $namaAset = $peminjaman->ruangan->nama_ruangan ?? 'Ruangan/Aula';
            }

            $namaPeminjam = $peminjaman->user->name ?? 'Civitas PNUP';

            $pesan = "❌ *PEMBERITAHUAN: PENGAJUAN PINJAM DITOLAK*\n\n"
                   . "Halo *" . $namaPeminjam . "*,\n"
                   . "Mohon maaf, pengajuan peminjaman aset Anda berikut ini *BELUM DAPAT DISETUJUI* oleh Admin Divisi Rumah Tangga PNUP:\n\n"
                   . "📌 *Detail Aset :* " . $namaAset . "\n"
                   . "📅 *Rencana Pinjam :* " . date('d M Y', strtotime($peminjaman->tgl_pinjam)) . "\n"
                   . "💡 *Keperluan :* " . ($peminjaman->keperluan ?? '-') . "\n\n"
                   . "Silakan ajukan kembali permohonan dengan menyesuaikan jadwal pengosongan aset atau hubungi bagian admin untuk info lebih lanjut.\n"
                   . "Terima kasih!\n\n"
                   . "_- Sistem Pinjam-INV PNUP -_";

            if (class_exists('App\Services\WhatsappService')) {
                WhatsappService::sendMessage($peminjaman->nomor_wa, $pesan);
            }
        }

        return redirect()->back()->with('success', 'Permintaan peminjaman ditolak dan notifikasi WA telah dikirim.');
    }

    /**
     * Menghapus data transaksi dari sistem (Histori Data).
     */
    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status == 'disetujui') {
            if ($peminjaman->barang_id) {
                $peminjaman->barang()->increment('jumlah_stok', $peminjaman->jumlah_item);
            }
            if ($peminjaman->kendaraan_id) {
                Kendaraan::where('id', $peminjaman->kendaraan_id)->update(['status' => 'Tersedia']);
            }
            if ($peminjaman->ruangan_id) {
                Ruangan::where('id', $peminjaman->ruangan_id)->update(['status' => 'Tersedia']);
            }
        }

        $peminjaman->delete();
        return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
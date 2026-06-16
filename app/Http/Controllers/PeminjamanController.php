<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\Kendaraan;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsappService; // <-- 1. PANGGIL SERVICE WHATSAPP DI SINI

class PeminjamanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $query = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->latest();

        if (Auth::user()->role == 'admin') {
            $peminjamans = $query->get();
        } else {
            $peminjamans = $query->where('user_id', Auth::id())->get();
        }

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create(Request $request)
    {
        // Menangkap parameter dari tombol "Pinjam" di halaman Informasi Ruangan/Kendaraan
        $selected_item_id = $request->query('item_id');
        $kategori_pilihan = $request->query('kategori');

        // Mengambil data yang hanya berstatus tersedia atau memiliki stok
        $barangs = Barang::where('jumlah_stok', '>', 0)->get();
        $kendaraans = Kendaraan::where('status', 'Tersedia')->get();
        $ruangans = Ruangan::where('status', 'Tersedia')->get();

        return view('peminjaman.create', compact('barangs', 'kendaraans', 'ruangans', 'selected_item_id', 'kategori_pilihan'));
    }
    
    public function store(Request $request)
    {
        // ... bagian validasi tetap sama ...

        if ($request->kategori === 'barang') {
            foreach ($request->barang_id as $key => $id) {
                if ($id) {
                    Peminjaman::create([
                        'user_id' => auth()->id(),
                        'barang_id' => $id,
                        'jumlah_item' => $request->jumlah[$key],
                        'tgl_pinjam' => $request->tgl_pinjam,
                        'tgl_kembali' => $request->tgl_kembali,
                        'keperluan' => $request->keperluan,
                        'nomor_wa' => $request->nomor_wa,
                        'status' => 'pending'
                    ]);
                }
            }
        } elseif ($request->kategori === 'kendaraan') {
            Peminjaman::create([
                'user_id' => auth()->id(),
                'kendaraan_id' => $request->kendaraan_id,
                'jumlah_item' => 1,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali' => $request->tgl_kembali,
                'keperluan' => $request->keperluan,
                'nomor_wa' => $request->nomor_wa,
                'status' => 'pending'
            ]);
        } elseif ($request->kategori === 'ruangan') {
            Peminjaman::create([
                'user_id' => auth()->id(),
                'ruangan_id' => $request->ruangan_id,
                'jumlah_item' => 1,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali' => $request->tgl_kembali,
                'keperluan' => $request->keperluan,
                'nomor_wa' => $request->nomor_wa,
                'status' => 'pending'
            ]);
        }

        return redirect()->route('peminjaman.index')->with('success', 'Permohonan berhasil dikirim!');
    }

    /**
     * 2. MODIFIKASI FUNGSI SETUJUI (Menambahkan Auto-Notif WhatsApp)
     */
    public function setujui($id) 
    {
        $peminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->findOrFail($id);
        
        // 1. Logika untuk Barang (Stok)
        if ($peminjaman->barang_id) {
            $barang = Barang::find($peminjaman->barang_id);
            if ($barang->jumlah_stok < $peminjaman->jumlah_item) {
                return redirect()->back()->with('error', 'Gagal setuju: Stok barang tidak mencukupi.');
            }
            $barang->decrement('jumlah_stok', $peminjaman->jumlah_item);
        }

        // 2. Logika untuk Kendaraan (Status)
        if ($peminjaman->kendaraan_id) {
            Kendaraan::where('id', $peminjaman->kendaraan_id)->update(['status' => 'Dipinjam']);
        }

        // 3. Logika untuk Ruangan (Status)
        if ($peminjaman->ruangan_id) {
            Ruangan::where('id', $peminjaman->ruangan_id)->update(['status' => 'Dipakai']);
        }

        // Jalankan update status transaksi peminjaman
        $peminjaman->update(['status' => 'disetujui']);

        // --- PROSES KIRIM NOTIFIKASI WHATSAPP VIA FONNTE ---
        if ($peminjaman->nomor_wa) {
            // Deteksi Nama Aset secara otomatis berdasarkan kategori pilihan
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

            // Susun template pesan teks rapi (Gunakan bintang * untuk cetak tebal di WA)
            $pesan = "Halo *" . $peminjaman->user->name . "*,\n\n"
                   . "Pengajuan peminjaman Anda telah *DISETUJUI* oleh Admin Divisi Rumah Tangga PNUP.\n\n"
                   . "📌 *Detail Aset :* " . $namaAset . "\n"
                   . "🔢 *Jumlah :* " . $detailJumlah . "\n"
                   . "📅 *Mulai Pinjam :* " . date('d M Y', strtotime($peminjaman->tgl_pinjam)) . "\n"
                   . "📅 *Batas Kembali :* " . date('d M Y', strtotime($peminjaman->tgl_kembali)) . "\n"
                   . "💡 *Keperluan :* " . ($peminjaman->keperluan ?? '-') . "\n\n"
                   . "Silakan gunakan/ambil aset sesuai ketentuan jadwal di atas.\n"
                   . "Terima kasih!\n\n"
                   . "_- Sistem Pinjam-INV PNUP -_";

            // Eksekusi kirim via Fonnte service Anda
            WhatsappService::sendMessage($peminjaman->nomor_wa, $pesan);
        }
        // --- SELESAI PROSES WHATSAPP ---

        return redirect()->back()->with('success', 'Peminjaman disetujui dan notifikasi WA berhasil dikirim.');
    }

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
     * 3. MODIFIKASI FUNGSI TOLAK (Menambahkan Auto-Notif WhatsApp)
     */
    public function tolak($id)
    {
        // Ambil data peminjaman lengkap beserta relasi datanya
        $peminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->findOrFail($id);
        
        // Jalankan update status transaksi menjadi ditolak
        $peminjaman->update(['status' => 'ditolak']);

        // --- PROSES KIRIM NOTIFIKASI WHATSAPP UNTUK STATUS DITOLAK ---
        if ($peminjaman->nomor_wa) {
            // Deteksi Nama Aset secara otomatis berdasarkan kategori pilihan
            $namaAset = '';
            if ($peminjaman->barang_id) {
                $namaAset = $peminjaman->barang->nama_barang ?? 'Barang Inventaris';
            } elseif ($peminjaman->kendaraan_id) {
                $namaAset = ($peminjaman->kendaraan->nama_kendaraan ?? 'Kendaraan') . ' [' . ($peminjaman->kendaraan->plat_nomor ?? '-') . ']';
            } elseif ($peminjaman->ruangan_id) {
                $namaAset = $peminjaman->ruangan->nama_ruangan ?? 'Ruangan/Aula';
            }

            // Susun template pesan penolakan rapi dengan tanda silang merah ❌
            $pesan = "❌ *PEMBERITAHUAN: PENGAJUAN PINJAM DITOLAK*\n\n"
                   . "Halo *" . $peminjaman->user->name . "*,\n"
                   . "Mohon maaf, pengajuan peminjaman aset Anda berikut ini *BELUM DAPAT DISETUJUI* oleh Admin Divisi Rumah Tangga PNUP:\n\n"
                   . "📌 *Detail Aset :* " . $namaAset . "\n"
                   . "📅 *Rencana Pinjam :* " . date('d M Y', strtotime($peminjaman->tgl_pinjam)) . "\n"
                   . "💡 *Keperluan :* " . ($peminjaman->keperluan ?? '-') . "\n\n"
                   . "Silakan ajukan kembali permohonan dengan menyesuaikan jadwal pengosongan aset atau hubungi bagian admin untuk info lebih lanjut.\n"
                   . "Terima kasih!\n\n"
                   . "_- Sistem Pinjam-INV PNUP -_";

            // Eksekusi kirim via Fonnte service
            WhatsappService::sendMessage($peminjaman->nomor_wa, $pesan);
        }
        // --- SELESAI PROSES WHATSAPP ---

        return redirect()->back()->with('success', 'Permintaan peminjaman ditolak dan notifikasi WA telah dikirim.');
    }

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
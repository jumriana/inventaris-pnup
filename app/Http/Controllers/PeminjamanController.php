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

        if (Auth::user()->role == 'admin') {
            $peminjamans = $query->get();
        } else {
            $peminjamans = $query->where('user_id', Auth::user()->identity_number)
                                 ->orWhere('user_id', Auth::id())
                                 ->get();
        }

        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Menampilkan form buat pinjaman baru dengan opsi parameter dinamis.
     */
    public function create(Request $request)
    {
        $selected_item_id = $request->query('item_id');
        $kategori_pilihan = $request->query('kategori');

        $barangs = Barang::where('jumlah_stok', '>', 0)->get();
        $kendaraans = Kendaraan::where('status', 'Tersedia')->get();
        $ruangans = Ruangan::where('status', 'Tersedia')->get();

        return view('peminjaman.create', compact('barangs', 'kendaraans', 'ruangans', 'selected_item_id', 'kategori_pilihan'));
    }
    
    /**
     * Memproses penyimpanan data permohonan peminjaman baru dengan validasi Race Condition jadwal.
     */
    public function store(Request $request)
    {
        // 1. VALIDASI INPUT FORM UTAMA
        $request->validate([
            'tgl_pinjam'   => 'required|date|after_or_equal:today',
            'tgl_kembali'  => 'required|date|after_or_equal:tgl_pinjam',
            'nomor_wa'     => 'required|string',
            'keperluan'    => 'required|string',
            'kategori'     => 'required|in:barang,kendaraan,ruangan',
            'surat_izin'   => 'required_if:kategori,ruangan|required_if:kategori,kendaraan|nullable|mimes:pdf|max:2048',
        ]);

        // 2. HANDLE UPLOAD FILE SURAT IZIN
        $pathSuratIzin = null;
        if ($request->hasFile('surat_izin')) {
            $file = $request->file('surat_izin');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $pathSuratIzin = $file->storeAs('surat_izin', $namaFile, 'public');
        }

        $userIdTersimpan = Auth::user()->identity_number ?? Auth::id();
        $tgl_pnm = $request->tgl_pinjam;
        $tgl_kmb = $request->tgl_kembali;

        try {
            // 3. JALANKAN DATABASE TRANSACTION UNTUK MENGHINDARI RACE CONDITION
            DB::transaction(function () use ($request, $userIdTersimpan, $pathSuratIzin, $tgl_pnm, $tgl_kmb) {
                
                if ($request->kategori === 'barang') {
                    if ($request->has('barang_id')) {
                        foreach ($request->barang_id as $key => $id) {
                            if ($id) {
                                // Mengunci baris data barang yang dipilih
                                $barang = Barang::where('id', $id)->lockForUpdate()->first();
                                $qty_req = $request->jumlah[$key] ?? 1;

                                if (!$barang || $barang->jumlah_stok < $qty_req) {
                                    throw new \Exception("Stok untuk barang " . ($barang->nama_barang ?? '') . " tidak mencukupi.");
                                }

                                Peminjaman::create([
                                    'user_id'     => $userIdTersimpan,
                                    'barang_id'   => $id,
                                    'jumlah_item' => $qty_req,
                                    'tgl_pinjam'  => $tgl_pnm,
                                    'tgl_kembali' => $tgl_kmb,
                                    'keperluan'   => $request->keperluan,
                                    'nomor_wa'    => $request->nomor_wa,
                                    'surat_izin'  => $pathSuratIzin,
                                    'status'      => 'pending'
                                ]);
                            }
                        }
                    }
                } 
                
                elseif ($request->kategori === 'kendaraan') {
                    // Kunci eksklusif baris data kendaraan
                    $kendaraan = Kendaraan::where('id', $request->kendaraan_id)->lockForUpdate()->first();

                    // Cek bentrokan jadwal pengajuan lain yang berstatus pending/disetujui
                    $isBentrok = Peminjaman::where('kendaraan_id', $request->kendaraan_id)
                        ->whereIn('status', ['pending', 'disetujui'])
                        ->where(function ($query) use ($tgl_pnm, $tgl_kmb) {
                            $query->whereBetween('tgl_pinjam', [$tgl_pnm, $tgl_kmb])
                                  ->orWhereBetween('tgl_kembali', [$tgl_pnm, $tgl_kmb])
                                  ->orWhere(function ($q) use ($tgl_pnm, $tgl_kmb) {
                                      $q->where('tgl_pinjam', '<=', $tgl_pnm)
                                        ->where('tgl_kembali', '>=', $tgl_kmb);
                                  });
                        })->exists();

                    if ($isBentrok) {
                        throw new \Exception("Maaf, kendaraan tersebut sudah dipesan oleh pengguna lain pada tanggal pilihan Anda.");
                    }

                    Peminjaman::create([
                        'user_id'      => $userIdTersimpan,
                        'kendaraan_id' => $request->kendaraan_id,
                        'jumlah_item'  => 1,
                        'tgl_pinjam'   => $tgl_pnm,
                        'tgl_kembali'  => $tgl_kmb,
                        'keperluan'    => $request->keperluan,
                        'nomor_wa'     => $request->nomor_wa,
                        'surat_izin'   => $pathSuratIzin,
                        'status'       => 'pending'
                    ]);
                } 
                
                elseif ($request->kategori === 'ruangan') {
                    // Kunci eksklusif baris data ruangan
                    $ruangan = Ruangan::where('id', $request->ruangan_id)->lockForUpdate()->first();

                    // Cek bentrokan jadwal ruangan
                    $isBentrok = Peminjaman::where('ruangan_id', $request->ruangan_id)
                        ->whereIn('status', ['pending', 'disetujui'])
                        ->where(function ($query) use ($tgl_pnm, $tgl_kmb) {
                            $query->whereBetween('tgl_pinjam', [$tgl_pnm, $tgl_kmb])
                                  ->orWhereBetween('tgl_kembali', [$tgl_pnm, $tgl_kmb])
                                  ->orWhere(function ($q) use ($tgl_pnm, $tgl_kmb) {
                                      $q->where('tgl_pinjam', '<=', $tgl_pnm)
                                        ->where('tgl_kembali', '>=', $tgl_kmb);
                                  });
                        })->exists();

                    if ($isBentrok) {
                        throw new \Exception("Maaf, ruangan/aula tersebut sudah dipesan pada rentang tanggal pilihan Anda.");
                    }

                    Peminjaman::create([
                        'user_id'     => $userIdTersimpan,
                        'ruangan_id'  => $request->ruangan_id,
                        'jumlah_item' => 1,
                        'tgl_pinjam'  => $tgl_pnm,
                        'tgl_kembali' => $tgl_kmb,
                        'keperluan'   => $request->keperluan,
                        'nomor_wa'    => $request->nomor_wa,
                        'surat_izin'  => $pathSuratIzin,
                        'status'      => 'pending'
                    ]);
                }
            });

            return redirect()->route('peminjaman.index')->with('success', 'Permohonan peminjaman berhasil diajukan! Menunggu validasi berkas oleh Admin.');

        } catch (\Exception $e) {
            // Semua query otomatis dibatalkan jika melempar exception di sini (Rollback)
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Menyetujui peminjaman aset, memotong stok/status dengan proteksi transaksi.
     */
    public function setujui($id) 
    {
        try {
            DB::transaction(function () use ($id, &$peminjaman) {
                // Ambil dan kunci baris transaksi peminjaman ini
                $peminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->lockForUpdate()->findOrFail($id);

                if ($peminjaman->status === 'disetujui') {
                    throw new \Exception("Transaksi ini sudah disetujui sebelumnya.");
                }

                // 1. Logika Pengurangan Stok untuk Barang
                if ($peminjaman->barang_id) {
                    $barang = Barang::where('id', $peminjaman->barang_id)->lockForUpdate()->first();
                    if (!$barang || $barang->jumlah_stok < $peminjaman->jumlah_item) {
                        throw new \Exception('Gagal setuju: Stok barang tidak mencukupi.');
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

                $peminjaman->update(['status' => 'disetujui']);
            });

            // --- PROSES KIRIM NOTIFIKASI WHATSAPP VIA FONNTE ---
            if ($peminjaman && $peminjaman->nomor_wa) {
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

                if (class_exists('App\Services\WhatsappService')) {
                    WhatsappService::sendMessage($peminjaman->nomor_wa, $pesan);
                }
            }

            return redirect()->back()->with('success', 'Peminjaman disetujui dan notifikasi WA berhasil dikirim.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memproses pemulihan/pengembalian aset menjadi tersedia kembali.
     */
    public function kembalikan($id) 
    {
        try {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::lockForUpdate()->findOrFail($id);

                if (strtolower($peminjaman->status) == 'disetujui') {
                    if ($peminjaman->barang_id) {
                        $peminjaman->barang()->increment('jumlah_stok', $peminjaman->jumlah_item);
                    }
                    if ($peminjaman->kendaraan_id) {
                        Kendaraan::where('id', $peminjaman->kendaraan_id)->update(['status' => 'Tersedia']);
                    }
                    if ($peminjaman->ruangan_id) {
                        Ruangan::where('id', $peminjaman->ruangan_id)->update(['status' => 'Tersedia']);
                    }

                    $peminjaman->update(['status' => 'dikembalikan']);
                } else {
                    throw new \Exception('Gagal memproses: Status transaksi tidak valid untuk dikembalikan.');
                }
            });

            return redirect()->back()->with('success', 'Aset telah dikembalikan dan status dipulihkan menjadi Tersedia.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menolak permintaan peminjaman dan mengabari user lewat notifikasi otomatis WhatsApp.
     */
    public function tolak($id)
    {
        $peminjaman = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])->findOrFail($id);
        $peminjaman->update(['status' => 'ditolak']);

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
        try {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::lockForUpdate()->findOrFail($id);
                
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
            });

            return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // <-- Sudah ditambahkan di sini

class RemindPeminjamanHMin1 extends Command
{
    /**
     * Nama perintah artisan
     */
    protected $signature = 'peminjaman:remind-h1';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Kirim notifikasi pengingat WA H-1 sebelum tanggal pengembalian aset';

    public function handle()
    {
        // 1. Ambil tanggal besok
        $besok = Carbon::tomorrow()->format('Y-m-d');

        $this->info("Mencari data transaksi yang jatuh tempo pada tanggal: {$besok}...");

        // 2. Cari transaksi berstatus 'disetujui' atau 'dipinjam' dengan tgl_kembali == besok
        $peminjamans = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])
            ->whereIn(DB::raw('LOWER(status)'), ['disetujui', 'dipinjam'])
            ->whereDate('tgl_kembali', $besok)
            ->get();

        $totalData = $peminjamans->count();

        if ($totalData === 0) {
            $this->warn("Tidak ada peminjaman yang jatuh tempo besok ({$besok}).");
            return 0;
        }

        $this->info("Ditemukan {$totalData} peminjaman yang jatuh tempo esok hari. Memulai pengiriman WA...");

        // 3. Loop pengiriman pesan dengan Jeda (Delay) Antrean Anti-Spam
        foreach ($peminjamans as $index => $peminjaman) {

            if ($peminjaman->nomor_wa) {
                // Tentukan nama aset
                $namaAset = '';
                if ($peminjaman->barang_id) {
                    $namaAset = $peminjaman->barang->nama_barang ?? 'Barang Inventaris';
                } elseif ($peminjaman->kendaraan_id) {
                    $namaAset = ($peminjaman->kendaraan->nama_kendaraan ?? 'Kendaraan') . ' [' . ($peminjaman->kendaraan->plat_nomor ?? '-') . ']';
                } elseif ($peminjaman->ruangan_id) {
                    $namaAset = $peminjaman->ruangan->nama_ruangan ?? 'Ruangan/Aula';
                }

                $namaPeminjam = $peminjaman->user->name ?? 'Civitas PNUP';

                // Format Pesan Pengingat H-1
                $pesan = "⏰ *PENGINGAT: MASA PEMINJAMAN SEGERA BERAKHIR (H-1)*\n\n"
                       . "Halo *" . $namaPeminjam . "*,\n"
                       . "Kami ingin mengingatkan bahwa masa peminjaman aset Anda akan *BERAKHIR BESOK*:\n\n"
                       . "📌 *Detail Aset :* " . $namaAset . "\n"
                       . "📅 *Batas Pengembalian :* " . date('d M Y', strtotime($peminjaman->tgl_kembali)) . "\n\n"
                       . "Mohon untuk mempersiapkan pengembalian aset tepat waktu ke Divisi Rumah Tangga PNUP.\n"
                       . "Terima kasih atas kerja samanya!\n\n"
                       . "_- Sistem Pinjam-INV PNUP -_";

                // Kirim via WhatsappService Fonnte
                if (class_exists('App\Services\WhatsappService')) {
                    WhatsappService::sendMessage($peminjaman->nomor_wa, $pesan);
                    $this->info("[" . ($index + 1) . "/{$totalData}] WA pengingat berhasil dikirim ke: " . $peminjaman->nomor_wa);
                }

                // --- JEDA (DELAY) ANTAR PESAN ---
                // Jika ada urutan data berikutnya, beri jeda delay 5 detik
                if ($index < $totalData - 1) {
                    $jedaDetik = 5;
                    $this->comment("Menunggu antrean {$jedaDetik} detik sebelum mengirim ke pengguna berikutnya...");
                    sleep($jedaDetik);
                }
            }
        }

        $this->info("Semua notifikasi pengingat H-1 berhasil dikirimkan!");
        return 0;
    }
}
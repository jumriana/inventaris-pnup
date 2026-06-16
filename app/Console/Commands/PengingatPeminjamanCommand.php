<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use App\Services\WhatsappService;
use Carbon\Carbon;

class PengingatPeminjamanCommand extends Command
{
    /**
     * Nama perintah yang akan diketikkan di terminal nanti
     */
    protected $signature = 'peminjaman:ingatkan';

    /**
     * Deskripsi singkat kegunaan perintah
     */
    protected $description = 'Kirim otomatis notifikasi WA khusus untuk peringatan telat kembali (Overdue)';

    public function handle()
    {
        // Ambil data tanggal hari ini
        $hariIni = Carbon::today()->format('Y-m-d');

        // =========================================================================
        // NOTIFIKASI PERINGATAN KARENA TELAT MENGEMBALIKAN (OVERDUE)
        // =========================================================================
        $sudahTelat = Peminjaman::with(['user', 'barang', 'kendaraan', 'ruangan'])
                                ->where('status', 'disetujui') // Status masih aktif dipinjam
                                ->whereDate('tgl_kembali', '<', $hariIni) // Tanggal kembali sudah lewat dari hari ini
                                ->get();

        foreach ($sudahTelat as $p) {
            if ($p->nomor_wa) {
                // Deteksi Nama Aset secara dinamis
                $namaAset = '';
                if ($p->barang_id) {
                    $namaAset = $p->barang->nama_barang;
                } elseif ($p->kendaraan_id) {
                    $namaAset = $p->kendaraan->nama_kendaraan . ' [' . ($p->kendaraan->plat_nomor ?? '-') . ']';
                } elseif ($p->ruangan_id) {
                    $namaAset = $p->ruangan->nama_ruangan;
                }

                // Hitung selisih hari keterlambatan secara akurat
                $tglKembali = Carbon::parse($p->tgl_kembali);
                $selisihHari = $tglKembali->diffInDays(Carbon::today());

                // Susun template pesan teks peringatan keterlambatan
                $pesanTelat = "⚠️ *PERINGATAN KETERLAMBATAN PENGEMBALIAN ASET*\n\n"
                            . "Halo *" . $p->user->name . "*,\n"
                            . "Sistem mendeteksi bahwa Anda *BELUM MENGEMBALIKAN* aset inventaris kampus yang telah melewati batas waktu peminjaman:\n\n"
                            . "📦 *Nama Aset:* " . $namaAset  . "\n"
                            . "📅 *Batas Jatuh Tempo:* " . date('d M Y', strtotime($p->tgl_kembali)) . "\n"
                            . "🚨 *Status Keterlambatan:* Telat *" . $selisihHari . " Hari*\n\n"
                            . "Mohon untuk SEGERA mengembalikan aset tersebut ke divisi rumah tangga PNUP hari ini juga dan lakukan konfirmasi kepada Admin.\n\n"
                            . "Terima kasih atas kerja samanya.\n\n"
                            . "_- Sistem Pinjam-INV PNUP -_";

                // Eksekusi pengiriman pesan via Fonnte API
                WhatsappService::sendMessage($p->nomor_wa, $pesanTelat);
            }
        }

        $this->info('Notifikasi peringatan keterlambatan berhasil diproses!');
    }
}
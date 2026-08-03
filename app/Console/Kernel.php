<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Mendefinisikan jadwal eksekusi perintah otomatis (Task Schedule)
     */
    protected function schedule(Schedule $schedule)
    {
        // Mengatur agar perintah pengingat WA H-1 dijalankan otomatis setiap hari pukul 08:00 pagi
        $schedule->command('peminjaman:remind-h1')->dailyAt('08:00');
    }

    /**
     * Mendaftarkan perintah-perintah (Commands) yang ada di folder Commands
     */
    protected function commands()
    {
        // Kode di bawah ini bertugas otomatis me-load semua file di folder Commands
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
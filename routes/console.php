<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| File ini digunakan untuk mendaftarkan command console bawaan maupun
| penjadwalan tugas (Task Scheduling) aplikasi Laravel Anda.
|
*/

// Command bawaan Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Penjadwalan Tugas Otomatis (Scheduler)
// Mengatur agar command pengingat WA H-1 berjalan setiap hari jam 08:00 Pagi
Schedule::command('peminjaman:remind-h1')->dailyAt('08:00');
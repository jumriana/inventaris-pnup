<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // Untuk mengatur hak akses (Gate)
use App\Models\User;                // Untuk mereferensikan model User

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Mendefinisikan akses khusus untuk Admin.
         * Gate ini digunakan oleh AdminLTE untuk menyembunyikan/menampilkan menu.
         * Pastikan nama 'admin-only' sama dengan yang ada di config/adminlte.php
         */
        Gate::define('admin-only', function (User $user) {
            // Mengecek apakah kolom 'role' user yang login adalah 'admin'
            return $user->role === 'admin'; 
        });
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SINKRONISASI_PASSWORD_SEEDER extends Seeder
{
    /**
     * Jalankan seeder untuk mereset seluruh password menjadi seragam.
     */
    public function run()
    {
    $users = User::all();

    foreach ($users as $user) {
        // Cukup ubah bagian ini (hapus tanda pagarnya)
        $user->password = Hash::make('Pnup123');
        $user->save();
    }

    $this->command->info('Berhasil! Semua password di database sekarang seragam menjadi: Pnup123');
    }
}
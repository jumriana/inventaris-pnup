<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini bertanggung jawab untuk menangani pengiriman email link
    | reset password menggunakan trait bawaan dari Laravel Auth.
    |
    |--------------------------------------------------------------------------
    */

    use SendsPasswordResetEmails;

    /**
     * Menampilkan form untuk pengajuan reset password.
     * Mengarah ke resources/views/auth/passwords/email.blade.php
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Validasi email sebelum link reset password dikirimkan.
     * Mengatur pesan error kustom berbahasa Indonesia.
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Kolom Gmail wajib diisi.',
            'email.email'    => 'Format alamat Gmail tidak valid.',
            'email.exists'   => 'Alamat Gmail tersebut tidak terdaftar di sistem Pinjam-INV PNUP.',
        ]);
    }
}
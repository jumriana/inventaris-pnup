<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // Tambahkan ini untuk fungsi logout
use Illuminate\Validation\ValidationException; // Ditambahkan untuk handle error custom
use Illuminate\Support\Facades\Auth; // Ditambahkan untuk fungsi Auth::logout()

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Ubah ke /dashboard agar setelah login langsung masuk ke AdminLTE
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * PERBAIKAN UTAMA: Mengubah field login utama Laravel dari email 
     * menjadi identity_number (NIM/NIP) sesuai dengan database kita.
     *
     * @return string
     */
    public function username()
    {   
        return 'identity_number';
    }

    /**
     * PENGAMAN STATUS LOGIN: Mencegah user berstatus 'nonaktif' atau 'pending' 
     * tembus masuk ke dashboard meskipun password yang diinput sudah benar.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->status !== 'aktif') {
            // Paksa logout sesi yang baru saja dibuat agar tidak tersangkut
            Auth::logout();

            // Lemparkan error spesifik ke kolom identity_number/password agar muncul di view login
            throw ValidationException::withMessages([
                'identity_number' => ['Akun Anda belum aktif atau sedang dinonaktifkan. Silakan ajukan aktivasi akun untuk mendapatkan password bawaan.'],
            ]);
        }

        // Jika statusnya 'aktif', izinkan lanjut masuk ke halaman dashboard utama
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Tambahkan fungsi ini agar setelah Logout 
     * langsung diarahkan kembali ke halaman Login.
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('login');
    }

    /**
     * FITUR BARU: Mengubah kalimat pesan error dan memindahkan 
     * posisinya agar mengunci di bawah kolom password jika kredensial salah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'password' => ['Username atau password salah!!!'],
        ]);
    }
}
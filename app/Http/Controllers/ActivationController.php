<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Services\WhatsappService; // Memanggil WhatsappService global untuk pengiriman cURL

class ActivationController extends Controller
{
    /**
     * SISI CIVITAS: Menampilkan Form Pengajuan Aktivasi Akun
     */
    public function showForm()
    {
        return view('auth.activation');
    }

    /**
     * SISI CIVITAS: Memproses Pengajuan Aktivasi Nomor WhatsApp Civitas & Email
     */
    public function requestActivation(Request $request)
    {
        $request->validate([
            'identity_number' => 'required|string', 
            'email'           => 'required|email|max:255',
            'no_hp'           => 'required|string|min:10|max:15'  
        ]);

        $user = User::where('identity_number', $request->identity_number)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Nomor identitas Anda tidak terdaftar dalam master data civitas PNUP.');
        }

        if ($user->status === 'aktif') {
            return redirect()->back()->with('info', 'Akun Anda sudah aktif. Silakan langsung login menggunakan password Anda.');
        }

        $nomorHp = $request->no_hp;
        if (substr($nomorHp, 0, 1) === '0') {
            $nomorHp = '62' . substr($nomorHp, 1);
        }

        $user->email = $request->email; 
        $user->no_hp = $nomorHp;
        $user->status = 'pending';
        $user->save();

        return redirect()->route('login')->with('success', 'Pengajuan berhasil! Silakan tunggu verifikasi Admin untuk mendapatkan password melalui WhatsApp.');
    }

    /**
     * SISI ADMIN: Menampilkan daftar civitas yang berstatus 'pending'
     */
    public function adminIndex()
    {
        $users = User::where('status', 'pending')->orderBy('updated_at', 'desc')->get();
        return view('admin.verifikasi.index', compact('users'));
    }

    /**
     * SISI ADMIN: Menyetujui akun dan menetapkan password bawaan Pnup123
     */
    public function approveActivation($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'Akun tidak dalam antrean aktivasi.');
        }

        // UPDATE BARU: Password diubah menjadi statis 'Pnup123' untuk semua user
        $passwordBawaan = "Pnup123";

        // Ubah status akun menjadi aktif dan simpan enkripsi password barunya
        $user->password = Hash::make($passwordBawaan);
        $user->status = 'aktif';
        $user->save();

        // Susun template pesan teks WhatsApp Persetujuan
        $pesanWA = "Halo, *{$user->name}*.\n\n";
        $pesanWA .= "Pengajuan aktivasi akun Anda di *Sistem Peminjaman Barang & Inventaris PNUP* telah *DISETUJUI*.\n\n";
        $pesanWA .= "Silakan login menggunakan kredensial berikut:\n";
        $pesanWA .= "• Nomor Identitas: `{$user->identity_number}`\n";
        $pesanWA .= "• Password Bawaan: *{$passwordBawaan}*\n\n";
        $pesanWA .= "⚠️ _Demi keamanan data, mohon segera ubah password bawaan ini setelah Anda berhasil masuk ke sistem melalui menu Profile._";

        // Kirim WA menggunakan WhatsappService global
        WhatsappService::sendMessage($user->no_hp, $pesanWA);

        return redirect()->back()->with('success', "Akun {$user->name} berhasil diaktifkan! Password bawaan telah otomatis dikirim ke WhatsApp.");
    }

    /**
     * SISI ADMIN: Menolak atau membatalkan antrean aktivasi akun
     */
    public function rejectActivation($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'Akun tidak dalam antrean aktivasi.');
        }

        $user->status = 'nonaktif';
        $user->save();

        $pesanWA = "Halo, *{$user->name}*.\n\n";
        $pesanWA .= "Mohon maaf, pengajuan aktivasi akun Anda di *Sistem Peminjaman Barang & Inventaris PNUP* telah *DITOLAK* oleh Admin.\n\n";
        $pesanWA .= "Silakan pastikan kembali data diri Anda atau hubungi bagian Rumah Tangga / Admin Sistem jika merasa ada kekeliruan data.";

        WhatsappService::sendMessage($user->no_hp, $pesanWA);

        return redirect()->back()->with('success', "Permintaan aktivasi akun {$user->name} berhasil ditolak dan notifikasi telah dikirim.");
    }
}
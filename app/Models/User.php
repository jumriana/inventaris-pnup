<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'identity_number', // <-- PERBAIKAN: Diizinkan agar Laravel bisa membaca NIM / NIP saat login
        'email',
        'password',
        'role',            // <-- PERBAIKAN: Diizinkan agar Laravel bisa membaca hak akses (User/Admin)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * PERBAIKAN UTAMA: Mengunci sistem autentikasi Laravel agar selalu
     * mencocokkan kredensial password berdasarkan data kolom 'identity_number'.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'identity_number';
    }
}
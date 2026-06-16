<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    /**
     * Atribut yang dapat diisi (Mass Assignable).
     * Pastikan 'nomor_wa' sudah ada di sini agar tidak error saat simpan data.
     */
    protected $fillable = [
        'user_id', 
        'barang_id', 
        'kendaraan_id', 
        'ruangan_id', 
        'jumlah_item', 
        'tgl_pinjam', 
        'tgl_kembali', 
        'keperluan', 
        'nomor_wa', // Kolom tambahan untuk fitur WhatsApp
        'status',
        'bukti_fisik',
        'kondisi_saat_ini'
    ];

    /**
     * Relasi ke Model User (Peminjam)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Model Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Relasi ke Model Kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    /**
     * Relasi ke Model Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Casting tanggal agar otomatis menjadi objek Carbon
     */
    protected $casts = [
        'tgl_pinjam' => 'date',
        'tgl_kembali' => 'date',
    ];
}
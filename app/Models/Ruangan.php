<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'lokasi',
        'kapasitas',   
        'keterangan',
        'gambar',      
        'status',      
        'butuh_surat'
    ];

    /**
     * TAMBAHAN PERBAIKAN: Relasi Jamak ke tabel peminjaman (One to Many)
     * Dibutuhkan oleh RuanganController::index() dan filter status 'disetujui' di Blade View.
     */
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_id', 'id');
    }

    /**
     * Relasi ke model Peminjaman untuk mengambil data peminjaman terbaru yang sedang berjalan.
     */
    public function peminjamanAktif()
    {
        // Parameter 2 adalah 'ruangan_id' sesuai kolom asli di DB Anda!
        // Parameter 3 adalah 'id' (Primary Key milik tabel ruangan)
        return $this->hasOne(Peminjaman::class, 'ruangan_id', 'id') 
                    ->where('status', 'disetujui') 
                    ->latestOfMany();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// PENAMBAHAN: Mengimpor Model Peminjaman agar relasi terbaca dengan sempurna
use App\Models\Peminjaman; 

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'nama_kendaraan', 
        'plat_nomor', 
        'keterangan', 
        'jenis_kendaraan', 
        'kondisi', 
        'status'
    ];

    /**
     * Relasi ke model Peminjaman untuk mengambil data peminjaman kendaraan terbaru yang sedang berjalan.
     */
    public function peminjamanAktif()
    {
        // Parameter 2: 'kendaraan_id' adalah kolom FK di tabel peminjaman Anda
        // Parameter 3: 'id' adalah Primary Key milik tabel kendaraan Anda
        return $this->hasOne(Peminjaman::class, 'kendaraan_id', 'id') 
                    ->where('status', 'disetujui') 
                    ->latestOfMany(); // Mengunci 1 baris transaksi terbaru yang aktif
    }
}
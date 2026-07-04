<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    // PENTING: Ubah menjadi false untuk tes jika error save() terus muncul
    public $timestamps = false; 

    protected $fillable = [
        'kode_inventaris', 
        'nama_barang', 
        'kondisi', 
        'jumlah_stok', 
        'tanggal_regis',  
        'ruangan_id', 
        'created_at', // Tambahkan ini agar bisa diisi manual jika butuh
        'updated_at'  // Tambahkan ini agar bisa diisi manual jika butuh
    ];
}
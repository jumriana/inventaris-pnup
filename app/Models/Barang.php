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
        'max_hari',       // PENAMBAHAN REVISI: Batas Maksimal Hari Peminjaman
        'kategori',       
        'tanggal_regis',  
        'ruangan_id', 
        'created_at',     
        'updated_at'      
    ];
}
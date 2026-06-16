<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Nama tabel di database Anda
    protected $table = 'kategori';

    // Cukup satu kali saja menuliskan $fillable
    protected $fillable = [
        'kategori_induk',
        'kode_jenis', 
        'nama_jenis', 
        'keterangan',
        'gambar'
    ];
}
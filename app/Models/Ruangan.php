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
}

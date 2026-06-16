<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}

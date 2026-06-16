<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kendaraan'); // Contoh: Toyota Avanza
        $table->string('plat_nomor')->unique(); // Contoh: DD 1234 AB
        $table->enum('jenis_kendaraan', ['Mobil', 'Motor', 'Bus']);
        $table->string('kondisi')->default('Baik'); // Baik, Rusak, Servis
        $table->enum('status', ['Tersedia', 'Dipinjam'])->default('Tersedia');
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
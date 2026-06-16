<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jenis')->unique(); // ID unik kategori
            $table->string('nama_jenis'); // Elektronik, Furniture, Kendaraan, Ruang, dll
            $table->text('keterangan')->nullable();
            $table->string('gambar')->nullable()->after('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
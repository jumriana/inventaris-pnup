<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
    Schema::create('barang', function (Blueprint $table) {
        $table->id();
        $table->string('kode_barang')->unique();
        $table->string('nama_barang');
        // Relasi ke tabel kategori (sesuaikan nama kolom di tabel kategori kamu)
        $table->unsignedBigInteger('kategori_id'); 
        $table->integer('stok');
        $table->string('kondisi')->default('Baik');
        $table->string('lokasi');
        $table->string('gambar')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();

        // Foreign key ke tabel kategori
        $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('cascade');
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
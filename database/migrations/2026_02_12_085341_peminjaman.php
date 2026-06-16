<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
    Schema::create('peminjamans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
        $table->date('tgl_pinjam');
        $table->date('tgl_kembali');
        // Status: pending, disetujui, ditolak, dikembalikan
        $table->string('status')->default('pending'); 
        $table->timestamps();
    });
    }
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
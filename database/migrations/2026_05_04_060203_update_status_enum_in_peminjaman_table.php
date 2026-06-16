<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk mengubah struktur tabel.
     */
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Ini akan memperbarui kolom ENUM agar menerima status 'dikembalikan'
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])
                  ->default('pending')
                  ->change();
        });
    }
};
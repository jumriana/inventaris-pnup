<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('ruangans', function (Blueprint $table) {
        $table->id();
        $table->string('nama_ruangan');
        $table->string('lokasi');
        $table->integer('kapasitas');
        $table->text('keterangan')->nullable();
        $table->string('gambar')->nullable(); // Foto ruangan
        $table->enum('status', ['Tersedia', 'Dipakai', 'Perbaikan'])->default('Tersedia');
        $table->boolean('butuh_surat')->default(false); // Untuk Aula/Auditorium
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Diubah menjadi 'peminjaman' tanpa huruf S
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('surat_izin')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Diubah menjadi 'peminjaman' tanpa huruf S
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('surat_izin');
        });
    }
};
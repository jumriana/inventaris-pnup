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
        Schema::table('peminjaman', function (Blueprint $table) {
            // Mengecek apakah kolom nomor_wa sudah ada atau belum
            if (!Schema::hasColumn('peminjaman', 'nomor_wa')) {
                $table->string('nomor_wa')->nullable()->after('keperluan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Menghapus kolom nomor_wa jika migrasi di-rollback
            if (Schema::hasColumn('peminjaman', 'nomor_wa')) {
                $table->dropColumn('nomor_wa');
            }
        });
    }
};
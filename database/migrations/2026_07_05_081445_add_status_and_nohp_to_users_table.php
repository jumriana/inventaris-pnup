<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menyimpan nomor HP/WhatsApp setelah kolom password
            $table->string('no_hp')->nullable()->after('password');
            
            // Menyimpan status verifikasi akun civitas PNUP setelah kolom password
            $table->enum('status', ['nonaktif', 'pending', 'aktif'])->default('nonaktif')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kembali kolom saat dilakukan rollback
            $table->dropColumn('no_hp');
            $table->dropColumn('status');
        });
    }
};
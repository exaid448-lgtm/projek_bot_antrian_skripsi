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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung MODIFY COLUMN jenis_kelamin ENUM('laki-laki', 'wanita') NULL");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung ADD COLUMN status_disabilitas ENUM('tidak', 'menunggu_validasi', 'permanen', 'ditolak') DEFAULT 'tidak' AFTER jenis_kelamin");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung ADD COLUMN dokumen_disabilitas VARCHAR(255) NULL AFTER status_disabilitas");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung DROP COLUMN dokumen_disabilitas, DROP COLUMN status_disabilitas");
    }
};

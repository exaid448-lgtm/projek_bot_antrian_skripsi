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
        Schema::create('absensi', function (Blueprint $table) {
            $table->increments('id_absen');
            $table->unsignedInteger('id_profil');
            $table->time('waktu_masuk');
            $table->time('waktu_pulang')->nullable();
            $table->date('tanggal');
            $table->enum('status_absen', ['hadir', 'telat', 'izin']);
            $table->string('surat_izin', 250)->nullable();

            $table->unique(['id_profil', 'tanggal'], 'unique_absen_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

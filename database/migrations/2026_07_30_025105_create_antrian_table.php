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
        Schema::create('antrian', function (Blueprint $table) {
            $table->integer('id_antrian', true);
            $table->integer('id_loket')->nullable()->index('antrian_ibfk_1');
            $table->string('nomor_antrian');
            $table->dateTime('waktu_voice');
            $table->time('waktu_panggil')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('jenis_antrian')->nullable();
            $table->enum('setatus_pengambilan', ['offline', 'online'])->nullable();
            $table->integer('id_pengunjung')->nullable()->index('id_profil_pengunjung');
            $table->enum('status_antrian', ['menunggu', 'dipanggil', 'selesai', 'terlewat', 'batal', 'booking'])->nullable();
            $table->integer('id_karyawan')->nullable()->index('id_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};

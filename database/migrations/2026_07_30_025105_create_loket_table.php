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
        Schema::create('loket', function (Blueprint $table) {
            $table->integer('id_loket', true);
            $table->string('nama_loket')->nullable();
            $table->enum('status_pelayanan', ['BUKA', 'TUTUP']);
            $table->string('nama_pelayanan');
            $table->time('waktu_terakhir');
            $table->date('tanggal');
            $table->string('logo')->nullable();
            $table->string('prefix', 5)->nullable();
            $table->string('lokasi_loket', 250);
            $table->integer('kuota_booking')->nullable()->default(10);
            $table->enum('status_booking', ['aktif', 'nonaktif'])->nullable()->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loket');
    }
};

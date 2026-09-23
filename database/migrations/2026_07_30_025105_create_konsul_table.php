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
        Schema::create('konsul', function (Blueprint $table) {
            $table->integer('id_konsul', true);
            $table->integer('id_loket')->index('id_loket');
            $table->string('konsultasi');
            $table->dateTime('tanggal_konsul');
            $table->enum('pelayanan_status', ['belum', 'sudah', '', '']);
            $table->integer('id_pengunjung')->index('id_pengunjung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsul');
    }
};

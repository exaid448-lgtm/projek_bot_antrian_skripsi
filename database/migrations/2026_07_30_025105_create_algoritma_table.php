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
        Schema::create('algoritma', function (Blueprint $table) {
            $table->integer('id_algoritma', true);
            $table->integer('id_loket')->index('id_loket');
            $table->string('algoritma');
            $table->dateTime('tanggal_dan_waktu');
            $table->enum('tipe_layanan', ['layanan', 'lokasi', '', '']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('algoritma');
    }
};

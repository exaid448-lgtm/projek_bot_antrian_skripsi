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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->integer('id_jadwal', true);
            $table->integer('id_profil')->index('id_profil');
            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->enum('shift', ['pagi', 'siang', 'sore', '']);
            $table->enum('setatus', ['aktif', 'libur', 'cuti', '']);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};

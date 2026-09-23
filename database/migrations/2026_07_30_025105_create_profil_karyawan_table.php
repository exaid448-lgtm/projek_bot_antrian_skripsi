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
        Schema::create('profil_karyawan', function (Blueprint $table) {
            $table->integer('id_profil', true);
            $table->integer('id_user')->index('id_user');
            $table->string('nama_user');
            $table->string('status_devisi');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan', '', '']);
            $table->date('tanggal_lahir');
            $table->integer('id_loket')->nullable()->index('id_loket');
            $table->string('img_user', 250);
            $table->string('email', 150);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_karyawan');
    }
};

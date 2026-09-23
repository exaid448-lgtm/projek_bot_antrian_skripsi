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
        Schema::create('profil_pengunjung', function (Blueprint $table) {
            $table->integer('id_pengunjung', true);
            $table->string('nama', 125);
            $table->string('email', 125);
            $table->string('nomor_whatsapp', 125);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['laki-laki', 'wanita', '', '']);
            $table->integer('id_user')->index('id_user');
            $table->string('foto', 250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_pengunjung');
    }
};

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
        Schema::create('chat_konsultasi', function (Blueprint $table) {
            $table->bigIncrements('id_chat');
            $table->integer('id_loket')->index('fk_chat_loket');
            $table->integer('id_profil')->nullable()->index('fk_chat_profil');
            $table->integer('id_pengunjung')->nullable()->index('fk_chat_pengunjung');
            $table->enum('tipe_pengirim', ['karyawan', 'pengunjung']);
            $table->text('pesan')->nullable();
            $table->string('file_lampiran', 250)->nullable();
            $table->string('ukuran_file', 50)->nullable();
            $table->boolean('status_baca')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_konsultasi');
    }
};

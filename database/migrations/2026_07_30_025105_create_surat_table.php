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
        Schema::create('surat', function (Blueprint $table) {
            $table->integer('id_surat', true);
            $table->integer('id_user_pengupload');
            $table->string('judul_surat', 250);
            $table->text('sinopsis_surat')->nullable();
            $table->string('file_surat', 250);
            $table->enum('status_surat', ['belum_verifikasi', 'sudah_verifikasi'])->default('belum_verifikasi');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};

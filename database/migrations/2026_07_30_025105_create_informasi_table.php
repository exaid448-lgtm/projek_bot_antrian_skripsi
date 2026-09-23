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
        Schema::create('informasi', function (Blueprint $table) {
            $table->integer('id_informasi', true);
            $table->integer('id_loket')->index('fk_informasi_loket');
            $table->string('judul_info');
            $table->text('deskripsi_info');
            $table->text('solusi_info')->nullable();
            $table->enum('kategori_info', ['critical', 'warning', 'normal'])->nullable()->default('normal');
            $table->enum('status_info', ['aktif', 'arsip'])->nullable()->default('aktif');
            $table->date('tanggal_info');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi');
    }
};

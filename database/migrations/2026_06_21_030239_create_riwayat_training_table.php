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
        Schema::create('riwayat_training', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->string('algoritma_dipakai')->default('Naive Bayes');
            $table->decimal('akurasi', 5, 2)->nullable();
            $table->integer('jumlah_data')->nullable();
            $table->string('waktu_eksekusi')->nullable(); // misal "1.5 detik"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_training');
    }
};

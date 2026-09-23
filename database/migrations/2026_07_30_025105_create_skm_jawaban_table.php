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
        Schema::create('skm_jawaban', function (Blueprint $table) {
            $table->integer('id_jawaban', true);
            $table->integer('id_soal')->index('id_soal');
            $table->integer('id_loket')->index('id_loket');
            $table->integer('id_antrain')->index('id_antrain');
            $table->enum('jawaban', ['sangat bagus', 'bagus', 'kurang', 'sangat kurang']);
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skm_jawaban');
    }
};

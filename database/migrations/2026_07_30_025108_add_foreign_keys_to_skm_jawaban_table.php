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
        Schema::table('skm_jawaban', function (Blueprint $table) {
            $table->foreign(['id_soal'], 'skm_jawaban_ibfk_1')->references(['id_soal'])->on('skm_soal')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_antrain'], 'skm_jawaban_ibfk_2')->references(['id_antrian'])->on('antrian')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_loket'], 'skm_jawaban_ibfk_3')->references(['id_loket'])->on('loket')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skm_jawaban', function (Blueprint $table) {
            $table->dropForeign('skm_jawaban_ibfk_1');
            $table->dropForeign('skm_jawaban_ibfk_2');
            $table->dropForeign('skm_jawaban_ibfk_3');
        });
    }
};

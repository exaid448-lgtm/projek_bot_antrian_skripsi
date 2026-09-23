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
        Schema::table('skm_soal', function (Blueprint $table) {
            $table->foreign(['id_loket'], 'skm_soal_ibfk_1')->references(['id_loket'])->on('loket')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skm_soal', function (Blueprint $table) {
            $table->dropForeign('skm_soal_ibfk_1');
        });
    }
};

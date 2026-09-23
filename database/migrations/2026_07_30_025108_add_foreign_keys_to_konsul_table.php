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
        Schema::table('konsul', function (Blueprint $table) {
            $table->foreign(['id_loket'], 'konsul_ibfk_1')->references(['id_loket'])->on('loket')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_pengunjung'], 'konsul_ibfk_2')->references(['id_pengunjung'])->on('profil_pengunjung')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsul', function (Blueprint $table) {
            $table->dropForeign('konsul_ibfk_1');
            $table->dropForeign('konsul_ibfk_2');
        });
    }
};

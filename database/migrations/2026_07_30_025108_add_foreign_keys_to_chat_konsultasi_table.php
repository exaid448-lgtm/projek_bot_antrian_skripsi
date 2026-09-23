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
        Schema::table('chat_konsultasi', function (Blueprint $table) {
            $table->foreign(['id_loket'], 'fk_chat_loket')->references(['id_loket'])->on('loket')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_pengunjung'], 'fk_chat_pengunjung')->references(['id_pengunjung'])->on('profil_pengunjung')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_profil'], 'fk_chat_profil')->references(['id_profil'])->on('profil_karyawan')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_konsultasi', function (Blueprint $table) {
            $table->dropForeign('fk_chat_loket');
            $table->dropForeign('fk_chat_pengunjung');
            $table->dropForeign('fk_chat_profil');
        });
    }
};

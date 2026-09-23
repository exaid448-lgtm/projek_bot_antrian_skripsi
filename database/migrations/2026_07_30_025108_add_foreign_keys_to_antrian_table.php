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
        Schema::table('antrian', function (Blueprint $table) {
            $table->foreign(['id_loket'], 'antrian_ibfk_1')->references(['id_loket'])->on('loket')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_pengunjung'], 'antrian_ibfk_2')->references(['id_pengunjung'])->on('profil_pengunjung')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_karyawan'], 'antrian_ibfk_3')->references(['id_profil'])->on('profil_karyawan')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropForeign('antrian_ibfk_1');
            $table->dropForeign('antrian_ibfk_2');
            $table->dropForeign('antrian_ibfk_3');
        });
    }
};

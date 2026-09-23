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
        Schema::table('profil_karyawan', function (Blueprint $table) {
            $table->foreign(['id_user'], 'fk_profil_user')->references(['id_user'])->on('user')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_loket'], 'profil_karyawan_ibfk_1')->references(['id_loket'])->on('loket')->onUpdate('no action')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_karyawan', function (Blueprint $table) {
            $table->dropForeign('fk_profil_user');
            $table->dropForeign('profil_karyawan_ibfk_1');
        });
    }
};

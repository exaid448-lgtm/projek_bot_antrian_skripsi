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
        Schema::table('informasi', function (Blueprint $table) {
            $table->foreign(['id_loket'], 'fk_informasi_loket')->references(['id_loket'])->on('loket')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informasi', function (Blueprint $table) {
            $table->dropForeign('fk_informasi_loket');
        });
    }
};

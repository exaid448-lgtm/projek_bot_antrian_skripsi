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
        Schema::table('syarat', function (Blueprint $table) {
            $table->foreign(['id_loket'], 'fk_syarat_loket')->references(['id_loket'])->on('loket')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syarat', function (Blueprint $table) {
            $table->dropForeign('fk_syarat_loket');
        });
    }
};

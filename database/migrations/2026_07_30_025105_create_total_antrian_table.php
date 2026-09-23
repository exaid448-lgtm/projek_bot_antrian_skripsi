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
        Schema::create('total_antrian', function (Blueprint $table) {
            $table->comment('Table \'antrian_bot.total_antrian\' doesn\'t exist in engine');
            $table->integer('id_total', true);
            $table->integer('id_antrian');
            $table->date('tanggal');
            $table->string('jumlah_antrian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_antrian');
    }
};

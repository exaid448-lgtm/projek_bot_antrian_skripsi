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
        Schema::create('skm_soal', function (Blueprint $table) {
            $table->integer('id_soal', true);
            $table->integer('id_loket')->index('id_loket');
            $table->string('pertanyaan', 250);
            $table->boolean('is_active');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skm_soal');
    }
};

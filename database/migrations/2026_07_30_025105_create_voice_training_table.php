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
        Schema::create('voice_training', function (Blueprint $table) {
            $table->integer('id_training', true);
            $table->integer('id_loket')->index('id_loket');
            $table->text('teks_transkripsi');
            $table->string('file_audio', 250)->nullable();
            $table->enum('sumber_data', ['manual', 'pengunjung', '', '']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_training');
    }
};

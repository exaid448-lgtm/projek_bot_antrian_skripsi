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
        Schema::create('syarat', function (Blueprint $table) {
            $table->integer('id_syarat', true);
            $table->integer('id_loket')->index('fk_syarat_loket');
            $table->string('nama_syarat');
            $table->text('keterangan')->nullable();
            $table->enum('tipe_syarat', ['dokumen', 'prosedur'])->nullable()->default('dokumen');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syarat');
    }
};

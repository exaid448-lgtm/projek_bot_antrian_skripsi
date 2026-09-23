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
        Schema::create('persyaratan_pengunjung', function (Blueprint $table) {
            $table->integer('id_syarat', true);
            $table->integer('id_loket');
            $table->string('isi_persyaratan', 250);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persyaratan_pengunjung');
    }
};

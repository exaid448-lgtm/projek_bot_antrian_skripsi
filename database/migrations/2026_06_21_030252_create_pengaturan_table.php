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
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id('id_pengaturan');
            $table->string('nama_pengaturan')->unique();
            $table->string('nilai_pengaturan');
            $table->timestamps();
        });

        // Insert default mode
        \Illuminate\Support\Facades\DB::table('pengaturan')->insert([
            'nama_pengaturan' => 'engine_mode',
            'nilai_pengaturan' => 'hybrid',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};

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
        Schema::create('invitasi_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->integer('id_loket');
            $table->string('token')->unique();
            $table->dateTime('expires_at');
            $table->enum('status', ['pending', 'registered'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitasi_karyawan');
    }
};

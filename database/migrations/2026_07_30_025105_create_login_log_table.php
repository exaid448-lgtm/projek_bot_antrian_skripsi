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
        Schema::create('login_log', function (Blueprint $table) {
            $table->integer('id_log', true);
            $table->integer('id_user')->index('fk_login_user');
            $table->string('role', 50)->nullable();
            $table->integer('id_loket')->nullable();
            $table->dateTime('login_at');
            $table->dateTime('logout_at')->nullable();
            $table->enum('status', ['login', 'logout'])->nullable()->default('login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_log');
    }
};

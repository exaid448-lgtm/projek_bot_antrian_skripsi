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
        Schema::create('poin_kinerja', function (Blueprint $table) {
            $table->integer('id_poin', true);
            $table->integer('id_profil')->index('id_profil');
            $table->date('tanggal');
            $table->enum('jenis_pelanggaran', ['terlambat_absen', 'terlambat_buka_loket']);
            $table->time('waktu_kejadian');
            $table->integer('poin_dipotong');
            $table->string('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poin_kinerja');
    }
};

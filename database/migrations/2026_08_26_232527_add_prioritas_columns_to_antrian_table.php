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
        Schema::table('antrian', function (Blueprint $table) {
            $table->boolean('is_prioritas')->default(0)->after('status_antrian');
            $table->enum('jenis_prioritas', ['lansia', 'disabilitas_permanen', 'disabilitas_sementara', 'ibu_hamil'])->nullable()->after('is_prioritas');
            $table->string('dokumen_prioritas_sementara')->nullable()->after('jenis_prioritas');
            $table->enum('status_validasi_prioritas', ['menunggu', 'disetujui', 'ditolak'])->nullable()->after('dokumen_prioritas_sementara');
            $table->text('alasan_penolakan_prioritas')->nullable()->after('status_validasi_prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn([
                'is_prioritas', 
                'jenis_prioritas', 
                'dokumen_prioritas_sementara', 
                'status_validasi_prioritas', 
                'alasan_penolakan_prioritas'
            ]);
        });
    }
};

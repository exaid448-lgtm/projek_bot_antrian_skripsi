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
        Schema::table('profil_pengunjung', function (Blueprint $table) {
            // Rename columns using DB::statement for older MySQL compatibility or ENUM rename issues
            // status_disabilitas -> status_prioritas
            // dokumen_disabilitas -> dokumen_prioritas
        });
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung CHANGE status_disabilitas status_prioritas ENUM('tidak', 'menunggu_validasi', 'disetujui', 'ditolak') DEFAULT 'tidak'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung CHANGE dokumen_disabilitas dokumen_prioritas VARCHAR(255) NULL");
        
        Schema::table('profil_pengunjung', function (Blueprint $table) {
            $table->enum('jenis_prioritas', ['disabilitas_permanen', 'disabilitas_sementara', 'ibu_hamil'])->nullable()->after('dokumen_prioritas');
            $table->date('tanggal_berakhir_prioritas')->nullable()->after('jenis_prioritas');
            $table->text('alasan_penolakan_prioritas')->nullable()->after('tanggal_berakhir_prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_pengunjung', function (Blueprint $table) {
            $table->dropColumn(['jenis_prioritas', 'tanggal_berakhir_prioritas', 'alasan_penolakan_prioritas']);
        });
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung CHANGE status_prioritas status_disabilitas ENUM('tidak', 'menunggu_validasi', 'permanen', 'ditolak') DEFAULT 'tidak'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE profil_pengunjung CHANGE dokumen_prioritas dokumen_disabilitas VARCHAR(255) NULL");
    }
};

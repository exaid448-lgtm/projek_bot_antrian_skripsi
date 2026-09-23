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
        // 1. Tambahkan kolom sesi_booking ke tabel loket jika belum ada
        if (Schema::hasTable('loket') && !Schema::hasColumn('loket', 'sesi_booking')) {
            Schema::table('loket', function (Blueprint $table) {
                $table->enum('sesi_booking', ['pagi_siang', 'pagi', 'siang'])
                      ->default('pagi_siang')
                      ->after('status_booking');
            });
        }

        // 2. Tambahkan kolom khusus booking ke tabel antrian jika belum ada
        if (Schema::hasTable('antrian')) {
            Schema::table('antrian', function (Blueprint $table) {
                if (!Schema::hasColumn('antrian', 'kode_booking_unik')) {
                    $table->string('kode_booking_unik', 50)->nullable()->after('id_antrian');
                }
                if (!Schema::hasColumn('antrian', 'tanggal_booking')) {
                    $table->date('tanggal_booking')->nullable()->after('nomor_antrian');
                }
                if (!Schema::hasColumn('antrian', 'slot_waktu')) {
                    $table->string('slot_waktu', 50)->nullable()->after('tanggal_booking');
                }
                if (!Schema::hasColumn('antrian', 'waktu_check_in')) {
                    $table->dateTime('waktu_check_in')->nullable()->after('slot_waktu');
                }
                if (!Schema::hasColumn('antrian', 'batas_check_in')) {
                    $table->time('batas_check_in')->nullable()->after('waktu_check_in');
                }
                if (!Schema::hasColumn('antrian', 'status_booking')) {
                    $table->enum('status_booking', ['booking', 'check_in', 'no_show', 'selesai', 'batal'])
                          ->nullable()
                          ->default('booking')
                          ->after('batas_check_in');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('loket') && Schema::hasColumn('loket', 'sesi_booking')) {
            Schema::table('loket', function (Blueprint $table) {
                $table->dropColumn('sesi_booking');
            });
        }

        if (Schema::hasTable('antrian')) {
            Schema::table('antrian', function (Blueprint $table) {
                $columns = ['kode_booking_unik', 'tanggal_booking', 'slot_waktu', 'waktu_check_in', 'batas_check_in', 'status_booking'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('antrian', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

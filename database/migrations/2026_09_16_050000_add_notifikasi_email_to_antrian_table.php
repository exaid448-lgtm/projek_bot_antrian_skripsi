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
            if (!Schema::hasColumn('antrian', 'notifikasi_email_dikirim')) {
                $table->boolean('notifikasi_email_dikirim')->default(false)->after('status_booking');
            }
            if (!Schema::hasColumn('antrian', 'waktu_notifikasi_email')) {
                $table->timestamp('waktu_notifikasi_email')->nullable()->after('notifikasi_email_dikirim');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn(['notifikasi_email_dikirim', 'waktu_notifikasi_email']);
        });
    }
};

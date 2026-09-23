<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Antrian;

$antrianToday = Antrian::whereDate('waktu_voice', \Carbon\Carbon::today())->get();

foreach ($antrianToday as $a) {
    echo "ID: " . $a->id_antrian . "\n";
    echo "Nomor: " . $a->nomor_antrian . "\n";
    echo "Status: " . $a->status_antrian . "\n";
    echo "Waktu Voice: " . $a->waktu_voice . "\n";
    echo "Waktu Panggil: " . ($a->waktu_panggil ?? 'NULL') . "\n";
    echo "Waktu Selesai: " . ($a->waktu_selesai ?? 'NULL') . "\n";
    echo "Loket: " . $a->id_loket . "\n";
    echo "Pengunjung: " . $a->id_pengunjung . "\n";
    echo "---------------------------\n";
}

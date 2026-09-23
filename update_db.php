<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE loket ADD COLUMN kuota_booking INT DEFAULT 10");
    echo "Column kuota_booking added.\n";
} catch (\Exception $e) {
    echo "kuota_booking: " . $e->getMessage() . "\n";
}

try {
    DB::statement("ALTER TABLE loket ADD COLUMN status_booking ENUM('aktif', 'nonaktif') DEFAULT 'aktif'");
    echo "Column status_booking added.\n";
} catch (\Exception $e) {
    echo "status_booking: " . $e->getMessage() . "\n";
}

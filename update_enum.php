<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE antrian MODIFY COLUMN setatus_pengambilan ENUM('offline', 'online')");
    DB::statement("ALTER TABLE antrian MODIFY COLUMN status_antrian ENUM('menunggu', 'dipanggil', 'selesai', 'terlewat', 'batal', 'booking')");
    echo "Success: ENUM updated.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

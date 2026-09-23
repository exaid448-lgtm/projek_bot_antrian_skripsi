<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$antrian = App\Models\Antrian::where('status_antrian', 'selesai')
    ->whereNotIn('id_antrian', function($query) {
        $query->select('id_antrain')->from('skm_jawaban')->whereNotNull('id_antrain');
    })
    ->get()
    ->pluck('id_antrian');
dump($antrian);

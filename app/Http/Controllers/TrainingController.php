<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class TrainingController extends Controller
{
    public function train()
    {
        // Lokasi folder script
        $dirPath = base_path('resources/python_service/python_scripts');
        
        // Perintah: Pindah ke folder tersebut (cd) baru jalankan python
        // Ini memastikan model_mpp.pkl tersimpan di folder yang sama dengan script
        $command = "cd " . escapeshellarg($dirPath) . " && python train_model.py";

        $result = Process::run($command);

        if ($result->successful()) {
            return back()->with('success', 'Bot berhasil dilatih ulang! Otak AI sekarang lebih pintar.');
        }

        return back()->with('error', 'Gagal melatih bot: ' . $result->errorOutput());
    }
}
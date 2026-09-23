<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Loket;

class DataTrainingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil pengaturan engine_mode
        $engineMode = DB::table('pengaturan')->where('nama_pengaturan', 'engine_mode')->value('nilai_pengaturan') ?? 'hybrid';

        // Ambil riwayat training
        $riwayat = DB::table('riwayat_training')->orderBy('created_at', 'desc')->get();

        // Ambil distribusi data latih per loket
        $distribusiData = DB::table('voice_training')
            ->join('loket', 'voice_training.id_loket', '=', 'loket.id_loket')
            ->select('loket.nama_loket', DB::raw('count(*) as total'))
            ->groupBy('loket.id_loket', 'loket.nama_loket')
            ->get();

        $labelsDistribusi = $distribusiData->pluck('nama_loket')->values()->toArray();
        $dataDistribusi = $distribusiData->pluck('total')->map(function($val) { return (int) $val; })->values()->toArray();

        // Ambil Data Voice Training (Dataset AI)
        $voiceTraining = DB::table('voice_training')
            ->join('loket', 'voice_training.id_loket', '=', 'loket.id_loket')
            ->select('voice_training.*', 'loket.nama_loket')
            ->orderBy('id_training', 'desc')
            ->get();

        // Ambil Data Algoritma (Kamus Kata Kunci)
        $dataAlgoritma = DB::table('algoritma')
            ->join('loket', 'algoritma.id_loket', '=', 'loket.id_loket')
            ->select('algoritma.*', 'loket.nama_loket')
            ->orderBy('id_algoritma', 'desc')
            ->get();

        return view('administrator.data_traning', compact(
            'user', 'engineMode', 'riwayat', 'labelsDistribusi', 'dataDistribusi', 'voiceTraining', 'dataAlgoritma'
        ));
    }

    public function updateMode(Request $request)
    {
        $request->validate([
            'engine_mode' => 'required|in:rule_based,ai_only,hybrid'
        ]);

        DB::table('pengaturan')
            ->where('nama_pengaturan', 'engine_mode')
            ->update(['nilai_pengaturan' => $request->engine_mode]);

        return redirect()->back()->with('success', 'Mode Engine berhasil diubah menjadi: ' . strtoupper($request->engine_mode));
    }

    public function train(Request $request)
    {
        $algoritma = escapeshellarg($request->input('algoritma', 'Naive Bayes'));
        
        // Path ke file python (Sesuaikan path jika perlu)
        $pythonScript = base_path('resources/python_service/python_scripts/train_model.py');
        
        // Command (Gunakan python atau python3 sesuai server)
        $command = "python \"$pythonScript\" $algoritma 2>&1";
        
        // Eksekusi script python
        $output = shell_exec($command);
        
        // Parsing output (contoh output: SUCCESS|Akurasi: 95.00%|Waktu: 1.5 detik|Algoritma: Naive Bayes)
        if (str_contains($output, 'SUCCESS|')) {
            $parts = explode('|', $output);
            $msg = implode(', ', array_slice($parts, 1)); // gabung info akurasi dsb
            return redirect()->back()->with('success', 'Training Berhasil! ' . $msg);
        } else {
            return redirect()->back()->with('error', 'Gagal Training: ' . $output);
        }
    }

    public function destroyVoice($id)
    {
        DB::table('voice_training')->where('id_training', $id)->delete();
        return redirect()->back()->with('success', 'Data latih suara berhasil dihapus!');
    }
}

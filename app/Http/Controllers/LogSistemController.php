<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LogSistemController extends Controller
{
    /**
     * Endpoint API untuk mencatat kegagalan sistem (dipanggil via fetch/AJAX)
     */
    public function catatKegagalan(Request $request)
    {
        $komponen = $request->input('komponen', 'Sistem');
        $tingkat = $request->input('tingkat', 'Warning'); // Warning, Error, Critical
        $pesan = $request->input('pesan_error', 'Terjadi gangguan sistem');
        $aksi = $request->input('aksi_fallback', 'Mode Manual');
        $ip = $request->ip();

        // Tulis ke laravel.log dengan tag khusus [SYSTEM_FAILURE]
        $logMessage = "[SYSTEM_FAILURE] [Komponen: {$komponen}] [Tingkat: {$tingkat}] [Aksi: {$aksi}] Pesan: {$pesan} | IP: {$ip}";
        
        if (strtolower($tingkat) === 'error' || strtolower($tingkat) === 'critical') {
            Log::error($logMessage);
        } else {
            Log::warning($logMessage);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kegagalan sistem berhasil dicatat.'
        ]);
    }

    /**
     * Halaman Administrator untuk melihat riwayat log gangguan sistem
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];
        $totalInsiden = 0;
        $insidenHariIni = 0;
        $insidenPython = 0;
        $insidenMikrofon = 0;

        $filterKomponen = $request->query('komponen');
        $todayStr = Carbon::today()->format('Y-m-d');

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (strpos($line, '[SYSTEM_FAILURE]') !== false) {
                    $totalInsiden++;

                    // Ekstraksi Timestamp: [2026-09-17 04:15:00]
                    $waktu = '-';
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matchWaktu)) {
                        $waktu = $matchWaktu[1];
                        if (substr($waktu, 0, 10) === $todayStr) {
                            $insidenHariIni++;
                        }
                    }

                    // Ekstraksi Komponen: [Komponen: Server Python]
                    $komponen = 'Sistem';
                    if (preg_match('/\[Komponen:\s*([^\]]+)\]/', $line, $matchKomponen)) {
                        $komponen = trim($matchKomponen[1]);
                    }

                    // Ekstraksi Tingkat: [Tingkat: Warning]
                    $tingkat = 'Warning';
                    if (preg_match('/\[Tingkat:\s*([^\]]+)\]/', $line, $matchTingkat)) {
                        $tingkat = trim($matchTingkat[1]);
                    }

                    // Ekstraksi Aksi: [Aksi: Dialihkan ke Antrean Manual]
                    $aksi = 'Dialihkan ke Mode Manual';
                    if (preg_match('/\[Aksi:\s*([^\]]+)\]/', $line, $matchAksi)) {
                        $aksi = trim($matchAksi[1]);
                    }

                    // Ekstraksi Pesan dan IP
                    $pesan = '-';
                    $ip = '-';
                    if (preg_match('/Pesan:\s*(.*?)\s*\|\s*IP:\s*([0-9a-fA-F\.:]+)/', $line, $matchPesan)) {
                        $pesan = trim($matchPesan[1]);
                        $ip = trim($matchPesan[2]);
                    } elseif (preg_match('/Pesan:\s*(.*)$/', $line, $matchPesan)) {
                        $pesan = trim($matchPesan[1]);
                        $ip = '-';
                    }

                    // Hitung statistik per komponen
                    if (stripos($komponen, 'python') !== false || stripos($komponen, 'whisper') !== false) {
                        $insidenPython++;
                    } elseif (stripos($komponen, 'mikrofon') !== false || stripos($komponen, 'audio') !== false) {
                        $insidenMikrofon++;
                    }

                    // Filter jika ada parameter query komponen
                    if ($filterKomponen && stripos($komponen, $filterKomponen) === false) {
                        continue;
                    }

                    $logs[] = [
                        'waktu' => $waktu,
                        'komponen' => $komponen,
                        'tingkat' => $tingkat,
                        'aksi' => $aksi,
                        'pesan' => $pesan,
                        'ip' => $ip,
                    ];
                }
            }

            // Urutkan dari yang paling baru
            $logs = array_reverse($logs);
        }

        return view('administrator.data_log_sistem', compact(
            'logs',
            'totalInsiden',
            'insidenHariIni',
            'insidenPython',
            'insidenMikrofon',
            'filterKomponen'
        ));
    }

    /**
     * Membersihkan file log gangguan sistem
     */
    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            $newLines = [];

            // Simpan baris log lain, hapus yang bertanda [SYSTEM_FAILURE]
            foreach ($lines as $line) {
                if (strpos($line, '[SYSTEM_FAILURE]') === false) {
                    $newLines[] = $line;
                }
            }

            File::put($logPath, implode("\n", $newLines));
        }

        return redirect()->route('admin.log_sistem.index')->with('success', 'Riwayat log gangguan sistem berhasil dibersihkan.');
    }
}

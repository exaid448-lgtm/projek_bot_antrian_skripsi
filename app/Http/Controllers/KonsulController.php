<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Konsultasi; 
use Illuminate\Support\Facades\Auth;
// Pastikan hanya ada satu baris ini untuk ProfilPengunjung
use App\Models\ProfilPengunjung; 

class KonsulController extends Controller
{
    public function index()
    {
        $loket_unik = DB::table('loket')
            ->select('nama_loket')
            ->distinct()
            ->get();
        
        $semua_data = DB::table('loket')->get();

        // Perbaikan: Langsung panggil ProfilPengunjung karena sudah di-'use' di atas
        $profil = ProfilPengunjung::where('id_user', Auth::id())->first();

        return view('konsul.konsul', compact('loket_unik', 'semua_data', 'profil'));
    }

public function store(Request $request)
    {
        $request->validate([
            'layanan' => 'required',
            'konsultasi' => 'required|min:10',
        ]);

        $dataLoket = DB::table('loket')
            ->where('nama_pelayanan', $request->layanan)
            ->first();

        if (!$dataLoket) {
            return back()->with('error', 'Layanan tidak ditemukan.');
        }

        // Ambil profil pengunjung berdasarkan user yang login
        $profil = ProfilPengunjung::where('id_user', Auth::id())->first();

        if (!$profil) {
            return back()->with('error', 'Profil pengunjung tidak ditemukan. Silakan lengkapi profil terlebih dahulu.');
        }

        try {
            Konsultasi::create([
                'id_loket'         => $dataLoket->id_loket,
                'id_pengunjung'    => $profil->id_pengunjung, // Menggunakan ID dari tabel profil_pengunjung
                'konsultasi'       => $request->konsultasi,
                'tanggal_konsul'   => now(),
                'pelayanan_status' => 'belum'
            ]);

            return back()->with('success', 'Konsultasi berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
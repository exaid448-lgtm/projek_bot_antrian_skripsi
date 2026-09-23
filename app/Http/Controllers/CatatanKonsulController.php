<?php

namespace App\Http\Controllers; // WAJIB ADA

use App\Models\Konsultasi;
// WAJIB DIIMPORT
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanKonsulController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data profil pengunjung dari user yang login
        $user = Auth::user();
        $profil = $user->profil_pengunjung;

        if (!$profil) {
            return redirect()->back()->with('error', 'Profil pengunjung tidak ditemukan.');
        }

        $idPengunjung = $profil->id_pengunjung;

        // 2. Ambil data loket untuk dropdown filter
        $lokets = \App\Models\Loket::all();

        // 3. Query data riwayat konsultasi milik user tersebut
        $query = Konsultasi::where('id_pengunjung', $idPengunjung);

        // Filter Layanan (berdasarkan nama loket)
        if ($request->filled('layanan') && $request->layanan !== 'Semua Layanan') {
            $query->whereHas('loket', function ($q) use ($request) {
                $q->where('nama_loket', $request->layanan);
            });
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_konsul', $request->tanggal);
        }

        $riwayat = $query->latest('tanggal_konsul')->get();

        // 4. Statistik untuk Sidebar
        // Belum di Konsultasikan (status bukan 'selesai')
        $belumKonsultasi = Konsultasi::where('id_pengunjung', $idPengunjung)
            ->where('pelayanan_status', '!=', 'selesai')
            ->count();
            
        // Sudah di Konsultasikan (status 'selesai')
        $sudahKonsultasi = Konsultasi::where('id_pengunjung', $idPengunjung)
            ->where('pelayanan_status', 'selesai')
            ->count();

        return view('pengunjung.catatan_konsul', compact(
            'riwayat', 
            'profil', 
            'lokets', 
            'belumKonsultasi', 
            'sudahKonsultasi'
        ));
    }
}

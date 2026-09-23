<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $id_profil = Session::get('id_profil');

        if (!$id_profil) {
            return redirect('/login');
        }

        $profilLogin = Profil::with('loket')->findOrFail($id_profil);
        $id_loket = $profilLogin->id_loket;
        $nama_loket = $profilLogin->loket->nama_loket ?? 'Loket';

        // Ambil ID semua karyawan yang berada di loket yang sama
        $profilLoket = Profil::where('id_loket', $id_loket)->pluck('id_profil');

        $query = Jadwal::whereIn('id_profil', $profilLoket)
            ->with('profil');

        // ================== FIX: FILTER SESUAI INPUT BLADE ==================

        // 1. Filter Pencarian Nama Karyawan (Berdasarkan relasi profil)
        if ($request->filled('search')) {
            $query->whereHas('profil', function($q) use ($request) {
                $q->where('nama_user', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter Berdasarkan Rentang Tanggal (Start Date)
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        // 3. Filter Berdasarkan Rentang Tanggal (End Date)
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        
        // ====================================================================

        $jadwal = $query->orderBy('tanggal', 'desc')->get();

        // Mengirimkan variabel status yang digunakan di PDF agar konsisten
        $statuses = ['hadir', 'izin', 'sakit', 'alfa', 'libur'];

        return view('admin_loket.jadwal_loket', compact('jadwal', 'nama_loket', 'statuses'));
    }

    public function cetak(Request $request)
    {
        $id_profil = Session::get('id_profil');
        
        if (!$id_profil) {
            return redirect('/login');
        }

        $profilLogin = Profil::findOrFail($id_profil);
        
        // Query disesuaikan agar hanya mengambil jadwal rekan satu loket
        $query = Jadwal::whereIn('id_profil', Profil::where('id_loket', $profilLogin->id_loket)->pluck('id_profil'))
                    ->with('profil');

        // Filter Pencarian di laporan
        if ($request->filled('search')) {
            $query->whereHas('profil', function($q) use ($request) {
                $q->where('nama_user', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $jadwal = $query->orderBy('tanggal', 'asc')->get();

        // Pastikan view PDF mengarah ke file yang benar
        return view('pdf.laporan_jadwal_loket', compact('jadwal'));
    }
}
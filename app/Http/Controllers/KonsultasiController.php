<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konsultasi;
use App\Models\Profil;

class KonsultasiController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $profil = Profil::where('id_user', session('id_user'))->first();
        
        // 🔄 Tambahkan with('pengunjung') untuk mengambil data nama & nomor_whatsapp
        // Di dalam KonsultasiController.php pada method index() dan cetakPDF()

        // 1. Ubah Eager Loading-nya
        $query = Konsultasi::with('data_pengunjung')->where('id_loket', $profil->id_loket);

        // 2. Ubah juga di bagian Filter Pencarian (jika ada)
        if ($request->filled('q')) {
            $query->whereHas('data_pengunjung', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        // 📊 Filter Status
        if ($request->filled('status')) {
            $query->where('pelayanan_status', $request->status);
        }

        // 📅 Filter Range Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_konsul', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_konsul', '<=', $request->end_date);
        }

        $konsultasi = $query->orderBy('tanggal_konsul', 'desc')->get();

        return view('admin_loket.loket_konsul', compact('konsultasi', 'profil'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['id_konsul' => 'required|integer']);
        $konsul = Konsultasi::find($request->id_konsul);

        if (!$konsul) return response()->json(['success' => false]);

        $konsul->pelayanan_status = 'sudah';
        $konsul->save();

        return response()->json(['success' => true]);
    }

    public function cetakPDF(Request $request)
    {
        $profil = Profil::where('id_user', session('id_user'))->first();
        
        // 🔄 Tambahkan juga eager loading di sini agar cetak PDF tidak error
        $query = Konsultasi::with('pengunjung')->where('id_loket', $profil->id_loket);

        if ($request->filled('q')) {
            $query->whereHas('pengunjung', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('pelayanan_status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_konsul', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_konsul', '<=', $request->end_date);
        }

        $data = $query->orderBy('tanggal_konsul', 'asc')->get();

        return view('pdf.laporan_konsultasi', compact('data', 'profil', 'request'));
    }
}
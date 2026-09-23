<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilPengunjung;
use Illuminate\Support\Facades\DB;

class ValidasiPrioritasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Pengajuan prioritas (Semua tipe) dari profil yang menunggu validasi
        $profilMenunggu = ProfilPengunjung::where('status_prioritas', 'menunggu_validasi')->get();

        // Riwayat pengajuan prioritas (yang sudah disetujui atau ditolak)
        $queryRiwayat = ProfilPengunjung::whereIn('status_prioritas', ['disetujui', 'ditolak']);
            
        if ($search) {
            $queryRiwayat->where('nama', 'like', "%{$search}%");
        }
        
        // if ($startDate && $endDate) {
        //     $queryRiwayat->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        // }
        
        $profilRiwayat = $queryRiwayat->orderBy('id_pengunjung', 'desc')->get();

        return view('administrator.validasi_prioritas', compact('profilMenunggu', 'profilRiwayat'));
    }

    public function cetak(Request $request)
    {
        $useQr = $request->query('qrcode', 0);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $queryRiwayat = ProfilPengunjung::whereIn('status_prioritas', ['disetujui', 'ditolak']);
            
        if ($search) {
            $queryRiwayat->where('nama', 'like', "%{$search}%");
        }
        
        // if ($startDate && $endDate) {
        //     $queryRiwayat->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        // }
        
        $profilRiwayat = $queryRiwayat->orderBy('id_pengunjung', 'desc')->get();

        return view('pdf.laporan_prioritas', compact('profilRiwayat', 'useQr'));
    }

    public function setujuProfil(Request $request, $id)
    {
        $profil = ProfilPengunjung::findOrFail($id);
        
        if (in_array($profil->jenis_prioritas, ['disabilitas_sementara', 'ibu_hamil'])) {
            $request->validate([
                'tanggal_berakhir_prioritas' => 'required|date'
            ]);
            $profil->tanggal_berakhir_prioritas = $request->tanggal_berakhir_prioritas;
        }

        $profil->status_prioritas = 'disetujui';
        $profil->save();
        return redirect()->back()->with('success', 'Status prioritas berhasil disetujui.');
    }

    public function tolakProfil(Request $request, $id)
    {
        $request->validate(['alasan_penolakan' => 'required|string']);
        
        $profil = ProfilPengunjung::findOrFail($id);
        $profil->status_prioritas = 'ditolak';
        $profil->alasan_penolakan_prioritas = $request->alasan_penolakan;
        // Optionally delete the document here to save space
        
        $profil->save();
        return redirect()->back()->with('success', 'Status prioritas ditolak.');
    }

    public function hapusProfil(Request $request, $id)
    {
        $profil = ProfilPengunjung::findOrFail($id);
        $profil->status_prioritas = null;
        $profil->jenis_prioritas = null;
        $profil->dokumen_prioritas = null;
        $profil->tanggal_berakhir_prioritas = null;
        $profil->alasan_penolakan_prioritas = null;
        $profil->save();

        return redirect()->back()->with('success', 'Riwayat validasi prioritas berhasil dihapus.');
    }

    public function editProfil(Request $request, $id)
    {
        $request->validate([
            'status_prioritas' => 'required|in:disetujui,ditolak',
            'tanggal_berakhir_prioritas' => 'nullable|date',
            'alasan_penolakan' => 'nullable|string'
        ]);

        $profil = ProfilPengunjung::findOrFail($id);
        $profil->status_prioritas = $request->status_prioritas;
        
        if ($request->status_prioritas == 'disetujui') {
            $profil->tanggal_berakhir_prioritas = $request->tanggal_berakhir_prioritas;
            $profil->alasan_penolakan_prioritas = null;
        } else {
            $profil->tanggal_berakhir_prioritas = null;
            $profil->alasan_penolakan_prioritas = $request->alasan_penolakan;
        }
        
        $profil->save();
        return redirect()->back()->with('success', 'Riwayat validasi prioritas berhasil diperbarui.');
    }

    public function getDokumenBase64(Request $request)
    {
        $path = $request->input('path');
        if (!$path) {
            return response()->json(['success' => false, 'message' => 'Path tidak diberikan.']);
        }
        
        $fullPath = storage_path('app/public/' . str_replace('storage/', '', $path));
        
        if (file_exists($fullPath)) {
            $mime = mime_content_type($fullPath);
            $base64 = base64_encode(file_get_contents($fullPath));
            return response()->json([
                'success' => true,
                'mime' => $mime,
                'data' => $base64
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'File tidak ditemukan.']);
    }
}

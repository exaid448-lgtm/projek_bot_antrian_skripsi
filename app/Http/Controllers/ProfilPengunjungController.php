<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Antrian;
use App\Models\Loket;
use App\Models\ProfilPengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfilPengunjungController extends Controller // Nama diperbaiki (Controller)
{
    public function index()
    {
        // 1. Ambil data profil dulu
        $profil = ProfilPengunjung::where('id_user', Auth::id())->first();

        // 2. CEK: Jika profil tidak ada, langsung redirect (Jangan jalankan kode di bawahnya)
        if (! $profil) {
            return redirect()->route('profil.create')->with('error', 'Silakan lengkapi profil Anda.');
        }

        // 3. Jika profil ada, baru ambil data antrian untuk chart
        $dataLoket = Antrian::where('id_pengunjung', $profil->id_pengunjung)
            ->select('id_loket', DB::raw('count(*) as total'))
            ->groupBy('id_loket')
            ->with('loket')
            ->get();

        // 4. Siapkan data untuk Chart.js
        $labels = $dataLoket->pluck('loket.nama_loket');
        $totals = $dataLoket->pluck('total');

        // 1. Data Kepadatan Pengunjung (Semua Pengunjung Hari Ini)
        $rawKepadatan = Antrian::whereDate('waktu_voice', Carbon::today())
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
            ->get();

        $kepadatanGrouped = [];
        foreach ($rawKepadatan as $antrian) {
            $baseName = strtoupper(explode(' ', trim($antrian->nama_loket))[0]);
            if (!isset($kepadatanGrouped[$baseName])) {
                $kepadatanGrouped[$baseName] = 0;
            }
            $kepadatanGrouped[$baseName]++;
        }
        
        $kepadatanData = collect();
        foreach ($kepadatanGrouped as $name => $total) {
            $kepadatanData->push((object)['nama_loket' => $name, 'total' => $total]);
        }

        // 2. Data Riwayat Pribadi (Loket yang sering dikunjungi User ini)
        $rawSeringDikunjungi = Antrian::where('id_pengunjung', $profil->id_pengunjung)
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
            ->get();
            
        $riwayatGrouped = [];
        foreach ($rawSeringDikunjungi as $antrian) {
            $baseName = strtoupper(explode(' ', trim($antrian->nama_loket))[0]);
            if (!isset($riwayatGrouped[$baseName])) {
                $riwayatGrouped[$baseName] = 0;
            }
            $riwayatGrouped[$baseName]++;
        }
        
        $seringDikunjungi = collect();
        foreach ($riwayatGrouped as $name => $total) {
            $seringDikunjungi->push((object)['nama_loket' => $name, 'total' => $total]);
        }
        $seringDikunjungi = $seringDikunjungi->sortByDesc('total')->take(4)->values();

        // 5. Kirim semua ke view
        return view('pengunjung.profil_pengunjung', compact('profil', 'labels', 'totals', 'kepadatanData', 'seringDikunjungi'));
    }
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,jfif,avif|max:5120',
        ], [
            'foto.required' => 'Silakan pilih foto terlebih dahulu.',
            'foto.file' => 'File yang diunggah harus berupa file yang valid.',
            'foto.mimes' => 'Format file foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ]);
        $profil = ProfilPengunjung::where('id_user', Auth::id())->first();
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($profil->foto && Storage::disk('public')->exists($profil->foto)) {
                Storage::disk('public')->delete($profil->foto);
            }

            // Simpan foto baru
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $content = file_get_contents($file->getRealPath());
            if (str_contains($content, '<svg') || str_contains($content, '<?xml')) {
                $extension = 'svg';
            }
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $extension;
            $path = $file->storeAs('profil_fotos', $filename, 'public');

            // Update database
            $profil->update([
                'foto' => $path,
            ]);

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui foto profil.');
    }
    public function uploadPrioritas(Request $request)
    {
        $request->validate([
            'jenis_prioritas' => 'required|in:disabilitas_permanen,disabilitas_sementara,ibu_hamil',
            'dokumen_prioritas' => 'required|mimes:pdf,jpeg,png,jpg|max:2048',
        ], [
            'jenis_prioritas.required' => 'Jenis prioritas wajib dipilih.',
            'jenis_prioritas.in' => 'Jenis prioritas tidak valid.',
            'dokumen_prioritas.required' => 'Dokumen bukti wajib diunggah.',
            'dokumen_prioritas.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'dokumen_prioritas.max' => 'Ukuran maksimal file adalah 2MB.',
        ]);

        $profil = ProfilPengunjung::where('id_user', Auth::id())->first();

        if ($request->hasFile('dokumen_prioritas')) {
            // Hapus dokumen lama jika ada
            if ($profil->dokumen_prioritas && Storage::disk('public')->exists($profil->dokumen_prioritas)) {
                Storage::disk('public')->delete($profil->dokumen_prioritas);
            }

            // Simpan dokumen baru
            $file = $request->file('dokumen_prioritas');
            $filename = time() . '_prioritas_' . $file->getClientOriginalName();
            $path = $file->storeAs('dokumen_prioritas', $filename, 'public');

            // Update database dan ubah status ke menunggu validasi
            $profil->update([
                'jenis_prioritas' => $request->jenis_prioritas,
                'dokumen_prioritas' => $path,
                'status_prioritas' => 'menunggu_validasi',
                'tanggal_berakhir_prioritas' => null, // Reset tanggal jika upload ulang
                'alasan_penolakan_prioritas' => null, // Reset alasan jika upload ulang
            ]);

            return redirect()->back()->with('success', 'Dokumen prioritas berhasil diunggah. Silakan tunggu validasi dari Administrator.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah dokumen.');
    }
}

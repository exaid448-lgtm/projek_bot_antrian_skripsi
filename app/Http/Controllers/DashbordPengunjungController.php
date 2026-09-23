<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loket; 
use App\Models\Antrian;
use App\Models\ProfilPengunjung;
use App\Models\SkmJawaban;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashbordPengunjungController extends Controller
{
    public function index()
    {
        $lokets = Loket::all();

        // Ambil rekap data SKM per loket untuk menghindari query N+1
        $skmRaw = SkmJawaban::selectRaw('id_loket, jawaban, count(*) as count')
            ->groupBy('id_loket', 'jawaban')
            ->get();
            
        $skmGrouped = [];
        foreach ($skmRaw as $raw) {
            $skmGrouped[$raw->id_loket][$raw->jawaban] = $raw->count;
        }

        // Calculate stats for individual lokets
        foreach ($lokets as $loket) {
            $stats = $skmGrouped[$loket->id_loket] ?? [];
            $sangatBagus = $stats['sangat bagus'] ?? 0;
            $bagus = $stats['bagus'] ?? 0;
            $kurang = $stats['kurang'] ?? 0;
            $sangatKurang = $stats['sangat kurang'] ?? 0;
            
            $total = $sangatBagus + $bagus + $kurang + $sangatKurang;
            
            $loket->skm_stats = [
                'total' => $total,
                'sangat_bagus' => $total > 0 ? round(($sangatBagus / $total) * 100) : 0,
                'bagus' => $total > 0 ? round(($bagus / $total) * 100) : 0,
                'kurang' => $total > 0 ? round(($kurang / $total) * 100) : 0,
                'sangat_kurang' => $total > 0 ? round(($sangatKurang / $total) * 100) : 0,
                'sangat_bagus_count' => $sangatBagus,
                'bagus_count' => $bagus,
                'kurang_count' => $kurang,
                'sangat_kurang_count' => $sangatKurang,
            ];
        }

        $groupedLokets = collect();

        foreach ($lokets->groupBy('nama_loket') as $namaGrup => $grup) {
            $mainLoket = $grup->first();
            $statusBuka = $grup->contains('status_pelayanan', 'BUKA') ? 'BUKA' : 'TUTUP';
            
            // Collect all services for this group
            $layanans = $grup->map(function($l) {
                return [
                    'nama_pelayanan' => $l->nama_pelayanan ?: 'Pelayanan Umum',
                    'status' => $l->status_pelayanan,
                    'prefix' => $l->prefix
                ];
            })->values();

            // Calculate aggregated SKM stats for this group
            $sangatBagus = 0; $bagus = 0; $kurang = 0; $sangatKurang = 0;
            foreach ($grup as $l) {
                $stats = $skmGrouped[$l->id_loket] ?? [];
                $sangatBagus += $stats['sangat bagus'] ?? 0;
                $bagus += $stats['bagus'] ?? 0;
                $kurang += $stats['kurang'] ?? 0;
                $sangatKurang += $stats['sangat kurang'] ?? 0;
            }
            
            $total = $sangatBagus + $bagus + $kurang + $sangatKurang;
            
            $skm_stats = [
                'total' => $total,
                'sangat_bagus' => $total > 0 ? round(($sangatBagus / $total) * 100) : 0,
                'bagus' => $total > 0 ? round(($bagus / $total) * 100) : 0,
                'kurang' => $total > 0 ? round(($kurang / $total) * 100) : 0,
                'sangat_kurang' => $total > 0 ? round(($sangatKurang / $total) * 100) : 0,
                'sangat_bagus_count' => $sangatBagus,
                'bagus_count' => $bagus,
                'kurang_count' => $kurang,
                'sangat_kurang_count' => $sangatKurang,
            ];

            // Assign to grouped object
            $groupedObj = (object) [
                'nama_loket' => $namaGrup,
                'logo' => $mainLoket->logo,
                'lokasi_loket' => $mainLoket->lokasi_loket,
                'status_pelayanan' => $statusBuka,
                'has_multiple' => $grup->count() > 1,
                'layanans' => $layanans,
                'skm_stats' => $skm_stats,
                'id_loket_first' => $mainLoket->id_loket
            ];
            
            $groupedLokets->push($groupedObj);
        }

        $id_user = session('id_user');

        $antrianSaya = collect(); 
        
        if ($id_user) {
            $profil = ProfilPengunjung::where('id_user', $id_user)->first();

            if ($profil) {
                // Perbaikan Query: Mengunci id_pengunjung (tampilkan semua metode pengambilan milik user ini)
                $antrianSaya = Antrian::where('id_pengunjung', $profil->id_pengunjung)
                    
                    // 2. Hanya tampilkan jika statusnya 'menunggu', 'dipanggil', atau 'booking'
                    ->whereIn('status_antrian', ['menunggu', 'dipanggil', 'booking'])
                    
                    // 3. Filter hari ini dan masa depan (Booking)
                    ->whereDate('waktu_voice', '>=', Carbon::today())
                    
                    ->with('loket')
                    ->orderBy('waktu_voice', 'asc') // Urutkan berdasarkan waktu (yang terdekat dulu)
                    ->get();
            }
        }

        return view('pengunjung.Dashbord_pengunjung', compact('lokets', 'groupedLokets', 'antrianSaya'));
    }

    public function batalAntrian($id)
    {
        $id_user = session('id_user');
        if (!$id_user) {
            return response()->json(['success' => false, 'message' => 'Anda harus login terlebih dahulu.']);
        }

        $profil = ProfilPengunjung::where('id_user', $id_user)->first();
        if (!$profil) {
            return response()->json(['success' => false, 'message' => 'Profil pengunjung tidak ditemukan.']);
        }

        $antrian = Antrian::where('id_antrian', $id)
            ->where('id_pengunjung', $profil->id_pengunjung)
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Antrean tidak ditemukan atau bukan milik Anda.']);
        }

        if (!in_array($antrian->status_antrian, ['menunggu', 'booking'])) {
            return response()->json(['success' => false, 'message' => 'Hanya antrean dengan status menunggu atau booking yang dapat dibatalkan.']);
        }

        $antrian->status_antrian = 'batal';
        $antrian->save();

        return response()->json(['success' => true, 'message' => 'Antrean Anda berhasil dibatalkan.']);
    }

    public function checkIn(Request $request, $id)
    {
        $id_user = session('id_user');
        if (!$id_user) {
            return response()->json(['success' => false, 'message' => 'Anda harus login terlebih dahulu.']);
        }

        $profil = ProfilPengunjung::where('id_user', $id_user)->first();
        if (!$profil) {
            return response()->json(['success' => false, 'message' => 'Profil pengunjung tidak ditemukan.']);
        }

        $antrian = Antrian::where('id_antrian', $id)
            ->where('id_pengunjung', $profil->id_pengunjung)
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Antrean tidak ditemukan atau bukan milik Anda.']);
        }

        // Cek tanggal booking
        $tglBooking = $antrian->tanggal_booking ? Carbon::parse($antrian->tanggal_booking) : Carbon::parse($antrian->waktu_voice);
        if (!$tglBooking->isToday()) {
            return response()->json([
                'success' => false, 
                'message' => 'Check-in hanya dapat dilakukan pada hari kunjungan (' . $tglBooking->format('d-m-Y') . ').'
            ]);
        }

        if ($antrian->status_booking === 'check_in') {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan check-in sebelumnya.']);
        }

        if (in_array($antrian->status_antrian, ['selesai', 'batal'])) {
            return response()->json(['success' => false, 'message' => 'Antrean sudah selesai atau dibatalkan.']);
        }

        $antrian->status_booking = 'check_in';
        $antrian->waktu_check_in = Carbon::now();
        if ($antrian->status_antrian === 'booking') {
            $antrian->status_antrian = 'menunggu';
        }
        $antrian->save();

        return response()->json([
            'success' => true, 
            'message' => 'Check-in berhasil! Kehadiran Anda telah terkonfirmasi. Silakan menunggu nomor Anda dipanggil di loket.',
            'waktu_check_in' => Carbon::now()->format('H:i')
        ]);
    }
}
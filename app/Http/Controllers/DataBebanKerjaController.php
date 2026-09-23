<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loket;

class DataBebanKerjaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $year = $request->input('year');
        $id_loket = $request->input('id_loket');

        $activeYear = date('Y');
        $activeMonth = date('m');

        // Mengambil semua data loket untuk filter
        $lokets = Loket::orderBy('nama_loket', 'asc')->get();

        // 1. Query Utama untuk Tabel Beban Kerja Karyawan
        $query = DB::table('antrian')
            ->join('profil_karyawan', 'antrian.id_karyawan', '=', 'profil_karyawan.id_user')
            ->leftJoin('loket', 'profil_karyawan.id_loket', '=', 'loket.id_loket')
            ->whereNotNull('antrian.id_karyawan');

        if ($search) {
            $query->where('profil_karyawan.nama_user', 'like', '%' . $search . '%');
        }
        if ($start_date) {
            $query->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $query->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $query->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            // Default: Tampilkan data bulan ini
            $query->whereYear('antrian.waktu_voice', $activeYear)
                  ->whereMonth('antrian.waktu_voice', $activeMonth);
        }
        if ($id_loket) {
            $query->where('profil_karyawan.id_loket', $id_loket);
        }

        $queryHarian = clone $query;

        // Aggregate Data per Karyawan
        $bebanKerjaRaw = $query->select(
                'profil_karyawan.id_user',
                'profil_karyawan.nama_user',
                'profil_karyawan.img_user',
                'loket.nama_loket',
                'loket.nama_pelayanan',
                DB::raw("COUNT(antrian.id_antrian) as total_ditangani"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'selesai' THEN 1 ELSE 0 END) as selesai_count"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'terlewat' THEN 1 ELSE 0 END) as terlewat_count"),
                DB::raw("AVG(TIME_TO_SEC(TIMEDIFF(antrian.waktu_selesai, antrian.waktu_panggil))) as avg_seconds")
            )
            ->groupBy('profil_karyawan.id_user', 'profil_karyawan.nama_user', 'profil_karyawan.img_user', 'loket.nama_loket', 'loket.nama_pelayanan')
            ->orderBy('selesai_count', 'desc')
            ->get();

        // Aggregate Data Riwayat Harian per Karyawan
        $riwayatHarian = $queryHarian->select(
                'profil_karyawan.id_user',
                'profil_karyawan.nama_user',
                'profil_karyawan.img_user',
                'loket.nama_loket',
                'loket.nama_pelayanan',
                DB::raw("DATE(antrian.waktu_voice) as tanggal_kerja"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'selesai' THEN 1 ELSE 0 END) as selesai_count"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'terlewat' THEN 1 ELSE 0 END) as terlewat_count"),
                DB::raw("AVG(TIME_TO_SEC(TIMEDIFF(antrian.waktu_selesai, antrian.waktu_panggil))) as avg_seconds")
            )
            ->groupBy('profil_karyawan.id_user', 'profil_karyawan.nama_user', 'profil_karyawan.img_user', 'loket.nama_loket', 'loket.nama_pelayanan', DB::raw("DATE(antrian.waktu_voice)"))
            ->orderBy('tanggal_kerja', 'desc')
            ->orderBy('selesai_count', 'desc')
            ->get();

        // 2. Data untuk Chart (Top 10 Karyawan Paling Produktif)
        $chartLabels = [];
        $chartDataSelesai = [];
        $chartDataTerlewat = [];
        
        $topKaryawan = $bebanKerjaRaw->take(10);
        foreach ($topKaryawan as $karyawan) {
            $chartLabels[] = explode(' ', trim($karyawan->nama_user))[0]; // Ambil nama depan saja
            $chartDataSelesai[] = $karyawan->selesai_count;
            $chartDataTerlewat[] = $karyawan->terlewat_count;
        }

        // Summary Cards
        $totalKaryawanAktif = $bebanKerjaRaw->count();
        $totalAntreanSelesaiNasional = $bebanKerjaRaw->sum('selesai_count');
        $avgBebanKerja = $totalKaryawanAktif > 0 ? round($totalAntreanSelesaiNasional / $totalKaryawanAktif) : 0;

        return view('administrator.data_beban_kerja', compact(
            'bebanKerjaRaw',
            'riwayatHarian',
            'lokets',
            'search',
            'activeYear',
            'chartLabels',
            'chartDataSelesai',
            'chartDataTerlewat',
            'totalKaryawanAktif',
            'totalAntreanSelesaiNasional',
            'avgBebanKerja'
        ));
    }

    public function cetak(Request $request)
    {
        $search = $request->input('q');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $year = $request->input('year');
        $id_loket = $request->input('id_loket');

        $activeYear = date('Y');
        $activeMonth = date('m');

        // Query Utama untuk Laporan Beban Kerja Karyawan
        $query = DB::table('antrian')
            ->join('profil_karyawan', 'antrian.id_karyawan', '=', 'profil_karyawan.id_user')
            ->leftJoin('loket', 'profil_karyawan.id_loket', '=', 'loket.id_loket')
            ->whereNotNull('antrian.id_karyawan');

        if ($search) {
            $query->where('profil_karyawan.nama_user', 'like', '%' . $search . '%');
        }
        if ($start_date) {
            $query->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $query->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $query->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            // Default: Tampilkan data bulan ini
            $query->whereYear('antrian.waktu_voice', $activeYear)
                  ->whereMonth('antrian.waktu_voice', $activeMonth);
        }
        if ($id_loket) {
            $query->where('profil_karyawan.id_loket', $id_loket);
        }

        $queryHarian = clone $query;

        // Aggregate Data per Karyawan
        $bebanKerjaRaw = $query->select(
                'profil_karyawan.nama_user',
                'loket.nama_loket',
                'loket.nama_pelayanan',
                DB::raw("COUNT(antrian.id_antrian) as total_ditangani"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'selesai' THEN 1 ELSE 0 END) as selesai_count"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'terlewat' THEN 1 ELSE 0 END) as terlewat_count"),
                DB::raw("AVG(TIME_TO_SEC(TIMEDIFF(antrian.waktu_selesai, antrian.waktu_panggil))) as avg_seconds")
            )
            ->groupBy('profil_karyawan.nama_user', 'loket.nama_loket', 'loket.nama_pelayanan')
            ->orderBy('selesai_count', 'desc')
            ->get();

        // Aggregate Data Riwayat Harian per Karyawan
        $riwayatHarian = $queryHarian->select(
                'profil_karyawan.nama_user',
                'loket.nama_loket',
                'loket.nama_pelayanan',
                DB::raw("DATE(antrian.waktu_voice) as tanggal_kerja"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'selesai' THEN 1 ELSE 0 END) as selesai_count"),
                DB::raw("SUM(CASE WHEN antrian.status_antrian = 'terlewat' THEN 1 ELSE 0 END) as terlewat_count"),
                DB::raw("AVG(TIME_TO_SEC(TIMEDIFF(antrian.waktu_selesai, antrian.waktu_panggil))) as avg_seconds")
            )
            ->groupBy('profil_karyawan.nama_user', 'loket.nama_loket', 'loket.nama_pelayanan', DB::raw("DATE(antrian.waktu_voice)"))
            ->orderBy('tanggal_kerja', 'desc')
            ->orderBy('selesai_count', 'desc')
            ->get();

        return view('pdf.laporan_beban_kerja', compact('bebanKerjaRaw', 'riwayatHarian', 'request'));
    }
}

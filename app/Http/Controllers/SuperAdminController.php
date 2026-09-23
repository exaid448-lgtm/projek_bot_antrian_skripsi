<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
   public function index()
        {
            $q = request('q'); // keyword pencarian

            // =======================
            // LOKET AKTIF + SEARCH
            // =======================
            $loketAktif = DB::table('loket')
                ->when($q, function ($query) use ($q) {
                    $query->where('nama_loket', 'like', "%$q%");
                })
                ->orderBy('nama_loket')
                ->get();

            // =======================
            // LAYANAN LOKET
            // =======================
            $layananBuka = DB::table('loket')
                ->where('status_pelayanan', 'BUKA')
                ->count();

            $layananTutup = DB::table('loket')
                ->where('status_pelayanan', 'TUTUP')
                ->count();

            // =======================
            // KARYAWAN LOGIN
            // =======================
            $karyawanStatus = DB::table('profil_karyawan')
                ->join('user', 'profil_karyawan.id_user', '=', 'user.id_user')
                ->leftJoin('loket', 'profil_karyawan.id_loket', '=', 'loket.id_loket')
                ->where('user.kategori', 'admin_loket')
                ->select(
                    'profil_karyawan.nama_user',
                    'profil_karyawan.img_user',
                    'loket.nama_loket',
                    'user.status_login',
                    'user.last_seen', // Ambil kolom ini
                    'user.id_user'
                )
                ->get()
                ->map(function ($user) {
                    // Logika: Jika last_seen lebih dari 2 menit yang lalu, paksa jadi offline
                    $isOnline = false;
                    if ($user->last_seen) {
                        $isOnline = \Carbon\Carbon::parse($user->last_seen)->diffInMinutes(now()) < 2;
                    }
                    
                    $user->status_display = $isOnline ? 'online' : 'offline';
                    return $user;
                });

                // Hitung total karyawan online berdasarkan status_display
                $totalOnline = $karyawanStatus->filter(function ($user) {
                    return isset($user->status_display) && $user->status_display === 'online';
                })->count();

                return view('administrator.Dashbord_admininistrator', [
                    'loketAktif'         => $loketAktif,
                    'layananBuka'        => $layananBuka,
                    'layananTutup'       => $layananTutup,
                    'karyawanLogin'      => $karyawanStatus,
                    'totalKaryawanLogin' => $totalOnline,
                ]);
        }

}

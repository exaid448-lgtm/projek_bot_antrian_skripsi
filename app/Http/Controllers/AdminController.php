<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Ambil data identitas dari Session yang dibuat saat login
        $id_user = session('id_user');
        $role    = session('role');
        $id_loket = session('id_loket');

        // 2. Ambil data profil terbaru (termasuk foto profil img_user)
        $user_profil = DB::table('profil_karyawan')
            ->where('id_user', $id_user)
            ->first();

        // Simpan nama foto ke session agar bisa dipanggil di blade dengan session('foto')
        if ($user_profil) {
            session(['foto' => $user_profil->img_user]);
        }

        // 3. Logika Filter Data & Hitung Antrian
        if ($role == 'admin_loket') {
            // Jika Super Admin, ambil semua data tanpa filter
            $data_konsul = DB::table('konsul')
                ->orderBy('tanggal_konsul', 'desc')
                ->get();

            $total_antrian = DB::table('konsul')->count();
        } else {
            // Jika admin_loket, hanya ambil data yang id_loket-nya sama dengan user tersebut
            $data_konsul = DB::table('konsul')
                ->where('id_loket', $id_loket)
                ->orderBy('tanggal_konsul', 'desc')
                ->get();

            $total_antrian = DB::table('konsul')
                ->where('id_loket', $id_loket)
                ->count();
        }

        // 4. Kirim data ke view home_loket.blade.php
        return view('admin_loket.home_loket', [
            'data_konsul'   => $data_konsul,
            'total_antrian' => $total_antrian,
            'profil'        => $user_profil
        ]);
    }
}
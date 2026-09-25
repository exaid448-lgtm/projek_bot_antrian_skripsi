<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class CekLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 0. IZINKAN AKSES TANPA CEK LOGIN (PENTING!)
        // Jika sedang buka halaman login atau register, langsung izinkan (skip pengecekan session)
        if ($request->is('pengunjung') || 
            $request->is('login-proses-pengunjung') || 
            $request->is('register*') || 
            $request->is('login') || 
            $request->is('login/proses') || 
            $request->is('reset-password*') || 
            $request->is('/')) {
            return $next($request);
        }

        // 1. CEK APAKAH USER SUDAH LOGIN
        if (!session()->has('is_login') || session('is_login') !== true) {
            
            // Jika mau ke area pengunjung tapi belum login
            if (
                $request->is('pengunjung/*') || 
                $request->is('dashboard-pengunjung*') ||
                $request->is('riwayat-antrian*') ||
                $request->is('booking-antrian*') ||
                $request->is('konsul*') ||
                $request->is('chat-konsultasi*') ||
                $request->is('catatan_konsul*') ||
                $request->is('profil*') ||
                $request->is('syarat-layanan*') ||
                $request->is('pusat-informasi*') ||
                $request->is('status-antrian*') ||
                $request->is('monitor-antrian*') ||
                $request->is('cek-status-skm*') ||
                $request->is('simpan-skm*')
            ) {
                return redirect()->guest(route('login_pengunjung'))->with('error', 'Silakan login terlebih dahulu untuk mengakses layanan ini.');
            }

            // Jika mau ke area pegawai tapi belum login
            if (
                $request->is('dashboard') || 
                $request->is('dashboard/*') || 
                $request->is('admin*') || 
                $request->is('super*') ||
                $request->is('loket*') ||
                $request->is('data-*') ||
                $request->is('laporan-skm*') ||
                $request->is('algoritma*') ||
                $request->is('chat_loket*')
            ) {
                return redirect()->route('login')->with('error', 'Sesi pegawai berakhir.');
            }

            return redirect()->guest(route('login_pengunjung'))->with('error', 'Silakan login terlebih dahulu untuk mengakses layanan ini.'); 
        }

        // 2. LOGIKA UPDATE STATUS (Jika sudah login)
        if (session()->has('id_user')) {
            DB::table('user')
                ->where('id_user', session('id_user'))
                ->update([
                    'last_seen' => now(),
                    'status_login' => 'online'
                ]);
        }

        $response = $next($request);

        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }
}
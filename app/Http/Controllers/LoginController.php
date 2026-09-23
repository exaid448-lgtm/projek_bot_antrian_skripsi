<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function proses(Request $request)
    {
        $user = DB::table('user')->where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->kategori === 'pengunjung') {
            return back()->with('error', 'Username atau password salah');
        }

        // UPDATE STATUS
        DB::table('user')->where('id_user', $user->id_user)->update(['status_login' => 'online']);

        // SET SESSION DASAR
        $request->session()->put([
            'is_login' => true,
            'id_user'  => $user->id_user,
            'role'     => $user->kategori,
        ]);

        // ================== LOGIKA ADMIN LOKET ==================
        if ($user->kategori === 'admin_loket') {
            $profil = DB::table('profil_karyawan')->where('id_user', $user->id_user)->first();
            if ($profil) {
                $request->session()->put([
                    'id_profil' => $profil->id_profil,
                    'id_loket'  => $profil->id_loket,
                    'nama'      => $profil->nama_user,
                ]);

                DB::table('login_log')->insert([
                    'id_user'  => $user->id_user,
                    'role'     => 'admin_loket',
                    'id_loket' => $profil->id_loket,
                    'login_at' => now(),
                    'status'   => 'login'
                ]);
            }
            $request->session()->save(); // PENTING
            return redirect()->route('dashboard');
        }

        // ================== LOGIKA SUPER ADMIN ==================
        // Bagian Super Admin di LoginController.php
        if ($user->kategori === 'administrator') {
            $profil = DB::table('profil_karyawan')->where('id_user', $user->id_user)->first();
            $nama = $profil ? $profil->nama_user : 'Administrator';
            $foto = ($profil && $profil->img_user) ? $profil->img_user : 'default-user.png';

            $request->session()->put('nama', $nama);
            $request->session()->put('foto', $foto);
            $request->session()->put('id_user', $user->id_user);
            $request->session()->put('role', 'administrator');
            $request->session()->put('is_login', true);

            // KUNCI: Wajib save sebelum redirect agar tidak mental
            $request->session()->save(); 
            
            return redirect()->route('super.dashboard');
        }

        return redirect('/login');
    }

    public function logout(Request $request)
    {
        $id = session('id_user');
        if ($id) {
            DB::table('user')->where('id_user', $id)->update(['status_login' => 'offline']);
            DB::table('login_log')
                ->where('id_user', $id)
                ->where('status', 'login')
                ->whereNull('logout_at')
                ->update(['logout_at' => now(), 'status' => 'logout']);
        }

        $request->session()->flush();
        $request->session()->save();
        return redirect('/login_karyawan');
    }
}
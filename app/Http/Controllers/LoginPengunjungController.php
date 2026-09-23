<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginPengunjungController extends Controller
{
    public function index()
    {
        return view('pengunjung.login_pengunjung'); // Sesuaikan dengan nama file blade login kamu
    }

    public function loginProses(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password) && $user->kategori === 'pengunjung') {
            
            // 1. Login secara Auth Laravel (untuk Pengunjung)
            Auth::login($user);

            // 2. WAJIB: Isi session agar tidak dianggap 'belum login' oleh middleware pegawai
            // Sesuaikan dengan yang dicek di file CekLogin.php
            session([
                'is_login' => true,
                'id_user'  => $user->id_user,
                'role'     => 'pengunjung', // Tambahkan role agar RoleMiddleware tidak error
                'username' => $user->username
            ]);

            $user->update([
                'status_login' => 'online',
                'last_seen'    => now(),
            ]);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.pengunjung'));
        }

        return back()->with('error', 'Username atau password salah.');
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */ // Tambahkan baris ini
        if ($user) {
            $user->update([
                'status_login' => 'offline',
                'last_seen'    => now(),
            ]);
        }

        // 1. Logout dari Auth Laravel
        Auth::logout();

        // 2. Hapus SEMUA data session (termasuk is_login, id_user, dll)
        $request->session()->flush();

        // 3. Batalkan sesi yang ada dan generate token baru untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 4. Redirect ke route login pengunjung (gunakan nama route agar konsisten)
        return redirect()->route('login_pengunjung')->with('success', 'Berhasil logout.');
    }
}
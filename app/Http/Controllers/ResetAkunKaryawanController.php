<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetAkunKaryawanController extends Controller
{
    public function index($token)
    {
        return view('administrator.riset_akun_loket_karyawan', compact('token'));
    }

    public function proses(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:profil_karyawan,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.exists' => 'Email ini tidak terdaftar sebagai karyawan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord) {
            return redirect()->route('login')->with('error', 'Token reset tidak valid atau tidak ditemukan untuk email ini.');
        }

        if (!Hash::check($request->token, $resetRecord->token)) {
            return redirect()->route('login')->with('error', 'Token reset tidak valid atau salah.');
        }

        // Cek kedaluwarsa (24 jam)
        if (now()->diffInHours($resetRecord->created_at) > 24) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('login')->with('error', 'Tautan reset sudah kedaluwarsa.');
        }

        try {
            $profil = \App\Models\Profil::where('email', $request->email)->first();
            $user = User::find($profil->id_user);

            $user->password = Hash::make($request->password);
            
            // Opsional ganti username jika diisi
            if ($request->filled('username')) {
                // cek apakah username unik
                $exists = User::where('username', $request->username)->where('id_user', '!=', $user->id_user)->exists();
                if ($exists) {
                    return redirect()->back()->withInput()->with('error', 'Username sudah digunakan oleh akun lain.');
                }
                $user->username = $request->username;
            }

            $user->save();

            // Hapus token setelah sukses
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('login')->with('success', 'Kredensial berhasil diubah! Silakan login dengan password baru.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilPengunjung;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Riset_akunController extends Controller
{
    /**
     * Mengirimkan link reset password ke email pengunjung.
     */
    public function sendResetLink(Request $request)
    {
        // 1. Validasi Input Email
        $request->validate([
            'email' => 'required|email',
        ]);

        // 2. Cari email di profil pengunjung
        $profil = ProfilPengunjung::where('email', $request->email)->first();

        if (!$profil) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat email tidak terdaftar di sistem kami.'
            ], 404);
        }

        $email = $request->email;
        $token = Str::random(60);

        // 3. Simpan token ke database (tabel password_reset_tokens)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // 4. Generate URL reset password
        $link = route('password.reset', ['token' => $token, 'email' => $email]);

        // 5. Kirim email
        try {
            Mail::send('emails.reset_password', ['link' => $link], function($message) use ($email) {
                $message->to($email);
                $message->subject('Atur Ulang Password - MPP Kota Banjarbaru');
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email. Silakan hubungi admin atau periksa konfigurasi mail di file .env Anda. Detail Error: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Link atur ulang password telah dikirim ke email Anda! Silakan periksa kotak masuk (inbox) atau spam.'
        ], 200);
    }

    /**
     * Menampilkan form reset password (riset_akun).
     */
    public function showResetForm(Request $request, $token)
    {
        $profil = ProfilPengunjung::where('email', $request->email)->first();
        $namaPengguna = $profil ? $profil->nama : 'Pengguna';

        return view('pengunjung.riset_akun', [
            'token' => $token,
            'email' => $request->email,
            'namaPengguna' => $namaPengguna
        ]);
    }

    /**
     * Memproses penggantian password baru.
     */
    public function resetPassword(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru harus diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        // 2. Cek token di database
        $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$reset) {
            return back()->with('error', 'Token atur ulang password tidak valid atau email salah. Silakan coba kirim ulang link reset.');
        }

        // 3. Cek kedaluwarsa token (60 menit)
        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Link atur ulang password telah kedaluwarsa. Silakan ajukan ulang.');
        }

        // 4. Cek kecocokan token
        if (!Hash::check($request->token, $reset->token)) {
            return back()->with('error', 'Token tidak valid. Silakan ajukan ulang.');
        }

        // 5. Update Password User
        $profil = ProfilPengunjung::where('email', $request->email)->first();
        if (!$profil) {
            return back()->with('error', 'Pengunjung dengan email ini tidak ditemukan.');
        }

        $user = User::find($profil->id_user);
        if (!$user) {
            return back()->with('error', 'Akun pengguna tidak ditemukan.');
        }

        // Simpan password baru
        $user->password = Hash::make($request->password);
        $user->save();

        // 6. Hapus token dari tabel reset password
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login_pengunjung')->with('success', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
    }
}

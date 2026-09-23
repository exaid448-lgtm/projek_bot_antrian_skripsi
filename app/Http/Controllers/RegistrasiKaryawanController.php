<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profil;

class RegistrasiKaryawanController extends Controller
{
    public function index($token)
    {
        $invitasi = DB::table('invitasi_karyawan')->where('token', $token)->first();

        if (!$invitasi) {
            return redirect()->route('login')->with('error', 'Tautan registrasi tidak valid atau tidak ditemukan.');
        }

        if ($invitasi->status !== 'pending') {
            return redirect()->route('login')->with('error', 'Tautan registrasi ini sudah pernah digunakan.');
        }

        if (now()->greaterThan($invitasi->expires_at)) {
            return redirect()->route('login')->with('error', 'Tautan registrasi sudah kedaluwarsa.');
        }

        return view('administrator.registrasi_akun_karyawan', compact('invitasi'));
    }

    public function proses(Request $request)
    {
        $rules = [
            'token' => 'required',
            'email' => 'required|email|unique:profil_karyawan,email',
            'username' => 'required|unique:user,username',
            'password' => 'required|min:8|confirmed',
            'nama_user' => 'required',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tanggal_lahir' => 'required|date'
        ];

        $messages = [
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ];

        if ($request->hasFile('foto')) {
            $rules['foto'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120';
            $messages['foto.image'] = 'File yang diunggah harus berupa gambar!';
            $messages['foto.mimes'] = 'Format gambar harus jpeg, png, jpg, gif, svg, atau webp!';
            $messages['foto.max'] = 'Ukuran gambar maksimal 5MB!';
        }

        $request->validate($rules, $messages);

        $invitasi = DB::table('invitasi_karyawan')->where('token', $request->token)->first();

        if (!$invitasi || $invitasi->status !== 'pending') {
            return redirect()->route('login')->with('error', 'Tautan registrasi tidak valid atau sudah digunakan.');
        }

        DB::beginTransaction();
        try {
            // 1. Buat User
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'kategori' => 'admin_loket'
            ]);

            // 2. Upload Foto
            $fileName = 'default-user.png';
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('img/foto_karyawan'), $fileName);
            }

            // 3. Buat Profil Karyawan
            Profil::create([
                'id_user' => $user->id_user,
                'nama_user' => $request->nama_user,
                'email' => $request->email,
                'id_loket' => $invitasi->id_loket,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'img_user' => $fileName,
                'status_devisi' => 'Aktif'
            ]);

            // 4. Update status invitasi
            DB::table('invitasi_karyawan')
                ->where('id', $invitasi->id)
                ->update([
                    'status' => 'registered',
                    'updated_at' => now()
                ]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login menggunakan Username dan Password yang baru Anda buat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}

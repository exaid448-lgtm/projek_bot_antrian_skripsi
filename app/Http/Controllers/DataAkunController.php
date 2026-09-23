<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profil;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\InvitasiKaryawanMail;

class DataAkunController extends Controller
{
    public function index()
    {
       $users = User::with(['profil.loket'])
                ->where('kategori', 'admin_loket') 
                ->get();
        $data_loket = Loket::all();
        return view('administrator.data_akun_karyawan', compact('users', 'data_loket'));
    }

public function store(Request $request)
{
    // 1. Validasi dengan pesan custom agar tahu bagian mana yang salah
    $rules = [
        'username'      => 'required|unique:user,username',
        'email'         => 'required|email|unique:profil_karyawan,email', 
        'password'      => 'required|min:8',
        'name'          => 'required',
        'id_loket'      => 'required',
        'jenis_kelamin' => 'required',
        'tanggal_lahir' => 'required|date'
    ];

    $messages = [];

    if ($request->hasFile('foto')) {
        $rules['foto'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120';
        $messages['foto.image'] = 'File yang diunggah harus berupa gambar!';
        $messages['foto.mimes'] = 'Format gambar harus jpeg, png, jpg, gif, svg, atau webp!';
        $messages['foto.max'] = 'Ukuran gambar maksimal 5MB!';
    }

    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator) // Kirim detail error ke view
            ->withInput()
            ->with('error', 'Validasi Gagal: ' . $validator->errors()->first());
    }

    DB::beginTransaction();
    try {
        // 2. Simpan User
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'kategori' => 'admin_loket'
        ]);

        $fileName = 'default-user.png';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/foto_karyawan'), $fileName);
        }

        // 3. Simpan Profil
        Profil::create([
            'id_user'       => $user->id_user,
            'nama_user'     => $request->name,
            'email'         => $request->email,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_loket'      => $request->id_loket,
            'img_user'      => $fileName,
            'status_devisi' => 'penjaga loket',
        ]);

        DB::commit();
        return redirect()->back()->with('success', 'Akun Berhasil Dibuat');

    } catch (\Exception $e) {
        DB::rollback();
        // DEBUG: Tulis error ke log agar bisa dicek di folder storage/logs
        Log::error('Gagal Simpan User: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal Database: ' . $e->getMessage());
    }
}

    public function undangKaryawan(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:profil_karyawan,email',
            'id_loket' => 'required'
        ], [
            'email.unique' => 'Email ini sudah terdaftar sebagai karyawan.'
        ]);

        try {
            $token = Str::random(60);
            
            DB::table('invitasi_karyawan')->where('email', $request->email)->delete();

            $invitasiId = DB::table('invitasi_karyawan')->insertGetId([
                'email' => $request->email,
                'id_loket' => $request->id_loket,
                'token' => $token,
                'expires_at' => now()->addDays(3),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $invitasi = DB::table('invitasi_karyawan')->where('id', $invitasiId)->first();
            $loket = Loket::find($request->id_loket);

            Mail::to($request->email)->send(new InvitasiKaryawanMail($invitasi, $loket));

            return redirect()->back()->with('success', 'Undangan registrasi berhasil dikirim ke ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Error Undang Karyawan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengirim undangan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $profil = Profil::where('id_user', $id)->firstOrFail();

            // Update Foto
            if ($request->hasFile('foto')) {
                if($profil->img_user && File::exists(public_path('img/foto_karyawan/'.$profil->img_user))) {
                    File::delete(public_path('img/foto_karyawan/'.$profil->img_user));
                }
                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('img/foto_karyawan'), $fileName);
                $profil->img_user = $fileName;
            }

            // Update Profil
            $profil->update([
                'nama_user'     => $request->name,
                'email'         => $request->email, // Pastikan input name di form adalah 'email'
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'id_loket'      => $request->id_loket,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Berhasil Diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function sendResetLink($id)
    {
        try {
            $user = User::with('profil')->findOrFail($id);
            
            if (!$user->profil || !$user->profil->email) {
                return redirect()->back()->with('error', 'Karyawan ini tidak memiliki alamat email yang valid.');
            }

            $email = $user->profil->email;
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            Mail::to($email)->send(new \App\Mail\ResetAkunMail($user, $token));

            return redirect()->back()->with('success', 'Email tautan reset akun berhasil dikirim ke ' . $email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email reset: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $profil = Profil::where('id_user', $id)->first();
            
            if($profil && $profil->img_user && File::exists(public_path('img/foto_karyawan/'.$profil->img_user))) {
                File::delete(public_path('img/foto_karyawan/'.$profil->img_user));
            }

            $user->delete(); 
            DB::commit();
            return redirect()->back()->with('success', 'Akun Berhasil Dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal Hapus: ' . $e->getMessage());
        }
    }
}
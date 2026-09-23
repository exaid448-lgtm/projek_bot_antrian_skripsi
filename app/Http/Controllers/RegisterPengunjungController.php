<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\ProfilPengunjung; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator;

class RegisterPengunjungController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input (Tambahkan jenis_kelamin)
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:125',
            'email'         => 'required|email|unique:profil_pengunjung,email',
            'no_wa'         => 'required|string|max:125',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:laki-laki,wanita', // <-- Tambahkan validasi ini
            'username'      => 'required|string|unique:user,username|max:255',
            'password'      => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // SIMPAN KE TABEL 'user'
            $user = User::create([
                'username'     => $request->username,
                'password'     => Hash::make($request->password), 
                'kategori'     => 'pengunjung',
                'status_login' => 'offline',
                'last_seen'    => now(),
            ]);

            // SIMPAN KE TABEL 'profil_pengunjung' (Tambahkan jenis_kelamin)
            ProfilPengunjung::create([
                'nama'           => $request->nama,
                'email'          => $request->email,
                'nomor_whatsapp' => $request->no_wa,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'jenis_kelamin'  => $request->jenis_kelamin, // <-- Masukkan ke database di sini
                'id_user'        => $user->id_user, 
            ]);

            DB::commit(); 
            
            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi Berhasil! Silahkan login.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendaftar: ' . $e->getMessage()
            ], 500);
        }
    }
}
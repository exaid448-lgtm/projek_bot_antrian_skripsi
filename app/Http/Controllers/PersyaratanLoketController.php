<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersyaratanLoketController extends Controller
{
    public function index()
    {
        // 1. Cek status login sesuai custom middleware Anda
        if (!session()->has('is_login') || session('is_login') !== true) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Ambil id_user dari session
        $userId = session('id_user') ?? session('id_akun') ?? session('id');

        // 3. Tarik data id_loket dengan melakukan JOIN ke tabel profil_karyawan
        $userProfil = DB::table('user')
            ->join('profil_karyawan', 'user.id_user', '=', 'profil_karyawan.id_user')
            ->where('user.id_user', $userId)
            ->select('profil_karyawan.id_loket')
            ->first();

        // Jika data profil atau id_loket tidak ditemukan
        if (!$userProfil || !$userProfil->id_loket) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak memiliki hak akses id_loket di tabel profil_karyawan.');
        }

        $id_loket = $userProfil->id_loket;

        // 4. Ambil data dari tabel syarat berdasarkan id_loket pegawai
        $data_syarat = DB::table('syarat')
            ->where('id_loket', $id_loket)
            ->get();

        // 5. Lempar data_syarat ke view blade
        return view('admin_loket.persyaratan_loket', compact('data_syarat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_syarat' => 'required|string|max:255',
            'tipe_syarat' => 'required|in:dokumen,prosedur',
            'keterangan'  => 'required|string',
        ]);

        $userId = session('id_user') ?? session('id_akun');
        
        // Ambil id_loket dari tabel profil_karyawan
        $userProfil = DB::table('profil_karyawan')->where('id_user', $userId)->first();
        $id_loket = $userProfil ? $userProfil->id_loket : null;

        DB::table('syarat')->insert([
            'id_loket'    => $id_loket,
            'nama_syarat' => $request->nama_syarat,
            'tipe_syarat' => $request->tipe_syarat,
            'keterangan'  => $request->keterangan,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->back()->with('success', 'Persyaratan baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_syarat' => 'required|string|max:255',
            'tipe_syarat' => 'required|in:dokumen,prosedur',
            'keterangan'  => 'required|string',
        ]);

        $userId = session('id_user') ?? session('id_akun');
        $userProfil = DB::table('profil_karyawan')->where('id_user', $userId)->first();
        $id_loket = $userProfil ? $userProfil->id_loket : null;

        DB::table('syarat')
            ->where('id_syarat', $id)
            ->where('id_loket', $id_loket)
            ->update([
                'nama_syarat' => $request->nama_syarat,
                'tipe_syarat' => $request->tipe_syarat,
                'keterangan'  => $request->keterangan,
                'updated_at'  => now(),
            ]);

        return redirect()->back()->with('success', 'Persyaratan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $userId = session('id_user') ?? session('id_akun');
        $userProfil = DB::table('profil_karyawan')->where('id_user', $userId)->first();
        $id_loket = $userProfil ? $userProfil->id_loket : null;

        DB::table('syarat')
            ->where('id_syarat', $id)
            ->where('id_loket', $id_loket)
            ->delete();

        return redirect()->back()->with('success', 'Persyaratan berhasil dihapus!');
    }
}
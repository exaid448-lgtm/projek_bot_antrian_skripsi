<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformasiLoketController extends Controller
{
    // Menampilkan halaman pemberitahuan kendala loket
    public function index()
    {
        if (!session()->has('is_login') || session('is_login') !== true) {
            return redirect('/login_karyawan')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('id_user');

        // Ambil data loket milik pegawai dari tabel profil_karyawan
        $userProfil = DB::table('profil_karyawan')
            ->where('id_user', $userId)
            ->first();

        if (!$userProfil || !$userProfil->id_loket) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum dikaitkan dengan loket mana pun.');
        }

        $id_loket = $userProfil->id_loket;

        // Ambil nama loket untuk ditampilkan di header
        $loket = DB::table('loket')->where('id_loket', $id_loket)->first();
        $nama_loket = $loket->nama_loket ?? 'Loket Anda';

        // Ambil seluruh data informasi kendala dari loket ini
        $informasi = DB::table('informasi')
            ->where('id_loket', $id_loket)
            ->orderBy('id_informasi', 'desc')
            ->get();

        return view('admin_loket.pemberitahuan_loket_bermasalah', compact('informasi', 'nama_loket', 'id_loket'));
    }

    // Menyimpan pemberitahuan kendala baru
    public function store(Request $request)
    {
        $request->validate([
            'judul_info'     => 'required|string|max:255',
            'kategori_info'  => 'required|in:critical,warning,normal',
            'status_info'    => 'required|in:aktif,arsip',
            'deskripsi_info' => 'required|string',
            'solusi_info'    => 'required|string',
        ]);

        $userId = session('id_user');
        $userProfil = DB::table('profil_karyawan')->where('id_user', $userId)->first();

        DB::table('informasi')->insert([
            'id_loket'       => $userProfil->id_loket,
            'judul_info'     => $request->judul_info,
            'deskripsi_info' => $request->deskripsi_info,
            'solusi_info'    => $request->solusi_info,
            'kategori_info'  => $request->kategori_info,
            'status_info'    => $request->status_info,
            'tanggal_info'   => now()->toDateString(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->back()->with('success', 'Pemberitahuan kendala berhasil ditambahkan!');
    }

    // Memperbarui data pemberitahuan kendala
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_info'     => 'required|string|max:255',
            'kategori_info'  => 'required|in:critical,warning,normal',
            'status_info'    => 'required|in:aktif,arsip',
            'deskripsi_info' => 'required|string',
            'solusi_info'    => 'required|string',
        ]);

        $userId = session('id_user');
        $userProfil = DB::table('profil_karyawan')->where('id_user', $userId)->first();

        DB::table('informasi')
            ->where('id_informasi', $id)
            ->where('id_loket', $userProfil->id_loket)
            ->update([
                'judul_info'     => $request->judul_info,
                'deskripsi_info' => $request->deskripsi_info,
                'solusi_info'    => $request->solusi_info,
                'kategori_info'  => $request->kategori_info,
                'status_info'    => $request->status_info,
                'tanggal_info'   => now()->toDateString(),
                'updated_at'     => now(),
            ]);

        return redirect()->back()->with('success', 'Pemberitahuan kendala berhasil diperbarui!');
    }

    // Menghapus data pemberitahuan kendala
    public function destroy($id)
    {
        $userId = session('id_user');
        $userProfil = DB::table('profil_karyawan')->where('id_user', $userId)->first();

        DB::table('informasi')
            ->where('id_informasi', $id)
            ->where('id_loket', $userProfil->id_loket)
            ->delete();

        return redirect()->back()->with('success', 'Pemberitahuan berhasil dihapus secara permanen!');
    }
}
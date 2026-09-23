<?php

namespace App\Http\Controllers;

use App\Models\Algoritma;
use App\Models\User;
use Illuminate\Http\Request;

class AlgoritmaController extends Controller
{
    // Ambil data user dari session manual
    private function getAuthenticatedUser() {
        $userId = session('id_user');
        return User::with('profil')->find($userId);
    }

    public function index() {
        $user = $this->getAuthenticatedUser();
        if (!$user) return redirect()->route('login');

        $id_loket = optional($user->profil)->id_loket;
        
        $algoritmas = Algoritma::where('id_loket', $id_loket)
            ->orderBy('tanggal_dan_waktu', 'desc')
            ->get();

        return view('admin_loket.data_algoritma', compact('algoritmas', 'user'));
    }

 public function store(Request $request)
{
    $request->validate([
        'algoritma' => 'required|string|max:255',
        'tanggal_dan_waktu' => 'required',
        'tipe_layanan' => 'required',
    ]);

    // Ambil user dari session manual
    $userId = session('id_user');
    $user = \App\Models\User::with('profil')->find($userId);

    // Validasi apakah profil ada
    if (!$user || !$user->profil) {
        return back()->with('error', 'Gagal: Profil loket tidak ditemukan.');
    }

    // Eksekusi simpan
    \App\Models\Algoritma::create([
        'id_loket'          => $user->profil->id_loket,
        'algoritma'         => $request->algoritma,
        'tanggal_dan_waktu' => $request->tanggal_dan_waktu,
        'tipe_layanan'      => $request->tipe_layanan,
    ]);

    return redirect()->route('algoritma.index')->with('success', 'Data berhasil ditambahkan!');
}

    public function update(Request $request, $id) {
        $user = $this->getAuthenticatedUser();
        $id_loket = $user->profil->id_loket;

        $algoritma = Algoritma::where('id_algoritma', $id)
            ->where('id_loket', $id_loket)
            ->firstOrFail();

        $algoritma->update($request->all());
        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id) {
        $user = $this->getAuthenticatedUser();
        $id_loket = $user->profil->id_loket;

        Algoritma::where('id_algoritma', $id)
            ->where('id_loket', $id_loket)
            ->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}
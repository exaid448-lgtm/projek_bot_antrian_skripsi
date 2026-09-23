<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loket;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DataLoketController extends Controller
{
    // --- TAMBAHKAN FUNGSI INI ---
    public function index()
    {
        // Mengambil semua data dari tabel loket
        $data_loket = Loket::all(); 
        // Mengirim data ke view data_loket.blade.php
        return view('administrator.data_loket', compact('data_loket'));
    }

    public function store(Request $request)
    {
        $loket = new Loket();
        $loket->nama_loket = $request->input('nama_loket');
        $loket->nama_pelayanan = $request->input('nama_pelayanan');
        $loket->lokasi_loket = $request->input('lokasi');
        $loket->prefix = $request->input('prefix');
        $loket->status_pelayanan = 'BUKA';
        $loket->waktu_terakhir = '00:00:00';
        $loket->tanggal = date('Y-m-d');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = $file->getClientOriginalName(); 
            
            $file->move(public_path('img/logo_loket'), $fileName);
            $loket->logo = $fileName; 
        } else {
            $loket->logo = 'logo_placeholder.png'; // Atau file default lain
        }

        $loket->save();
        return redirect()->back()->with('success', 'Loket Berhasil Dibuat');
    }

    public function update(Request $request, $id)
    {
        $loket = Loket::findOrFail($id);
        $loket->nama_loket = $request->input('nama_loket');
        $loket->nama_pelayanan = $request->input('nama_pelayanan');
        $loket->lokasi_loket = $request->input('lokasi');
        $loket->prefix = $request->input('prefix');

        if ($request->hasFile('logo')) {
            if($loket->logo && File::exists(public_path('img/'.$loket->logo))) {
                File::delete(public_path('img/'.$loket->logo));
            }
            if($loket->logo && File::exists(public_path('img/logo_loket/'.$loket->logo))) {
                File::delete(public_path('img/logo_loket/'.$loket->logo));
            }

            $file = $request->file('logo');
            $fileName = $file->getClientOriginalName();
            
            $file->move(public_path('img/logo_loket'), $fileName);
            $loket->logo = 'logo_loket/' . $fileName;
        }

        $loket->save();
        return redirect()->back()->with('success', 'Data Loket Diperbarui');
    }
    public function destroy($id)
    {
        $loket = Loket::findOrFail($id);
        // Hapus file logo jika ada
        if($loket->logo && File::exists(public_path('img/'.$loket->logo))) {
            File::delete(public_path('img/'.$loket->logo));
        }
        $loket->delete();
        return redirect()->back()->with('success', 'Loket Berhasil Dihapus');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\Loket;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua data loket untuk sidebar filter (distinct berdasar nama_loket)
        $lokets = \App\Models\Loket::select('nama_loket')->distinct()->get(); 

        // 2. Logika filter informasi berdasarkan request 'loket'
        $query = \App\Models\Informasi::with('loket');

        if ($request->has('loket')) {
            $query->whereHas('loket', function($q) use ($request) {
                $q->where('nama_loket', $request->loket);
            });
        }

        $informasi = $query->get();

        // 3. Kirim KEDUA variabel ke view
        return view('pengunjung.pusat_informasi', compact('informasi', 'lokets'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Loket;
use Illuminate\Http\Request;

class InformasiSyaratController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data loket BESERTA relasi syaratnya
        // Pastikan nama relasi di model Loket adalah 'syarat'
        $data_syarat = Loket::with(['syarat'])->get();

        // Debugging: Jika Anda ingin memastikan data ada di sisi server, 
        // aktifkan baris di bawah ini untuk melihat datanya di layar.
        // dd($data_syarat); 

        return view('pengunjung.syarat_layanan', compact('data_syarat'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Profil;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DataAntrianController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        if (Session::get('role') === 'administrator') {
            $query = Antrian::with('loket');
            
            // Filter Loket specifically for Administrator
            if ($request->filled('id_loket')) {
                $query->where('id_loket', $request->id_loket);
            }
        } else {
            $id_profil = Session::get('id_profil');
            $profil = Profil::where('id_profil', $id_profil)->first();
            $id_loket = $profil ? $profil->id_loket : null;

            $query = Antrian::where('id_loket', $id_loket);
        }

        // 🔍 Filter Nomor Antrian
        if ($request->filled('q')) {
            $query->where('nomor_antrian', 'like', '%'.$request->q.'%');
        }

        // 📊 Filter Status
        if ($request->filled('status')) {
            if ($request->status == 'selesai') {
                $query->whereNotNull('waktu_selesai')
                      ->where('status_antrian', '!=', 'batal');
            } elseif ($request->status == 'dipanggil') {
                $query->whereNotNull('waktu_panggil')
                      ->whereNull('waktu_selesai')
                      ->where('status_antrian', '!=', 'batal');
            } elseif ($request->status == 'menunggu') {
                $query->whereNull('waktu_panggil')
                      ->whereNull('waktu_selesai')
                      ->where('status_antrian', '!=', 'batal');
            } elseif ($request->status == 'batal') {
                $query->where('status_antrian', 'batal');
            }
        }

        // 📅 Filter Range Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_voice', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_voice', '<=', $request->end_date);
        }

        return $query;
    }

    public function index(Request $request)
    {
        if (! Session::has('is_login')) {
            return redirect()->route('login');
        }

        $query = $this->getFilteredQuery($request);
        $antrian = $query->orderBy('waktu_voice', 'desc')->get();

        if (Session::get('role') === 'administrator') {
            $loketList = Loket::all();
            return view('administrator.data_antrian_mpp', compact('antrian', 'loketList'));
        }

        return view('admin_loket.data_antrian_loket', compact('antrian'));
    }

    public function cetak(Request $request)
    {
        if (! Session::has('is_login')) {
            return redirect()->route('login');
        }

        $query = $this->getFilteredQuery($request);
        $antrian = $query->orderBy('waktu_voice', 'asc')->get();

        // Mengambil info profil untuk judul laporan
        $profil = null;
        if (Session::get('role') !== 'administrator') {
            $id_profil = Session::get('id_profil');
            $profil = Profil::with('loket')->where('id_profil', $id_profil)->first();
        } else {
            // For administrator, if a specific loket is selected, we can fetch its info or pass a mock profil
            if ($request->filled('id_loket')) {
                $loket = Loket::find($request->id_loket);
                if ($loket) {
                    $profil = new \stdClass();
                    $profil->nama_user = 'Administrator';
                    $profil->id_user = Session::get('id_user');
                    $profil->loket = $loket;
                }
            }
        }

        return view('pdf.laporan_antrian', compact('antrian', 'profil', 'request'));
    }
}

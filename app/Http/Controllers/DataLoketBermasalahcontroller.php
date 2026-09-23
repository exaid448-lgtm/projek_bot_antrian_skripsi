<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Informasi;
use App\Models\Loket;
use App\Models\Profil;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataLoketBermasalahcontroller extends Controller
{
    // Menampilkan halaman utama manajemen kendala loket
    public function index(Request $request)
    {
        $search = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_loket = $request->input('id_loket');
        $kategori_info = $request->input('kategori_info');
        $status_info = $request->input('status_info');

        // Ambil daftar loket untuk dropdown filter/input
        $lokets = Loket::orderBy('nama_loket', 'asc')->get();

        // Query data kendala
        $query = Informasi::with('loket');

        if ($start_date) {
            $query->whereDate('tanggal_info', '>=', $start_date);
        }
        if ($end_date) {
            $query->whereDate('tanggal_info', '<=', $end_date);
        }
        if ($id_loket) {
            $query->where('id_loket', $id_loket);
        }
        if ($kategori_info) {
            $query->where('kategori_info', $kategori_info);
        }
        if ($status_info) {
            $query->where('status_info', $status_info);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul_info', 'like', "%{$search}%")
                  ->orWhere('deskripsi_info', 'like', "%{$search}%")
                  ->orWhere('solusi_info', 'like', "%{$search}%");
            });
        }

        $problems = $query->orderBy('tanggal_info', 'desc')->orderBy('created_at', 'desc')->get();

        // Hitung statistik untuk KPI Cards berdasarkan data terfilter
        $totalKendala = $problems->count();
        $totalAktif = $problems->where('status_info', 'aktif')->count();
        $totalArsip = $problems->where('status_info', 'arsip')->count();
        
        $totalCritical = $problems->where('kategori_info', 'critical')->count();
        $totalWarning = $problems->where('kategori_info', 'warning')->count();
        $totalNormal = $problems->where('kategori_info', 'normal')->count();

        // Data untuk diagram lingkaran (Doughnut Chart) sebaran kategori
        $chartLabels = ['Critical', 'Warning', 'Normal'];
        $chartValues = [$totalCritical, $totalWarning, $totalNormal];

        return view('administrator.data_loket_bermasalah', compact(
            'problems',
            'lokets',
            'search',
            'start_date',
            'end_date',
            'id_loket',
            'kategori_info',
            'status_info',
            'totalKendala',
            'totalAktif',
            'totalArsip',
            'totalCritical',
            'totalWarning',
            'totalNormal',
            'chartLabels',
            'chartValues'
        ));
    }

    // Menyimpan data kendala loket baru
    public function store(Request $request)
    {
        $request->validate([
            'id_loket'       => 'required|exists:loket,id_loket',
            'judul_info'     => 'required|string|max:255',
            'kategori_info'  => 'required|in:critical,warning,normal',
            'status_info'    => 'required|in:aktif,arsip',
            'deskripsi_info' => 'required|string',
            'solusi_info'    => 'nullable|string',
            'tanggal_info'   => 'required|date',
        ]);

        Informasi::create([
            'id_loket'       => $request->id_loket,
            'judul_info'     => $request->judul_info,
            'deskripsi_info' => $request->deskripsi_info,
            'solusi_info'    => $request->solusi_info,
            'kategori_info'  => $request->kategori_info,
            'status_info'    => $request->status_info,
            'tanggal_info'   => $request->tanggal_info,
        ]);

        return redirect()->back()->with('success', 'Data kendala loket berhasil ditambahkan.');
    }

    // Memperbarui data kendala loket
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_loket'       => 'required|exists:loket,id_loket',
            'judul_info'     => 'required|string|max:255',
            'kategori_info'  => 'required|in:critical,warning,normal',
            'status_info'    => 'required|in:aktif,arsip',
            'deskripsi_info' => 'required|string',
            'solusi_info'    => 'nullable|string',
            'tanggal_info'   => 'required|date',
        ]);

        $informasi = Informasi::findOrFail($id);
        $informasi->update([
            'id_loket'       => $request->id_loket,
            'judul_info'     => $request->judul_info,
            'deskripsi_info' => $request->deskripsi_info,
            'solusi_info'    => $request->solusi_info,
            'kategori_info'  => $request->kategori_info,
            'status_info'    => $request->status_info,
            'tanggal_info'   => $request->tanggal_info,
        ]);

        return redirect()->back()->with('success', 'Data kendala loket berhasil diperbarui.');
    }

    // Menghapus data kendala loket
    public function destroy($id)
    {
        $informasi = Informasi::findOrFail($id);
        $informasi->delete();

        return redirect()->back()->with('success', 'Data kendala loket berhasil dihapus secara permanen.');
    }

    // Mencetak laporan kendala loket terfilter ke halaman print view
    public function cetak(Request $request)
    {
        $id_profil = session('id_profil');
        $profil = Profil::with('loket')->where('id_profil', $id_profil)->first();

        $search = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $id_loket = $request->input('id_loket');
        $kategori_info = $request->input('kategori_info');
        $status_info = $request->input('status_info');

        // Query data kendala
        $query = Informasi::with('loket');

        if ($start_date) {
            $query->whereDate('tanggal_info', '>=', $start_date);
        }
        if ($end_date) {
            $query->whereDate('tanggal_info', '<=', $end_date);
        }
        if ($id_loket) {
            $query->where('id_loket', $id_loket);
        }
        if ($kategori_info) {
            $query->where('kategori_info', $kategori_info);
        }
        if ($status_info) {
            $query->where('status_info', $status_info);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul_info', 'like', "%{$search}%")
                  ->orWhere('deskripsi_info', 'like', "%{$search}%")
                  ->orWhere('solusi_info', 'like', "%{$search}%");
            });
        }

        $problems = $query->orderBy('tanggal_info', 'desc')->get();

        // Keterangan filter untuk ditampilkan di laporan cetak
        $loketFilterName = 'Semua Loket';
        if ($id_loket) {
            $selectedLoket = Loket::find($id_loket);
            if ($selectedLoket) {
                $loketFilterName = $selectedLoket->nama_loket . ($selectedLoket->nama_pelayanan ? ' - ' . $selectedLoket->nama_pelayanan : '');
            }
        }

        $kategoriFilterName = 'Semua Kategori';
        if ($kategori_info) {
            $kategoriFilterName = ucfirst($kategori_info);
        }

        $statusFilterName = 'Semua Status';
        if ($status_info) {
            $statusFilterName = ucfirst($status_info);
        }

        $periodeFilterName = 'Semua Waktu';
        if ($start_date && $end_date) {
            $periodeFilterName = Carbon::parse($start_date)->format('d-m-Y') . ' s/d ' . Carbon::parse($end_date)->format('d-m-Y');
        } elseif ($start_date) {
            $periodeFilterName = 'Mulai ' . Carbon::parse($start_date)->format('d-m-Y');
        } elseif ($end_date) {
            $periodeFilterName = 'Sampai ' . Carbon::parse($end_date)->format('d-m-Y');
        }

        return view('pdf.laporan_loket_bermasalah', compact(
            'profil',
            'problems',
            'loketFilterName',
            'kategoriFilterName',
            'statusFilterName',
            'periodeFilterName'
        ));
    }
}

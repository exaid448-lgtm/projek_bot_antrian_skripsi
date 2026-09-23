<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loket;
use App\Models\Antrian;
use App\Models\Profil;
use Carbon\Carbon;

class DataBookingController extends Controller
{
    public function index(Request $request)
    {
        // Filter params
        $search = $request->input('search');
        $id_loket = $request->input('loket');
        $tgl_mulai = $request->input('tgl_mulai', Carbon::today()->format('Y-m-d'));
        $tgl_selesai = $request->input('tgl_selesai', Carbon::today()->addDays(7)->format('Y-m-d'));

        // Ambil list loket untuk dropdown filter
        $list_loket = Loket::orderBy('nama_loket', 'asc')->get();

        // Query Booking dengan Filter
        $query = Antrian::with('loket', 'pengunjung')
            ->where('jenis_antrian', 'booking')
            ->whereDate('waktu_voice', '>=', $tgl_mulai)
            ->whereDate('waktu_voice', '<=', $tgl_selesai);

        if ($id_loket) {
            $query->where('id_loket', $id_loket);
        }

        if ($search) {
            $query->whereHas('pengunjung', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_whatsapp', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('waktu_voice', 'asc')->get();

        // KPI
        $totalBooking = $bookings->count();
        $menunggu = $bookings->where('status_antrian', 'menunggu')->count();
        $selesai = $bookings->where('status_antrian', 'selesai')->count();
        $batal = $bookings->where('status_antrian', 'batal')->count();

        // Chart Data (Jumlah Booking per Loket dalam rentang tanggal)
        $chartData = Antrian::where('jenis_antrian', 'booking')
            ->whereDate('waktu_voice', '>=', $tgl_mulai)
            ->whereDate('waktu_voice', '<=', $tgl_selesai)
            ->where('status_antrian', '!=', 'batal')
            ->selectRaw('id_loket, count(*) as total')
            ->groupBy('id_loket')
            ->get();
            
        $chartGroup = [];
        foreach ($list_loket as $lkt) {
            $count = $chartData->where('id_loket', $lkt->id_loket)->first()->total ?? 0;
            if (!isset($chartGroup[$lkt->nama_loket])) {
                $chartGroup[$lkt->nama_loket] = 0;
            }
            $chartGroup[$lkt->nama_loket] += $count;
        }

        $loketChartNames = array_keys($chartGroup);
        $loketChartCounts = array_values($chartGroup);

        return view('administrator.data_antrian_booking', compact(
            'list_loket', 'bookings', 'search', 'id_loket', 'tgl_mulai', 'tgl_selesai',
            'totalBooking', 'menunggu', 'selesai', 'batal',
            'loketChartNames', 'loketChartCounts'
        ));
    }

    public function cetak(Request $request)
    {
        $id_loket = $request->input('loket');
        $tgl_mulai = $request->input('tgl_mulai', Carbon::today()->format('Y-m-d'));
        $tgl_selesai = $request->input('tgl_selesai', Carbon::today()->addDays(7)->format('Y-m-d'));
        $search = $request->input('search');

        $query = Antrian::with('loket', 'pengunjung')
            ->where('jenis_antrian', 'booking')
            ->whereDate('waktu_voice', '>=', $tgl_mulai)
            ->whereDate('waktu_voice', '<=', $tgl_selesai);

        if ($id_loket) {
            $query->where('id_loket', $id_loket);
        }

        if ($search) {
            $query->whereHas('pengunjung', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('waktu_voice', 'asc')->get();
        
        $loketObj = $id_loket ? Loket::find($id_loket) : null;
        $loketFilterName = $loketObj ? $loketObj->nama_loket . ($loketObj->nama_pelayanan ? ' - ' . $loketObj->nama_pelayanan : '') : 'Semua Loket';
        $periodeFilterName = Carbon::parse($tgl_mulai)->format('d/m/Y') . ' - ' . Carbon::parse($tgl_selesai)->format('d/m/Y');
        
        $adminProfil = null;
        if(session()->has('id_user')) {
            $adminProfil = Profil::with('loket')->where('id_user', session('id_user'))->first();
        }

        $with_qr = $request->input('with_qr', 1);

        return view('pdf.laporan_booking', compact(
            'bookings', 'loketFilterName', 'periodeFilterName', 'search', 'adminProfil', 'with_qr'
        ));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'id_loket' => 'required|exists:loket,id_loket',
            'kuota_booking' => 'required|integer|min:1',
            'status_booking' => 'required|in:aktif,nonaktif',
            'sesi_booking' => 'required|in:pagi_siang,pagi,siang'
        ]);

        \DB::table('loket')
            ->where('id_loket', $request->id_loket)
            ->update([
                'kuota_booking' => $request->kuota_booking,
                'status_booking' => $request->status_booking,
                'sesi_booking' => $request->sesi_booking,
            ]);

        return redirect()->back()->with('success', 'Pengaturan Booking Loket berhasil diperbarui!');
    }
}

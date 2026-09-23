<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use App\Models\PoinKinerja;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProfilController extends Controller
{
    public function index()
    {
        $id_profil = Session::get('id_profil');

        $profil = Profil::with('loket')
            ->where('id_profil', $id_profil)
            ->firstOrFail();

        // 📅 Filter Periode Bulan/Tahun (e.g. "2026-06")
        $periode = request('periode', date('Y-m'));
        $parts = explode('-', $periode);
        $tahun = $parts[0] ?? date('Y');
        $bulan = $parts[1] ?? date('m');

        // 🔍 Ambil semua riwayat pelanggaran poin untuk bulan bersangkutan
        $pelanggaran = PoinKinerja::where('id_profil', $id_profil)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_kejadian', 'desc')
            ->get();

        // 💯 Hitung sisa poin kinerja (Poin awal 100)
        $totalDeductions = $pelanggaran->sum('poin_dipotong');
        $total_poin = max(0, 100 - $totalDeductions);

        // 📊 Hitung tren poin kinerja mingguan (5 titik interval mingguan)
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        
        $intervals = [
            'Minggu 1' => 7,
            'Minggu 2' => 14,
            'Minggu 3' => 21,
            'Minggu 4' => 28,
            'Minggu 5' => $daysInMonth
        ];

        $chartLabels = [];
        $chartData = [];

        foreach ($intervals as $label => $day) {
            // Pastikan tidak melebihi hari saat ini jika memfilter bulan sekarang
            $dayVal = min($day, $daysInMonth);
            $limitDate = Carbon::createFromDate($tahun, $bulan, $dayVal)->endOfDay()->toDateString();
            
            $deductionsBefore = PoinKinerja::where('id_profil', $id_profil)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('tanggal', '<=', $limitDate)
                ->sum('poin_dipotong');

            $chartLabels[] = $label;
            $chartData[] = max(0, 100 - $deductionsBefore);
        }

        return view('admin_loket.profil_loket', compact(
            'profil', 
            'periode', 
            'pelanggaran', 
            'total_poin', 
            'chartLabels', 
            'chartData'
        ));
    }

    public function update(Request $request)
    {
        $id_profil = Session::get('id_profil');
        $profil = Profil::where('id_profil', $id_profil)->firstOrFail();

        $request->validate([
            'nama_user' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tanggal_lahir' => 'required|date',
            'img_user' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $profil->nama_user = $request->nama_user;
        $profil->jenis_kelamin = $request->jenis_kelamin;
        $profil->tanggal_lahir = $request->tanggal_lahir;

        // Update profile picture
        if ($request->hasFile('img_user')) {
            if ($profil->img_user && File::exists(public_path('img/foto_karyawan/' . $profil->img_user))) {
                try {
                    File::delete(public_path('img/foto_karyawan/' . $profil->img_user));
                } catch (\Exception $e) {
                    // Ignore error if delete fails
                }
            }
            $file = $request->file('img_user');
            $fileName = time() . '_avatar_' . $file->getClientOriginalName();
            $file->move(public_path('img/foto_karyawan'), $fileName);
            $profil->img_user = $fileName;
        }



        $profil->save();

        return redirect()->route('profil.loket')->with('success', 'Profil berhasil diperbarui.');
    }
}

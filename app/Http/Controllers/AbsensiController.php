<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\PoinKinerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index()
    {
        $id_profil = Session::get('id_profil');

        if (!$id_profil) {
            return redirect('/login')->with('error', 'Sesi habis, silakan login kembali.');
        }

        $today = Carbon::today()->toDateString();
        $jadwalHariIni = DB::table('jadwal')
            ->where('id_profil', $id_profil)
            ->where('tanggal', $today)
            ->where('setatus', 'aktif')
            ->first();

        $absensi = Absensi::where('id_profil', $id_profil)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_masuk', 'desc')
            ->get();

        return view('admin_loket.absensi_loket', [
            'absensi' => $absensi,
            'hadir' => Absensi::where('id_profil', $id_profil)->where('status_absen', 'hadir')->count(),
            'izin'  => Absensi::where('id_profil', $id_profil)->where('status_absen', 'izin')->count(),
            'telat' => Absensi::where('id_profil', $id_profil)->where('status_absen', 'telat')->count(),
            'jadwalHariIni' => $jadwalHariIni,
        ]);
    }

    // ============================
    // ABSEN MASUK
    // ============================
    public function store(Request $request)
    {
        $id_profil = Session::get('id_profil');
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 📅 Validasi Jadwal Karyawan Hari Ini
        $jadwal = DB::table('jadwal')
            ->where('id_profil', $id_profil)
            ->where('tanggal', $today)
            ->where('setatus', 'aktif')
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda tidak memiliki jadwal bertugas aktif hari ini.');
        }

        // ❌ Cegah dobel absen harian (AMAN karena composite unique)
        if (Absensi::where('id_profil', $id_profil)->where('tanggal', $today)->exists()) {
            return back()->with('error', 'Anda sudah absen hari ini.');
        }

        $statusInput = strtolower($request->status_absen);
        $statusFinal = 'hadir';

        // ================= JAM HADIR / TELAT =================
        // Batas hadir disesuaikan dengan jam_masuk dari jadwal aktif ditambah batas toleransi 15 menit
        $batasHadir = Carbon::parse($today . ' ' . $jadwal->jam_masuk)->addMinutes(15);
        $statusFinal = $now->greaterThan($batasHadir) ? 'telat' : 'hadir';

        Absensi::create([
            'id_profil'    => $id_profil,
            'tanggal'      => $today,
            'waktu_masuk'  => $now->format('H:i:s'),
            'status_absen' => $statusFinal,
            'surat_izin'   => null
        ]);

        // 🕒 Logika Keterlambatan Absensi Poin Kinerja
        if ($statusFinal === 'telat') {
            $exists = PoinKinerja::where('id_profil', $id_profil)
                ->where('tanggal', $today)
                ->where('jenis_pelanggaran', 'terlambat_absen')
                ->exists();

            if (!$exists) {
                PoinKinerja::create([
                    'id_profil'         => $id_profil,
                    'tanggal'           => $today,
                    'jenis_pelanggaran' => 'terlambat_absen',
                    'waktu_kejadian'    => $now->format('H:i:s'),
                    'poin_dipotong'     => 5,
                    'keterangan'        => 'Terlambat absensi masuk (' . $now->format('H:i') . '). Jadwal: ' . Carbon::parse($jadwal->jam_masuk)->format('H:i') . ', Batas toleransi: ' . $batasHadir->format('H:i'),
                    'created_at'        => Carbon::now()
                ]);
            }
        }

        return redirect()->route('absensi.index')
            ->with('success', 'Absen masuk berhasil ('.strtoupper($statusFinal).')');
    }

    // ============================
    // PENGAJUAN IZIN
    // ============================
    public function ajukanIzin(Request $request)
    {
        $id_profil = Session::get('id_profil');
        
        if (!$id_profil) {
            return redirect('/login')->with('error', 'Sesi habis, silakan login kembali.');
        }

        $tanggal_izin = $request->tanggal_izin;

        // 1. Validasi jadwal untuk tanggal tersebut
        $jadwal = DB::table('jadwal')
            ->where('id_profil', $id_profil)
            ->where('tanggal', $tanggal_izin)
            ->where('setatus', 'aktif')
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda tidak memiliki jadwal kerja pada tanggal ' . $tanggal_izin . '. Tidak perlu mengajukan izin.');
        }

        // 2. Validasi file surat izin
        if (!$request->hasFile('surat_izin')) {
            return back()->with('error', 'Bukti surat izin wajib diupload.');
        }

        // Cek apakah sudah mengajukan izin/hadir di tanggal tersebut
        if (Absensi::where('id_profil', $id_profil)->where('tanggal', $tanggal_izin)->exists()) {
            return back()->with('error', 'Anda sudah melakukan absensi atau mengajukan izin untuk tanggal ' . $tanggal_izin . '.');
        }

        $file = $request->file('surat_izin');
        $namaFile = time() . '_' . $id_profil . '.' . $file->getClientOriginalExtension();
        $tujuanFolder = public_path('penyimpanan_dokumen/surat_izin');

        if (!File::exists($tujuanFolder)) {
            File::makeDirectory($tujuanFolder, 0755, true);
        }
        $file->move($tujuanFolder, $namaFile);

        // Buat record absensi
        Absensi::create([
            'id_profil'    => $id_profil,
            'tanggal'      => $tanggal_izin,
            'waktu_masuk'  => Carbon::now()->format('H:i:s'), // waktu submit
            'status_absen' => 'izin',
            'surat_izin'   => $namaFile
        ]);

        return redirect()->route('absensi.index')->with('success', 'Pengajuan izin untuk tanggal ' . $tanggal_izin . ' berhasil dikirim.');
    }

    // ============================
    // ABSEN PULANG
    // ============================
    public function pulang()
    {
        $id_profil = Session::get('id_profil');
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 📅 Validasi Jadwal Karyawan Hari Ini
        $jadwal = DB::table('jadwal')
            ->where('id_profil', $id_profil)
            ->where('tanggal', $today)
            ->where('setatus', 'aktif')
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Anda tidak memiliki jadwal bertugas aktif hari ini.');
        }

        // Batas pulang dinamis sesuai jam_pulang jadwal
        $jamPulang = Carbon::parse($today . ' ' . $jadwal->jam_pulang);
        if ($now->lessThan($jamPulang)) {
            return back()->with('error', 'Absen pulang hanya dapat dilakukan mulai pukul ' . $jamPulang->format('H:i'));
        }

        $absen = Absensi::where('id_profil', $id_profil)
            ->where('tanggal', $today)
            ->whereNull('waktu_pulang')
            ->first();

        if (!$absen) {
            return back()->with('error', 'Anda belum absen masuk.');
        }

        $absen->update([
            'waktu_pulang' => $now->format('H:i:s')
        ]);

        return redirect()->route('absensi.index')
            ->with('success', 'Absen pulang berhasil.');
    }
}

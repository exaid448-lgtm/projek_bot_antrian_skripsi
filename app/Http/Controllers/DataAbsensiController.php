<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DataAbsensiController extends Controller
{
    /**
     * Privat fungsi untuk menyatukan logika filter
     * Agar apa yang difilter di tabel, itu juga yang tercetak di PDF
     */
    private function getFilteredQuery(Request $request)
    {
        $query = DB::table('absensi')
            ->join('profil_karyawan', 'absensi.id_profil', '=', 'profil_karyawan.id_profil')
            ->leftJoin('loket', 'profil_karyawan.id_loket', '=', 'loket.id_loket')
            ->select('absensi.*', 'profil_karyawan.nama_user', 'loket.nama_loket', 'loket.nama_pelayanan', 'loket.lokasi_loket');

        // 🔍 Filter Nama User
        if ($request->filled('search')) {
            $query->where('profil_karyawan.nama_user', 'like', '%' . $request->search . '%');
        }

        // 🏢 Filter Nama Loket
        if ($request->filled('loket') && $request->loket !== 'Semua Loket') {
            $query->where('loket.nama_loket', $request->loket);
        }

        // 📅 Filter Range Tanggal
        if ($request->filled('tgl_mulai')) {
            $query->whereDate('absensi.tanggal', '>=', $request->tgl_mulai);
        }
        if ($request->filled('tgl_selesai')) {
            $query->whereDate('absensi.tanggal', '<=', $request->tgl_selesai);
        }

        return $query;
    }

    public function index(Request $request)
    {
        // Ambil list loket untuk isi dropdown filter
        $list_loket = DB::table('loket')->get();
        
        // Ambil query yang sudah difilter
        $query = $this->getFilteredQuery($request);
        
        // Eksekusi data untuk tabel (Urutkan yang terbaru di atas)
        $absensi = $query->orderBy('absensi.tanggal', 'desc')
                         ->orderBy('absensi.waktu_masuk', 'desc')
                         ->get();

        // Query khusus untuk izin
        $queryIzin = clone $query;
        $absensiIzin = $queryIzin->where('absensi.status_absen', 'izin')
                                 ->orderBy('absensi.tanggal', 'desc')
                                 ->get();

        return view('administrator.data_absensi', compact('absensi', 'absensiIzin', 'list_loket'));
    }

    public function cetak(Request $request)
    {
        // Ambil data menggunakan filter yang sama dengan index
        $query = $this->getFilteredQuery($request);
        
        // Urutkan data berdasarkan tanggal
        $absensi = $query->orderBy('absensi.tanggal', 'asc')->get();

        // Langsung return view biasa (Bukan format PDF)
        return view('pdf.laporan_absensi', compact('absensi', 'request'));
    }

    public function getKaryawanSatuLoket(Request $request)
    {
        $id_profil_izin = $request->id_profil_izin;
        
        $karyawanIzin = DB::table('profil_karyawan')->where('id_profil', $id_profil_izin)->first();
        if (!$karyawanIzin) {
            return response()->json([]);
        }

        $karyawanSatuLoket = DB::table('profil_karyawan')
            ->where('id_loket', $karyawanIzin->id_loket)
            ->where('id_profil', '!=', $id_profil_izin)
            ->select('id_profil', 'nama_user')
            ->get();

        return response()->json($karyawanSatuLoket);
    }

    public function tukarShift(Request $request)
    {
        $id_profil_izin = $request->id_profil_izin;
        $id_profil_pengganti = $request->id_profil_pengganti;
        $tanggal_izin = $request->tanggal_izin;

        // Cari jadwal karyawan yang izin pada tanggal tersebut
        $jadwal = DB::table('jadwal')
            ->where('id_profil', $id_profil_izin)
            ->where('tanggal', $tanggal_izin)
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Gagal tukar shift: Karyawan yang izin tidak memiliki jadwal pada tanggal ' . $tanggal_izin);
        }

        // Update jadwal dengan id_profil pengganti
        DB::table('jadwal')
            ->where('id_jadwal', $jadwal->id_jadwal)
            ->update([
                'id_profil' => $id_profil_pengganti,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        return back()->with('success', 'Berhasil melakukan pertukaran shift!');
    }

    public function showSuratIzin($filename)
    {
        return view('surat.surat_izin.surat_izin', compact('filename'));
    }
}
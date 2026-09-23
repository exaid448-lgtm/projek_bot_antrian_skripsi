<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Antrian;
use App\Models\Loket;
use Carbon\Carbon;

class BookingAntrianController extends Controller
{
    // Konstanta kuota maksimal per sesi per loket
    const KUOTA_PER_SESI = 10;

    // Definisikan sesi jam operasional (misal: jam 08:00 - 15:00, per jam)
    public $sesiJam = [
        '08:00:00', '09:00:00', '10:00:00', '11:00:00', 
        '13:00:00', '14:00:00' // Skip 12:00 for istirahat
    ];

    public function index(Request $request)
    {
        $lokets = Loket::orderBy('nama_loket', 'asc')->get();
        
        // Kelompokkan loket berdasarkan nama instansinya (case insensitive)
        $groupedLoket = $lokets->groupBy(function($item) {
            return strtolower(trim($item->nama_loket));
        });

        $id_loket = $request->get('id_loket');
        
        // Cek profil pengunjung untuk status prioritas
        $profil = \App\Models\ProfilPengunjung::where('id_user', session('id_user'))->first();
        $isPermanen = $profil && $profil->status_prioritas === 'disetujui' && $profil->jenis_prioritas === 'disabilitas_permanen';
        $isPrioritasUmum = $profil && $profil->status_prioritas === 'disetujui';
        $isLansia = false;
        if ($profil && $profil->tanggal_lahir) {
            $umur = \Carbon\Carbon::parse($profil->tanggal_lahir)->age;
            if ($umur >= 60) $isLansia = true;
        }

        return view('pengunjung.booking_antrian', compact('groupedLoket', 'id_loket', 'isPermanen', 'isLansia', 'isPrioritasUmum', 'profil'));
    }

    public function getKuota(Request $request)
    {
        $id_loket = $request->id_loket;
        $tanggal = $request->tanggal; // Format Y-m-d

        if (!$id_loket || !$tanggal) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }

        $loket = Loket::find($id_loket);
        $kuotaMaks = $loket->kuota_booking ?? self::KUOTA_PER_SESI;

        // Hitung total antrean booking di loket pada hari tersebut
        $terisi = DB::table('antrian')
            ->where('id_loket', $id_loket)
            ->where('jenis_antrian', 'booking')
            ->whereDate('waktu_voice', $tanggal)
            ->where('status_antrian', '!=', 'batal')
            ->count();

        $sisa = max(0, $kuotaMaks - $terisi);

        return response()->json([
            'sisa' => $sisa,
            'max_kuota' => $kuotaMaks,
            'is_full' => $sisa <= 0
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_loket' => 'required|exists:loket,id_loket',
            'tanggal' => 'required|date',
            'slot_waktu' => 'required|string'
        ]);

        $id_user = session('id_user');
        if (!$id_user) {
            return redirect()->route('login_pengunjung')->with('error', 'Silakan login terlebih dahulu');
        }

        $profil = \App\Models\ProfilPengunjung::where('id_user', $id_user)->first();
        if (!$profil) {
            return redirect()->route('login_pengunjung')->with('error', 'Profil pengunjung tidak ditemukan');
        }
        $id_pengunjung = $profil->id_pengunjung;

        $tanggalBooking = Carbon::parse($request->tanggal);
        $slotWaktu = $request->slot_waktu;
        
        // Tentukan batas check-in berdasarkan slot waktu (30 menit sebelum sesi berakhir)
        // Pagi: 08:00 - 11:00 -> Batas check-in: 10:30:00
        // Siang: 13:00 - 15:00 -> Batas check-in: 14:30:00
        $batasCheckIn = '10:30:00';
        if (str_contains($slotWaktu, '15:00') || str_contains($slotWaktu, 'Siang')) {
            $batasCheckIn = '14:30:00';
        }

        // Tentukan waktu_voice
        if ($tanggalBooking->isToday()) {
            if ((str_contains($slotWaktu, '15:00') || str_contains($slotWaktu, 'Siang')) && Carbon::now()->hour < 13) {
                $waktu_booking = $tanggalBooking->format('Y-m-d') . ' 13:00:00';
            } else {
                $waktu_booking = Carbon::now()->format('Y-m-d H:i:s');
            }
        } else {
            if (str_contains($slotWaktu, '15:00') || str_contains($slotWaktu, 'Siang')) {
                $waktu_booking = $tanggalBooking->format('Y-m-d') . ' 13:00:00';
            } else {
                $waktu_booking = $tanggalBooking->format('Y-m-d') . ' 08:00:00';
            }
        }

        // Validasi Kuota lagi sebelum simpan
        $loket = Loket::where('id_loket', $request->id_loket)->first();
        $kuotaMaks = $loket->kuota_booking ?? self::KUOTA_PER_SESI;

        $terisi = DB::table('antrian')
            ->where('id_loket', $request->id_loket)
            ->where('jenis_antrian', 'booking')
            ->whereDate('waktu_voice', $tanggalBooking->format('Y-m-d'))
            ->where('status_antrian', '!=', 'batal')
            ->count();

        if ($terisi >= $kuotaMaks) {
            return back()->with('error', 'Maaf, kuota antrean online untuk tanggal ini sudah penuh!')->withInput();
        }

        // PENYATUAN NOMOR ANTREAN: Melanjutkan nomor offline di hari tersebut
        $totalHariIni = Antrian::where('id_loket', $loket->id_loket)
                                ->whereDate('waktu_voice', $tanggalBooking->format('Y-m-d'))
                                ->count();
        $nomorUrut = $totalHariIni + 1;

        $prefix = $loket->prefix ?? 'X';
        $nomorAntrianBaru = $prefix . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // Generate Kode Booking Unik
        do {
            $kodeBooking = 'BK-' . $tanggalBooking->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Antrian::where('kode_booking_unik', $kodeBooking)->exists());

        $statusAntrian = 'booking';
        // Jika booking hari ini dan loket sedang buka, langsung masuk antrean berjalan (menunggu)
        if ($tanggalBooking->isToday() && strtoupper($loket->status_pelayanan ?? '') === 'BUKA') {
            $statusAntrian = 'menunggu';
        }

        // PENENTUAN PRIORITAS
        $isPrioritas = 0;
        $jenisPrioritas = null;

        $umur = Carbon::parse($profil->tanggal_lahir)->age;
        if ($umur >= 60) {
            $isPrioritas = 1;
            $jenisPrioritas = 'lansia';
        } elseif ($profil->status_prioritas === 'disetujui') {
            if ($profil->jenis_prioritas === 'disabilitas_permanen') {
                $isPrioritas = 1;
                $jenisPrioritas = 'disabilitas_permanen';
            } elseif ($profil->tanggal_berakhir_prioritas && Carbon::today()->lte(Carbon::parse($profil->tanggal_berakhir_prioritas))) {
                $isPrioritas = 1;
                $jenisPrioritas = $profil->jenis_prioritas;
            }
        }

        Antrian::create([
            'id_loket' => $request->id_loket,
            'nomor_antrian' => $nomorAntrianBaru,
            'waktu_voice' => $waktu_booking,
            'jenis_antrian' => 'booking',
            'setatus_pengambilan' => 'online',
            'status_antrian' => $statusAntrian,
            'id_pengunjung' => $id_pengunjung,
            'is_prioritas' => $isPrioritas,
            'jenis_prioritas' => $jenisPrioritas,
            // 6 Kolom Revisi Dosen Penguji 3
            'kode_booking_unik' => $kodeBooking,
            'tanggal_booking' => $tanggalBooking->format('Y-m-d'),
            'slot_waktu' => $slotWaktu,
            'waktu_check_in' => null,
            'status_booking' => 'booking',
            'batas_check_in' => $batasCheckIn,
        ]);

        return redirect()->route('dashboard.pengunjung')->with('success', 'Antrean berhasil dibooking! Nomor: ' . $nomorAntrianBaru . ' (Kode: ' . $kodeBooking . ')');
    }
}


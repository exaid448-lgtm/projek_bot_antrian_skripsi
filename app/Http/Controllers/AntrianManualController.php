<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loket;   // Model master loket/layanan Anda
use App\Models\Antrian; // Model transaksi antrian Anda
use App\Models\ProfilPengunjung; // Diperlukan untuk ambil id_pengunjung yang benar
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AntrianManualController extends Controller
{
    public function index(Request $request)
    {
        $hariIni = Carbon::today();
        $search = $request->query('q');

        // MODIFIKASI: Ambil master loket yang status pelayanannya 'BUKA' ATAU 'TUTUP'
        $query = Loket::whereIn('status_pelayanan', ['BUKA', 'TUTUP']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_loket', 'like', '%' . $search . '%')
                  ->orWhere('nama_pelayanan', 'like', '%' . $search . '%')
                  ->orWhere('prefix', 'like', '%' . $search . '%');
            });
        }

        $daftarLoket = $query->get();

        // Hitung sisa antrean 'menunggu' hari ini secara dinamis untuk masing-masing loket
        foreach ($daftarLoket as $loket) {
            $loket->jumlah_antrean = Antrian::where('id_loket', $loket->id_loket)
                                            ->whereDate('waktu_voice', $hariIni) 
                                            ->where('status_antrian', 'menunggu') 
                                            ->count();
        }

        // Kelompokkan loket berdasarkan nama instansinya (case insensitive)
        $groupedLoket = $daftarLoket->groupBy(function($item) {
            return strtolower(trim($item->nama_loket));
        });

        // Cek status prioritas untuk Kiosk Mode
        $isLansia = false;
        $isPermanen = false;
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $profil = \App\Models\ProfilPengunjung::where('id_user', $user->id_user ?? $user->id)->first();
            if ($profil) {
                // Cek jika lansia
                if ($profil->tanggal_lahir) {
                    $umur = \Carbon\Carbon::parse($profil->tanggal_lahir)->age;
                    if ($umur >= 60) $isLansia = true;
                }
                
                // Cek jika punya prioritas yang masih aktif/disetujui
                if ($profil->status_prioritas === 'disetujui') {
                    if ($profil->jenis_prioritas === 'disabilitas_permanen') {
                        $isPermanen = true;
                    } elseif ($profil->tanggal_berakhir_prioritas && \Carbon\Carbon::today()->lte(\Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas))) {
                        $isPermanen = true;
                    }
                }
            }
        }

        return view('pengunjung.antrain_manual', compact('groupedLoket', 'search', 'isLansia', 'isPermanen'));
    }

    /**
     * Memproses pengambilan nomor antrian baru via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_loket' => 'required|integer'
        ]);

        try {
            $hariIni = Carbon::today();

            // 1. Cari data loket terkait di database
            $loket = Loket::findOrFail($request->id_loket);

            // VALIDASI TAMBAHAN: Mencegah tembakan API/AJAX jika loket sebenarnya TUTUP
            if ($loket->status_pelayanan === 'TUTUP') {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, loket ini sudah tutup dan tidak menerima antrian lagi.'
                ], 422);
            }

            // 2. Hitung total antrian loket tersebut hari ini untuk generate nomor urut baru
            $totalHariIni = Antrian::where('id_loket', $loket->id_loket)
                                    ->whereDate('waktu_voice', $hariIni)
                                    ->count();
            $nomorUrut = $totalHariIni + 1;

            $prefix = $loket->prefix ?? 'X';
            $nomorAntrianLengkap = $prefix . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

            // 3. Simpan data antrian baru ke database
            $antrian = new Antrian();
            $antrian->id_loket = $loket->id_loket;
            $antrian->nomor_antrian = $nomorAntrianLengkap;
            $antrian->waktu_voice = Carbon::now();
            $antrian->status_antrian = 'menunggu';
            $antrian->jenis_antrian = 'manual';

            if (Auth::check()) {
                $user = Auth::user();
                $profil = ProfilPengunjung::where('id_user', $user->id_user ?? $user->id)->first();

                if ($profil) {
                    $antrian->id_pengunjung    = $profil->id_pengunjung;
                    $antrian->setatus_pengambilan = 'manual';
                } else {
                    $antrian->id_pengunjung    = null;
                    $antrian->setatus_pengambilan = 'manual';
                }
            } else {
                $antrian->id_pengunjung    = null;
                $antrian->setatus_pengambilan = 'manual';
            }

            // Set Prioritas
            $antrian->is_prioritas = 0;
            $antrian->jenis_prioritas = null;

            if (Auth::check() && isset($profil)) {
                // OTOMATIS DARI PROFIL UNTUK USER ONLINE
                $umur = \Carbon\Carbon::parse($profil->tanggal_lahir)->age;
                if ($umur >= 60) {
                    $antrian->is_prioritas = 1;
                    $antrian->jenis_prioritas = 'lansia';
                } elseif ($profil->status_prioritas === 'disetujui') {
                    if ($profil->jenis_prioritas === 'disabilitas_permanen') {
                        $antrian->is_prioritas = 1;
                        $antrian->jenis_prioritas = 'disabilitas_permanen';
                    } elseif ($profil->tanggal_berakhir_prioritas && \Carbon\Carbon::today()->lte(\Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas))) {
                        $antrian->is_prioritas = 1;
                        $antrian->jenis_prioritas = $profil->jenis_prioritas;
                    }
                }
            } else {
                // DARI REQUEST JIKA KIOSK OFFLINE
                if ($request->has('is_prioritas') && $request->is_prioritas == 1) {
                    $antrian->is_prioritas = 1;
                    $antrian->jenis_prioritas = $request->jenis_prioritas ?: 'disabilitas_sementara';
                }
            }

            $antrian->save();

            $sisaAntreanTerbaru = Antrian::where('id_loket', $loket->id_loket)
                                         ->whereDate('waktu_voice', $hariIni)
                                         ->where('status_antrian', 'menunggu')
                                         ->count();

            return response()->json([
                'success' => true,
                'message' => 'Antrian berhasil didaftarkan!',
                'data' => [
                    'nomor_antrian' => $nomorAntrianLengkap,
                    'nama_loket' => $loket->nama_loket,
                    'nama_pelayanan' => $loket->nama_pelayanan,
                    'sisa_antrean' => $sisaAntreanTerbaru,
                    'mode' => $antrian->is_prioritas ? 'PRIORITAS' : $antrian->setatus_pengambilan
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses antrian: ' . $e->getMessage()
            ], 500);
        }
    }
}
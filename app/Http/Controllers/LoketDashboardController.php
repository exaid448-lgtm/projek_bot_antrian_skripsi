<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\PoinKinerja;
use App\Mail\NotifikasiAntreanMendekati;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class LoketDashboardController extends Controller
{
    public function index()
    {
        $today   = Carbon::today();
        $id_user = session('id_user');
        $keyword = request('q');

        $profil = DB::table('profil_karyawan')->where('id_user', $id_user)->first();

        if ($profil) {
            session([
                'nama' => $profil->nama_user,
                'foto' => $profil->img_user ?: 'default.png' // Jika null di DB, beri default
            ]);
        }

        $loket = null;
        if ($profil && $profil->id_loket) {
            $loket = DB::table('loket')->where('id_loket', $profil->id_loket)->first();
        }

        if (!$profil || !$profil->id_loket || !$loket) {
            return view('admin_loket.home_loket', [
                'no_loket' => true,
                'antrianHariIni'   => collect(),
                'total_antrian'   => 0,
                'antrian_selesai' => 0,
                'total_pelayanan' => 0,
                'status_pelayanan'=> 'TUTUP',
                'keyword'         => $keyword,
            ]);
        }

        $query = Antrian::whereDate('waktu_voice', $today)
            ->where('id_loket', $profil->id_loket)
            ->where('status_antrian', '!=', 'batal');

        if ($keyword) {
            $query->where('nomor_antrian', 'LIKE', "%$keyword%");
        }

        $antrianHariIni = $query->orderBy('is_prioritas', 'desc')
                                ->orderBy('waktu_voice', 'asc')
                                ->get();

        return view('admin_loket.home_loket', [
            'antrianHariIni'   => $antrianHariIni,
            'total_antrian'   => $antrianHariIni->whereNull('waktu_selesai')->count(),
            'antrian_selesai' => $antrianHariIni->whereNotNull('waktu_selesai')->count(),
            'total_pelayanan' => $antrianHariIni->count(),
            'status_pelayanan'=> $loket->status_pelayanan,
            'keyword'         => $keyword,
        ]);
    }

    public function togglePelayanan()
    {
        $id_user = session('id_user');
        $profil = DB::table('profil_karyawan')->where('id_user', $id_user)->first();
        if (!$profil) return back();

        // 📅 Validasi Jadwal Karyawan Hari Ini & Jam Kerja
        $now = Carbon::now();
        $today = $now->toDateString();

        $jadwal = DB::table('jadwal')
            ->where('id_profil', $profil->id_profil)
            ->where('tanggal', $today)
            ->where('setatus', 'aktif')
            ->first();

        // Jika tidak ada jadwal bertugas hari ini
        if (!$jadwal) {
            return back()->with('error', 'Anda tidak bertugas di jadwal sekarang.');
        }

        // Tentukan batas waktu masuk dan pulang (diberikan buffer 1 jam lebih awal untuk bersiap, dan 1 jam setelah pulang)
        $jamMasuk = Carbon::parse($today . ' ' . $jadwal->jam_masuk)->subMinutes(60);
        $jamPulang = Carbon::parse($today . ' ' . $jadwal->jam_pulang)->addMinutes(60);

        // Jika jam pulang lebih awal dari jam masuk, berarti shift lewat tengah malam (tambah 1 hari ke jam pulang)
        if (Carbon::parse($jadwal->jam_pulang)->lt(Carbon::parse($jadwal->jam_masuk))) {
            $jamPulang->addDay();
        }

        if ($now->lt($jamMasuk) || $now->gt($jamPulang)) {
            return back()->with('error', 'Anda tidak bertugas di jadwal sekarang. (Jam Masuk: '.$jadwal->jam_masuk.', Jam Pulang: '.$jadwal->jam_pulang.')');
        }

        $loket = DB::table('loket')->where('id_loket', $profil->id_loket)->first();
        if (!$loket) return back();

        $statusBaru = ($loket->status_pelayanan === 'BUKA') ? 'TUTUP' : 'BUKA';

        // 🕒 Logika Keterlambatan Buka Loket
        if ($statusBaru === 'BUKA') {
            $jamBatasBuka = Carbon::parse($jadwal->jam_masuk);
            if ($now->greaterThan($jamBatasBuka)) {
                // Catat pelanggaran terlambat buka loket jika belum dicatat hari ini
                $exists = PoinKinerja::where('id_profil', $profil->id_profil)
                    ->where('tanggal', $today)
                    ->where('jenis_pelanggaran', 'terlambat_buka_loket')
                    ->exists();

                if (!$exists) {
                    PoinKinerja::create([
                        'id_profil'         => $profil->id_profil,
                        'tanggal'           => $today,
                        'jenis_pelanggaran' => 'terlambat_buka_loket',
                        'waktu_kejadian'    => $now->format('H:i:s'),
                        'poin_dipotong'     => 5,
                        'keterangan'        => 'Terlambat membuka pelayanan loket (' . $now->format('H:i') . ', jadwal masuk: ' . Carbon::parse($jadwal->jam_masuk)->format('H:i') . ')',
                        'created_at'        => Carbon::now()
                    ]);
                }
            }
        }

        DB::table('loket')->where('id_loket', $profil->id_loket)->update([
            'status_pelayanan' => $statusBaru
        ]);

        if ($statusBaru === 'BUKA') {
            // Ketika loket dibuka, ubah semua antrean booking HARI INI yang statusnya masih 'booking' menjadi 'menunggu'
            Antrian::where('id_loket', $profil->id_loket)
                ->whereDate('waktu_voice', $today)
                ->where('status_antrian', 'booking')
                ->update(['status_antrian' => 'menunggu']);
        }

        $pesan = 'Status pelayanan berhasil diubah menjadi ' . $statusBaru . '.';
        return back()->with('success', $pesan);
    }

    public function updateStatus(Request $request)
    {
        try {
            $id = $request->id_antrian;
            $status = $request->status_antrian;

            $antrian = Antrian::where('id_antrian', $id)->first();

            if (!$antrian) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            // Siapkan array data yang akan diupdate
            $updateData = ['status_antrian' => $status];

            // Sinkronisasi status_booking jika antrean adalah booking
            if ($antrian->jenis_antrian === 'booking' || $antrian->kode_booking_unik) {
                if ($status === 'dipanggil') {
                    $updateData['status_booking'] = 'check_in';
                    if (empty($antrian->waktu_check_in)) {
                        $updateData['waktu_check_in'] = Carbon::now();
                    }
                } elseif ($status === 'terlewat') {
                    $updateData['status_booking'] = 'no_show';
                } elseif ($status === 'selesai') {
                    $updateData['status_booking'] = 'selesai';
                }
            }

            // Logika pengisian timestamps otomatis berdasarkan pilihan dropdown
            if ($status === 'dipanggil') {
                $updateData['waktu_panggil'] = Carbon::now();
                $updateData['id_karyawan'] = session('id_user');
                
                // ==============================================================
                // 🔔 NOTIFIKASI EMAIL (MAILTRAP) & WHATSAPP H-1 GILIRAN MENDEKATI
                // (Jeda 1 Antrean: misal Antrean 2 masuk loket, Antrean 3 dapat notif)
                // ==============================================================
                try {
                    // Cari antrean selanjutnya di loket yang sama yang masih menunggu/booking hari ini
                    $nextAntrian = Antrian::where('id_loket', $antrian->id_loket)
                        ->whereDate('waktu_voice', Carbon::today())
                        ->whereIn('status_antrian', ['menunggu', 'booking'])
                        ->where('id_antrian', '!=', $antrian->id_antrian)
                        ->orderBy('is_prioritas', 'desc')
                        ->orderBy('waktu_voice', 'asc')
                        ->first();

                    // Jika antrean selanjutnya ada dan terkait akun pengunjung
                    if ($nextAntrian && $nextAntrian->id_pengunjung) {
                        $profilPengunjung = \App\Models\ProfilPengunjung::find($nextAntrian->id_pengunjung);

                        // 1. KIRIM NOTIFIKASI EMAIL VIA MAILTRAP (Cegah duplikasi jika sudah pernah dikirim)
                        if ($profilPengunjung && !empty($profilPengunjung->email) && !$nextAntrian->notifikasi_email_dikirim) {
                            try {
                                Mail::to($profilPengunjung->email)->send(new NotifikasiAntreanMendekati($nextAntrian, $antrian));
                                
                                // Tandai bahwa notifikasi email sudah terkirim (Anti-Spam)
                                $nextAntrian->update([
                                    'notifikasi_email_dikirim' => true,
                                    'waktu_notifikasi_email'   => Carbon::now(),
                                ]);

                                \Illuminate\Support\Facades\Log::info("Mailtrap: Notifikasi Email Giliran Mendekati berhasil dikirim ke {$profilPengunjung->email} untuk Nomor Antrean {$nextAntrian->nomor_antrian} (Saat ini melayani {$antrian->nomor_antrian})");
                            } catch (\Exception $mailEx) {
                                \Illuminate\Support\Facades\Log::error("Mailtrap Notification Error: " . $mailEx->getMessage());
                            }
                        }

                        // 2. KIRIM NOTIFIKASI WHATSAPP TWILIO (Jika nomor WA tersedia)
                        if ($profilPengunjung && !empty($profilPengunjung->nomor_whatsapp)) {
                            try {
                                // Format nomor WA ke format internasional (+62...)
                                $no_wa = $profilPengunjung->nomor_whatsapp;
                                if (str_starts_with($no_wa, '08')) {
                                    $no_wa = '+628' . substr($no_wa, 2);
                                } elseif (str_starts_with($no_wa, '62')) {
                                    $no_wa = '+' . $no_wa;
                                } elseif (str_starts_with($no_wa, '8')) {
                                    $no_wa = '+628' . substr($no_wa, 1);
                                }
                                
                                $sid    = env('TWILIO_ACCOUNT_SID');
                                $token  = env('TWILIO_AUTH_TOKEN');
                                $from   = env('TWILIO_WHATSAPP_FROM'); // Format: "whatsapp:+1..."
                                
                                if ($sid && $token && $from) {
                                    $twilio = new \Twilio\Rest\Client($sid, $token);
                                    
                                    $pesan = "Halo *{$profilPengunjung->nama}* 👋\n\nNomor antrean Anda (*{$nextAntrian->nomor_antrian}*) adalah giliran berikutnya di *{$antrian->loket->nama_loket}*.\nSaat ini loket sedang melayani nomor *{$antrian->nomor_antrian}*. Mohon segera bersiap-siap menuju ke area loket.";
                                    
                                    $pesanParams = [
                                        "from" => str_starts_with($from, 'whatsapp:') ? $from : "whatsapp:" . $from,
                                        "body" => $pesan
                                    ];
                                    
                                    $twilio->messages->create("whatsapp:" . $no_wa, $pesanParams);
                                    \Illuminate\Support\Facades\Log::info("Twilio: Notifikasi H-1 terkirim ke $no_wa (Antrean: {$nextAntrian->nomor_antrian})");
                                }
                            } catch (\Exception $waEx) {
                                $fromDebug = str_starts_with($from ?? '', 'whatsapp:') ? ($from ?? '') : "whatsapp:" . ($from ?? '');
                                \Illuminate\Support\Facades\Log::error("Twilio H-1 Notification Error: " . $waEx->getMessage() . " | FROM: " . $fromDebug . " | TO: whatsapp:" . ($no_wa ?? ''));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("General H-1 Notification Error: " . $e->getMessage());
                }
                
            } elseif ($status === 'selesai') {
                $updateData['waktu_selesai'] = Carbon::now();
                $updateData['id_karyawan'] = session('id_user');
            } elseif ($status === 'menunggu') {
                // Reset jika dikembalikan ke nunggu
                $updateData['waktu_panggil'] = null;
                $updateData['waktu_selesai'] = null;
                $updateData['id_karyawan'] = null;
            }

            $antrian->update($updateData);

            // Ambil data terbaru setelah diupdate untuk dikirim balik ke JavaScript UI
            $antrianTerbaru = Antrian::where('id_antrian', $id)->first();

            return response()->json([
                'success' => true,
                'waktu_panggil' => $antrianTerbaru->waktu_panggil ? Carbon::parse($antrianTerbaru->waktu_panggil)->format('H:i:s') : null,
                'waktu_selesai' => $antrianTerbaru->waktu_selesai ? Carbon::parse($antrianTerbaru->waktu_selesai)->format('H:i:s') : null,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function tolakPrioritas(Request $request, $id)
    {
        try {
            $antrian = Antrian::findOrFail($id);
            $antrian->is_prioritas = 0;
            $antrian->jenis_prioritas = null;
            $antrian->status_validasi_prioritas = 'ditolak';
            $antrian->alasan_penolakan_prioritas = $request->input('alasan', 'Ditolak oleh karyawan loket');
            $antrian->save();

            return back()->with('success', 'Status prioritas berhasil dibatalkan. Pengunjung kembali ke antrean reguler.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
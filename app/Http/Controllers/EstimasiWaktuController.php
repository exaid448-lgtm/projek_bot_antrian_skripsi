<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use App\Models\Loket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class EstimasiWaktuController extends Controller
{
    public function getEstimasiDinamis()
    {
        $hariIni = Carbon::today();
        $daftarLoket = Loket::whereRaw('LOWER(status_pelayanan) = ?', ['buka'])->get();
        $dataEstimasi = [];

        foreach ($daftarLoket as $loket) {
            $id_loket = $loket->id_loket;

            // 1. Ambil nilai rata-rata historis (default 5 menit)
            $rataRataMenit = Antrian::where('id_loket', $id_loket)
                ->where('status_antrian', 'selesai')
                ->selectRaw('AVG(TIME_TO_SEC(TIMEDIFF(waktu_selesai, waktu_panggil))) / 60 as avg_menit')
                ->value('avg_menit');

            $rataRataMenit = $rataRataMenit ? ceil(max(2, $rataRataMenit)) : 5; 

            // 2. Cari antrean yang aktif dipanggil saat ini
            $antrianAktif = Antrian::where('id_loket', $id_loket)
                ->where('status_antrian', 'dipanggil')
                ->whereDate('waktu_voice', $hariIni)
                ->first();

            $tambahanOvertime = 0;

            if ($antrianAktif) {
                // Gunakan waktu_panggil jika tersedia, fallback ke waktu_voice jika NULL
                $waktuMulai = Carbon::parse($antrianAktif->waktu_panggil ?? $antrianAktif->waktu_voice);
                $durasiBerjalanMenit = $waktuMulai->diffInMinutes(Carbon::now());

                if ($durasiBerjalanMenit > $rataRataMenit) {
                    $tambahanOvertime = $durasiBerjalanMenit - $rataRataMenit;
                    
                    // ============================================================
                    // TRIGGER WA NOTIFIKASI: Dipanggil saat terdeteksi overtime
                    // ============================================================
                    $this->kirimNotifikasiWaDelay($antrianAktif, $id_loket);
                }
            } else {
                $antrianPertamaMenunggu = Antrian::where('id_loket', $id_loket)
                    ->where('status_antrian', 'menunggu')
                    ->whereDate('waktu_voice', $hariIni)
                    ->orderBy('id_antrian', 'asc')
                    ->first();

                if ($antrianPertamaMenunggu && $antrianPertamaMenunggu->waktu_voice) {
                    $waktuPembuatanTiket = Carbon::parse($antrianPertamaMenunggu->waktu_voice);
                    $durasiMengantreMenit = $waktuPembuatanTiket->diffInMinutes(Carbon::now());

                    if ($durasiMengantreMenit > $rataRataMenit) {
                        $tambahanOvertime = $durasiMengantreMenit - $rataRataMenit;
                    }
                }
            }

            // 3. Ambil barisan antrean menunggu untuk dikirim ke frontend
            $antreanMenunggu = Antrian::where('id_loket', $id_loket)
                ->where('status_antrian', 'menunggu')
                ->whereDate('waktu_voice', $hariIni)
                ->orderBy('id_antrian', 'asc')
                ->get();

            foreach ($antreanMenunggu as $index => $antrian) {
                $orangDiDepan = $index + ($antrianAktif ? 1 : 0);
                $totalEstimasi = ($orangDiDepan * $rataRataMenit) + $tambahanOvertime;
                
                $nomorDatabase = $antrian->nomor_antrian;
                $nomorFormatStrip = $this->formatKeBentukStrip($nomorDatabase);

                $payload = [
                    'nomor_antrian' => $nomorDatabase,
                    'estimasi_menit' => ceil($totalEstimasi) > 0 ? (int) ceil($totalEstimasi) : (int) $rataRataMenit,
                    'sisa_antrean_depan' => $orangDiDepan
                ];

                $dataEstimasi[$nomorDatabase] = $payload;
                if ($nomorFormatStrip !== $nomorDatabase) {
                    $dataEstimasi[$nomorFormatStrip] = $payload;
                }
            }
        }

        return response()->json([
            'success' => true,
            'estimasi_data' => $dataEstimasi
        ]);
    }

    private function formatKeBentukStrip($nomor)
    {
        if (str_contains($nomor, '-')) { return $nomor; }
        if (preg_match('/^([A-Z]\d+)(\d{3})$/', $nomor, $matches)) { return $matches[1] . '-' . $matches[2]; }
        if (preg_match('/^([A-Z]\d+)(\d+)$/', $nomor, $matches)) { return $matches[1] . '-' . $matches[2]; }
        return $nomor;
    }

    private function kirimNotifikasiWaDelay($antrianAktif, $id_loket)
    {
        // Ambil semua pengunjung berikutnya yang sedang mengantre
        $pengunjungMenunggu = Antrian::where('id_loket', $id_loket)
            ->where('status_antrian', 'menunggu')
            ->whereDate('waktu_voice', Carbon::today())
            ->orderBy('id_antrian', 'asc')
            ->get();

        foreach ($pengunjungMenunggu as $pengunjung) {
            if ($pengunjung && $pengunjung->id_pengunjung) {
                // Mengambil profil untuk mendapatkan nomor wa user yang sedang login/mengantre
                $profil = \App\Models\ProfilPengunjung::find($pengunjung->id_pengunjung);

                if ($profil && $profil->nomor_whatsapp) {
                    $nomorWa = $profil->nomor_whatsapp;
                    
                    // Format nomor WA jika mulai dengan '0' ke format international '62'
                    if (str_starts_with($nomorWa, '0')) {
                        $nomorWa = '62' . substr($nomorWa, 1);
                    }
                    
                    $nomorAntrianTarget = $pengunjung->nomor_antrian;

                    // Mencegah spam kirim WA berulang-ulang dalam waktu 15 menit untuk nomor tiket yang sama
                    $lockKey = 'wa_lock_' . $nomorAntrianTarget;
                    if (!cache()->has($lockKey)) {
                        
                        $namaLoket = $antrianAktif->loket->nama_loket ?? 'Loket Pelayanan';
                        $pesan = "📌 *PEMBERITAHUAN MPP BANJARBARU*\n\n" .
                                 "Halo Sdr/i *" . $profil->nama . "*,\n\n" .
                                 "Mohon maaf, nomor antrean di depan Anda (" . $antrianAktif->nomor_antrian . ") pada *" . $namaLoket . "* memerlukan waktu pelayanan lebih lama dari biasanya.\n\n" .
                                 "Estimasi waktu tunggu untuk nomor Anda (*" . $nomorAntrianTarget . "*) telah otomatis disesuaikan pada sistem monitor.\n\n" .
                                 "Terima kasih atas kesabaran Anda menunggu.";

                        // Mengirim request ke Fonnte menggunakan HTTP Client Laravel
                        // Menggunakan token yang terkonfigurasi di config/services.php (.env)
                        Http::withHeaders([
                            'Authorization' => config('services.fonnte.token'), 
                        ])->post('https://api.fonnte.com/send', [
                            'target' => $nomorWa,
                            'message' => $pesan,
                        ]);

                        // Kunci agar tidak mengirim ulang pesan delay ke orang yang sama selama 15 menit ke depan
                        cache()->put($lockKey, true, now()->addMinutes(15));
                    }
                }
            }
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Loket;

class SKMSeeder extends Seeder
{
    public function run()
    {
        // Find existing pengunjung (id_user=9 is kana as per screenshot, or any)
        $profilPengunjung = DB::table('profil_pengunjung')->first();
        if (!$profilPengunjung) {
            $this->command->info("Tidak ada data di tabel profil_pengunjung.");
            return;
        }
        
        $pengunjung = DB::table('user')->where('id_user', $profilPengunjung->id_user)->first();

        $lokets = Loket::all();

        // Clear existing data (optional, but good to avoid duplicates)
        // DB::table('skm_jawaban')->truncate();
        // DB::table('skm_soal')->truncate();

        $pertanyaanSkm = [
            "Bagaimana pendapat Anda tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?",
            "Bagaimana pemahaman Anda tentang kemudahan prosedur pelayanan di unit ini?",
            "Bagaimana pendapat Anda tentang kecepatan waktu dalam memberikan pelayanan?",
            "Bagaimana pendapat Anda tentang kewajaran biaya/tarif dalam pelayanan?",
            "Bagaimana pendapat Anda tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?",
            "Bagaimana pendapat Anda tentang kompetensi/kemampuan petugas dalam pelayanan?",
            "Bagaimana pendapat Anda tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?",
            "Bagaimana pendapat Anda tentang kualitas sarana dan prasarana?",
            "Bagaimana pendapat Anda tentang penanganan pengaduan pengguna layanan?"
        ];

        $jawabanOptions = ['sangat bagus', 'bagus', 'kurang', 'sangat kurang'];

        foreach ($lokets as $loket) {
            $soalIds = [];
            // Buat Soal untuk loket
            foreach ($pertanyaanSkm as $pertanyaan) {
                $soalId = DB::table('skm_soal')->insertGetId([
                    'id_loket' => $loket->id_loket,
                    'pertanyaan' => $pertanyaan,
                    'is_active' => 1,
                    'created_at' => Carbon::now()
                ]);
                $soalIds[] = $soalId;
            }

            // Buat 10 dummy respon / antrian SKM per loket
            for ($i = 1; $i <= 10; $i++) {
                $antrianId = DB::table('antrian')->insertGetId([
                    'id_loket' => $loket->id_loket,
                    'nomor_antrian' => 'SKM-' . $loket->id_loket . '-' . $i,
                    'id_pengunjung' => $profilPengunjung->id_pengunjung,
                    'waktu_voice' => Carbon::now()->subDays(rand(1, 30)),
                    'waktu_panggil' => Carbon::now()->subDays(rand(1, 30))->addMinutes(10),
                    'waktu_selesai' => Carbon::now()->subDays(rand(1, 30))->addMinutes(25),
                    'jenis_antrian' => 'online',
                    'setatus_pengambilan' => 'online',
                    'status_antrian' => 'selesai',
                ]);

                // Isi jawaban untuk setiap soal SKM
                foreach ($soalIds as $id_soal) {
                    // Beri bobot agar hasilnya variatif (cenderung bagus)
                    $rand = rand(1, 100);
                    if ($rand <= 50) $ans = 'sangat bagus';
                    elseif ($rand <= 85) $ans = 'bagus';
                    elseif ($rand <= 95) $ans = 'kurang';
                    else $ans = 'sangat kurang';

                    DB::table('skm_jawaban')->insert([
                        'id_soal' => $id_soal,
                        'id_loket' => $loket->id_loket,
                        'id_antrain' => $antrianId,
                        'jawaban' => $ans,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
        }

        $this->command->info("Data Soal dan Jawaban SKM berhasil diisi menggunakan user pengunjung: " . $pengunjung->username);
    }
}

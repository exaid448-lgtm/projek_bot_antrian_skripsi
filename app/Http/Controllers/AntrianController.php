<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Algoritma;
use App\Models\VoiceTraining; 
use Carbon\Carbon;
use App\Models\SkmSoal;
use App\Models\SkmJawaban;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk cek mode pengaturan

class AntrianController extends Controller
{
    public function storeFromVoice(Request $request)
    {
        // =========================
        // 1. VALIDASI
        // =========================
        if (!$request->has('loket') || trim($request->loket) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Data suara tidak diterima'
            ], 400);
        }

        // =========================
        // 2. NORMALISASI TEKS
        // =========================
        $kalimat = strtolower($request->loket);
        $kalimat = preg_replace('/[^a-z0-9 ]/', ' ', $kalimat);
        $kalimat = preg_replace('/\b(\w+)( \1\b)+/i', '$1', $kalimat);
        $kalimat = preg_replace('/\s+/', ' ', $kalimat);
        $kalimat = trim($kalimat);

        // ==========================================
        // 3. LOGIKA ENGINE BERDASARKAN MODE
        // ==========================================
        $idLoketFinal = null;
        $engineMode = DB::table('pengaturan')->where('nama_pengaturan', 'engine_mode')->value('nilai_pengaturan') ?? 'hybrid';

        // CEK APAKAH ADA PARAMETER LOKET PILIHAN DARI FRONTEND (MODAL AMBIGU)
        if ($request->has('id_loket_pilihan') && $request->id_loket_pilihan) {
            $idLoketFinal = (int) $request->id_loket_pilihan;
        } else {
            // TAHAP 1: RULE-BASED (Kamus Kata Kunci)
            $matchedAlgorithms = [];
            if ($engineMode === 'hybrid' || $engineMode === 'rule_based') {
                $daftarAlgoritma = Algoritma::where('tipe_layanan', 'layanan')->get();
                foreach ($daftarAlgoritma as $alg) {
                    if (str_contains($kalimat, strtolower(trim($alg->algoritma)))) {
                        $matchedAlgorithms[] = $alg;
                    }
                }
            }

            if (count($matchedAlgorithms) > 0) {
                // Urutkan berdasarkan panjang keyword (terpanjang = paling spesifik)
                usort($matchedAlgorithms, function($a, $b) {
                    return strlen(trim($b->algoritma)) - strlen(trim($a->algoritma));
                });

                $bestMatchKeyword = strtolower(trim($matchedAlgorithms[0]->algoritma));
                
                // Cari semua loket yang memiliki keyword yang sama persis dengan keyword terbaik ini
                $ambiguousLokets = [];
                foreach ($daftarAlgoritma as $alg) {
                    if (strtolower(trim($alg->algoritma)) === $bestMatchKeyword) {
                        // Pastikan tidak duplikat id_loket
                        if (!in_array($alg->id_loket, array_column($ambiguousLokets, 'id_loket'))) {
                            $loketModel = Loket::find($alg->id_loket);
                            if ($loketModel) {
                                $ambiguousLokets[] = [
                                    'id_loket' => $loketModel->id_loket,
                                    'nama_loket' => $loketModel->nama_loket
                                ];
                            }
                        }
                    }
                }

                // Jika ada lebih dari 1 loket untuk keyword yang persis sama, kembalikan opsi
                if (count($ambiguousLokets) > 1) {
                    $namaLokets = array_column($ambiguousLokets, 'nama_loket');
                    $namaLoketsStr = implode(' atau ', $namaLokets);
                    return response()->json([
                        'success' => false,
                        'status' => 'butuh_pilihan',
                        'pilihan_loket' => $ambiguousLokets,
                        'message' => 'Apakah Anda menuju ' . $namaLoketsStr . '?'
                    ]);
                } else {
                    $idLoketFinal = $ambiguousLokets[0]['id_loket'];
                }
            }

            // TAHAP 2: MACHINE LEARNING (Fallback)
            if (!$idLoketFinal && ($engineMode === 'hybrid' || $engineMode === 'ai_only')) {
                $pythonScript = base_path('resources/python_service/python_scripts/predict.py');
                $kalimatEscaped = escapeshellarg($kalimat);
                $command = "python \"$pythonScript\" $kalimatEscaped 2>&1";
                $output = shell_exec($command);
                
                if (str_contains($output, 'SUCCESS|')) {
                    $parts = explode('|', $output);
                    $predictedLoketId = trim($parts[1]);
                    if (is_numeric($predictedLoketId)) {
                        $idLoketFinal = (int) $predictedLoketId;
                    }
                }
            }

            // KOREKSI SKRIPSI: Jika tetap gagal (bahkan setelah AI menebak)
            if (!$idLoketFinal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan belum dikenali. Silakan coba kalimat lain.'
                ], 422);
            }

            // =========================================================================
            // 4. OTOMATIS SIMPAN KE DATASET (Hanya jika suara baru pertama kali diproses)
            // =========================================================================
            VoiceTraining::create([
                'id_loket'          => $idLoketFinal, 
                'teks_transkripsi'  => $kalimat,
                'sumber_data'       => 'pengunjung'
            ]);
        }

        // ==========================================
        // 5. PENENTUAN LOKET FISIK
        // ==========================================
        $loket = Loket::find($idLoketFinal);

        if (!$loket) {
            return response()->json(['success' => false, 'message' => 'Loket tidak ditemukan'], 404);
        }

        // 6. CEK STATUS PELAYANAN
        if ($loket->status_pelayanan === 'TUTUP') {
            return response()->json([
                'success' => false,
                'status_pelayanan' => 'TUTUP',
                'loket' => strtoupper($loket->nama_loket),
                'message' => 'Maaf loket yang anda tuju sedang tutup, jika anda mau melakukan booking antrian bisa melalui link berikut',
                'qr_url' => url('/booking-antrian')
            ]);
        }

        // ==========================================
        // 7. CEK IDENTITAS VISITORS
        // ==========================================
        $idPengunjung = null;
        $jenisAntrian = 'voice';
        $userIdInput = $request->input('id_user');

        if (auth()->check()) {
            $user = auth()->user();
            $profil = \App\Models\ProfilPengunjung::where('id_user', $user->id_user ?? $user->id)->first();
            if ($profil) {
                $idPengunjung = $profil->id_pengunjung;
            }
        }
        
        if ($jenisAntrian === 'voice' && $userIdInput && !in_array($userIdInput, ["", "null", "undefined"])) {
            $profil = \App\Models\ProfilPengunjung::where('id_user', $userIdInput)->first();
            if ($profil) {
                $idPengunjung = $profil->id_pengunjung;
            }
        }

        // ==========================================
        // 8. LOGIKA MODE KHUSUS & PRIORITAS
        // ==========================================
        $isPrioritas = 0;
        $jenisPrioritas = null;

        if ($request->input('mode_khusus') == 1) {
            // INI UNTUK PENGUNJUNG OFFLINE VIA KIOSK (SATPAM MENCET TOMBOL)
            $isPrioritas = 1;
            $jenisPrioritas = $request->input('jenis_prioritas') ?: 'disabilitas_sementara'; 
        } elseif ($idPengunjung && isset($profil)) {
            // INI UNTUK PENGUNJUNG ONLINE (DETEKSI OTOMATIS DARI PROFIL)
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
        }

        // 9. BUAT NOMOR ANTRIAN
        $last = Antrian::where('id_loket', $loket->id_loket)
            ->whereDate('waktu_voice', Carbon::today())
            ->latest('id_antrian')
            ->first();

        $next = 1;
        if ($last && preg_match('/-(\d+)/', $last->nomor_antrian, $m)) {
            $next = (int)$m[1] + 1;
        }
        $nomor = $loket->prefix . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);

        // 10. SIMPAN DATA ANTRIAN FINAL
        Antrian::create([
            'id_loket'            => $loket->id_loket,
            'nomor_antrian'       => $nomor,
            'waktu_voice'         => now(),
            'id_pengunjung'       => $idPengunjung,
            'setatus_pengambilan' => $jenisAntrian, 
            'status_antrian'      => 'menunggu',
            'is_prioritas'        => $isPrioritas,
            'jenis_prioritas'     => $jenisPrioritas
        ]);

        return response()->json([
            'success' => true,
            'loket'   => strtoupper($loket->nama_loket),
            'nomor'   => $nomor,
            'tipe'    => $jenisAntrian
        ]);
    }
    public function monitor()
    {
        // Ambil antrian yang statusnya 'dipanggil' hari ini
        $antrianDipanggil = Antrian::with('loket')
            ->where('status_antrian', 'dipanggil')
            ->whereDate('waktu_voice', Carbon::today())
            ->orderBy('waktu_panggil', 'desc') // Panggilan terbaru di depan
            ->take(6) // Batasi 6 agar tidak berantakan di layar
            ->get();

        return view('pengunjung.monitor', compact('antrianDipanggil'));
    }
    public function cekStatusSkm()
    {
        if (!auth()->check()) {
            return response()->json(['perlu_skm' => false, 'debug' => 'User belum login']);
        }

        $user = auth()->user();
        
        // Ambil relasi profil pengunjung untuk mendapatkan id_pengunjung
        $profil = $user->profil_pengunjung;
        
        if (!$profil || !$profil->id_pengunjung) {
            return response()->json(['perlu_skm' => false, 'debug' => 'Data profil_pengunjung tidak ditemukan untuk user ini']);
        }

        $antrian = Antrian::where('id_pengunjung', $profil->id_pengunjung)
                            ->where('status_antrian', 'selesai')
                            ->whereDate('waktu_voice', \Carbon\Carbon::today())
                            ->whereNotIn('id_antrian', function($query) {
                                $query->select('id_antrain')->from('skm_jawaban')->whereNotNull('id_antrain');
                            })
                            ->orderBy('id_antrian', 'desc')
                            ->first();

        if (!$antrian) {
            return response()->json(['perlu_skm' => false, 'debug' => 'Tidak ada antrean status selesai yang belum isi SKM']);
        }

        $soal = SkmSoal::where('id_loket', $antrian->id_loket)
                                ->where('is_active', 1)
                                ->get();

        if ($soal->isEmpty()) {
            return response()->json(['perlu_skm' => false, 'debug' => 'Soal untuk loket ID '.$antrian->id_loket.' tidak ditemukan atau is_active=0']);
        }

        return response()->json([
            'perlu_skm' => true,
            'id_antrian' => $antrian->id_antrian,
            'id_loket' => $antrian->id_loket,
            'soal' => $soal
        ]);
    }
    public function simpanSkm(Request $request)
    {
        $request->validate([
            'id_antrian' => 'required',
            'id_loket' => 'required',
            'jawaban' => 'required|array',
        ]);
        // Simpan setiap jawaban soal
        foreach ($request->jawaban as $id_soal => $nilai) {
            SkmJawaban::create([
                'id_soal' => $id_soal,
                'id_loket' => $request->id_loket,
                'id_antrain' => $request->id_antrian,
                'jawaban' => $nilai,
            ]);
        }

        // Tidak perlu update is_skm_filled karena kita sekarang ngecek langsung ke tabel skm_jawaban
        return response()->json(['success' => true, 'message' => 'Terima kasih atas penilaian Anda!']);
    }
}
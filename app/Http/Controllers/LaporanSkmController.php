<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loket;
use App\Models\SkmSoal;
use App\Models\SkmJawaban;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanSkmController extends Controller
{
    public function index(Request $request)
    {
        $tglMulai = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        
        // KUNCI OTOMATIS: Jika login sebagai karyawan (punya session id_loket), paksa gunakan session tersebut.
        if (session()->has('id_loket')) {
            $filterLoket = session('id_loket');
        } else {
            // Jika login sebagai Super Admin, baru boleh ambil dari request dropdown
            $filterLoket = $request->input('id_loket_filter'); 
        }

        // 1. Fetch all lokets for dropdown (Hanya untuk Admin Utama)
        $lokets = Loket::all();

        // 2. Fetch answers matching the filters
        $jawabanQuery = SkmJawaban::query();
        if ($tglMulai) {
            $jawabanQuery->whereDate('created_at', '>=', $tglMulai);
        }
        if ($tglSelesai) {
            $jawabanQuery->whereDate('created_at', '<=', $tglSelesai);
        }
        if ($filterLoket) {
            $jawabanQuery->where('id_loket', $filterLoket);
        }

        // Calculate total respondents (unique antrian/id_antrain)
        $totalResponden = $jawabanQuery->clone()->distinct('id_antrain')->count('id_antrain');

        // Calculate average points
        $answers = $jawabanQuery->clone()->get();
        $scoreSum = 0;
        $totalAnswersCount = $answers->count();
        foreach ($answers as $ans) {
            $scoreSum += match($ans->jawaban) {
                'sangat bagus' => 4,
                'bagus' => 3,
                'kurang' => 2,
                'sangat kurang' => 1,
                default => 0
            };
        }
        $rataRataPoin = $totalAnswersCount > 0 ? round($scoreSum / $totalAnswersCount, 2) : 0.0;

        // Determine general predicate
        if ($rataRataPoin >= 3.53) {
            $predikatUmum = 'Sangat Baik (Mutu A)';
            $predikatWarna = 'limegreen';
        } elseif ($rataRataPoin >= 3.06) {
            $predikatUmum = 'Baik (Mutu B)';
            $predikatWarna = '#3182ce';
        } elseif ($rataRataPoin >= 2.60) {
            $predikatUmum = 'Kurang Baik (Mutu C)';
            $predikatWarna = 'orange';
        } else {
            $predikatUmum = $totalResponden > 0 ? 'Tidak Baik (Mutu D)' : 'Belum Ada Penilaian';
            $predikatWarna = 'red';
        }

        // 3. Compile chart data (Dynamic Years)
        $filterYears = $request->input('filter_years');
        if (!$filterYears || !is_array($filterYears)) {
            // Default 3 Tahun
            $currentYear = date('Y');
            $filterYears = [(string)($currentYear - 2), (string)($currentYear - 1), (string)$currentYear];
        }
        sort($filterYears);

        $monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $chartLabels = $monthsIndo;
        $chartDatasets = [];
        $tableRincianData = [];

        foreach ($monthsIndo as $m) {
            $tableRincianData[$m] = [];
            foreach ($filterYears as $y) {
                $tableRincianData[$m][$y] = 0;
            }
        }

        foreach ($filterYears as $year) {
            $monthlyData = array_fill(1, 12, 0);

            $dbMonthlyQuery = SkmJawaban::select(
                DB::raw('MONTH(created_at) as month'),
                'jawaban'
            );
            if ($filterLoket) {
                $dbMonthlyQuery->where('id_loket', $filterLoket);
            }
            $dbMonthlyQuery->whereYear('created_at', $year);
            $allYearAnswers = $dbMonthlyQuery->get()->groupBy('month');

            foreach ($allYearAnswers as $monthNum => $mAnswers) {
                $mScoreSum = 0;
                $mCount = $mAnswers->count();
                foreach ($mAnswers as $ans) {
                    $mScoreSum += match($ans->jawaban) {
                        'sangat bagus' => 4,
                        'bagus' => 3,
                        'kurang' => 2,
                        'sangat kurang' => 1,
                        default => 0
                    };
                }
                $mAvg = $mCount > 0 ? round($mScoreSum / $mCount, 2) : 0;
                $monthlyData[$monthNum] = $mAvg;
                
                if (isset($monthsIndo[$monthNum - 1])) {
                    $tableRincianData[$monthsIndo[$monthNum - 1]][$year] = $mAvg;
                }
            }

            $chartDatasets[] = [
                'label' => 'Tahun ' . $year,
                'data' => array_values($monthlyData)
            ];
        }

        // 4. Fetch answers grouped by queue (id_antrain) for table detail
        $distinctAntrianQuery = SkmJawaban::query();
        if ($tglMulai) {
            $distinctAntrianQuery->whereDate('created_at', '>=', $tglMulai);
        }
        if ($tglSelesai) {
            $distinctAntrianQuery->whereDate('created_at', '<=', $tglSelesai);
        }
        if ($filterLoket) {
            $distinctAntrianQuery->where('id_loket', $filterLoket);
        }
        
        $distinctAntrianIds = $distinctAntrianQuery->distinct('id_antrain')->pluck('id_antrain');
        
        $antrianAnswers = SkmJawaban::with(['antrian', 'loket'])
            ->whereIn('id_antrain', $distinctAntrianIds)
            ->get()
            ->groupBy('id_antrain');

        $tableData = [];
        foreach ($antrianAnswers as $idAntrian => $mAnswers) {
            $firstAnswer = $mAnswers->first();
            $nomorAntrian = $firstAnswer->antrian->nomor_antrian ?? '-';
            $namaLoket = $firstAnswer->loket->nama_loket ?? '-';
            $tanggalPengisian = $firstAnswer->created_at ? $firstAnswer->created_at->format('d-m-Y H:i') : '-';
            
            $scoreSum = 0;
            $count = $mAnswers->count();
            foreach ($mAnswers as $ans) {
                $scoreSum += match($ans->jawaban) {
                    'sangat bagus' => 4,
                    'bagus' => 3,
                    'kurang' => 2,
                    'sangat kurang' => 1,
                    default => 0
                };
            }
            $avgScore = $count > 0 ? round($scoreSum / $count, 2) : 0;

            if ($avgScore >= 3.53) {
                $badgeClass = 'skm-badge-success';
                $predikat = 'Sangat Baik';
            } elseif ($avgScore >= 3.06) {
                $badgeClass = 'skm-badge-success';
                $predikat = 'Baik';
            } elseif ($avgScore >= 2.60) {
                $badgeClass = 'skm-badge-warning';
                $predikat = 'Kurang Baik';
            } else {
                $badgeClass = 'skm-badge-warning';
                $predikat = $count > 0 ? 'Tidak Baik' : 'Belum Ada Penilaian';
            }

            $tableData[] = [
                'nomor_antrian' => $nomorAntrian,
                'loket' => $namaLoket,
                'tanggal' => $tanggalPengisian,
                'avg_score' => number_format($avgScore, 2),
                'badge_class' => $badgeClass,
                'predikat' => $predikat
            ];
        }

        // KUNCI DATA SOAL: Jika login sebagai karyawan, hanya panggil daftar soal milik loketnya sendiri
        $pertanyaanQuery = SkmSoal::query();
        if ($filterLoket) {
            $pertanyaanQuery->where('id_loket', $filterLoket);
        }
        $pertanyaan = $pertanyaanQuery->get();

        return view('admin_loket.laporan_skm', compact(
            'lokets',
            'totalResponden',
            'rataRataPoin',
            'predikatUmum',
            'predikatWarna',
            'chartLabels',
            'chartDatasets',
            'tableRincianData',
            'filterYears',
            'tableData',
            'tglMulai',
            'tglSelesai',
            'filterLoket',
            'pertanyaan'
        ));
    }

    public function edit($id)
    {
        $soal = SkmSoal::findOrFail($id);

        // Proteksi Tambahan: Karyawan dilarang mengubah status soal loket lain
        if (session()->has('id_loket') && $soal->id_loket != session('id_loket')) {
            return redirect()->back()->with('error', 'Akses ditolak! Anda tidak berwenang mengelola soal loket lain.');
        }

        $statusBaru = $soal->is_active == 1 ? 0 : 1;
        SkmSoal::where('id_soal', $id)->update([
            'is_active' => $statusBaru
        ]);

        return redirect()->back()->with('success', 'Status pertanyaan SKM berhasil diperbarui!');
    }

    public function delete($id)
    {
        $soal = SkmSoal::findOrFail($id);
        
        // Proteksi Tambahan: Karyawan dilarang menghapus soal loket lain
        if (session()->has('id_loket') && $soal->id_loket != session('id_loket')) {
            return redirect()->back()->with('error', 'Akses ditolak! Anda tidak berwenang menghapus soal loket lain.');
        }

        $soal->delete();
        return redirect()->back()->with('success', 'Pertanyaan SKM berhasil dihapus!');
    }
    
    public function storeSoal(Request $request)
    {
        $id_loket = session('id_loket') ?? $request->input('id_loket');
        $request->merge(['id_loket' => $id_loket]);

        $request->validate([
            'id_loket' => 'required|exists:loket,id_loket',
            'pertanyaan' => 'required|string',
            'is_active' => 'required|in:0,1',
        ]);

        SkmSoal::create([
            'id_loket' => $request->id_loket,
            'pertanyaan' => $request->pertanyaan,
            'is_active' => $request->is_active,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Soal SKM baru berhasil ditambahkan!');
    }

    public function cetak(Request $request)
    {
        $id_profil = session('id_profil');
        $profil = \App\Models\Profil::with('loket')->where('id_profil', $id_profil)->first();

        $tglMulai = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        
        // KUNCI DATA CETAK: Sinkronisasi aturan multi-tenant loket karyawan
        if (session()->has('id_loket')) {
            $filterLoket = session('id_loket');
        } else {
            $filterLoket = $request->input('id_loket_filter');
        }

        $loketNama = 'Semua Loket';
        if ($filterLoket) {
            $l = Loket::find($filterLoket);
            if ($l) {
                $loketNama = $l->nama_loket;
            }
        }

        $jawabanQuery = SkmJawaban::query();
        if ($tglMulai) {
            $jawabanQuery->whereDate('created_at', '>=', $tglMulai);
        }
        if ($tglSelesai) {
            $jawabanQuery->whereDate('created_at', '<=', $tglSelesai);
        }
        if ($filterLoket) {
            $jawabanQuery->where('id_loket', $filterLoket);
        }

        $totalResponden = $jawabanQuery->clone()->distinct('id_antrain')->count('id_antrain');

        $answers = $jawabanQuery->clone()->get();
        $scoreSum = 0;
        $totalAnswersCount = $answers->count();
        foreach ($answers as $ans) {
            $scoreSum += match($ans->jawaban) {
                'sangat bagus' => 4,
                'bagus' => 3,
                'kurang' => 2,
                'sangat kurang' => 1,
                default => 0
            };
        }
        $rataRataPoin = $totalAnswersCount > 0 ? round($scoreSum / $totalAnswersCount, 2) : 0.0;

        if ($rataRataPoin >= 3.53) {
            $predikatUmum = 'Sangat Baik (Mutu A)';
        } elseif ($rataRataPoin >= 3.06) {
            $predikatUmum = 'Baik (Mutu B)';
        } elseif ($rataRataPoin >= 2.60) {
            $predikatUmum = 'Kurang Baik (Mutu C)';
        } else {
            $predikatUmum = $totalResponden > 0 ? 'Tidak Baik (Mutu D)' : 'Belum Ada Penilaian';
        }

        // --- Compile chart data (Dynamic Years) untuk PDF ---
        $filterYears = $request->input('filter_years');
        if (!$filterYears || !is_array($filterYears)) {
            $currentYear = date('Y');
            $filterYears = [(string)($currentYear - 2), (string)($currentYear - 1), (string)$currentYear];
        }
        sort($filterYears);

        $monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $chartLabels = $monthsIndo;
        $chartDatasets = [];
        $tableRincianData = [];

        foreach ($monthsIndo as $m) {
            $tableRincianData[$m] = [];
            foreach ($filterYears as $y) {
                $tableRincianData[$m][$y] = 0;
            }
        }

        foreach ($filterYears as $year) {
            $monthlyData = array_fill(1, 12, 0);

            $dbMonthlyQuery = SkmJawaban::select(
                DB::raw('MONTH(created_at) as month'),
                'jawaban'
            );
            if ($filterLoket) {
                $dbMonthlyQuery->where('id_loket', $filterLoket);
            }
            $dbMonthlyQuery->whereYear('created_at', $year);
            $allYearAnswers = $dbMonthlyQuery->get()->groupBy('month');

            foreach ($allYearAnswers as $monthNum => $mAnswers) {
                $mScoreSum = 0;
                $mCount = $mAnswers->count();
                foreach ($mAnswers as $ans) {
                    $mScoreSum += match($ans->jawaban) {
                        'sangat bagus' => 4,
                        'bagus' => 3,
                        'kurang' => 2,
                        'sangat kurang' => 1,
                        default => 0
                    };
                }
                $mAvg = $mCount > 0 ? round($mScoreSum / $mCount, 2) : 0;
                $monthlyData[$monthNum] = $mAvg;
                
                if (isset($monthsIndo[$monthNum - 1])) {
                    $tableRincianData[$monthsIndo[$monthNum - 1]][$year] = $mAvg;
                }
            }

            $chartDatasets[] = [
                'label' => 'Tahun ' . $year,
                'data' => array_values($monthlyData)
            ];
        }
        // ----------------------------------------------------

        $distinctAntrianQuery = SkmJawaban::query();
        if ($tglMulai) {
            $distinctAntrianQuery->whereDate('created_at', '>=', $tglMulai);
        }
        if ($tglSelesai) {
            $distinctAntrianQuery->whereDate('created_at', '<=', $tglSelesai);
        }
        if ($filterLoket) {
            $distinctAntrianQuery->where('id_loket', $filterLoket);
        }
        
        $distinctAntrianIds = $distinctAntrianQuery->distinct('id_antrain')->pluck('id_antrain');
        
        $antrianAnswers = SkmJawaban::with(['antrian', 'loket'])
            ->whereIn('id_antrain', $distinctAntrianIds)
            ->get()
            ->groupBy('id_antrain');

        $tableData = [];
        foreach ($antrianAnswers as $idAntrian => $mAnswers) {
            $firstAnswer = $mAnswers->first();
            $nomorAntrian = $firstAnswer->antrian->nomor_antrian ?? '-';
            $namaLoket = $firstAnswer->loket->nama_loket ?? '-';
            $tanggalPengisian = $firstAnswer->created_at ? $firstAnswer->created_at->format('d-m-Y H:i') : '-';
            
            $scoreSum = 0;
            $count = $mAnswers->count();
            foreach ($mAnswers as $ans) {
                $scoreSum += match($ans->jawaban) {
                    'sangat bagus' => 4,
                    'bagus' => 3,
                    'kurang' => 2,
                    'sangat kurang' => 1,
                    default => 0
                };
            }
            $avgScore = $count > 0 ? round($scoreSum / $count, 2) : 0;

            if ($avgScore >= 3.53) {
                $badgeClass = 'skm-badge-success';
                $predikat = 'Sangat Baik';
            } elseif ($avgScore >= 3.06) {
                $badgeClass = 'skm-badge-success';
                $predikat = 'Baik';
            } elseif ($avgScore >= 2.60) {
                $badgeClass = 'skm-badge-warning';
                $predikat = 'Kurang Baik';
            } else {
                $badgeClass = 'skm-badge-warning';
                $predikat = $count > 0 ? 'Tidak Baik' : 'Belum Ada Penilaian';
            }

            $tableData[] = [
                'nomor_antrian' => $nomorAntrian,
                'loket' => $namaLoket,
                'tanggal' => $tanggalPengisian,
                'avg_score' => number_format($avgScore, 2),
                'badge_class' => $badgeClass,
                'predikat' => $predikat
            ];
        }

        return view('pdf.laporan_hasil_skm', compact(
            'tglMulai',
            'tglSelesai',
            'loketNama',
            'totalResponden',
            'rataRataPoin',
            'predikatUmum',
            'tableData',
            'profil',
            'chartLabels',
            'chartDatasets',
            'tableRincianData',
            'filterYears'
        ));
    }
}
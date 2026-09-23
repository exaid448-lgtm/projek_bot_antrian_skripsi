<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loket;
use App\Models\SkmJawaban;
use Illuminate\Support\Facades\DB;

class DataSkmController extends Controller
{
    public function index(Request $request)
    {
        // 0. Ambil Filter Tahun (Global)
        $filterYears = $request->input('filter_years');
        if (!$filterYears || !is_array($filterYears)) {
            $currentYear = date('Y');
            $filterYears = [(string)($currentYear - 2), (string)($currentYear - 1), (string)$currentYear];
        }
        sort($filterYears);

        // 1. Ambil semua loket untuk dropdown
        $allLokets = Loket::all();

        // 1.5 Ambil filter Loket
        $selectedLoketId = $request->input('id_loket');
        // Default ke 'all' jika tidak ada yang dipilih
        if (!$selectedLoketId) {
            $selectedLoketId = 'all';
        }

        if ($selectedLoketId !== 'all') {
            $loketsToAnalyze = Loket::where('id_loket', $selectedLoketId)->get();
        } else {
            $loketsToAnalyze = $allLokets;
        }

        // 2. Statistik global/terfilter
        $globalQuery = SkmJawaban::whereIn(DB::raw('YEAR(created_at)'), $filterYears);
        if ($selectedLoketId !== 'all') {
            $globalQuery->where('id_loket', $selectedLoketId);
        }

        $totalRespondenAll = (clone $globalQuery)->distinct('id_antrain')->count('id_antrain');
        
        $allAnswers = $globalQuery->get();
        $totalAnswersCount = $allAnswers->count();
        $allScoreSum = 0;
        foreach ($allAnswers as $ans) {
            $allScoreSum += match($ans->jawaban) {
                'sangat bagus' => 4,
                'bagus' => 3,
                'kurang' => 2,
                'sangat kurang' => 1,
                default => 0
            };
        }
        $avgScoreAll = $totalAnswersCount > 0 ? round($allScoreSum / $totalAnswersCount, 2) : 0.00;

        // 3. Hitung rata-rata SKM untuk setiap loket (diagram batang)
        $barQuery = SkmJawaban::select('id_loket', DB::raw('
            AVG(CASE jawaban 
                WHEN "sangat bagus" THEN 4 
                WHEN "bagus" THEN 3 
                WHEN "kurang" THEN 2 
                WHEN "sangat kurang" THEN 1 
                ELSE 0 
            END) as avg_score
        '))
        ->whereIn(DB::raw('YEAR(created_at)'), $filterYears);
        
        if ($selectedLoketId !== 'all') {
            $barQuery->where('id_loket', $selectedLoketId);
        }

        $loketScores = $barQuery->groupBy('id_loket')
        ->get()
        ->keyBy('id_loket');

        $loketSummaryGroups = [];
        foreach ($loketsToAnalyze as $loket) {
            $scoreObj = $loketScores->get($loket->id_loket);
            $avg = $scoreObj ? floatval($scoreObj->avg_score) : 0.00;
            $respCount = SkmJawaban::where('id_loket', $loket->id_loket)
                ->whereIn(DB::raw('YEAR(created_at)'), $filterYears)
                ->distinct('id_antrain')
                ->count('id_antrain');
            
            if (!isset($loketSummaryGroups[$loket->nama_loket])) {
                $loketSummaryGroups[$loket->nama_loket] = [
                    'nama_loket' => $loket->nama_loket,
                    'sum_score' => 0,
                    'total_responden' => 0
                ];
            }
            if ($respCount > 0) {
                $loketSummaryGroups[$loket->nama_loket]['sum_score'] += ($avg * $respCount);
                $loketSummaryGroups[$loket->nama_loket]['total_responden'] += $respCount;
            }
        }

        $loketSummary = [];
        foreach ($loketSummaryGroups as $group) {
            $avg = $group['total_responden'] > 0 ? round($group['sum_score'] / $group['total_responden'], 2) : 0.00;
            $loketSummary[] = [
                'nama_loket' => $group['nama_loket'],
                'avg_score' => $avg,
                'total_responden' => $group['total_responden']
            ];
        }

        // Urutkan berdasarkan nilai rata-rata tertinggi ke terendah
        usort($loketSummary, function ($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });

        // Loket terbaik
        $bestLoket = !empty($loketSummary) && $loketSummary[0]['avg_score'] > 0 ? $loketSummary[0] : null;

        // 4. Fokus Detail per Loket / Semua Loket (interaktif)
        $selectedLoket = ($selectedLoketId !== 'all') ? Loket::find($selectedLoketId) : null;
        
        $breakdown = [
            'sangat_bagus' => 0,
            'bagus' => 0,
            'kurang' => 0,
            'sangat_kurang' => 0,
        ];
        $totalRespondenSelected = 0;
        $avgScoreSelected = 0.00;

        $detailQuery = SkmJawaban::whereIn(DB::raw('YEAR(created_at)'), $filterYears);
        if ($selectedLoketId !== 'all') {
            $detailQuery->where('id_loket', $selectedLoketId);
        }
        
        $selectedAnswers = $detailQuery->get();
        $totalResponSelectedAnswers = $selectedAnswers->count();
        
        $totalRespondenSelected = (clone $detailQuery)->distinct('id_antrain')->count('id_antrain');

        $scoreSumSelected = 0;
        foreach ($selectedAnswers as $ans) {
                $val = match($ans->jawaban) {
                    'sangat bagus' => 4,
                    'bagus' => 3,
                    'kurang' => 2,
                    'sangat kurang' => 1,
                    default => 0
                };
                $scoreSumSelected += $val;

                if ($ans->jawaban == 'sangat bagus') $breakdown['sangat_bagus']++;
                elseif ($ans->jawaban == 'bagus') $breakdown['bagus']++;
                elseif ($ans->jawaban == 'kurang') $breakdown['kurang']++;
                elseif ($ans->jawaban == 'sangat kurang') $breakdown['sangat_kurang']++;
            }
            $avgScoreSelected = $totalResponSelectedAnswers > 0 ? round($scoreSumSelected / $totalResponSelectedAnswers, 2) : 0.00;

        // Tren Bulanan Multi-Tahun untuk loket terpilih
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

            $trendQuery = SkmJawaban::whereYear('created_at', $year);
            if ($selectedLoketId !== 'all') {
                $trendQuery->where('id_loket', $selectedLoketId);
            }

            $dbMonthly = $trendQuery->select(DB::raw('MONTH(created_at) as month'), 'jawaban')
                ->get()
                ->groupBy('month');

            foreach ($dbMonthly as $monthNum => $mAnswers) {
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

        return view('administrator.data_skm_perloket', compact(
            'allLokets',
            'totalRespondenAll',
            'avgScoreAll',
            'loketSummary',
            'bestLoket',
            'selectedLoketId',
            'selectedLoket',
            'breakdown',
            'totalRespondenSelected',
            'avgScoreSelected',
            'chartLabels',
            'chartDatasets',
            'tableRincianData',
            'filterYears'
        ));
    }

    public function cetakGlobal(Request $request)
    {
        $id_profil = session('id_profil');
        $profil = \App\Models\Profil::with('loket')->where('id_profil', $id_profil)->first();

        // 0. Ambil array filter tahun, default tahun ini
        $filterYearsRaw = $request->input('filter_years');
        if (!empty($filterYearsRaw) && is_array($filterYearsRaw)) {
            $filterYears = array_map('intval', $filterYearsRaw);
        } else {
            $filterYears = [(int)date('Y')];
        }
        sort($filterYears);

        // 1. Ambil semua loket untuk dropdown
        $allLokets = \App\Models\Loket::all();

        // 1.5 Ambil filter Loket
        $selectedLoketId = $request->input('id_loket');
        // Default ke 'all' jika tidak ada yang dipilih
        if (!$selectedLoketId) {
            $selectedLoketId = 'all';
        }

        if ($selectedLoketId !== 'all') {
            $loketsToAnalyze = \App\Models\Loket::where('id_loket', $selectedLoketId)->get();
        } else {
            $loketsToAnalyze = $allLokets;
        }

        // 2. Statistik global/terfilter
        $globalQuery = \App\Models\SkmJawaban::whereIn(\Illuminate\Support\Facades\DB::raw('YEAR(created_at)'), $filterYears);
        if ($selectedLoketId !== 'all') {
            $globalQuery->where('id_loket', $selectedLoketId);
        }

        $totalRespondenAll = (clone $globalQuery)->distinct('id_antrain')->count('id_antrain');
        
        $allAnswers = $globalQuery->get();
        $totalAnswersCount = $allAnswers->count();
        $allScoreSum = 0;
        foreach ($allAnswers as $ans) {
            $allScoreSum += match($ans->jawaban) {
                'sangat bagus' => 4,
                'bagus' => 3,
                'kurang' => 2,
                'sangat kurang' => 1,
                default => 0
            };
        }
        $avgScoreAll = $totalAnswersCount > 0 ? round($allScoreSum / $totalAnswersCount, 2) : 0.00;

        // 3. Hitung rata-rata SKM untuk setiap loket (diagram batang)
        $barQuery = \App\Models\SkmJawaban::select('id_loket', \Illuminate\Support\Facades\DB::raw('
            AVG(CASE jawaban 
                WHEN "sangat bagus" THEN 4 
                WHEN "bagus" THEN 3 
                WHEN "kurang" THEN 2 
                WHEN "sangat kurang" THEN 1 
                ELSE 0 
            END) as avg_score
        '))
        ->whereIn(\Illuminate\Support\Facades\DB::raw('YEAR(created_at)'), $filterYears);
        
        if ($selectedLoketId !== 'all') {
            $barQuery->where('id_loket', $selectedLoketId);
        }

        $loketScores = $barQuery->groupBy('id_loket')
        ->get()
        ->keyBy('id_loket');

        $loketSummaryGroups = [];
        foreach ($loketsToAnalyze as $loket) {
            $scoreObj = $loketScores->get($loket->id_loket);
            $avg = $scoreObj ? floatval($scoreObj->avg_score) : 0.00;
            $respCount = \App\Models\SkmJawaban::where('id_loket', $loket->id_loket)
                ->whereIn(\Illuminate\Support\Facades\DB::raw('YEAR(created_at)'), $filterYears)
                ->distinct('id_antrain')
                ->count('id_antrain');
            
            if (!isset($loketSummaryGroups[$loket->nama_loket])) {
                $loketSummaryGroups[$loket->nama_loket] = [
                    'nama_loket' => $loket->nama_loket,
                    'sum_score' => 0,
                    'total_responden' => 0
                ];
            }
            if ($respCount > 0) {
                $loketSummaryGroups[$loket->nama_loket]['sum_score'] += ($avg * $respCount);
                $loketSummaryGroups[$loket->nama_loket]['total_responden'] += $respCount;
            }
        }

        $loketSummary = [];
        foreach ($loketSummaryGroups as $group) {
            $avg = $group['total_responden'] > 0 ? round($group['sum_score'] / $group['total_responden'], 2) : 0.00;
            $loketSummary[] = [
                'nama_loket' => $group['nama_loket'],
                'avg_score' => $avg,
                'total_responden' => $group['total_responden']
            ];
        }

        // Urutkan berdasarkan nilai rata-rata tertinggi ke terendah
        usort($loketSummary, function ($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });

        // Loket terbaik
        $bestLoket = !empty($loketSummary) && $loketSummary[0]['avg_score'] > 0 ? $loketSummary[0] : null;

        // 4. Fokus Detail per Loket / Semua Loket (interaktif)
        $selectedLoket = ($selectedLoketId !== 'all') ? \App\Models\Loket::find($selectedLoketId) : null;
        
        $breakdown = [
            'sangat_bagus' => 0,
            'bagus' => 0,
            'kurang' => 0,
            'sangat_kurang' => 0,
        ];
        $totalRespondenSelected = 0;
        $avgScoreSelected = 0.00;

        $detailQuery = \App\Models\SkmJawaban::whereIn(\Illuminate\Support\Facades\DB::raw('YEAR(created_at)'), $filterYears);
        if ($selectedLoketId !== 'all') {
            $detailQuery->where('id_loket', $selectedLoketId);
        }
        
        $selectedAnswers = $detailQuery->get();
        $totalResponSelectedAnswers = $selectedAnswers->count();
        
        $totalRespondenSelected = (clone $detailQuery)->distinct('id_antrain')->count('id_antrain');

        $scoreSumSelected = 0;
        foreach ($selectedAnswers as $ans) {
                $val = match($ans->jawaban) {
                    'sangat bagus' => 4,
                    'bagus' => 3,
                    'kurang' => 2,
                    'sangat kurang' => 1,
                    default => 0
                };
                $scoreSumSelected += $val;

                if ($ans->jawaban == 'sangat bagus') $breakdown['sangat_bagus']++;
                elseif ($ans->jawaban == 'bagus') $breakdown['bagus']++;
                elseif ($ans->jawaban == 'kurang') $breakdown['kurang']++;
                elseif ($ans->jawaban == 'sangat kurang') $breakdown['sangat_kurang']++;
            }
            $avgScoreSelected = $totalResponSelectedAnswers > 0 ? round($scoreSumSelected / $totalResponSelectedAnswers, 2) : 0.00;

        // Tren Bulanan Multi-Tahun untuk loket terpilih
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

            $trendQuery = \App\Models\SkmJawaban::whereYear('created_at', $year);
            if ($selectedLoketId !== 'all') {
                $trendQuery->where('id_loket', $selectedLoketId);
            }

            $dbMonthly = $trendQuery->select(\Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month'), 'jawaban')
                ->get()
                ->groupBy('month');

            foreach ($dbMonthly as $monthNum => $mAnswers) {
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

        $tipeCetak = $request->input('tipe_cetak', 'semua');

        return view('pdf.laporan_skm_global', compact(
            'profil',
            'allLokets',
            'totalRespondenAll',
            'avgScoreAll',
            'loketSummary',
            'bestLoket',
            'selectedLoketId',
            'selectedLoket',
            'breakdown',
            'totalRespondenSelected',
            'avgScoreSelected',
            'chartLabels',
            'chartDatasets',
            'tableRincianData',
            'filterYears',
            'tipeCetak'
        ));
    }
}

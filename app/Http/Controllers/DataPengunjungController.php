<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilPengunjung;
use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Profil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DataPengunjungController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data filter & pencarian untuk tabel
        $search = $request->input('q');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $year = $request->input('year');
        $id_loket = $request->input('id_loket');
        $tipe_laporan = $request->input('tipe_laporan', 'semua');

        // Ambil data loket untuk dropdown filter
        $lokets = Loket::orderBy('nama_loket', 'asc')->get();

        // 2. Query Pengunjung
        $query = ProfilPengunjung::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_whatsapp', 'like', "%{$search}%");
            });
        }

        // Terapkan filter relasi antrian jika ada filter aktif (tipe_laporan tidak memfilter database records)
        if ($start_date || $end_date || $year || $id_loket) {
            $query->whereHas('antrian', function($q) use ($start_date, $end_date, $year, $id_loket) {
                if ($start_date) {
                    $q->whereDate('waktu_voice', '>=', $start_date);
                }
                if ($end_date) {
                    $q->whereDate('waktu_voice', '<=', $end_date);
                }
                if ($year && !$start_date && !$end_date) {
                    $q->whereYear('waktu_voice', $year);
                }
                if ($id_loket) {
                    $q->where('id_loket', $id_loket);
                }
            });
        }
        
        // Ambil pengunjung terfilter
        $pengunjungs = $query->orderBy('nama', 'asc')->get();

        // 3. Query Antrian untuk Statistik Utama
        $antrianQuery = Antrian::query();
        if ($start_date) {
            $antrianQuery->whereDate('waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $antrianQuery->whereDate('waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $antrianQuery->whereYear('waktu_voice', $year);
        }
        if ($id_loket) {
            $antrianQuery->where('id_loket', $id_loket);
        }

        $totalAntrean = (clone $antrianQuery)->count();
        $onlineCount = (clone $antrianQuery)->where('setatus_pengambilan', 'online')->count();
        $offlineCount = (clone $antrianQuery)->where('setatus_pengambilan', 'offline')->count(); // Legacy
        $manualCount = (clone $antrianQuery)->whereIn('setatus_pengambilan', ['manual', 'offline'])->count(); // Gabung legacy offline ke manual
        $voiceCount = (clone $antrianQuery)->where('setatus_pengambilan', 'voice')->count();

        // Hitung status antrean
        $statusCounts = (clone $antrianQuery)
            ->select('status_antrian', DB::raw('count(*) as total'))
            ->groupBy('status_antrian')
            ->get()
            ->keyBy('status_antrian');

        $selesaiCount = isset($statusCounts['selesai']) ? $statusCounts['selesai']->total : 0;
        $batalCount = isset($statusCounts['batal']) ? $statusCounts['batal']->total : 0;
        $menungguCount = isset($statusCounts['menunggu']) ? $statusCounts['menunggu']->total : 0;
        $dipanggilCount = isset($statusCounts['dipanggil']) ? $statusCounts['dipanggil']->total : 0;
        $activeQueueCount = $menungguCount + $dipanggilCount;

        // Total Pengunjung yang beraktivitas sesuai filter (atau total jika tidak ada filter)
        $totalPengunjung = $pengunjungs->count();

        // 4. Klasifikasi Umur (Remaja: <= 18, Dewasa: 19 - 59, Lansia: >= 60)
        $ageGroups = [
            'remaja' => 0,
            'dewasa' => 0,
            'lansia' => 0
        ];
        
        foreach ($pengunjungs as $p) {
            if ($p->tanggal_lahir) {
                $age = Carbon::parse($p->tanggal_lahir)->age;
                if ($age <= 18) {
                    $ageGroups['remaja']++;
                } elseif ($age >= 60) {
                    $ageGroups['lansia']++;
                } else {
                    $ageGroups['dewasa']++;
                }
            }
        }

        // Determine active month and year for charts (default to current month/year, or extract from filters)
        $activeMonth = date('m');
        $activeYear = date('Y');

        if ($start_date) {
            $activeMonth = date('m', strtotime($start_date));
            $activeYear = date('Y', strtotime($start_date));
        } elseif ($year) {
            $activeYear = $year;
            if ($activeYear != date('Y')) {
                $activeMonth = '01'; // Default to January for other years
            }
        }

        $daysInMonth = Carbon::create($activeYear, $activeMonth, 1)->daysInMonth;

        // Indonesian months array for title
        $monthsIndoList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $activeMonthName = $monthsIndoList[sprintf('%02d', $activeMonth)];
        $chartTitlePeriod = $activeMonthName . ' ' . $activeYear;

        // 5. Kepadatan Pengunjung per Loket per Hari (Line Chart / Single Loket Bar Chart)
        $isSingleLoket = !empty($id_loket);
        $singleLoketName = '';
        $chartLabels = [];
        $chartDatasets = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartLabels[] = $d;
        }

        if ($isSingleLoket) {
            $loketObj = Loket::find($id_loket);
            $singleLoketName = $loketObj ? $loketObj->nama_loket : '';

            $monthlyQuery = Antrian::where('id_loket', $id_loket)
                ->whereYear('waktu_voice', $activeYear)
                ->whereMonth('waktu_voice', $activeMonth);
            
            $dbDaily = $monthlyQuery->select(DB::raw('DAY(waktu_voice) as day'), DB::raw('count(*) as total'))
                ->groupBy('day')
                ->get()
                ->keyBy('day');
                
            $singleData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $singleData[] = isset($dbDaily[$d]) ? intval($dbDaily[$d]->total) : 0;
            }

            $chartDatasets[] = [
                'label' => $singleLoketName ?: 'Kunjungan',
                'data' => $singleData
            ];
        } else {
            $activeLokets = Loket::all();
            
            $densityQuery = DB::table('antrian')
                ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
                ->whereYear('antrian.waktu_voice', $activeYear)
                ->whereMonth('antrian.waktu_voice', $activeMonth);

            $dbDensityDaily = $densityQuery->select(
                    'antrian.id_loket',
                    'loket.nama_loket',
                    DB::raw('DAY(antrian.waktu_voice) as day'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('antrian.id_loket', 'loket.nama_loket', 'day')
                ->get();
                
            $loketDailyData = [];
            foreach ($activeLokets as $loket) {
                if (!isset($loketDailyData[$loket->nama_loket])) {
                    $loketDailyData[$loket->nama_loket] = [
                        'label' => $loket->nama_loket,
                        'data' => array_fill(0, $daysInMonth, 0)
                    ];
                }
            }
            
            foreach ($dbDensityDaily as $row) {
                $lName = $row->nama_loket;
                $d = intval($row->day);
                if (isset($loketDailyData[$lName]) && $d >= 1 && $d <= $daysInMonth) {
                    $loketDailyData[$lName]['data'][$d - 1] += intval($row->total);
                }
            }
            
            foreach ($loketDailyData as $lName => $lData) {
                if (array_sum($lData['data']) > 0) {
                    $chartDatasets[] = [
                        'label' => $lData['label'],
                        'data' => $lData['data']
                    ];
                }
            }
        }

        // --- NEW: Kepadatan Pengunjung Per Bulan (Tren Tahunan) untuk Tabel ---
        $monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[$m] = [
                'nama_bulan' => $monthsIndo[$m - 1],
                'total' => 0
            ];
        }

        $monthlyTableQuery = Antrian::query();
        if ($start_date) {
            $monthlyTableQuery->whereDate('waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $monthlyTableQuery->whereDate('waktu_voice', '<=', $end_date);
        }
        if (!$start_date && !$end_date) {
            $monthlyTableQuery->whereYear('waktu_voice', $activeYear);
        }
        if ($id_loket) {
            $monthlyTableQuery->where('id_loket', $id_loket);
        }

        $dbMonthlyTable = $monthlyTableQuery->select(DB::raw('MONTH(waktu_voice) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        foreach ($dbMonthlyTable as $monthNum => $dataTbl) {
            if (isset($monthlyData[$monthNum])) {
                $monthlyData[$monthNum]['total'] = $dataTbl->total;
            }
        }
        $totalKunjunganTahun = collect($monthlyData)->sum('total');

        // 6. Saluran Antrean per Hari (Online vs Manual vs Voice)
        $channelOnlineData = array_fill(0, $daysInMonth, 0);
        $channelOfflineData = array_fill(0, $daysInMonth, 0); // Legacy
        $channelManualData = array_fill(0, $daysInMonth, 0);
        $channelVoiceData = array_fill(0, $daysInMonth, 0);

        $channelQuery = Antrian::whereYear('waktu_voice', $activeYear)
            ->whereMonth('waktu_voice', $activeMonth);

        if ($id_loket) {
            $channelQuery->where('id_loket', $id_loket);
        }

        $channelDailyRaw = $channelQuery->select(
                DB::raw('DAY(waktu_voice) as day'),
                'setatus_pengambilan',
                DB::raw('count(*) as total')
            )
            ->groupBy('day', 'setatus_pengambilan')
            ->get();

        foreach ($channelDailyRaw as $item) {
            $d = intval($item->day);
            if ($d >= 1 && $d <= $daysInMonth) {
                if (strtolower($item->setatus_pengambilan) === 'online') {
                    $channelOnlineData[$d - 1] = intval($item->total);
                } elseif (in_array(strtolower($item->setatus_pengambilan), ['manual', 'offline'])) {
                    $channelManualData[$d - 1] += intval($item->total); // Gabung legacy offline
                } elseif (strtolower($item->setatus_pengambilan) === 'voice') {
                    $channelVoiceData[$d - 1] = intval($item->total);
                }
            }
        }

        // Kalkulasi Total Offline (Manual + Voice) per hari untuk chart
        for ($i = 0; $i < $daysInMonth; $i++) {
            $channelOfflineData[$i] = $channelManualData[$i] + $channelVoiceData[$i];
        }

        // 7. Rata-rata Waktu Evaluasi per Loket (Bulan Aktif)
        $activeLokets = Loket::orderBy('nama_loket', 'asc')->get();

        $evaluationQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
            ->whereYear('antrian.waktu_voice', $activeYear)
            ->whereMonth('antrian.waktu_voice', $activeMonth)
            ->whereNotNull('antrian.waktu_panggil')
            ->whereNotNull('antrian.waktu_selesai')
            ->where('antrian.status_antrian', 'selesai');

        if ($id_loket) {
            $evaluationQuery->where('antrian.id_loket', $id_loket);
        }

        $evaluationRaw = $evaluationQuery->select(
                'loket.id_loket',
                DB::raw('COUNT(antrian.id_antrian) as total_selesai'),
                DB::raw('AVG(TIME_TO_SEC(TIMEDIFF(antrian.waktu_selesai, antrian.waktu_panggil))) as avg_seconds')
            )
            ->groupBy('loket.id_loket')
            ->get()
            ->keyBy('id_loket');

        $evalLabels = [];
        $evalValues = [];
        $evaluations = []; // For detailed table

        $groupedEval = [];
        foreach ($activeLokets as $loket) {
            // Jika ada filter loket tertentu, lewati loket lainnya
            if ($id_loket && $loket->id_loket != $id_loket) {
                continue;
            }

            if (isset($evaluationRaw[$loket->id_loket])) {
                $avg_s = floatval($evaluationRaw[$loket->id_loket]->avg_seconds);
                $tot_s = intval($evaluationRaw[$loket->id_loket]->total_selesai);
            } else {
                $avg_s = 0;
                $tot_s = 0;
            }

            // For chart grouping
            if (!isset($groupedEval[$loket->nama_loket])) {
                $groupedEval[$loket->nama_loket] = ['total_s' => 0, 'sum_time' => 0];
            }
            $groupedEval[$loket->nama_loket]['total_s'] += $tot_s;
            $groupedEval[$loket->nama_loket]['sum_time'] += ($avg_s * $tot_s);

            $evaluations[] = [
                'nama_loket' => $loket->nama_loket . ($loket->nama_pelayanan ? ' - ' . $loket->nama_pelayanan : ''),
                'avg_seconds' => round($avg_s, 1),
                'total_selesai' => $tot_s
            ];
        }

        foreach ($groupedEval as $lName => $data) {
            $evalLabels[] = $lName;
            $avg = $data['total_s'] > 0 ? ($data['sum_time'] / $data['total_s']) : 0;
            $evalValues[] = round($avg, 1);
        }

        // 8. Analisis Jam Sibuk Pengunjung per Loket (Bulan Aktif)
        $busyHoursQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
            ->whereYear('antrian.waktu_voice', $activeYear)
            ->whereMonth('antrian.waktu_voice', $activeMonth);

        if ($id_loket) {
            $busyHoursQuery->where('antrian.id_loket', $id_loket);
        }

        $busyHoursRaw = $busyHoursQuery->select(
                'antrian.id_loket',
                'loket.nama_loket',
                DB::raw('HOUR(antrian.waktu_voice) as hour'),
                DB::raw('count(*) as total')
            )
            ->groupBy('antrian.id_loket', 'loket.nama_loket', 'hour')
            ->get();

        $groupedBusy = [];
        foreach ($busyHoursRaw as $row) {
            $groupedBusy[$row->id_loket][$row->hour] = intval($row->total);
        }

        $hoursRange = [8, 9, 10, 11, 12, 13, 14, 15, 16];
        $groupedBusyChart = [];
        $busyDatasets = [];
        $busyReports = []; // For detailed table

        foreach ($activeLokets as $loket) {
            if ($id_loket && $loket->id_loket != $id_loket) {
                continue;
            }

            $dataAssoc = [];
            foreach ($hoursRange as $hr) {
                $val = isset($groupedBusy[$loket->id_loket][$hr]) ? $groupedBusy[$loket->id_loket][$hr] : 0;
                $dataAssoc[$hr] = $val;

                if (!isset($groupedBusyChart[$loket->nama_loket][$hr])) {
                    $groupedBusyChart[$loket->nama_loket][$hr] = 0;
                }
                $groupedBusyChart[$loket->nama_loket][$hr] += $val;
            }
            
            $busyReports[] = [
                'nama_loket' => $loket->nama_loket . ($loket->nama_pelayanan ? ' - ' . $loket->nama_pelayanan : ''),
                'data' => $dataAssoc,
                'total' => array_sum($dataAssoc)
            ];
        }

        foreach ($groupedBusyChart as $lName => $dataByHour) {
            $dataArr = [];
            foreach ($hoursRange as $hr) {
                $dataArr[] = $dataByHour[$hr];
            }
            $busyDatasets[] = [
                'label' => $lName,
                'data' => $dataArr
            ];
        }

        // --- NEW: Laporan Saluran Antrean per Loket (Online vs Offline) untuk Tabel ---
        $channelLoketQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket');
        if ($start_date) {
            $channelLoketQuery->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $channelLoketQuery->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $channelLoketQuery->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            $channelLoketQuery->whereYear('antrian.waktu_voice', $activeYear)
                              ->whereMonth('antrian.waktu_voice', $activeMonth);
        }
        if ($id_loket) {
            $channelLoketQuery->where('antrian.id_loket', $id_loket);
        }

        $channelLoketRaw = $channelLoketQuery->select(
                'antrian.id_loket',
                'antrian.setatus_pengambilan',
                DB::raw('count(*) as total')
            )
            ->groupBy('antrian.id_loket', 'antrian.setatus_pengambilan')
            ->get();

        $groupedChannel = [];
        foreach ($channelLoketRaw as $row) {
            $groupedChannel[$row->id_loket][strtolower($row->setatus_pengambilan)] = intval($row->total);
        }

        $loketChannels = [];
        $activeLoketsList = Loket::orderBy('nama_loket', 'asc')->get();
        foreach ($activeLoketsList as $lok) {
            if ($id_loket && $lok->id_loket != $id_loket) {
                continue;
            }
            $online = isset($groupedChannel[$lok->id_loket]['online']) ? $groupedChannel[$lok->id_loket]['online'] : 0;
            $offlineLegacy = isset($groupedChannel[$lok->id_loket]['offline']) ? $groupedChannel[$lok->id_loket]['offline'] : 0;
            $manual = isset($groupedChannel[$lok->id_loket]['manual']) ? $groupedChannel[$lok->id_loket]['manual'] : 0;
            $voice = isset($groupedChannel[$lok->id_loket]['voice']) ? $groupedChannel[$lok->id_loket]['voice'] : 0;
            
            $offlineTotal = $offlineLegacy + $manual + $voice;

            $loketChannels[] = [
                'nama_loket' => $lok->nama_loket . ($lok->nama_pelayanan ? ' - ' . $lok->nama_pelayanan : ''),
                'online' => $online,
                'offline' => $offlineTotal,
                'manual' => $manual + $offlineLegacy,
                'voice' => $voice,
                'total' => $online + $offlineTotal
            ];
        }

        // --- NEW: Laporan Distribusi Status Antrean per Loket untuk Tabel ---
        $statusPerLoketQuery = DB::table('antrian');
        if ($start_date) {
            $statusPerLoketQuery->whereDate('waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $statusPerLoketQuery->whereDate('waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $statusPerLoketQuery->whereYear('waktu_voice', $year);
        }
        if ($id_loket) {
            $statusPerLoketQuery->where('id_loket', $id_loket);
        }

        $statusPerLoketRaw = $statusPerLoketQuery->select(
                'id_loket',
                DB::raw("SUM(CASE WHEN status_antrian = 'batal' THEN 1 ELSE 0 END) as batal_count"),
                DB::raw("SUM(CASE WHEN waktu_selesai IS NOT NULL AND COALESCE(status_antrian, '') != 'batal' THEN 1 ELSE 0 END) as selesai_count"),
                DB::raw("SUM(CASE WHEN waktu_panggil IS NOT NULL AND waktu_selesai IS NULL AND COALESCE(status_antrian, '') != 'batal' THEN 1 ELSE 0 END) as dipanggil_count"),
                DB::raw("SUM(CASE WHEN waktu_panggil IS NULL AND waktu_selesai IS NULL AND COALESCE(status_antrian, '') != 'batal' THEN 1 ELSE 0 END) as menunggu_count")
            )
            ->groupBy('id_loket')
            ->get()
            ->keyBy('id_loket');

        $statusLoketReports = [];
        foreach ($activeLoketsList as $lok) {
            if ($id_loket && $lok->id_loket != $id_loket) {
                continue;
            }
            $rawStatus = isset($statusPerLoketRaw[$lok->id_loket]) ? $statusPerLoketRaw[$lok->id_loket] : null;
            $s_selesai = $rawStatus ? intval($rawStatus->selesai_count) : 0;
            $s_batal = $rawStatus ? intval($rawStatus->batal_count) : 0;
            $s_dipanggil = $rawStatus ? intval($rawStatus->dipanggil_count) : 0;
            $s_menunggu = $rawStatus ? intval($rawStatus->menunggu_count) : 0;
            
            $s_aktif = $s_dipanggil + $s_menunggu;
            $s_total = $s_selesai + $s_batal + $s_aktif;

            $statusLoketReports[] = [
                'nama_loket' => $lok->nama_loket . ($lok->nama_pelayanan ? ' - ' . $lok->nama_pelayanan : ''),
                'selesai' => $s_selesai,
                'batal' => $s_batal,
                'aktif' => $s_aktif,
                'total' => $s_total
            ];
        }

        return view('administrator.data_pengunjung', compact(
            'pengunjungs',
            'totalPengunjung',
            'totalAntrean',
            'onlineCount',
            'offlineCount',
            'manualCount',
            'voiceCount',
            'ageGroups',
            'search',
            'lokets',
            'tipe_laporan',
            'chartLabels',
            'chartDatasets',
            'channelOnlineData',
            'channelOfflineData',
            'channelManualData',
            'channelVoiceData',
            'isSingleLoket',
            'singleLoketName',
            'chartTitlePeriod',
            'evalLabels',
            'evalValues',
            'activeYear',
            'busyDatasets',
            'selesaiCount',
            'batalCount',
            'activeQueueCount',
            'monthlyData',
            'totalKunjunganTahun',
            'evaluations',
            'busyReports',
            'hoursRange',
            'loketChannels',
            'statusLoketReports'
        ));
    }

    public function cetak(Request $request)
    {
        $id_profil = session('id_profil');
        $profil = Profil::with('loket')->where('id_profil', $id_profil)->first();

        // 1. Ambil filter input
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $year = $request->input('year');
        $id_loket = $request->input('id_loket');
        $tipe_laporan = $request->input('tipe_laporan', 'semua');
        $search = $request->input('q');

        // 2. Query Pengunjung
        $visitorQuery = ProfilPengunjung::query();
        if ($search) {
            $visitorQuery->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_whatsapp', 'like', "%{$search}%");
            });
        }
        if ($start_date || $end_date || $year || $id_loket) {
            $visitorQuery->whereHas('antrian', function($q) use ($start_date, $end_date, $year, $id_loket) {
                if ($start_date) {
                    $q->whereDate('waktu_voice', '>=', $start_date);
                }
                if ($end_date) {
                    $q->whereDate('waktu_voice', '<=', $end_date);
                }
                if ($year && !$start_date && !$end_date) {
                    $q->whereYear('waktu_voice', $year);
                }
                if ($id_loket) {
                    $q->where('id_loket', $id_loket);
                }
            });
        }
        $pengunjungs = $visitorQuery->orderBy('nama', 'asc')->get();
        $totalPengunjung = $pengunjungs->count();

        // 3. Query Antrian untuk Statistik Utama & Cetak
        $antrianQuery = Antrian::query();
        if ($start_date) {
            $antrianQuery->whereDate('waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $antrianQuery->whereDate('waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $antrianQuery->whereYear('waktu_voice', $year);
        }
        if ($id_loket) {
            $antrianQuery->where('id_loket', $id_loket);
        }

        $totalAntrean = (clone $antrianQuery)->count();
        $onlineCount = (clone $antrianQuery)->where('setatus_pengambilan', 'online')->count();
        $offlineCount = (clone $antrianQuery)->where('setatus_pengambilan', 'offline')->count();

        // Hitung status antrean untuk Cetak
        $statusCounts = (clone $antrianQuery)
            ->select('status_antrian', DB::raw('count(*) as total'))
            ->groupBy('status_antrian')
            ->get()
            ->keyBy('status_antrian');

        $selesaiCount = isset($statusCounts['selesai']) ? $statusCounts['selesai']->total : 0;
        $batalCount = isset($statusCounts['batal']) ? $statusCounts['batal']->total : 0;
        $menungguCount = isset($statusCounts['menunggu']) ? $statusCounts['menunggu']->total : 0;
        $dipanggilCount = isset($statusCounts['dipanggil']) ? $statusCounts['dipanggil']->total : 0;
        $activeQueueCount = $menungguCount + $dipanggilCount;

        // 4. Klasifikasi Umur
        $ageGroups = [
            'remaja' => 0,
            'dewasa' => 0,
            'lansia' => 0
        ];
        
        foreach ($pengunjungs as $p) {
            if ($p->tanggal_lahir) {
                $age = Carbon::parse($p->tanggal_lahir)->age;
                if ($age <= 18) {
                    $ageGroups['remaja']++;
                } elseif ($age >= 60) {
                    $ageGroups['lansia']++;
                } else {
                    $ageGroups['dewasa']++;
                }
            }
        }

        // 5. Kepadatan Pengunjung Per Loket
        $densityQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket');

        if ($start_date) {
            $densityQuery->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $densityQuery->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $densityQuery->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            $oneYearAgo = Carbon::now()->subYear();
            $densityQuery->where('antrian.waktu_voice', '>=', $oneYearAgo);
        }
        if ($id_loket) {
            $densityQuery->where('antrian.id_loket', $id_loket);
        }

        $densityPerLoket = $densityQuery->select('loket.nama_loket', DB::raw('count(*) as total'))
            ->groupBy('loket.id_loket', 'loket.nama_loket')
            ->orderBy('total', 'desc')
            ->get();

        $totalKunjunganLoket = $densityPerLoket->sum('total');

        // 6. Kepadatan Pengunjung Per Bulan (Tren Tahunan)
        if ($year) {
            $yearVal = $year;
        } elseif ($start_date) {
            $yearVal = date('Y', strtotime($start_date));
        } else {
            $yearVal = date('Y');
        }

        $monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[$m] = [
                'nama_bulan' => $monthsIndo[$m - 1],
                'total' => 0
            ];
        }

        $monthlyQuery = Antrian::query();
        if ($start_date) {
            $monthlyQuery->whereDate('waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $monthlyQuery->whereDate('waktu_voice', '<=', $end_date);
        }
        if (!$start_date && !$end_date) {
            $monthlyQuery->whereYear('waktu_voice', $yearVal);
        }
        if ($id_loket) {
            $monthlyQuery->where('id_loket', $id_loket);
        }

        $dbMonthly = $monthlyQuery->select(DB::raw('MONTH(waktu_voice) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        foreach ($dbMonthly as $monthNum => $data) {
            if (isset($monthlyData[$monthNum])) {
                $monthlyData[$monthNum]['total'] = $data->total;
            }
        }

        $totalKunjunganTahun = collect($monthlyData)->sum('total');

        // NEW CALCULATIONS FOR EVALUATION & BUSY HOURS
        $activeLokets = Loket::orderBy('nama_loket', 'asc')->get();

        // Rata-rata Waktu Evaluasi per Loket
        $evaluationQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket')
            ->whereNotNull('antrian.waktu_panggil')
            ->whereNotNull('antrian.waktu_selesai')
            ->where('antrian.status_antrian', 'selesai');

        if ($start_date) {
            $evaluationQuery->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $evaluationQuery->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $evaluationQuery->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            $activeMonth = date('m');
            $activeYear = date('Y');
            $evaluationQuery->whereYear('antrian.waktu_voice', $activeYear)
                             ->whereMonth('antrian.waktu_voice', $activeMonth);
        }
        if ($id_loket) {
            $evaluationQuery->where('antrian.id_loket', $id_loket);
        }

        $evaluationRaw = $evaluationQuery->select(
                'loket.id_loket',
                DB::raw('COUNT(antrian.id_antrian) as total_selesai'),
                DB::raw('AVG(TIME_TO_SEC(TIMEDIFF(antrian.waktu_selesai, antrian.waktu_panggil))) as avg_seconds')
            )
            ->groupBy('loket.id_loket')
            ->get()
            ->keyBy('id_loket');

        $evaluations = [];
        foreach ($activeLokets as $loket) {
            if ($id_loket && $loket->id_loket != $id_loket) {
                continue;
            }
            $avgSeconds = isset($evaluationRaw[$loket->id_loket]) ? round(floatval($evaluationRaw[$loket->id_loket]->avg_seconds), 1) : 0;
            $totalSelesai = isset($evaluationRaw[$loket->id_loket]) ? intval($evaluationRaw[$loket->id_loket]->total_selesai) : 0;
            $evaluations[] = [
                'nama_loket' => $loket->nama_loket . ($loket->nama_pelayanan ? ' - ' . $loket->nama_pelayanan : ''),
                'avg_seconds' => $avgSeconds,
                'total_selesai' => $totalSelesai
            ];
        }

        // Analisis Jam Sibuk Pengunjung per Loket
        $busyHoursQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket');

        if ($start_date) {
            $busyHoursQuery->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $busyHoursQuery->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $busyHoursQuery->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            $activeMonth = date('m');
            $activeYear = date('Y');
            $busyHoursQuery->whereYear('antrian.waktu_voice', $activeYear)
                           ->whereMonth('antrian.waktu_voice', $activeMonth);
        }
        if ($id_loket) {
            $busyHoursQuery->where('antrian.id_loket', $id_loket);
        }

        $busyHoursRaw = $busyHoursQuery->select(
                'antrian.id_loket',
                'loket.nama_loket',
                DB::raw('HOUR(antrian.waktu_voice) as hour'),
                DB::raw('count(*) as total')
            )
            ->groupBy('antrian.id_loket', 'loket.nama_loket', 'hour')
            ->get();

        $groupedBusy = [];
        foreach ($busyHoursRaw as $row) {
            $groupedBusy[$row->id_loket][$row->hour] = intval($row->total);
        }

        $hoursRange = [8, 9, 10, 11, 12, 13, 14, 15, 16];
        $busyReports = [];

        foreach ($activeLokets as $loket) {
            if ($id_loket && $loket->id_loket != $id_loket) {
                continue;
            }

            $data = [];
            foreach ($hoursRange as $hr) {
                $data[$hr] = isset($groupedBusy[$loket->id_loket][$hr]) ? $groupedBusy[$loket->id_loket][$hr] : 0;
            }

            $busyReports[] = [
                'nama_loket' => $loket->nama_loket . ($loket->nama_pelayanan ? ' - ' . $loket->nama_pelayanan : ''),
                'data' => $data,
                'total' => array_sum($data)
            ];
        }

        // Saluran Antrean per Loket (Online vs Offline)
        $channelLoketQuery = DB::table('antrian')
            ->join('loket', 'antrian.id_loket', '=', 'loket.id_loket');

        if ($start_date) {
            $channelLoketQuery->whereDate('antrian.waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $channelLoketQuery->whereDate('antrian.waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $channelLoketQuery->whereYear('antrian.waktu_voice', $year);
        }
        if (!$start_date && !$end_date && !$year) {
            $activeMonth = date('m');
            $activeYear = date('Y');
            $channelLoketQuery->whereYear('antrian.waktu_voice', $activeYear)
                              ->whereMonth('antrian.waktu_voice', $activeMonth);
        }
        if ($id_loket) {
            $channelLoketQuery->where('antrian.id_loket', $id_loket);
        }

        $channelLoketRaw = $channelLoketQuery->select(
                'antrian.id_loket',
                'antrian.setatus_pengambilan',
                DB::raw('count(*) as total')
            )
            ->groupBy('antrian.id_loket', 'antrian.setatus_pengambilan')
            ->get();

        $groupedChannel = [];
        foreach ($channelLoketRaw as $row) {
            $groupedChannel[$row->id_loket][strtolower($row->setatus_pengambilan)] = intval($row->total);
        }

        $loketChannels = [];
        foreach ($activeLokets as $loket) {
            if ($id_loket && $loket->id_loket != $id_loket) {
                continue;
            }

            $online = isset($groupedChannel[$loket->id_loket]['online']) ? $groupedChannel[$loket->id_loket]['online'] : 0;
            $offlineLegacy = isset($groupedChannel[$loket->id_loket]['offline']) ? $groupedChannel[$loket->id_loket]['offline'] : 0;
            $manual = isset($groupedChannel[$loket->id_loket]['manual']) ? $groupedChannel[$loket->id_loket]['manual'] : 0;
            $voice = isset($groupedChannel[$loket->id_loket]['voice']) ? $groupedChannel[$loket->id_loket]['voice'] : 0;

            $offlineTotal = $offlineLegacy + $manual + $voice;

            $loketChannels[] = [
                'nama_loket' => $loket->nama_loket . ($loket->nama_pelayanan ? ' - ' . $loket->nama_pelayanan : ''),
                'online' => $online,
                'offline' => $offlineTotal,
                'manual' => $manual + $offlineLegacy,
                'voice' => $voice,
                'total' => $online + $offlineTotal
            ];
        }

        // Laporan Distribusi Status Antrean per Loket
        $statusPerLoketQuery = DB::table('antrian');
        if ($start_date) {
            $statusPerLoketQuery->whereDate('waktu_voice', '>=', $start_date);
        }
        if ($end_date) {
            $statusPerLoketQuery->whereDate('waktu_voice', '<=', $end_date);
        }
        if ($year && !$start_date && !$end_date) {
            $statusPerLoketQuery->whereYear('waktu_voice', $year);
        }
        if ($id_loket) {
            $statusPerLoketQuery->where('id_loket', $id_loket);
        }

        $statusPerLoketRaw = $statusPerLoketQuery->select(
                'id_loket',
                DB::raw("SUM(CASE WHEN status_antrian = 'batal' THEN 1 ELSE 0 END) as batal_count"),
                DB::raw("SUM(CASE WHEN waktu_selesai IS NOT NULL AND COALESCE(status_antrian, '') != 'batal' THEN 1 ELSE 0 END) as selesai_count"),
                DB::raw("SUM(CASE WHEN waktu_panggil IS NOT NULL AND waktu_selesai IS NULL AND COALESCE(status_antrian, '') != 'batal' THEN 1 ELSE 0 END) as dipanggil_count"),
                DB::raw("SUM(CASE WHEN waktu_panggil IS NULL AND waktu_selesai IS NULL AND COALESCE(status_antrian, '') != 'batal' THEN 1 ELSE 0 END) as menunggu_count")
            )
            ->groupBy('id_loket')
            ->get()
            ->keyBy('id_loket');

        $statusLoketReports = [];
        foreach ($activeLokets as $loket) {
            if ($id_loket && $loket->id_loket != $id_loket) {
                continue;
            }

            $raw = isset($statusPerLoketRaw[$loket->id_loket]) ? $statusPerLoketRaw[$loket->id_loket] : null;
            $selesai = $raw ? intval($raw->selesai_count) : 0;
            $batal = $raw ? intval($raw->batal_count) : 0;
            $dipanggil = $raw ? intval($raw->dipanggil_count) : 0;
            $menunggu = $raw ? intval($raw->menunggu_count) : 0;
            
            $aktif = $dipanggil + $menunggu;
            $total = $selesai + $batal + $aktif;

            $statusLoketReports[] = [
                'nama_loket' => $loket->nama_loket . ($loket->nama_pelayanan ? ' - ' . $loket->nama_pelayanan : ''),
                'selesai' => $selesai,
                'batal' => $batal,
                'aktif' => $aktif,
                'total' => $total
            ];
        }

        // 7. Label Filter Aktif untuk Ditampilkan di PDF
        $loketFilterName = 'Semua Loket';
        if ($id_loket) {
            $selectedLoket = Loket::find($id_loket);
            if ($selectedLoket) {
                $loketFilterName = $selectedLoket->nama_loket . ($selectedLoket->nama_pelayanan ? ' - ' . $selectedLoket->nama_pelayanan : '');
            }
        }

        $tipeLaporanName = 'Semua Laporan';
        if ($tipe_laporan === 'kepadatan_bulanan') {
            $tipeLaporanName = 'Kepadatan Bulanan';
        } elseif ($tipe_laporan === 'evaluasi') {
            $tipeLaporanName = 'Rata-rata Waktu Evaluasi per Loket';
        } elseif ($tipe_laporan === 'jam_sibuk') {
            $tipeLaporanName = 'Analisis Jam Sibuk Pengunjung';
        } elseif ($tipe_laporan === 'saluran_loket') {
            $tipeLaporanName = 'Laporan Saluran Antrean per Loket (Online/Offline)';
        } elseif ($tipe_laporan === 'detail_pengunjung') {
            $tipeLaporanName = 'Detail Data Pengunjung';
        }

        $periodeFilterName = 'Semua Waktu';
        if ($start_date && $end_date) {
            $periodeFilterName = Carbon::parse($start_date)->format('d-m-Y') . ' s/d ' . Carbon::parse($end_date)->format('d-m-Y');
        } elseif ($start_date) {
            $periodeFilterName = 'Mulai ' . Carbon::parse($start_date)->format('d-m-Y');
        } elseif ($end_date) {
            $periodeFilterName = 'Sampai ' . Carbon::parse($end_date)->format('d-m-Y');
        } elseif ($year) {
            $periodeFilterName = 'Tahun ' . $year;
        }

        return view('pdf.laporan_pengunjung', compact(
            'profil',
            'totalPengunjung',
            'totalAntrean',
            'onlineCount',
            'offlineCount',
            'ageGroups',
            'densityPerLoket',
            'totalKunjunganLoket',
            'monthlyData',
            'totalKunjunganTahun',
            'year',
            'yearVal',
            'pengunjungs',
            'loketFilterName',
            'tipeLaporanName',
            'periodeFilterName',
            'tipe_laporan',
            'evaluations',
            'busyReports',
            'hoursRange',
            'loketChannels',
            'selesaiCount',
            'batalCount',
            'activeQueueCount',
            'statusLoketReports'
        ));
    }
}

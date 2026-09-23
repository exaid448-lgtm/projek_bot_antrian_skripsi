<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use App\Models\PoinKinerja;
use App\Models\Loket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DataKinerjaLoketController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get Filters
        $search = $request->input('search');
        $id_loket = $request->input('loket');
        $tgl_mulai = $request->input('tgl_mulai');
        $tgl_selesai = $request->input('tgl_selesai');

        // Default: If no date filter is applied, we default to the current month to show fresh metrics
        if (!$tgl_mulai && !$tgl_selesai) {
            $tgl_mulai = Carbon::now()->startOfMonth()->toDateString();
            $tgl_selesai = Carbon::now()->endOfMonth()->toDateString();
        }

        // 2. Fetch Lokets for Filter Dropdown
        $list_loket = Loket::orderBy('nama_loket', 'asc')->get();

        // 3. Fetch Employees with relations (excluding administrator division)
        $karyawanQuery = Profil::with(['loket', 'poinKinerja'])
            ->where('status_devisi', '!=', 'administrator');

        if ($search) {
            $karyawanQuery->where('nama_user', 'like', '%' . $search . '%');
        }
        if ($id_loket) {
            $karyawanQuery->where('id_loket', $id_loket);
        }

        $karyawanData = $karyawanQuery->get();

        // 4. Calculate sisa poin and metrics for each employee
        $karyawanKinerja = $karyawanData->map(function ($karyawan) use ($tgl_mulai, $tgl_selesai) {
            $poinQuery = $karyawan->poinKinerja();
            
            if ($tgl_mulai) {
                $poinQuery->where('tanggal', '>=', $tgl_mulai);
            }
            if ($tgl_selesai) {
                $poinQuery->where('tanggal', '<=', $tgl_selesai);
            }

            $violations = $poinQuery->get();
            $totalDeductions = $violations->sum('poin_dipotong');
            $sisaPoin = max(0, 100 - $totalDeductions);

            // Determine status predikat
            if ($sisaPoin >= 90) {
                $status = 'Sangat Baik';
                $badgeClass = 'badge-sangat-baik';
            } elseif ($sisaPoin >= 80) {
                $status = 'Baik';
                $badgeClass = 'badge-baik';
            } elseif ($sisaPoin >= 70) {
                $status = 'Cukup';
                $badgeClass = 'badge-cukup';
            } else {
                $status = 'Kurang';
                $badgeClass = 'badge-kurang';
            }

            return [
                'id_profil' => $karyawan->id_profil,
                'nama_user' => $karyawan->nama_user,
                'status_devisi' => $karyawan->status_devisi,
                'nama_loket' => $karyawan->loket ? $karyawan->loket->nama_loket . ($karyawan->loket->nama_pelayanan ? ' - ' . $karyawan->loket->nama_pelayanan : '') : 'Tidak Ada',
                'id_loket' => $karyawan->id_loket,
                'img_user' => $karyawan->img_user ?? 'default.png',
                'total_pelanggaran' => $violations->count(),
                'total_deductions' => $totalDeductions,
                'sisa_poin' => $sisaPoin,
                'status_kinerja' => $status,
                'badge_class' => $badgeClass
            ];
        });

        // Sort by highest remaining points (best performing first)
        $karyawanKinerja = $karyawanKinerja->sortByDesc('sisa_poin')->values();

        // 5. Calculate Average Score per Loket (using employees in each loket)
        $loketKinerja = [];
        $groupedByLoket = $karyawanKinerja->groupBy('id_loket');

        foreach ($list_loket as $lkt) {
            $emps = $groupedByLoket->get($lkt->id_loket);
            if ($emps && $emps->count() > 0) {
                $avgScore = $emps->avg('sisa_poin');
                $loketKinerja[] = [
                    'nama_loket' => $lkt->nama_loket . ($lkt->nama_pelayanan ? ' - ' . $lkt->nama_pelayanan : ''),
                    'average_score' => round($avgScore, 1),
                    'total_karyawan' => $emps->count()
                ];
            } else {
                // If no employees assigned, set 100% or 0%?
                // Let's set 100% as baseline or skip. Skipping shows active lokets only, which is cleaner.
            }
        }

        // 6. Fetch Violation logs matching filters (excluding administrator division)
        $logQuery = PoinKinerja::with(['profil.loket'])
            ->whereHas('profil', function($q) {
                $q->where('status_devisi', '!=', 'administrator');
            });

        if ($tgl_mulai) {
            $logQuery->where('tanggal', '>=', $tgl_mulai);
        }
        if ($tgl_selesai) {
            $logQuery->where('tanggal', '<=', $tgl_selesai);
        }
        if ($id_loket) {
            $logQuery->whereHas('profil', function($q) use ($id_loket) {
                $q->where('id_loket', $id_loket);
            });
        }
        if ($search) {
            $logQuery->whereHas('profil', function($q) use ($search) {
                $q->where('nama_user', 'like', '%' . $search . '%');
            });
        }

        $logPelanggaran = $logQuery->orderBy('tanggal', 'desc')
                                   ->orderBy('waktu_kejadian', 'desc')
                                   ->get();

        // 7. Get violation types distribution for Doughnut Chart (excluding administrator division)
        $violationTypesQuery = PoinKinerja::whereHas('profil', function($q) {
            $q->where('status_devisi', '!=', 'administrator');
        });
        if ($tgl_mulai) {
            $violationTypesQuery->where('tanggal', '>=', $tgl_mulai);
        }
        if ($tgl_selesai) {
            $violationTypesQuery->where('tanggal', '<=', $tgl_selesai);
        }
        if ($id_loket) {
            $violationTypesQuery->whereHas('profil', function($q) use ($id_loket) {
                $q->where('id_loket', $id_loket);
            });
        }
        if ($search) {
            $violationTypesQuery->whereHas('profil', function($q) use ($search) {
                $q->where('nama_user', 'like', '%' . $search . '%');
            });
        }

        $violationTypes = $violationTypesQuery->select('jenis_pelanggaran', DB::raw('count(*) as total'))
                                              ->groupBy('jenis_pelanggaran')
                                              ->get();

        $chartViolationLabels = [];
        $chartViolationCounts = [];
        foreach ($violationTypes as $vt) {
            $chartViolationLabels[] = $vt->jenis_pelanggaran === 'terlambat_absen' ? 'Terlambat Absen' : 'Terlambat Buka Loket';
            $chartViolationCounts[] = $vt->total;
        }

        // 8. Statistics for KPI Cards
        $totalKaryawan = $karyawanKinerja->count();
        $totalPelanggaran = $karyawanKinerja->sum('total_pelanggaran');
        $rataRataPoin = $totalKaryawan > 0 ? round($karyawanKinerja->avg('sisa_poin'), 1) : 100;
        
        $terbaik = $karyawanKinerja->first();
        $terburuk = $karyawanKinerja->last();

        // 9. All employees for dropdown form (excluding administrator division)
        $all_karyawan = Profil::where('status_devisi', '!=', 'administrator')
            ->orderBy('nama_user', 'asc')
            ->get();

        return view('administrator.data_kinerja_loket', compact(
            'karyawanKinerja',
            'loketKinerja',
            'logPelanggaran',
            'list_loket',
            'all_karyawan',
            'search',
            'id_loket',
            'tgl_mulai',
            'tgl_selesai',
            'chartViolationLabels',
            'chartViolationCounts',
            'totalKaryawan',
            'totalPelanggaran',
            'rataRataPoin',
            'terbaik',
            'terburuk'
        ));
    }

    public function cetak(Request $request)
    {
        $search = $request->input('search');
        $id_loket = $request->input('loket');
        $tgl_mulai = $request->input('tgl_mulai');
        $tgl_selesai = $request->input('tgl_selesai');
        $tipe_laporan = $request->input('tipe_laporan', 'semua');

        // Fetch Loket Name for PDF Header
        $loketFilterName = 'Semua Loket';
        if ($id_loket) {
            $lkt = Loket::find($id_loket);
            if ($lkt) {
                $loketFilterName = $lkt->nama_loket . ($lkt->nama_pelayanan ? ' - ' . $lkt->nama_pelayanan : '');
            }
        }

        // Compile date period filter info for PDF Header
        $periodeFilterName = 'Semua Periode';
        if ($tgl_mulai && $tgl_selesai) {
            $periodeFilterName = Carbon::parse($tgl_mulai)->format('d F Y') . ' s.d. ' . Carbon::parse($tgl_selesai)->format('d F Y');
        } elseif ($tgl_mulai) {
            $periodeFilterName = 'Mulai ' . Carbon::parse($tgl_mulai)->format('d F Y');
        } elseif ($tgl_selesai) {
            $periodeFilterName = 'Sampai ' . Carbon::parse($tgl_selesai)->format('d F Y');
        }

        // Fetch Employees and calculate points (excluding administrator division)
        $karyawanQuery = Profil::with(['loket', 'poinKinerja'])
            ->where('status_devisi', '!=', 'administrator');
        if ($search) {
            $karyawanQuery->where('nama_user', 'like', '%' . $search . '%');
        }
        if ($id_loket) {
            $karyawanQuery->where('id_loket', $id_loket);
        }
        $karyawanData = $karyawanQuery->get();

        $karyawanKinerja = $karyawanData->map(function ($karyawan) use ($tgl_mulai, $tgl_selesai) {
            $poinQuery = $karyawan->poinKinerja();
            if ($tgl_mulai) {
                $poinQuery->where('tanggal', '>=', $tgl_mulai);
            }
            if ($tgl_selesai) {
                $poinQuery->where('tanggal', '<=', $tgl_selesai);
            }

            $violations = $poinQuery->get();
            $totalDeductions = $violations->sum('poin_dipotong');
            $sisaPoin = max(0, 100 - $totalDeductions);

            if ($sisaPoin >= 90) {
                $status = 'Sangat Baik';
            } elseif ($sisaPoin >= 80) {
                $status = 'Baik';
            } elseif ($sisaPoin >= 70) {
                $status = 'Cukup';
            } else {
                $status = 'Kurang';
            }

            return [
                'nama_user' => $karyawan->nama_user,
                'status_devisi' => $karyawan->status_devisi,
                'nama_loket' => $karyawan->loket ? $karyawan->loket->nama_loket . ($karyawan->loket->nama_pelayanan ? ' - ' . $karyawan->loket->nama_pelayanan : '') : 'Tidak Ada',
                'id_loket' => $karyawan->id_loket,
                'total_pelanggaran' => $violations->count(),
                'total_deductions' => $totalDeductions,
                'sisa_poin' => $sisaPoin,
                'status_kinerja' => $status
            ];
        })->sortByDesc('sisa_poin')->values();

        // Calculate Average Score per Loket
        $loketKinerja = [];
        $groupedByLoket = $karyawanKinerja->groupBy('id_loket');
        $list_loket = Loket::orderBy('nama_loket', 'asc')->get();

        foreach ($list_loket as $lkt) {
            $emps = $groupedByLoket->get($lkt->id_loket);
            if ($emps && $emps->count() > 0) {
                $avgScore = $emps->avg('sisa_poin');
                $loketKinerja[] = [
                    'nama_loket' => $lkt->nama_loket . ($lkt->nama_pelayanan ? ' - ' . $lkt->nama_pelayanan : ''),
                    'average_score' => round($avgScore, 1),
                    'total_karyawan' => $emps->count()
                ];
            }
        }

        // Fetch Log of Violations (excluding administrator division)
        $logQuery = PoinKinerja::with(['profil.loket'])
            ->whereHas('profil', function($q) {
                $q->where('status_devisi', '!=', 'administrator');
            });
        if ($tgl_mulai) {
            $logQuery->where('tanggal', '>=', $tgl_mulai);
        }
        if ($tgl_selesai) {
            $logQuery->where('tanggal', '<=', $tgl_selesai);
        }
        if ($id_loket) {
            $logQuery->whereHas('profil', function($q) use ($id_loket) {
                $q->where('id_loket', $id_loket);
            });
        }
        if ($search) {
            $logQuery->whereHas('profil', function($q) use ($search) {
                $q->where('nama_user', 'like', '%' . $search . '%');
            });
        }
        $logPelanggaran = $logQuery->orderBy('tanggal', 'desc')
                                   ->orderBy('waktu_kejadian', 'desc')
                                   ->get();

        // Get Administrator profile who prints the report
        $adminId = Session::get('id_user');
        $adminProfil = Profil::with('loket')->where('id_user', $adminId)->first();

        $with_qr = $request->input('with_qr', 1);

        return view('pdf.laporan_kinerja_karyawan', compact(
            'karyawanKinerja',
            'loketKinerja',
            'logPelanggaran',
            'loketFilterName',
            'periodeFilterName',
            'tipe_laporan',
            'tgl_mulai',
            'tgl_selesai',
            'search',
            'adminProfil',
            'with_qr'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_profil' => 'required|exists:profil_karyawan,id_profil',
            'jenis_pelanggaran' => 'required|in:terlambat_absen,terlambat_buka_loket',
            'tanggal' => 'required|date',
            'waktu_kejadian' => 'required',
            'poin_dipotong' => 'required|integer|min:1|max:100',
            'keterangan' => 'required|string|max:255',
        ]);

        PoinKinerja::create([
            'id_profil' => $request->id_profil,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'waktu_kejadian' => $request->waktu_kejadian,
            'poin_dipotong' => $request->poin_dipotong,
            'keterangan' => $request->keterangan,
            'created_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil dicatat dan poin karyawan telah dipotong.');
    }

    public function destroy($id)
    {
        $violation = PoinKinerja::findOrFail($id);
        $violation->delete();

        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil dihapus dan poin karyawan telah dikembalikan.');
    }
}

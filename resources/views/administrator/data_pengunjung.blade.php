<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manajemen Data Pengunjung - Administrator</title>
    <!-- Import FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Import SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/home_super_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data_loket.css') }}">
</head>
<body>
    @extends('layout.navbar_administrator')

    @section('content')
    <style>
        .skm-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .skm-stat-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            display: flex;
            align-items: center;
            gap: 18px;
            transition: all 0.3s ease;
        }
        .skm-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }
        .visitor-charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
            align-items: stretch;
        }
        .visitor-chart-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            transition: all 0.3s ease;
        }
        .visitor-chart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }
        .badge-gender {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
        }
        .badge-gender.pria {
            background-color: #eff6ff;
            color: #3b82f6;
            border: 1px solid #dbeafe;
        }
        .badge-gender.wanita {
            background-color: #fdf2f8;
            color: #db2777;
            border: 1px solid #fbcfe8;
        }
        .img-profile-mini {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eaeaea;
        }
        @media (max-width: 992px) {
            .visitor-charts-grid {
                grid-template-columns: 1fr;
            }
        }

        /* --- STYLES FOR THE PREMIUM FILTER BOX --- */
        .filter-card {
            background: #ffffff !important;
            border-radius: 16px !important;
            padding: 24px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02) !important;
            margin-bottom: 30px !important;
        }
        .filter-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .filter-header h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-group label i {
            color: #94a3b8;
            font-size: 14px;
        }
        .filter-control {
            padding: 10px 14px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            outline: none !important;
            color: #334155 !important;
            background-color: #f8fafc !important;
            font-family: 'Poppins', sans-serif !important;
            transition: all 0.2s ease !important;
            box-sizing: border-box !important;
            width: 100% !important;
            height: 44px !important;
        }
        .filter-control:hover {
            border-color: #94a3b8 !important;
            background-color: #ffffff !important;
        }
        .filter-control:focus {
            border-color: #3b82f6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
        }
        .filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
            border-top: 1px solid #f1f5f9;
            padding-top: 18px;
        }
        .btn-filter-reset {
            padding: 10px 20px !important;
            border-radius: 8px !important;
            background: #64748b !important;
            color: white !important;
            text-decoration: none !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            font-family: 'Poppins', sans-serif !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.2s !important;
            border: none !important;
        }
        .btn-filter-reset:hover {
            background: #475569 !important;
            transform: translateY(-1px);
        }
        .btn-filter-submit {
            padding: 10px 20px !important;
            border-radius: 8px !important;
            background: #3b82f6 !important;
            color: white !important;
            border: none !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            cursor: pointer !important;
            font-family: 'Poppins', sans-serif !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.2s !important;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1) !important;
        }
        .btn-filter-submit:hover {
            background: #2563eb !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 12px -3px rgba(59, 130, 246, 0.15) !important;
        }

        /* --- STYLES FOR ANALYTICAL TABS --- */
        .analytics-tabs-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            margin-bottom: 30px;
        }
        .analytics-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .analytics-tab-btn {
            padding: 10px 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: #64748b;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .analytics-tab-btn:hover {
            background: #e2e8f0;
            color: #334155;
        }
        .analytics-tab-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }
        .analytics-tab-content {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }
        .analytics-tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="main-content">
        <!-- HEADER -->
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div class="header-left">
                <h2 style="font-size: 24px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;">👥 Data Analisis Data Pengunjung</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 4px; font-weight: 500; font-family: 'Poppins', sans-serif;">Tinjau statistik demografi umur, saluran pengambilan antrean, dan kepadatan pelayanan.</p>
            </div>
            
            <a href="{{ route('super.pengunjung.cetak', request()->query()) }}" target="_blank" class="btn-add" onclick="return confirmPrint(this, event)" style="background: #3b82f6; font-family: 'Poppins', sans-serif; gap: 8px;">
                <i class="fa-solid fa-print"></i> Cetak Laporan Analisis (PDF)
            </a>
        </div>

        <!-- BOX FILTER -->
        <div class="filter-card">
            <div class="filter-header">
                <h3><i class="fa-solid fa-filter" style="color: #3b82f6;"></i> Filter Data Pengunjung & Laporan</h3>
            </div>
            <form method="GET" action="{{ route('super.pengunjung.index') }}" id="filterForm">
                @if($search)
                    <input type="hidden" name="q" value="{{ $search }}">
                @endif
                
                <div class="filter-grid">
                    <!-- Tanggal Mulai -->
                    <div class="filter-group">
                        <label><i class="fa-regular fa-calendar-days"></i> Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-control">
                    </div>
                    
                    <!-- Tanggal Selesai -->
                    <div class="filter-group">
                        <label><i class="fa-regular fa-calendar-days"></i> Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-control">
                    </div>

                    <!-- Tahun -->
                    <div class="filter-group">
                        <label><i class="fa-solid fa-calendar-minus"></i> Tahun</label>
                        <select name="year" class="filter-control">
                            <option value="">-- Pilih Tahun --</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = 2020;
                            @endphp
                            @for($y = $currentYear + 1; $y >= $startYear; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Loket -->
                    <div class="filter-group">
                        <label><i class="fa-solid fa-building-user"></i> Loket Pelayanan</label>
                        <select name="id_loket" class="filter-control">
                            <option value="">Semua Loket</option>
                            @foreach($lokets as $loket)
                                <option value="{{ $loket->id_loket }}" {{ request('id_loket') == $loket->id_loket ? 'selected' : '' }}>
                                    {{ $loket->nama_loket }} {{ $loket->nama_pelayanan ? '- ' . $loket->nama_pelayanan : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipe Laporan yang Dicetak -->
                    <div class="filter-group">
                        <label><i class="fa-solid fa-file-invoice-dollar"></i> Laporan yang Dicetak</label>
                        <select name="tipe_laporan" class="filter-control">
                            <option value="semua" {{ request('tipe_laporan') == 'semua' || !request('tipe_laporan') ? 'selected' : '' }}>Semua Laporan</option>
                            <option value="kepadatan_bulanan" {{ request('tipe_laporan') == 'kepadatan_bulanan' ? 'selected' : '' }}>Kepadatan Bulanan</option>
                            <option value="evaluasi" {{ request('tipe_laporan') == 'evaluasi' ? 'selected' : '' }}>Rata-rata Waktu Evaluasi per Loket</option>
                            <option value="jam_sibuk" {{ request('tipe_laporan') == 'jam_sibuk' ? 'selected' : '' }}>Analisis Jam Sibuk Pengunjung</option>
                            <option value="saluran_loket" {{ request('tipe_laporan') == 'saluran_loket' ? 'selected' : '' }}>Laporan Saluran Antrean per Loket (Online/Offline)</option>
                            <option value="status_antrian" {{ request('tipe_laporan') == 'status_antrian' ? 'selected' : '' }}>Laporan Distribusi Status Antrean (Selesai/Batal/Aktif)</option>
                            <option value="detail_pengunjung" {{ request('tipe_laporan') == 'detail_pengunjung' ? 'selected' : '' }}>Detail Data Pengunjung</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('super.pengunjung.index') }}" class="btn-filter-reset">
                        <i class="fa-solid fa-rotate-left"></i> Reset Filter
                    </a>
                    <button type="submit" class="btn-filter-submit">
                        <i class="fa-solid fa-magnifying-glass"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- STATS CARDS ROW -->
        <div class="skm-stat-grid">
            <!-- Card 1: Total Pengunjung -->
            <div class="skm-stat-card">
                <div style="background: #eff6ff; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <i class="fa-solid fa-users" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Pengunjung Terdaftar</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($totalPengunjung) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Orang</span></h2>
                </div>
            </div>

            <!-- Card 2: Total Antrean -->
            <div class="skm-stat-card">
                <div style="background: #f3e8ff; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #a855f7;">
                    <i class="fa-solid fa-ticket" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Total Kunjungan Antrean</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($totalAntrean) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket</span></h2>
                </div>
            </div>

            <!-- Card 3: Tiket Online -->
            <div class="skm-stat-card">
                <div style="background: #ecfdf5; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981;">
                    <i class="fa-solid fa-globe" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Antrean Online</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($onlineCount) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket</span></h2>
                </div>
            </div>

            <!-- Card 4: Tiket Offline (Kiosk Total) -->
            <div class="skm-stat-card">
                <div style="background: #fef2f2; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ef4444;">
                    <i class="fa-solid fa-store" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Antrean Offline (Total Kiosk)</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($manualCount + $voiceCount) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket</span></h2>
                </div>
            </div>

            <!-- Card 5: Tiket Manual -->
            <div class="skm-stat-card">
                <div style="background: #fff7ed; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f97316;">
                    <i class="fa-solid fa-hand-pointer" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Antrean Manual (Sentuh)</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($manualCount) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket</span></h2>
                </div>
            </div>

            <!-- Card 6: Tiket Voice -->
            <div class="skm-stat-card">
                <div style="background: #e0e7ff; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #4f46e5;">
                    <i class="fa-solid fa-microphone" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Antrean Voice (Suara)</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($voiceCount) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket</span></h2>
                </div>
            </div>
        </div>

        <!-- VISITOR DIAGRAMS ROW -->
        <div class="visitor-charts-grid">
            <!-- Diagram A: Kepadatan Pengunjung per Loket per Hari -->
            <div class="visitor-chart-card" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif;">
                    <i class="fa-solid fa-chart-column" style="color: #3b82f6; margin-right: 8px;"></i> 
                    @if($isSingleLoket)
                        Tren Kunjungan Harian: {{ $singleLoketName }} ({{ $chartTitlePeriod }})
                    @else
                        Kepadatan Pengunjung Per Loket - {{ $chartTitlePeriod }}
                    @endif
                </h3>
                <div style="height: 450px; position: relative; width: 100%; flex: 1;">
                    @if(!empty($chartDatasets))
                        <canvas id="loketDensityChart"></canvas>
                    @else
                        <div style="text-align: center; color: #94a3b8; padding: 50px 0; font-family: 'Poppins', sans-serif;">
                            <i class="fa-solid fa-chart-column" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                            <p style="font-size: 14px; font-weight: 500;">Belum ada riwayat antrean loket</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Diagram B: Saluran Antrean per Hari -->
            <div class="visitor-chart-card" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif; width: 100%; text-align: left;">
                    <i class="fa-solid fa-chart-column" style="color: #a855f7; margin-right: 8px;"></i> Saluran Antrean - {{ $chartTitlePeriod }}
                </h3>
                <div style="height: 450px; position: relative; width: 100%; flex: 1;">
                    @if($totalAntrean > 0)
                        <canvas id="channelChart"></canvas>
                    @else
                        <div style="text-align: center; color: #94a3b8; padding: 50px 0; font-family: 'Poppins', sans-serif;">
                            <i class="fa-solid fa-chart-column" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                            <p style="font-size: 14px; font-weight: 500;">Belum ada data antrean</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- EVALUATION & STATUS DIAGRAMS ROW -->
        <div class="visitor-charts-grid" style="margin-bottom: 30px;">
            <!-- Diagram C: Rata-rata Waktu Evaluasi per Loket -->
            <div class="visitor-chart-card" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif;">
                    <i class="fa-solid fa-clock" style="color: #ef4444; margin-right: 8px;"></i> 
                    @if($isSingleLoket)
                        Rata-rata Waktu Evaluasi: {{ $singleLoketName }} - {{ $chartTitlePeriod }} (Detik)
                    @else
                        Rata-rata Waktu Evaluasi per Loket - {{ $chartTitlePeriod }} (Detik)
                    @endif
                </h3>
                <div style="height: 380px; position: relative; width: 100%; flex: 1;">
                    @if(!empty($evalValues))
                        <canvas id="evaluationTimeChart"></canvas>
                    @else
                        <div style="text-align: center; color: #94a3b8; padding: 50px 0; font-family: 'Poppins', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                            <i class="fa-solid fa-clock" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                            <p style="font-size: 14px; font-weight: 500;">Belum ada data waktu evaluasi pelayanan pada bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Diagram E: Status Distribusi Antrean -->
            <div class="visitor-chart-card" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif;">
                    <i class="fa-solid fa-circle-info" style="color: #3b82f6; margin-right: 8px;"></i> 
                    Status Distribusi Antrean - {{ $chartTitlePeriod }}
                </h3>
                <div style="height: 380px; position: relative; width: 100%; flex: 1;">
                    @if($totalAntrean > 0)
                        <canvas id="queueStatusChart"></canvas>
                    @else
                        <div style="text-align: center; color: #94a3b8; padding: 50px 0; font-family: 'Poppins', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                            <i class="fa-solid fa-circle-info" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                            <p style="font-size: 14px; font-weight: 500;">Belum ada data antrean pada bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Diagram D: Waktu Sibuk Pengunjung Per Loket -->
        <div class="visitor-chart-card" style="display: flex; flex-direction: column; margin-bottom: 30px;">
            <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif;">
                <i class="fa-solid fa-user-clock" style="color: #f59e0b; margin-right: 8px;"></i> 
                @if($isSingleLoket)
                    Analisis Jam Sibuk Pengunjung: {{ $singleLoketName }} - {{ $chartTitlePeriod }}
                @else
                    Analisis Jam Sibuk Pengunjung per Loket - {{ $chartTitlePeriod }}
                @endif
            </h3>
            <div style="height: 380px; position: relative; width: 100%;">
                @if($totalAntrean > 0)
                    <canvas id="busyHoursChart"></canvas>
                @else
                    <div style="text-align: center; color: #94a3b8; padding: 50px 0; font-family: 'Poppins', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                        <i class="fa-solid fa-user-clock" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                        <p style="font-size: 14px; font-weight: 500;">Belum ada data antrean pengunjung pada bulan ini</p>
                    </div>
                @endif
            </div>
        </div>


        <!-- TABEL ANALITIK (TABS) -->
        <div class="analytics-tabs-container">
            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; font-family: 'Poppins', sans-serif;">📊 Tabel Analisis Detail</h3>
            
            <div class="analytics-tabs">
                <button class="analytics-tab-btn active" onclick="openAnalyticsTab('tab-kepadatan')">Kepadatan Bulanan</button>
                <button class="analytics-tab-btn" onclick="openAnalyticsTab('tab-evaluasi')">Waktu Evaluasi</button>
                <button class="analytics-tab-btn" onclick="openAnalyticsTab('tab-jamsibuk')">Jam Sibuk</button>
                <button class="analytics-tab-btn" onclick="openAnalyticsTab('tab-saluran')">Saluran (Online/Offline)</button>
                <button class="analytics-tab-btn" onclick="openAnalyticsTab('tab-status')">Status Antrean</button>
            </div>

            <!-- TAB 1: KEPADATAN BULANAN -->
            <div id="tab-kepadatan" class="analytics-tab-content active">
                <h4 style="font-size: 15px; font-weight: 600; color: #3b82f6; margin-bottom: 15px; font-family: 'Poppins', sans-serif;">I. Kepadatan Pengunjung Bulanan (Tahun {{ $activeYear }})</h4>
                <div class="table-responsive">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bulan Pelayanan</th>
                                <th style="text-align: center;">Volume Kunjungan</th>
                                <th style="text-align: center;">Rasio Bulanan</th>
                                <th style="text-align: center;">Status Kepadatan</th>
                                <th>Rekomendasi Manajemen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyData as $monthNum => $dataM)
                                @php
                                    $rasio = $totalKunjunganTahun > 0 ? ($dataM['total'] / $totalKunjunganTahun) * 100 : 0;
                                    $rasioFormatted = number_format($rasio, 1) . '%';
                                    
                                    if ($rasio >= 15) {
                                        $statusName = 'Padat';
                                        $statusColor = '#dc2626';
                                        $rekomendasi = 'Siagakan petugas tambahan & optimalkan waktu layanan.';
                                    } elseif ($rasio >= 5) {
                                        $statusName = 'Sedang';
                                        $statusColor = '#ea580c';
                                        $rekomendasi = 'Pelayanan normal & pemantauan standar.';
                                    } else {
                                        $statusName = 'Sepi';
                                        $statusColor = '#059669';
                                        $rekomendasi = 'Optimalkan waktu untuk penyelesaian administrasi back-office & evaluasi layanan.';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $monthNum }}</td>
                                    <td>{{ $dataM['nama_bulan'] }}</td>
                                    <td style="text-align: center;">{{ $dataM['total'] }}</td>
                                    <td style="text-align: center;">{{ $rasioFormatted }}</td>
                                    <td style="text-align: center; color: {{ $statusColor }}; font-weight: bold;">{{ $statusName }}</td>
                                    <td>{{ $rekomendasi }}</td>
                                </tr>
                            @endforeach
                            <tr style="background-color: #f8fafc; font-weight: bold;">
                                <td colspan="2" style="text-align: right;">Total Volume Kunjungan Tahunan:</td>
                                <td style="text-align: center;">{{ $totalKunjunganTahun }}</td>
                                <td style="text-align: center;">100%</td>
                                <td colspan="2">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: EVALUASI LOKET -->
            <div id="tab-evaluasi" class="analytics-tab-content">
                <h4 style="font-size: 15px; font-weight: 600; color: #ef4444; margin-bottom: 15px; font-family: 'Poppins', sans-serif;">II. Rata-rata Waktu Evaluasi Pelayanan per Loket</h4>
                <div class="table-responsive">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Loket Pelayanan</th>
                                <th style="text-align: center;">Antrean Selesai Terlayani</th>
                                <th style="text-align: center;">Rata-rata Waktu Layanan</th>
                                <th style="text-align: center;">Status Kinerja Layanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluations as $idx => $ev)
                                @php
                                    $avgMins = $ev['avg_seconds'] / 60;
                                    if ($avgMins <= 5 && $ev['total_selesai'] > 0) {
                                        $kinerja = 'Sangat Cepat';
                                        $kColor = '#10b981';
                                    } elseif ($avgMins <= 15 && $ev['total_selesai'] > 0) {
                                        $kinerja = 'Normal';
                                        $kColor = '#3b82f6';
                                    } elseif ($ev['total_selesai'] > 0) {
                                        $kinerja = 'Lambat (Perlu Evaluasi)';
                                        $kColor = '#dc2626';
                                    } else {
                                        $kinerja = 'Belum Ada Data';
                                        $kColor = '#94a3b8';
                                    }
                                    
                                    $formattedTime = gmdate("H:i:s", $ev['avg_seconds']);
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $ev['nama_loket'] }}</td>
                                    <td style="text-align: center;">{{ $ev['total_selesai'] }} Antrean</td>
                                    <td style="text-align: center;">{{ $formattedTime }}</td>
                                    <td style="text-align: center; color: {{ $kColor }}; font-weight: bold;">{{ $kinerja }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align: center;">Tidak ada data loket</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: JAM SIBUK -->
            <div id="tab-jamsibuk" class="analytics-tab-content">
                <h4 style="font-size: 15px; font-weight: 600; color: #f59e0b; margin-bottom: 15px; font-family: 'Poppins', sans-serif;">III. Analisis Jam Sibuk Pengunjung per Loket</h4>
                <div class="table-responsive">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif; font-size: 12.5px;">
                        <thead>
                            <tr>
                                <th>Loket</th>
                                @foreach($hoursRange as $hr)
                                    <th style="text-align: center;">{{ sprintf('%02d:00', $hr) }}</th>
                                @endforeach
                                <th style="text-align: center;">Total Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($busyReports as $br)
                                <tr>
                                    <td style="font-weight: bold;">{{ $br['nama_loket'] }}</td>
                                    @foreach($hoursRange as $hr)
                                        @php
                                            $val = $br['data'][$hr];
                                            $bgClass = '';
                                            if ($val >= 10) $bgClass = 'background-color: #fee2e2; color: #dc2626; font-weight: bold;';
                                            elseif ($val >= 5) $bgClass = 'background-color: #fef3c7; color: #d97706;';
                                            elseif ($val > 0) $bgClass = 'background-color: #ecfdf5; color: #059669;';
                                        @endphp
                                        <td style="text-align: center; {{ $bgClass }}">{{ $val }}</td>
                                    @endforeach
                                    <td style="text-align: center; font-weight: bold; background: #f8fafc;">{{ $br['total'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="11" style="text-align: center;">Tidak ada data jam sibuk</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- TAB 4: SALURAN ONLINE VS OFFLINE -->
        <div id="tab-saluran" class="analytics-tab-content">
            <h4 style="font-size: 15px; font-weight: 600; color: #a855f7; margin-bottom: 15px; font-family: 'Poppins', sans-serif;">IV. Laporan Saluran Antrean per Loket (Online vs Offline)</h4>
            <div class="table-responsive">
                <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Loket Pelayanan</th>
                            <th style="text-align: center; color: #10b981;">Online (Web/QR)</th>
                            <th style="text-align: center; color: #f97316;">Manual (Sentuh)</th>
                            <th style="text-align: center; color: #4f46e5;">Voice (Suara)</th>
                            <th style="text-align: center; color: #ef4444;">Offline Total (Kiosk)</th>
                            <th style="text-align: center;">Total Keseluruhan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loketChannels as $idx => $lc)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $lc['nama_loket'] }}</td>
                                <td style="text-align: center;">{{ $lc['online'] }}</td>
                                <td style="text-align: center;">{{ $lc['manual'] }}</td>
                                <td style="text-align: center;">{{ $lc['voice'] }}</td>
                                <td style="text-align: center;">{{ $lc['offline'] }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $lc['total'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align: center;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

            <!-- TAB 5: DISTRIBUSI STATUS ANTREAN -->
            <div id="tab-status" class="analytics-tab-content">
                <h4 style="font-size: 15px; font-weight: 600; color: #3b82f6; margin-bottom: 15px; font-family: 'Poppins', sans-serif;">V. Laporan Distribusi Status Antrean per Loket</h4>
                <div class="table-responsive">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Loket Pelayanan</th>
                                <th style="text-align: center; color: #10b981;">Selesai Dilayani</th>
                                <th style="text-align: center; color: #ef4444;">Dibatalkan</th>
                                <th style="text-align: center; color: #f59e0b;">Aktif (Menunggu/Dipanggil)</th>
                                <th style="text-align: center;">Total Keseluruhan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statusLoketReports as $idx => $sr)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $sr['nama_loket'] }}</td>
                                    <td style="text-align: center;">{{ $sr['selesai'] }}</td>
                                    <td style="text-align: center;">{{ $sr['batal'] }}</td>
                                    <td style="text-align: center;">{{ $sr['aktif'] }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $sr['total'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align: center;">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TABEL DATA PENGUNJUNG -->
        <div class="table-card-container" style="margin-top: 10px;">
            <div class="card card-table">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <h4 class="card-inner-title" style="margin: 0; font-family: 'Poppins', sans-serif;">📋 Daftar Pengunjung MPP</h4>
                    
                    <form method="GET" action="{{ route('super.pengunjung.index') }}" style="display: flex; gap: 10px;">
                        @if(request('start_date')) <input type="hidden" name="start_date" value="{{ request('start_date') }}"> @endif
                        @if(request('end_date')) <input type="hidden" name="end_date" value="{{ request('end_date') }}"> @endif
                        @if(request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif
                        @if(request('id_loket')) <input type="hidden" name="id_loket" value="{{ request('id_loket') }}"> @endif
                        @if(request('tipe_laporan')) <input type="hidden" name="tipe_laporan" value="{{ request('tipe_laporan') }}"> @endif
                        
                        <input type="text" name="q" placeholder="Cari nama, email, whatsapp..." value="{{ $search }}" style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; width: 220px; font-family: 'Poppins', sans-serif;" onfocus="this.style.borderColor='#3b82f6'">
                        <button type="submit" class="btn-search-blue" style="font-family: 'Poppins', sans-serif; padding: 8px 16px; border-radius: 8px; background: #3b82f6; color: white; border: none; font-weight: 600; cursor: pointer;">Cari</button>
                        @if($search || request('start_date') || request('end_date') || request('year') || request('id_loket') || request('tipe_laporan'))
                            <a href="{{ route('super.pengunjung.index') }}" style="padding: 8px 16px; border-radius: 8px; background: #ea580c; color: white; text-decoration: none; font-size: 13.5px; font-weight: 600; font-family: 'Poppins', sans-serif;">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="table-container">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. Whatsapp</th>
                                <th>Umur</th>
                                <th style="text-align: center;">Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengunjungs as $key => $p)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <img src="{{ $p->foto ? asset('storage/'.$p->foto) : 'https://ui-avatars.com/api/?name='.urlencode($p->nama).'&background=0D8ABC&color=fff' }}" class="img-profile-mini" alt="Foto">
                                </td>
                                <td class="font-bold">{{ $p->nama }}</td>
                                <td>{{ $p->email }}</td>
                                <td>{{ $p->nomor_whatsapp }}</td>
                                <td>
                                    @if($p->tanggal_lahir)
                                        {{ \Carbon\Carbon::parse($p->tanggal_lahir)->age }} Tahun
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @php
                                        $gk = strtolower($p->jenis_kelamin);
                                        $badgeClass = ($gk == 'laki-laki' || $gk == 'pria') ? 'pria' : 'wanita';
                                        $badgeText = ($gk == 'laki-laki' || $gk == 'pria') ? 'Laki-laki' : 'Wanita';
                                    @endphp
                                    <span class="badge-gender {{ $badgeClass }}">{{ $badgeText }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 25px; color: #94a3b8; font-style: italic;">Data pengunjung tidak ditemukan...</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT CHART GENERATION & CONFIRM PRINT -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. SMOOTH LINE CHART: Kepadatan Pengunjung per Loket per Hari ---
        @if(!empty($chartDatasets))
        const densityCtx = document.getElementById('loketDensityChart').getContext('2d');
        
        // Palette of 8 beautiful colors for different lokets
        const colors = [
            '#3b82f6', // blue
            '#10b981', // emerald green
            '#f59e0b', // amber yellow
            '#a855f7', // purple
            '#f43f5e', // rose
            '#06b6d4', // cyan
            '#ec4899', // pink
            '#6366f1'  // indigo
        ];

        const rawDatasets = @json($chartDatasets);
        const datasets = rawDatasets.map((ds, index) => {
            const color = colors[index % colors.length];
            return {
                label: ds.label,
                data: ds.data,
                borderColor: color,
                backgroundColor: color + '05', // Subtle translucent background area (approx 2% opacity)
                borderWidth: 2.5,
                tension: 0.38, // Smooth curve
                fill: true,
                pointBackgroundColor: color,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 1.5,
                pointRadius: 0, // Hide points by default for clean look
                pointHitRadius: 10,
                pointHoverRadius: 6,
                pointHoverBorderWidth: 2,
                pointHoverBackgroundColor: color
            };
        });
 
        const loketDensityChart = new Chart(densityCtx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8, // Keep circle points in legend small
                            boxHeight: 8,
                            font: { family: 'Poppins', size: 10.5, weight: '500' },
                            color: '#64748b',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600', size: 12 },
                        bodyFont: { family: 'Poppins' },
                        boxPadding: 6,
                        callbacks: {
                            title: function(context) {
                                return `Tanggal ${context[0].label} {{ $chartTitlePeriod }}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0, // Force labels to stay horizontal (no tilting)
                            minRotation: 0,
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5, weight: '500' }
                        }
                    }
                }
            }
        });
        @endif
 
        // --- 2. PREMIUM GRADIENT BAR CHART: Saluran Antrean per Hari ---
        @if($totalAntrean > 0)
        const channelCtx = document.getElementById('channelChart').getContext('2d');

        // Create elegant vertical gradients for the bars
        const gradOnline = channelCtx.createLinearGradient(0, 0, 0, 360);
        gradOnline.addColorStop(0, '#34d399'); // Emerald 400
        gradOnline.addColorStop(1, '#059669'); // Emerald 600

        const gradManual = channelCtx.createLinearGradient(0, 0, 0, 360);
        gradManual.addColorStop(0, '#fb923c'); // Orange 400
        gradManual.addColorStop(1, '#ea580c'); // Orange 600

        const gradVoice = channelCtx.createLinearGradient(0, 0, 0, 360);
        gradVoice.addColorStop(0, '#818cf8'); // Indigo 400
        gradVoice.addColorStop(1, '#4f46e5'); // Indigo 600

        const gradOffline = channelCtx.createLinearGradient(0, 0, 0, 360);
        gradOffline.addColorStop(0, '#f87171'); // Red 400
        gradOffline.addColorStop(1, '#ef4444'); // Red 500

        const channelChart = new Chart(channelCtx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Online',
                        data: @json($channelOnlineData),
                        backgroundColor: gradOnline,
                        borderColor: '#059669',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.9,
                        categoryPercentage: 0.75
                    },
                    {
                        label: 'Offline Total (Kiosk)',
                        data: @json($channelOfflineData),
                        backgroundColor: gradOffline,
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.9,
                        categoryPercentage: 0.75
                    },
                    {
                        label: 'Manual (Sentuh)',
                        data: @json($channelManualData),
                        backgroundColor: gradManual,
                        borderColor: '#ea580c',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.9,
                        categoryPercentage: 0.75
                    },
                    {
                        label: 'Voice (Suara)',
                        data: @json($channelVoiceData),
                        backgroundColor: gradVoice,
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.9,
                        categoryPercentage: 0.75
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8, // Keep circle points in legend small
                            boxHeight: 8,
                            font: { family: 'Poppins', size: 10.5, weight: '500' },
                            color: '#64748b',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600', size: 12 },
                        bodyFont: { family: 'Poppins' },
                        boxPadding: 6,
                        callbacks: {
                            title: function(context) {
                                return `Tanggal ${context[0].label} {{ $chartTitlePeriod }}`;
                            },
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.parsed.y} tiket`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0, // Force labels to stay horizontal (no tilting)
                            minRotation: 0,
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5, weight: '500' }
                        }
                    }
                }
            }
        });
        @endif

        // --- 3. BAR CHART: Rata-rata Waktu Evaluasi per Loket ---
        @if(!empty($evalValues))
        const evalCtx = document.getElementById('evaluationTimeChart').getContext('2d');
        const evalGradient = evalCtx.createLinearGradient(0, 0, 0, 360);
        evalGradient.addColorStop(0, '#f87171'); // Rose/Red 400
        evalGradient.addColorStop(1, '#dc2626'); // Rose/Red 600

        const evaluationTimeChart = new Chart(evalCtx, {
            type: 'bar',
            data: {
                labels: @json($evalLabels),
                datasets: [{
                    label: 'Rata-rata Waktu Evaluasi (Detik)',
                    data: @json($evalValues),
                    backgroundColor: evalGradient,
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.4,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600', size: 12 },
                        bodyFont: { family: 'Poppins' },
                        callbacks: {
                            label: function(context) {
                                return ` Rata-rata: ${context.parsed.y} Detik`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5 },
                            callback: function(value) {
                                return value + ' dtk';
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5, weight: '500' }
                        }
                    }
                }
            }
        });
        @endif

        // --- 4. LINE CHART: Analisis Jam Sibuk Pengunjung per Loket ---
        @if($totalAntrean > 0)
        const busyCtx = document.getElementById('busyHoursChart').getContext('2d');
        const busyColors = [
            '#3b82f6', // blue
            '#10b981', // emerald green
            '#f59e0b', // amber yellow
            '#a855f7', // purple
            '#f43f5e', // rose
            '#06b6d4', // cyan
            '#ec4899', // pink
            '#6366f1'  // indigo
        ];

        const rawBusyDatasets = @json($busyDatasets);
        const busyDatasets = rawBusyDatasets.map((ds, index) => {
            const color = busyColors[index % busyColors.length];
            return {
                label: ds.label,
                data: ds.data,
                borderColor: color,
                backgroundColor: color + '08', // Subtle translucent background area
                borderWidth: 3,
                tension: 0.38, // Smooth curve
                fill: true,
                pointBackgroundColor: color,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 1.5,
                pointRadius: 2,
                pointHoverRadius: 6,
                pointHoverBorderWidth: 2,
                pointHoverBackgroundColor: color
            };
        });

        const busyHoursChart = new Chart(busyCtx, {
            type: 'line',
            data: {
                labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'],
                datasets: busyDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            font: { family: 'Poppins', size: 10.5, weight: '500' },
                            color: '#64748b',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600', size: 12 },
                        bodyFont: { family: 'Poppins' },
                        boxPadding: 6,
                        callbacks: {
                            title: function(context) {
                                return `Jam ${context[0].label} - {{ $chartTitlePeriod }}`;
                            },
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.parsed.y} Pengunjung`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5 },
                            precision: 0
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10.5, weight: '500' }
                        }
                    }
                }
            }
        });
        // --- 5. DOUGHNUT CHART: Status Distribusi Antrean ---
        @if($totalAntrean > 0)
        const statusCtx = document.getElementById('queueStatusChart').getContext('2d');
        const queueStatusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Batal', 'Aktif (Menunggu/Dipanggil)'],
                datasets: [{
                    data: [{{ $selesaiCount }}, {{ $batalCount }}, {{ $activeQueueCount }}],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { family: 'Poppins', size: 11, weight: '500' },
                            color: '#64748b',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600', size: 12 },
                        bodyFont: { family: 'Poppins' },
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = {{ $totalAntrean }};
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${value} Tiket (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
        @endif
        @endif
    });

    // --- FUNCTION ANALYTICS TABS ---
    function openAnalyticsTab(tabId) {
        // Sembunyikan semua konten tab
        const contents = document.querySelectorAll('.analytics-tab-content');
        contents.forEach(el => el.classList.remove('active'));

        // Nonaktifkan semua tombol tab
        const btns = document.querySelectorAll('.analytics-tab-btn');
        btns.forEach(el => el.classList.remove('active'));

        // Tampilkan konten yang dipilih
        document.getElementById(tabId).classList.add('active');

        // Aktifkan tombol yang diklik
        event.currentTarget.classList.add('active');
    }

    // --- FUNCTION CONFIRM PRINT (SWEETALERT2) ---
    function confirmPrint(element, event) {
        event.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Cetak Laporan',
            text: 'Apakah Anda ingin mencantumkan QR Code verifikasi pada laporan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Cantumkan',
            cancelButtonText: 'Tidak, Kosongkan',
            background: '#ffffff',
            customClass: {
                popup: 'swal2-border-radius-custom'
            }
        }).then((result) => {
            let url = new URL(element.href);
            if (result.isConfirmed) {
                url.searchParams.set('qr', '1');
                window.open(url.toString(), '_blank');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                url.searchParams.set('qr', '0');
                window.open(url.toString(), '_blank');
            }
        });
        return false;
    }
    </script>
    @endsection
</body>
</html>
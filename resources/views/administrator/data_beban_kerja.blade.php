<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Beban Kerja Karyawan - Administrator</title>
    <!-- Import FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/home_super_admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data_loket.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    @extends('layout.navbar_administrator')

    @section('content')
    <style>
        .skm-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        .img-profile-mini {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eaeaea;
        }

        /* Filter Styles */
        .filter-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            margin-bottom: 30px;
        }
        .filter-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        .filter-control {
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            width: 100%;
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
            padding: 10px 20px;
            border-radius: 8px;
            background: #64748b;
            color: white;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }
        .btn-filter-submit {
            padding: 10px 20px;
            border-radius: 8px;
            background: #3b82f6;
            color: white;
            border: none;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
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
                <h2 style="font-size: 24px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;">🧑‍💻 Laporan Beban Kerja Karyawan</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 4px; font-weight: 500; font-family: 'Poppins', sans-serif;">Tinjau performa dan jumlah antrean yang ditangani masing-masing staf loket.</p>
            </div>
            
            <button class="btn-add" style="background: #3b82f6; font-family: 'Poppins', sans-serif; gap: 8px; border: none; cursor: pointer; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600;" onclick="konfirmasiCetak(event)">
                <i class="fa-solid fa-print"></i> Cetak Laporan (Print)
            </button>
        </div>

        <!-- BOX FILTER -->
        <div class="filter-card">
            <div class="filter-header">
                <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;"><i class="fa-solid fa-filter" style="color: #3b82f6;"></i> Filter Data Analisis</h3>
            </div>
            <form method="GET" action="{{ route('beban-kerja.index') }}" id="filterForm">
                @if($search)
                    <input type="hidden" name="q" value="{{ $search }}">
                @endif
                
                <div class="filter-grid">
                    <div class="filter-group">
                        <label><i class="fa-regular fa-calendar-days"></i> Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-control">
                    </div>
                    
                    <div class="filter-group">
                        <label><i class="fa-regular fa-calendar-days"></i> Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-control">
                    </div>

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

                    <div class="filter-group">
                        <label><i class="fa-solid fa-building-user"></i> Loket Spesifik</label>
                        <select name="id_loket" class="filter-control">
                            <option value="">Semua Loket</option>
                            @foreach($lokets as $loket)
                                <option value="{{ $loket->id_loket }}" {{ request('id_loket') == $loket->id_loket ? 'selected' : '' }}>{{ $loket->nama_loket }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fa-solid fa-file-invoice-dollar"></i> Laporan yang Dicetak</label>
                        <select name="tipe_laporan" class="filter-control">
                            <option value="semua" {{ request('tipe_laporan') == 'semua' || !request('tipe_laporan') ? 'selected' : '' }}>Semua Laporan</option>
                            <option value="detail_individu" {{ request('tipe_laporan') == 'detail_individu' ? 'selected' : '' }}>Detail Beban Kerja Individu</option>
                            <option value="riwayat_harian" {{ request('tipe_laporan') == 'riwayat_harian' ? 'selected' : '' }}>Catatan Riwayat Harian Karyawan</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('beban-kerja.index') }}" class="btn-filter-reset">Reset Filter</a>
                    <button type="submit" class="btn-filter-submit">Terapkan Filter</button>
                </div>
            </form>
        </div>

        <!-- STATS CARDS ROW -->
        <div class="skm-stat-grid">
            <div class="skm-stat-card">
                <div style="background: #eff6ff; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <i class="fa-solid fa-id-card-clip" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Total SDM Aktif</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($totalKaryawanAktif) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Karyawan</span></h2>
                </div>
            </div>

            <div class="skm-stat-card">
                <div style="background: #ecfdf5; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981;">
                    <i class="fa-solid fa-circle-check" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Total Layanan Diselesaikan</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($totalAntreanSelesaiNasional) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket</span></h2>
                </div>
            </div>

            <div class="skm-stat-card">
                <div style="background: #f3e8ff; width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #a855f7;">
                    <i class="fa-solid fa-scale-balanced" style="font-size: 22px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 12.5px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Rata-rata Beban per SDM</p>
                    <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 3px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($avgBebanKerja) }} <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Tiket / Orang</span></h2>
                </div>
            </div>
        </div>

        <!-- DIAGRAM PRODUKTIVITAS -->
        <div class="filter-card" style="margin-bottom: 30px;">
            <h3 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif;">
                <i class="fa-solid fa-chart-bar" style="color: #10b981; margin-right: 8px;"></i> 
                Top 10 Karyawan Paling Produktif
            </h3>
            <div style="height: 380px; position: relative; width: 100%;">
                @if(count($chartLabels) > 0)
                    <canvas id="productivityChart"></canvas>
                @else
                    <div style="text-align: center; color: #94a3b8; padding: 50px 0; font-family: 'Poppins', sans-serif;">
                        <i class="fa-solid fa-chart-column" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                        <p style="font-size: 14px; font-weight: 500;">Belum ada data kinerja karyawan</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- TABEL BEBAN KERJA (TABS) -->
        <div class="analytics-tabs-container">
            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; font-family: 'Poppins', sans-serif;">📊 Tabel Analisis Detail Beban Kerja</h3>
            
            <div class="analytics-tabs">
                <button class="analytics-tab-btn active" onclick="openAnalyticsTab('tab-individu')">Detail Beban Kerja Individu</button>
                <button class="analytics-tab-btn" onclick="openAnalyticsTab('tab-harian')">Riwayat Harian Karyawan</button>
            </div>

            <!-- TAB 1: Detail Beban Kerja Individu -->
            <div id="tab-individu" class="analytics-tab-content active">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <h4 class="card-inner-title" style="margin: 0; font-family: 'Poppins', sans-serif;">📋 Peringkat Bulanan Karyawan</h4>
                    
                    <form method="GET" action="{{ route('beban-kerja.index') }}" style="display: flex; gap: 10px;">
                        @if(request('start_date')) <input type="hidden" name="start_date" value="{{ request('start_date') }}"> @endif
                        @if(request('end_date')) <input type="hidden" name="end_date" value="{{ request('end_date') }}"> @endif
                        @if(request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif
                        @if(request('id_loket')) <input type="hidden" name="id_loket" value="{{ request('id_loket') }}"> @endif
                        
                        <input type="text" name="q" placeholder="Cari nama karyawan..." value="{{ $search }}" style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; width: 220px; font-family: 'Poppins', sans-serif;" onfocus="this.style.borderColor='#3b82f6'">
                        <button type="submit" style="font-family: 'Poppins', sans-serif; padding: 8px 16px; border-radius: 8px; background: #3b82f6; color: white; border: none; font-weight: 600; cursor: pointer;">Cari</button>
                    </form>
                </div>

                <div class="table-container" style="overflow-x: auto;">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                        <thead>
                            <tr style="background-color: #f8fafc;">
                                <th>Peringkat</th>
                                <th>Foto</th>
                                <th>Nama Karyawan</th>
                                <th>Loket Penugasan</th>
                                <th style="text-align: center;">Tiket Diselesaikan</th>
                                <th style="text-align: center;">Tiket Terlewat</th>
                                <th style="text-align: center;">Kecepatan Rata-rata</th>
                                <th style="text-align: center;">Status Kinerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bebanKerjaRaw as $key => $k)
                                @php
                                    $avgMins = $k->avg_seconds / 60;
                                    if ($avgMins <= 5 && $k->selesai_count > 0) {
                                        $kinerja = 'Sangat Cepat';
                                        $kColor = '#10b981';
                                    } elseif ($avgMins <= 15 && $k->selesai_count > 0) {
                                        $kinerja = 'Normal';
                                        $kColor = '#3b82f6';
                                    } elseif ($k->selesai_count > 0) {
                                        $kinerja = 'Lambat';
                                        $kColor = '#dc2626';
                                    } else {
                                        $kinerja = '-';
                                        $kColor = '#94a3b8';
                                    }
                                    
                                    $formattedTime = $k->selesai_count > 0 ? gmdate("H:i:s", $k->avg_seconds) : "-";
                                    $loketName = $k->nama_loket ? $k->nama_loket . ($k->nama_pelayanan ? ' - ' . $k->nama_pelayanan : '') : 'Tidak Diketahui';
                                @endphp
                                <tr>
                                    <td style="font-weight: bold; color: #64748b;">{{ $key + 1 }}</td>
                                    <td>
                                        <img src="{{ $k->img_user && file_exists(public_path('img/foto_karyawan/' . $k->img_user)) ? asset('img/foto_karyawan/'.$k->img_user) : 'https://ui-avatars.com/api/?name='.urlencode($k->nama_user).'&background=3b82f6&color=fff' }}" class="img-profile-mini" alt="Foto">
                                    </td>
                                    <td style="font-weight: 600;">{{ $k->nama_user }}</td>
                                    <td>{{ $loketName }}</td>
                                    <td style="text-align: center; color: #10b981; font-weight: bold;">{{ $k->selesai_count }}</td>
                                    <td style="text-align: center; color: #ef4444;">{{ $k->terlewat_count }}</td>
                                    <td style="text-align: center;">{{ $formattedTime }}</td>
                                    <td style="text-align: center;">
                                        <span style="background: {{ $kColor }}20; color: {{ $kColor }}; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap;">{{ $kinerja }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 25px; color: #94a3b8; font-style: italic;">Tidak ada data karyawan melayani antrean...</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Riwayat Harian Karyawan -->
            <div id="tab-harian" class="analytics-tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
                    <h4 class="card-inner-title" style="margin: 0; font-family: 'Poppins', sans-serif;">📅 Catatan Riwayat Harian Karyawan</h4>
                </div>

                <div class="table-container" style="overflow-x: auto;">
                    <table class="table-basic" style="font-family: 'Poppins', sans-serif;">
                        <thead>
                            <tr style="background-color: #f8fafc;">
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal</th>
                                <th>Loket Penugasan</th>
                                <th style="text-align: center;">Tiket Selesai (Per Hari)</th>
                                <th style="text-align: center;">Tiket Terlewat (Per Hari)</th>
                                <th style="text-align: center;">Kecepatan Rata-rata 1 Pengunjung</th>
                                <th style="text-align: center;">Status Kinerja Harian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatHarian as $idx => $k)
                                @php
                                    $avgMins = $k->avg_seconds / 60;
                                    if ($avgMins <= 5 && $k->selesai_count > 0) {
                                        $kinerja = 'Sangat Cepat';
                                        $kColor = '#10b981';
                                    } elseif ($avgMins <= 15 && $k->selesai_count > 0) {
                                        $kinerja = 'Normal';
                                        $kColor = '#3b82f6';
                                    } elseif ($k->selesai_count > 0) {
                                        $kinerja = 'Lambat';
                                        $kColor = '#dc2626';
                                    } else {
                                        $kinerja = '-';
                                        $kColor = '#94a3b8';
                                    }
                                    
                                    $formattedTime = $k->selesai_count > 0 ? gmdate("H:i:s", $k->avg_seconds) : "-";
                                    $loketName = $k->nama_loket ? $k->nama_loket . ($k->nama_pelayanan ? ' - ' . $k->nama_pelayanan : '') : 'Tidak Diketahui';
                                    $tanggalIndo = \Carbon\Carbon::parse($k->tanggal_kerja)->translatedFormat('d M Y');
                                @endphp
                                <tr>
                                    <td style="font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                                    <td>
                                        <img src="{{ $k->img_user && file_exists(public_path('img/foto_karyawan/' . $k->img_user)) ? asset('img/foto_karyawan/'.$k->img_user) : 'https://ui-avatars.com/api/?name='.urlencode($k->nama_user).'&background=3b82f6&color=fff' }}" class="img-profile-mini" alt="Foto">
                                    </td>
                                    <td style="font-weight: 600;">{{ $k->nama_user }}</td>
                                    <td style="font-weight: bold; color: #3b82f6;">{{ $tanggalIndo }}</td>
                                    <td>{{ $loketName }}</td>
                                    <td style="text-align: center; color: #10b981; font-weight: bold;">{{ $k->selesai_count }}</td>
                                    <td style="text-align: center; color: #ef4444;">{{ $k->terlewat_count }}</td>
                                    <td style="text-align: center; font-family: monospace; font-size: 14px;">{{ $formattedTime }}</td>
                                    <td style="text-align: center;">
                                        <span style="background: {{ $kColor }}20; color: {{ $kColor }}; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap;">{{ $kinerja }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 25px; color: #94a3b8; font-style: italic;">Belum ada riwayat harian di periode ini...</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
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

    document.addEventListener('DOMContentLoaded', function() {
        @if(count($chartLabels) > 0)
        const prodCtx = document.getElementById('productivityChart').getContext('2d');
        
        // Gradient Colors
        const gradSelesai = prodCtx.createLinearGradient(0, 0, 0, 360);
        gradSelesai.addColorStop(0, '#34d399'); 
        gradSelesai.addColorStop(1, '#059669'); 

        const gradTerlewat = prodCtx.createLinearGradient(0, 0, 0, 360);
        gradTerlewat.addColorStop(0, '#f87171');
        gradTerlewat.addColorStop(1, '#dc2626');

        new Chart(prodCtx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Tiket Selesai',
                        data: @json($chartDataSelesai),
                        backgroundColor: gradSelesai,
                        borderColor: '#059669',
                        borderWidth: 1,
                        borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 },
                        maxBarThickness: 50,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Tiket Terlewat',
                        data: @json($chartDataTerlewat),
                        backgroundColor: gradTerlewat,
                        borderColor: '#dc2626',
                        borderWidth: 1,
                        borderRadius: { topLeft: 6, topRight: 6, bottomLeft: 0, bottomRight: 0 },
                        maxBarThickness: 50,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    y: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: true
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { family: 'Poppins', size: 12, weight: '500' },
                            padding: 20,
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        position: 'nearest',
                        backgroundColor: 'rgba(15, 23, 42, 0.85)',
                        titleFont: { family: 'Poppins', size: 13, weight: '600' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        boxPadding: 4,
                        usePointStyle: true,
                        animation: {
                            duration: 400,
                            easing: 'easeOutQuart'
                        }
                    }
                },
                scales: {
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { 
                            color: '#e2e8f0',
                            borderDash: [5, 5],
                            drawBorder: false
                        },
                        ticks: { 
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748b',
                            stepSize: 1
                        },
                        border: { display: false }
                    },
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { 
                            font: { family: 'Poppins', size: 12, weight: '500' },
                            color: '#475569'
                        },
                        border: { display: false }
                    }
                }
            }
        });
        @endif
    });

    function konfirmasiCetak(e) {
        e.preventDefault();
        
        // Ambil URL dasar dengan query string saat ini
        const baseUrl = "{{ route('beban-kerja.cetak', request()->query()) }}";
        
        // Cek apakah URL sudah memiliki tanda tanya (query string)
        const separator = baseUrl.includes('?') ? '&' : '?';

        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Cetak Laporan',
            text: 'Apakah Anda ingin mencantumkan QR Code verifikasi pada laporan?',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonText: 'Ya, Cantumkan',
            denyButtonText: 'Tidak, Kosongkan',
            confirmButtonColor: '#3b82f6',
            denyButtonColor: '#64748b',
            customClass: {
                title: 'swal-title-custom',
                htmlContainer: 'swal-text-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Ya, pakai QR
                window.open(baseUrl + separator + 'qr=true', '_blank');
            } else if (result.isDenied) {
                // Tidak, tanpa QR
                window.open(baseUrl + separator + 'qr=false', '_blank');
            }
        });
    }
    </script>
    @endsection
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Analisis Grafik SKM - Administrator</title>
    <!-- Import FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/home_super_admin.css') }}">
</head>
<body>
    @extends('layout.navbar_administrator')

    @section('content')
    <style>
        /* TABS CSS */
        .analytics-tabs-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            box-sizing: border-box;
            margin-top: 24px;
            margin-bottom: 30px;
        }
        .skm-filter-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .analytics-tab-btn {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-family: 'Poppins', sans-serif;
        }
        .analytics-tab-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }
        .analytics-tab-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
        }
        .analytics-tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .analytics-tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .skm-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .skm-stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        .skm-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }
        .skm-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
            align-items: stretch;
        }
        .skm-chart-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            transition: all 0.3s ease;
        }
        .skm-chart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }
        @media (max-width: 992px) {
            .skm-details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="main-content">
        <!-- HEADER -->
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <div class="header-left">
                <h2 style="font-size: 24px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;">📊 Analisis Grafik SKM Per Loket</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 4px; font-weight: 500; font-family: 'Poppins', sans-serif;">Pantau dan tinjau tingkat kepuasan pengunjung di seluruh loket pelayanan MPP.</p>
            </div>
        </div>

        <!-- GLOBAL FILTER BAR -->
        <div class="skm-chart-card" style="margin-bottom: 30px; padding: 15px 25px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <form id="loketFilterForm" method="GET" action="{{ route('data-skm.index') }}" style="display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; width: 100%;">
                
                <!-- Filter Loket -->
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; font-weight: 600; color: #475569; font-family: 'Poppins', sans-serif;">Pilih Loket (Detail):</label>
                    <select name="id_loket" style="height: 38px; padding: 0 14px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-size: 13.5px; font-family: 'Poppins', sans-serif; color: #475569; background: white; cursor: pointer; transition: all 0.2s; min-width: 220px;" onfocus="this.style.borderColor='#3b82f6'">
                        <option value="all" {{ $selectedLoketId === 'all' ? 'selected' : '' }}>🌟 Semua Loket (Global)</option>
                        @foreach($allLokets as $loket)
                            <option value="{{ $loket->id_loket }}" {{ $selectedLoketId == $loket->id_loket ? 'selected' : '' }}>
                                {{ $loket->nama_loket }} {{ $loket->nama_pelayanan ? '- ' . $loket->nama_pelayanan : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tahun -->
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; font-weight: 600; color: #475569; font-family: 'Poppins', sans-serif;">Tahun Perbandingan:</label>
                    <div style="position: relative;" class="skm-custom-dropdown">
                        <button type="button" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';" style="height: 38px; padding: 0 14px; background: white; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; text-align: left; min-width: 150px; display: flex; justify-content: space-between; align-items: center; font-size: 13.5px; font-family: 'Poppins', sans-serif; color: #475569;">
                            <span>Pilih Tahun</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 8px;"></i>
                        </button>
                        <div style="display: none; position: absolute; background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; z-index: 100; min-width: 150px; margin-top: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;">
                            @php
                                $startYear = 2020;
                                $endYear = 2035;
                            @endphp
                            @for($y = $startYear; $y <= $endYear; $y++)
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; padding: 8px 12px; font-weight: normal; font-size: 13.5px; font-family: 'Poppins', sans-serif; color: #475569; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="filter_years[]" value="{{ $y }}" {{ in_array($y, $filterYears ?? []) ? 'checked' : '' }}>
                                    {{ $y }}
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div style="display: flex; gap: 10px; align-items: flex-end; height: 100%;">
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        <label style="font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; visibility: hidden;">Terapkan</label>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" style="height: 38px; background: #3b82f6; color: white; border: 1px solid #3b82f6; padding: 0 20px; border-radius: 6px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s;">
                                <i class="fa-solid fa-filter"></i> Terapkan
                            </button>
                            <a href="{{ route('data-skm.index') }}" style="height: 38px; background: #64748b; color: white; border: 1px solid #64748b; padding: 0 20px; border-radius: 6px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none;" onmouseover="this.style.background='#475569'; this.style.borderColor='#475569'" onmouseout="this.style.background='#64748b'; this.style.borderColor='#64748b'">
                                <i class="fa-solid fa-rotate-right"></i> Reset
                            </a>
                        </div>
                    </div>

                    <!-- Tombol Cetak Laporan Dropdown -->
                    <div style="display: flex; flex-direction: column; gap: 5px; position: relative;" class="skm-custom-dropdown">
                        <label style="font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; visibility: hidden;">Cetak</label>
                        <button type="button" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';" style="height: 38px; background: #10b981; color: white; border: 1px solid #10b981; padding: 0 20px; border-radius: 6px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s;">
                            <i class="fa-solid fa-print"></i> Cetak Laporan <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                        </button>
                        <div style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 5px 0; z-index: 100; min-width: 220px; margin-top: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            <a href="{{ route('data-skm.cetak', array_merge(request()->query(), ['tipe_cetak' => 'semua'])) }}" onclick="return confirmPrint(this, event)" style="display: block; padding: 8px 15px; color: #334155; text-decoration: none; font-size: 13px; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-file-pdf" style="color: #ef4444; margin-right: 5px;"></i> Cetak Semua Tabel
                            </a>
                            <a href="{{ route('data-skm.cetak', array_merge(request()->query(), ['tipe_cetak' => 'tren'])) }}" onclick="return confirmPrint(this, event)" style="display: block; padding: 8px 15px; color: #334155; text-decoration: none; font-size: 13px; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-chart-line" style="color: #3b82f6; margin-right: 5px;"></i> Cetak Tabel Tren Bulanan
                            </a>
                            <a href="{{ route('data-skm.cetak', array_merge(request()->query(), ['tipe_cetak' => 'detail'])) }}" onclick="return confirmPrint(this, event)" style="display: block; padding: 8px 15px; color: #334155; text-decoration: none; font-size: 13px; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-list-check" style="color: #10b981; margin-right: 5px;"></i> Cetak Tabel Detail Skor
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Import SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Menutup dropdown jika klik di luar
            document.addEventListener('click', function(event) {
                const dropdowns = document.querySelectorAll('.skm-custom-dropdown');
                dropdowns.forEach(dropdown => {
                    if (!dropdown.contains(event.target)) {
                        const content = dropdown.querySelector('div');
                        if (content) content.style.display = 'none';
                    }
                });
            });

            // Konfirmasi cetak
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

        <!-- STATS CARDS ROW -->
        <div class="skm-stat-grid">
            <!-- Card 1: Total Responden -->
            <div class="skm-stat-card">
                <div style="background: #eff6ff; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <i class="fa-solid fa-users" style="font-size: 24px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 13px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Total Responden SKM</p>
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 5px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($totalRespondenAll) }} <span style="font-size: 14px; font-weight: 500; color: #94a3b8;">Orang</span></h2>
                </div>
            </div>

            <!-- Card 2: Rata-rata Skor SKM -->
            <div class="skm-stat-card">
                <div style="background: #f0fdf4; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #22c55e;">
                    <i class="fa-solid fa-star" style="font-size: 24px;"></i>
                </div>
                <div>
                    <p style="color: #64748b; font-size: 13px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Rata-rata Skor Nasional</p>
                    <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 5px 0 0 0; font-family: 'Poppins', sans-serif;">{{ number_format($avgScoreAll, 2) }} <span style="font-size: 14px; font-weight: 500; color: #94a3b8;">/ 4.00</span></h2>
                </div>
            </div>

            <!-- Card 3: Loket Terbaik -->
            <div class="skm-stat-card">
                <div style="background: #fffbeb; width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #eab308;">
                    <i class="fa-solid fa-award" style="font-size: 24px;"></i>
                </div>
                <div style="overflow: hidden;">
                    <p style="color: #64748b; font-size: 13px; font-weight: 500; margin: 0; font-family: 'Poppins', sans-serif;">Loket Rating Tertinggi</p>
                    <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 5px 0 0 0; text-transform: uppercase; font-family: 'Poppins', sans-serif; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" title="{{ $bestLoket['nama_loket'] ?? 'Belum Ada Data' }}">
                        @if($bestLoket)
                            {{ $bestLoket['nama_loket'] }} ({{ number_format($bestLoket['avg_score'], 2) }})
                        @else
                            Belum Ada Data
                        @endif
                    </h2>
                </div>
            </div>
        </div>

        <!-- DIAGRAM PERBANDINGAN LOKET -->
        <div class="skm-chart-card" style="margin-bottom: 25px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-family: 'Poppins', sans-serif;">
                <i class="fa-solid fa-chart-column" style="color: #3b82f6; margin-right: 8px;"></i> Diagram Perbandingan Nilai SKM Antar Loket
            </h3>
            <div style="height: 350px; position: relative; width: 100%;">
                <canvas id="allLoketsChart"></canvas>
            </div>
        </div>

        <!-- DIAGRAM FOKUS DETAI PER LOKET -->
        <!-- DIAGRAM FOKUS DETAI PER LOKET -->
        <div class="skm-details-grid">
            <!-- Chart Distribusi Feedback (Pie/Doughnut) -->
            <div class="skm-chart-card" style="display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;">
                        <i class="fa-solid fa-chart-pie" style="color: #10b981; margin-right: 8px;"></i> Distribusi Feedback: {{ $selectedLoketId === 'all' ? 'Semua Loket' : ($selectedLoket->nama_loket ?? 'Loket') }}
                    </h3>
                </div>

                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 280px;">
                    @if($totalRespondenSelected > 0)
                        <div style="width: 100%; max-width: 230px; height: 230px; position: relative;">
                            <canvas id="selectedLoketPieChart"></canvas>
                        </div>
                        <div style="margin-top: 25px; text-align: center; font-family: 'Poppins', sans-serif;">
                            <p style="font-size: 14px; font-weight: 500; color: #475569; margin: 0;">Rata-rata Skor: <strong style="color: #10b981;">{{ number_format($avgScoreSelected, 2) }} / 4.00</strong></p>
                            <small style="color: #94a3b8; display: block; margin-top: 3px;">Diperoleh dari total <strong>{{ $totalRespondenSelected }}</strong> responden</small>
                        </div>
                    @else
                        <div style="text-align: center; color: #94a3b8; padding: 40px 0; font-family: 'Poppins', sans-serif;">
                            <i class="fa-solid fa-comment-slash" style="font-size: 44px; margin-bottom: 12px; color: #cbd5e1;"></i>
                            <p style="font-size: 14.5px; font-weight: 500; margin: 0;">Belum ada data survei untuk loket ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chart Tren Garis (Line) -->
            <div class="skm-chart-card" style="display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;">
                        <i class="fa-solid fa-chart-line" style="color: #8b5cf6; margin-right: 8px;"></i> Tren Kepuasan Bulanan
                    </h3>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; min-height: 280px; width: 100%;">
                    @if($totalRespondenSelected > 0)
                        <div style="height: 230px; position: relative; width: 100%;">
                            <canvas id="selectedLoketTrendChart"></canvas>
                        </div>
                        <div style="margin-top: 25px; text-align: center; font-family: 'Poppins', sans-serif;">
                            <small style="color: #94a3b8; display: block;">Perkembangan rata-rata skor bulanan {{ $selectedLoketId === 'all' ? 'seluruh loket' : 'untuk loket terpilih' }}</small>
                        </div>
                    @else
                        <div style="text-align: center; color: #94a3b8; padding: 40px 0; font-family: 'Poppins', sans-serif;">
                            <i class="fa-solid fa-chart-line" style="font-size: 44px; margin-bottom: 12px; color: #cbd5e1; opacity: 0.5;"></i>
                            <p style="font-size: 14.5px; font-weight: 500; margin: 0;">Belum ada riwayat tren bulanan untuk loket ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TABEL RINCIAN PERBANDINGAN TAHUN -->
        <div class="analytics-tabs-container">
            <div class="skm-filter-buttons">
                <button type="button" class="analytics-tab-btn active" onclick="openSkmTab(event, 'tab-tren')">
                    <i class="fa-solid fa-chart-line" style="margin-right: 5px;"></i> Tren Kepuasan
                </button>
                <button type="button" class="analytics-tab-btn" onclick="openSkmTab(event, 'tab-loket')">
                    <i class="fa-solid fa-building" style="margin-right: 5px;"></i> Rincian Skor Loket
                </button>
                <button type="button" class="analytics-tab-btn" onclick="openSkmTab(event, 'tab-distribusi')">
                    <i class="fa-solid fa-chart-pie" style="margin-right: 5px;"></i> Rincian Distribusi
                </button>
            </div>

            <!-- TAB 1: TREN KEPUASAN BULANAN -->
            <div id="tab-tren" class="analytics-tab-content active">
                <div style="overflow-x: auto; padding-bottom: 10px;">
                    <table style="width: 100%; min-width: 700px; border-collapse: collapse; font-family: 'Poppins', sans-serif; font-size: 13px; white-space: nowrap;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600;">Bulan</th>
                                @foreach($filterYears ?? [] as $y)
                                    <th style="padding: 10px 15px; color: #475569; font-weight: 600; text-align: center;">Tahun {{ $y }}</th>
                                @endforeach
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600; text-align: center;">Tren Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chartLabels ?? [] as $bulan)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 10px 15px; color: #334155;">{{ $bulan }}</td>
                                    @php
                                        $lastVal = null;
                                        $secondLastVal = null;
                                        $countYears = count($filterYears ?? []);
                                        
                                        if ($countYears >= 2) {
                                            $lastYear = $filterYears[$countYears - 1];
                                            $secondLastYear = $filterYears[$countYears - 2];
                                            $lastVal = $tableRincianData[$bulan][$lastYear] ?? 0;
                                            $secondLastVal = $tableRincianData[$bulan][$secondLastYear] ?? 0;
                                        }
                                    @endphp
    
                                    @foreach($filterYears ?? [] as $y)
                                        <td style="padding: 10px 15px; text-align: center; color: #64748b; font-weight: 500;">
                                            {{ number_format($tableRincianData[$bulan][$y] ?? 0, 2) }}
                                        </td>
                                    @endforeach
                                    
                                    <td style="padding: 10px 15px; text-align: center;">
                                        @if($countYears >= 2)
                                            @if($lastVal > $secondLastVal)
                                                <span style="background: #ecfdf5; color: #10b981; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;"><i class="fa-solid fa-arrow-trend-up"></i> Meningkat</span>
                                            @elseif($lastVal < $secondLastVal)
                                                <span style="background: #fef2f2; color: #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;"><i class="fa-solid fa-arrow-trend-down"></i> Menurun</span>
                                            @else
                                                <span style="background: #fffbeb; color: #f59e0b; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;"><i class="fa-solid fa-minus"></i> Stabil</span>
                                            @endif
                                        @else
                                            <span style="color: #cbd5e1;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: SKOR PER LOKET -->
            <div id="tab-loket" class="analytics-tab-content">
                <div style="overflow-x: auto; padding-bottom: 10px;">
                    <table style="width: 100%; min-width: 600px; border-collapse: collapse; font-family: 'Poppins', sans-serif; font-size: 13px; white-space: nowrap;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600; width: 60px;">Peringkat</th>
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600;">Nama Loket</th>
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600; text-align: center;">Total Responden</th>
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600; text-align: center;">Rata-rata Skor (SKM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loketSummary ?? [] as $index => $ls)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 10px 15px; color: #334155; font-weight: 600;">
                                        @if($index == 0)
                                            <span style="color: #eab308;"><i class="fa-solid fa-crown"></i> 1</span>
                                        @elseif($index == 1)
                                            <span style="color: #94a3b8;"><i class="fa-solid fa-medal"></i> 2</span>
                                        @elseif($index == 2)
                                            <span style="color: #b45309;"><i class="fa-solid fa-medal"></i> 3</span>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td style="padding: 10px 15px; color: #334155;">{{ $ls['nama_loket'] }}</td>
                                    <td style="padding: 10px 15px; color: #64748b; text-align: center;">{{ number_format($ls['total_responden']) }}</td>
                                    <td style="padding: 10px 15px; color: #10b981; text-align: center; font-weight: 600;">{{ number_format($ls['avg_score'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Belum ada data penilaian loket.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: DISTRIBUSI FEEDBACK -->
            <div id="tab-distribusi" class="analytics-tab-content">
                <div style="overflow-x: auto; padding-bottom: 10px;">
                    <table style="width: 100%; border-collapse: collapse; font-family: 'Poppins', sans-serif; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600;">Kategori Nilai</th>
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600;">Pilihan Jawaban</th>
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600; text-align: center;">Bobot</th>
                                <th style="padding: 10px 15px; color: #475569; font-weight: 600; text-align: right;">Jumlah Jawaban</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 15px; color: #10b981; font-weight: 600;">Sangat Baik (Mutu A)</td>
                                <td style="padding: 10px 15px; color: #334155;">Sangat Bagus</td>
                                <td style="padding: 10px 15px; color: #64748b; text-align: center;">4</td>
                                <td style="padding: 10px 15px; color: #334155; font-weight: 600; text-align: right;">{{ number_format($breakdown['sangat_bagus'] ?? 0) }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 15px; color: #3b82f6; font-weight: 600;">Baik (Mutu B)</td>
                                <td style="padding: 10px 15px; color: #334155;">Bagus</td>
                                <td style="padding: 10px 15px; color: #64748b; text-align: center;">3</td>
                                <td style="padding: 10px 15px; color: #334155; font-weight: 600; text-align: right;">{{ number_format($breakdown['bagus'] ?? 0) }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 15px; color: #f59e0b; font-weight: 600;">Kurang Baik (Mutu C)</td>
                                <td style="padding: 10px 15px; color: #334155;">Kurang</td>
                                <td style="padding: 10px 15px; color: #64748b; text-align: center;">2</td>
                                <td style="padding: 10px 15px; color: #334155; font-weight: 600; text-align: right;">{{ number_format($breakdown['kurang'] ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; color: #ef4444; font-weight: 600;">Tidak Baik (Mutu D)</td>
                                <td style="padding: 10px 15px; color: #334155;">Sangat Kurang</td>
                                <td style="padding: 10px 15px; color: #64748b; text-align: center;">1</td>
                                <td style="padding: 10px 15px; color: #334155; font-weight: 600; text-align: right;">{{ number_format($breakdown['sangat_kurang'] ?? 0) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                                <td colspan="3" style="padding: 10px 15px; color: #475569; font-weight: 700; text-align: right;">Total Jawaban:</td>
                                <td style="padding: 10px 15px; color: #1e293b; font-weight: 700; text-align: right;">{{ number_format(($breakdown['sangat_bagus'] ?? 0) + ($breakdown['bagus'] ?? 0) + ($breakdown['kurang'] ?? 0) + ($breakdown['sangat_kurang'] ?? 0)) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT CHART GENERATION -->
    <script>
    function openSkmTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("analytics-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("analytics-tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. BAR CHART: Perbandingan Seluruh Loket ---
        const barCtx = document.getElementById('allLoketsChart').getContext('2d');
        const gradientBar = barCtx.createLinearGradient(0, 0, 0, 300);
        gradientBar.addColorStop(0, 'rgba(59, 130, 246, 0.85)');
        gradientBar.addColorStop(1, 'rgba(59, 130, 246, 0.35)');

        const allLoketsChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($loketSummary)->pluck('nama_loket')),
                datasets: [{
                    label: 'Skor Rata-rata SKM',
                    data: @json(collect($loketSummary)->pluck('avg_score')),
                    backgroundColor: gradientBar,
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    hoverBackgroundColor: '#2563eb',
                    maxBarThickness: 60
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600' },
                        bodyFont: { family: 'Poppins' }
                    }
                },
                scales: {
                    y: {
                        min: 1,
                        max: 4,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 11, weight: '500' }
                        }
                    }
                }
            }
        });

        // --- 2. DOUGHNUT CHART: Distribusi Feedback Terpilih ---
        @if($totalRespondenSelected > 0)
        const pieCtx = document.getElementById('selectedLoketPieChart').getContext('2d');
        const selectedLoketPieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Bagus', 'Bagus', 'Kurang', 'Sangat Kurang'],
                datasets: [{
                    data: [
                        {{ $breakdown['sangat_bagus'] }},
                        {{ $breakdown['bagus'] }},
                        {{ $breakdown['kurang'] }},
                        {{ $breakdown['sangat_kurang'] }}
                    ],
                    backgroundColor: [
                        '#10b981', // Sangat Bagus (Emerald)
                        '#3b82f6', // Bagus (Blue)
                        '#f59e0b', // Kurang (Amber)
                        '#ef4444'  // Sangat Kurang (Red)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600' },
                        bodyFont: { family: 'Poppins' },
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                return ` ${label}: ${value} tanggapan`;
                            }
                        }
                    }
                }
            }
        });

        // --- 3. LINE CHART: Tren Bulanan Loket Terpilih ---
        const trendCtx = document.getElementById('selectedLoketTrendChart').getContext('2d');
        const rawDatasets = @json($chartDatasets ?? []);
        
        const colors = [
            'rgba(139, 92, 246, 1)',  // Purple
            'rgba(16, 185, 129, 1)',  // Emerald
            'rgba(59, 130, 246, 1)',  // Blue
            'rgba(245, 158, 11, 1)',  // Amber
            'rgba(239, 68, 68, 1)',   // Red
        ];

        const chartDatasets = rawDatasets.map((ds, index) => {
            const color = colors[index % colors.length];
            return {
                label: ds.label,
                data: ds.data,
                borderColor: color,
                backgroundColor: color,
                borderWidth: 3,
                tension: 0.35,
                fill: false,
                pointBackgroundColor: color,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            };
        });

        const selectedLoketTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: chartDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 11,
                        cornerRadius: 8,
                        titleFont: { family: 'Poppins', weight: '600' },
                        bodyFont: { family: 'Poppins' },
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.parsed.y} Poin`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 4,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10 }
                        },
                        title: {
                            display: true,
                            text: 'Indeks Kepuasan (0.0 - 4.0)',
                            color: '#475569',
                            font: { family: 'Poppins', size: 11, weight: '600' }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Poppins', size: 10 }
                        },
                        title: {
                            display: true,
                            text: 'Bulan',
                            color: '#475569',
                            font: { family: 'Poppins', size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
        @endif
    });
    </script>
    @endsection
</body>
</html>
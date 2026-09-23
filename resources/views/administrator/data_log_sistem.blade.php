@extends('layout.navbar_administrator')

@section('content')
<!-- Import SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Import Google Fonts (Outfit & Poppins) -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-navy: #2b3674;
        --secondary-muted: #707eae;
        --light-bg: #f4f7fe;
        --card-border: rgba(226, 232, 240, 0.8);
        --accent-indigo: #4318ff;
    }

    .main-content {
        padding: 30px;
        background: var(--light-bg);
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
    }
    
    /* Header Section */
    .page-header-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left-title {
        display: flex;
        flex-direction: column;
    }
    
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-navy);
        margin: 0;
        letter-spacing: -0.5px;
        font-family: 'Outfit', sans-serif;
    }
    
    .page-subtitle {
        color: var(--secondary-muted);
        font-size: 13px;
        margin-top: 5px;
        font-weight: 400;
        max-width: 720px;
        line-height: 1.5;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-action-premium {
        padding: 11px 20px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.25s ease;
        border: none;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .btn-action-refresh {
        background: white;
        color: var(--primary-navy);
        border: 1px solid #e2e8f0;
    }

    .btn-action-refresh:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    .btn-action-clear {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }

    .btn-action-clear:hover {
        background: #fca5a5;
        color: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
    }

    /* KPI Cards Grid */
    .kpi-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .kpi-card-modern {
        background: #ffffff;
        border-radius: 20px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.025);
        border: 1px solid var(--card-border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .kpi-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0px 16px 35px rgba(0, 0, 0, 0.06);
    }

    .kpi-card-modern::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        opacity: 0.8;
    }

    .kpi-card-red::after { background: linear-gradient(90deg, #ef4444, #f87171); }
    .kpi-card-amber::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .kpi-card-indigo::after { background: linear-gradient(90deg, #4318ff, #818cf8); }
    .kpi-card-emerald::after { background: linear-gradient(90deg, #10b981, #34d399); }
    
    .kpi-icon-square {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-right: 18px;
        flex-shrink: 0;
    }
    
    .icon-bg-red { background: #ffebee; color: #e53935; }
    .icon-bg-amber { background: #fff8e1; color: #d97706; }
    .icon-bg-indigo { background: #f0f3ff; color: #4318ff; }
    .icon-bg-emerald { background: #e8f5e9; color: #059669; }
    
    .kpi-text-info h5 {
        font-size: 12px;
        color: #a3aed0;
        margin: 0 0 5px 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .kpi-text-info h2 {
        font-size: 26px;
        color: var(--primary-navy);
        margin: 0;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        line-height: 1.1;
    }

    .kpi-text-info span.trend-pill {
        display: inline-block;
        font-size: 11px;
        color: var(--secondary-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* Main Table Container Card */
    .data-table-card {
        background: #ffffff;
        border-radius: 22px;
        padding: 26px;
        border: 1px solid var(--card-border);
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.025);
    }

    .table-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 16px;
    }

    .filter-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--primary-navy);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .custom-select-control {
        padding: 9px 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--primary-navy);
        background: #f8fafc url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23707eae' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") no-repeat right 12px center;
        background-size: 16px;
        padding-right: 38px;
        appearance: none;
        outline: none;
        transition: all 0.2s;
    }

    .custom-select-control:focus {
        border-color: #4318ff;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.1);
    }

    .system-status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #166534;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
        animation: pulseAnimation 2s infinite;
    }

    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    /* Custom DataTables Styling Overrides */
    .dataTables_wrapper {
        font-size: 13px;
    }

    .dataTables_wrapper .dataTables_length {
        margin-bottom: 18px;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 7px 12px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        outline: none;
        background-color: #f8fafc;
        margin: 0 6px;
        font-weight: 600;
        color: var(--primary-navy);
    }

    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 18px;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 8px 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        outline: none;
        background-color: #f8fafc;
        margin-left: 8px;
        transition: all 0.2s;
        font-size: 13px;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4318ff;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.1);
    }

    /* Modern Table Grid */
    table.dataTable.no-footer {
        border-bottom: 1px solid #e2e8f0 !important;
    }

    table.dataTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }

    table.dataTable thead th {
        background: #f8fafc !important;
        color: #8f9bba !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        padding: 15px 16px !important;
        border-top: 1px solid #f1f5f9 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    table.dataTable tbody td {
        padding: 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
        color: #2b3674 !important;
        font-size: 13px !important;
    }

    table.dataTable tbody tr {
        transition: background-color 0.2s ease;
    }

    table.dataTable tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* Status & Component Badges */
    .badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
    }

    .badge-python { background: #eef2ff; color: #4318ff; border: 1px solid #e0e7ff; }
    .badge-mic { background: #fdf4ff; color: #c026d3; border: 1px solid #fae8ff; }
    .badge-whisper { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
    .badge-network { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
    .badge-database { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }
    .badge-printer { background: #ecfeff; color: #0e7490; border: 1px solid #cffafe; }
    .badge-system { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    .badge-sev-error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-sev-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }

    .badge-fallback-pill {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ip-tag {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 12px;
        color: #64748b;
        background: #f8fafc;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    /* Modern Pagination */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 10px !important;
        border: 1px solid transparent !important;
        padding: 6px 13px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        color: #707eae !important;
        background: transparent !important;
        transition: all 0.2s !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: var(--primary-navy) !important;
        border-color: #e2e8f0 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4318ff !important;
        color: white !important;
        border-color: #4318ff !important;
        box-shadow: 0 4px 10px rgba(67, 24, 255, 0.3) !important;
    }

    .dataTables_wrapper .dataTables_info {
        margin-top: 20px;
        color: #a3aed0 !important;
        font-size: 12px !important;
        font-weight: 500 !important;
    }

    .empty-state-card {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-icon-circle {
        width: 72px;
        height: 72px;
        background: #f0fdf4;
        color: #22c55e;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin: 0 auto 16px auto;
        box-shadow: 0 8px 20px rgba(34, 197, 94, 0.15);
    }
</style>

<div class="main-content">
    <!-- Header Section -->
    <div class="page-header-container">
        <div class="header-left-title">
            <h1 class="page-title">
                <i class="fa-solid fa-shield-virus" style="color: #4318ff; margin-right: 8px;"></i>
                Log Gangguan Sistem & Fallback Manual
            </h1>
            <p class="page-subtitle">
                Monitoring ketahanan sistem: otomatis mencatat kegagalan (server Python, Whisper, mikrofon, jaringan) serta memastikan ketersediaan pengalihan ke <strong>Mode Antrean Manual</strong>.
            </p>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.log_sistem.index') }}" class="btn-action-premium btn-action-refresh">
                <i class="fa-solid fa-arrows-rotate text-indigo-500"></i>
                <span>Refresh Data</span>
            </a>

            @if(count($logs) > 0)
            <form id="formClearLog" action="{{ route('admin.log_sistem.clear') }}" method="POST" style="display:inline;">
                @csrf
                <button type="button" onclick="confirmClearLog()" class="btn-action-premium btn-action-clear">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Bersihkan Log</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        });
    </script>
    @endif

    <!-- Modern KPI Cards -->
    <div class="kpi-container">
        <!-- Card 1: Total Insiden -->
        <div class="kpi-card-modern kpi-card-red">
            <div class="kpi-icon-square icon-bg-red">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="kpi-text-info">
                <h5>Total Insiden</h5>
                <h2>{{ $totalInsiden }}</h2>
                <span class="trend-pill">Sepanjang Riwayat Log</span>
            </div>
        </div>

        <!-- Card 2: Hari Ini -->
        <div class="kpi-card-modern kpi-card-amber">
            <div class="kpi-icon-square icon-bg-amber">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="kpi-text-info">
                <h5>Insiden Hari Ini</h5>
                <h2>{{ $insidenHariIni }}</h2>
                <span class="trend-pill">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <!-- Card 3: Python / Whisper -->
        <div class="kpi-card-modern kpi-card-indigo">
            <div class="kpi-icon-square icon-bg-indigo">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="kpi-text-info">
                <h5>Python / Whisper</h5>
                <h2>{{ $insidenPython }}</h2>
                <span class="trend-pill">Koneksi ASR & Service</span>
            </div>
        </div>

        <!-- Card 4: Mikrofon -->
        <div class="kpi-card-modern kpi-card-emerald">
            <div class="kpi-icon-square icon-bg-emerald">
                <i class="fa-solid fa-microphone-slash"></i>
            </div>
            <div class="kpi-text-info">
                <h5>Mikrofon / Audio</h5>
                <h2>{{ $insidenMikrofon }}</h2>
                <span class="trend-pill">Hardware & Timeout Audio</span>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="data-table-card">
        <div class="table-top-bar">
            <div class="filter-wrapper">
                <span class="filter-label">
                    <i class="fa-solid fa-filter text-indigo-500"></i> Filter Komponen:
                </span>
                <select id="filterKomponen" class="custom-select-control" onchange="applyFilter(this.value)">
                    <option value="" {{ empty($filterKomponen) ? 'selected' : '' }}>Semua Komponen</option>
                    <option value="Python" {{ $filterKomponen == 'Python' ? 'selected' : '' }}>Server Python</option>
                    <option value="Whisper" {{ $filterKomponen == 'Whisper' ? 'selected' : '' }}>Whisper ASR</option>
                    <option value="Mikrofon" {{ $filterKomponen == 'Mikrofon' ? 'selected' : '' }}>Mikrofon Audio</option>
                    <option value="Jaringan" {{ $filterKomponen == 'Jaringan' ? 'selected' : '' }}>Jaringan Internet</option>
                    <option value="Database" {{ $filterKomponen == 'Database' ? 'selected' : '' }}>Database MySQL</option>
                    <option value="Printer" {{ $filterKomponen == 'Printer' ? 'selected' : '' }}>Printer / Struk Tiket</option>
                </select>
            </div>

            <div class="system-status-indicator">
                <span class="pulse-dot"></span>
                <span>Mekanisme Fallback Otomatis: Aktif</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="dataTable" id="tableLogSistem">
                <thead>
                    <tr>
                        <th style="width: 170px;">Waktu Kejadian</th>
                        <th>Komponen</th>
                        <th>Tingkat</th>
                        <th>Detail Kesalahan</th>
                        <th>Mekanisme Fallback</th>
                        <th style="width: 120px;">IP Terminal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="font-mono text-xs font-semibold" style="color: #475569;">
                            <i class="fa-regular fa-clock mr-1 text-slate-400"></i>
                            {{ $log['waktu'] }}
                        </td>
                        <td>
                            @php
                                $komp = strtolower($log['komponen']);
                                $badgeClass = 'badge-system';
                                $isPython = false;
                                $icon = 'fa-server';

                                if (strpos($komp, 'python') !== false) {
                                    $badgeClass = 'badge-python';
                                    $isPython = true;
                                } elseif (strpos($komp, 'mikrofon') !== false || strpos($komp, 'audio') !== false) {
                                    $badgeClass = 'badge-mic';
                                    $icon = 'fa-microphone';
                                } elseif (strpos($komp, 'whisper') !== false) {
                                    $badgeClass = 'badge-whisper';
                                    $icon = 'fa-wave-square';
                                } elseif (strpos($komp, 'jaringan') !== false || strpos($komp, 'internet') !== false) {
                                    $badgeClass = 'badge-network';
                                    $icon = 'fa-wifi';
                                } elseif (strpos($komp, 'database') !== false || strpos($komp, 'mysql') !== false) {
                                    $badgeClass = 'badge-database';
                                    $icon = 'fa-database';
                                } elseif (strpos($komp, 'printer') !== false || strpos($komp, 'cetak') !== false) {
                                    $badgeClass = 'badge-printer';
                                    $icon = 'fa-print';
                                }
                            @endphp
                            <span class="badge-modern {{ $badgeClass }}">
                                @if($isPython)
                                    <svg style="width: 13px; height: 13px; display: inline-block; vertical-align: -1.5px; fill: currentColor;" viewBox="0 0 448 512">
                                        <path d="M439.8 200.5c-7.7-30.9-22.3-54.2-53.4-54.2h-40.1v47.4c0 36.8-31.2 67.5-66.8 67.5H172.7c-29.2 0-53.4 25-53.4 54.3v101.8c0 29 25.2 46 53.4 54.3 33.8 9.9 66.3 11.7 106.8 0 26.9-7.8 53.4-23.5 53.4-54.3v-40.7H226.2v-13.6h160.2c31.1 0 42.6-21.7 53.4-54.2 11.2-33.5 10.7-65.7 0-108.2zM281 408.8c-11.4 0-20.7-9.5-20.7-21.2 0-11.8 9.3-21.2 20.7-21.2 11.4 0 20.7 9.5 20.7 21.2 0 11.7-9.3 21.2-20.7 21.2zM8.2 311.5c7.7 30.9 22.3 54.2 53.4 54.2h40.1v-47.4c0-36.8 31.2-67.5 66.8-67.5h106.8c29.2 0 53.4-25 53.4-54.3V94.7c0-29-25.2-46-53.4-54.3-33.8-9.9-66.3-11.7-106.8 0-26.9 7.8-53.4 23.5-53.4 54.3v40.7h106.8v13.6H14.7c-31.1 0-42.6 21.7-53.4 54.2-11.2 33.5-10.7 65.7 0 108.2zM167 103.2c11.4 0 20.7 9.5 20.7 21.2 0 11.8-9.3 21.2-20.7 21.2-11.4 0-20.7-9.5-20.7-21.2 0-11.7 9.3-21.2 20.7-21.2z"/>
                                    </svg>
                                @else
                                    <i class="fa-solid {{ $icon }}"></i>
                                @endif
                                {{ $log['komponen'] }}
                            </span>
                        </td>
                        <td>
                            @if(strtolower($log['tingkat']) == 'error' || strtolower($log['tingkat']) == 'critical')
                                <span class="badge-modern badge-sev-error">
                                    <i class="fa-solid fa-circle-xmark"></i> {{ $log['tingkat'] }}
                                </span>
                            @else
                                <span class="badge-modern badge-sev-warning">
                                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $log['tingkat'] }}
                                </span>
                            @endif
                        </td>
                        <td style="font-weight: 500; max-width: 320px; line-height: 1.4;">
                            {{ $log['pesan'] }}
                        </td>
                        <td>
                            <span class="badge-fallback-pill">
                                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                {{ $log['aksi'] }}
                            </span>
                        </td>
                        <td>
                            <span class="ip-tag">
                                {{ $log['ip'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state-card">
                                <div class="empty-icon-circle">
                                    <i class="fa-solid fa-shield-check"></i>
                                </div>
                                <h4 style="font-size: 16px; font-weight: 700; color: #2b3674; margin: 0 0 6px 0;">Belum Ada Catatan Gangguan Sistem</h4>
                                <p style="font-size: 13px; color: #a3aed0; margin: 0;">Seluruh komponen sistem berjalan normal atau riwayat telah dibersihkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#tableLogSistem tbody tr').length > 0 && !$('#tableLogSistem tbody tr td[colspan]').length) {
            $('#tableLogSistem').DataTable({
                "pageLength": 10,
                "ordering": false,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari Riwayat Log...",
                    "lengthMenu": "Tampilkan _MENU_ baris",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ insiden",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    }
                }
            });
        }
    });

    function applyFilter(komponen) {
        if (komponen) {
            window.location.href = "{{ route('admin.log_sistem.index') }}?komponen=" + encodeURIComponent(komponen);
        } else {
            window.location.href = "{{ route('admin.log_sistem.index') }}";
        }
    }

    function confirmClearLog() {
        Swal.fire({
            title: 'Bersihkan Riwayat Log?',
            text: 'Catatan gangguan sistem akan direset. Pastikan data sudah Anda periksa atau verifikasi sebelumnya.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Bersihkan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formClearLog').submit();
            }
        });
    }
</script>
@endpush
@endsection

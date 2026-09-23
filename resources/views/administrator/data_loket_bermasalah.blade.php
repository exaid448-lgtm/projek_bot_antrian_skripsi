@extends('layout.navbar_administrator')

@section('content')
<!-- Import Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Import SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Premium Page Styles */
    .main-content {
        padding: 20px;
        font-family: 'Poppins', sans-serif;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .page-title {
        font-size: 24px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }
    
    .page-subtitle {
        color: #64748b;
        font-size: 14px;
        margin-top: 4px;
        font-weight: 500;
    }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .kpi-card {
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
    
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    }
    
    .kpi-icon {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    
    .kpi-icon.blue { background: #eff6ff; color: #3b82f6; }
    .kpi-icon.red { background: #fef2f2; color: #ef4444; }
    .kpi-icon.orange { background: #fff7ed; color: #f97316; }
    .kpi-icon.green { background: #f0fdf4; color: #22c55e; }

    .kpi-info p {
        color: #64748b;
        font-size: 12.5px;
        font-weight: 500;
        margin: 0;
    }
    
    .kpi-info h2 {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        margin: 3px 0 0 0;
    }

    /* Filters Box */
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
    
    .filter-header h3 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
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
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .filter-control {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 500;
        outline: none;
        color: #334155;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        box-sizing: border-box;
        width: 100%;
        height: 44px;
    }
    
    .filter-control:hover {
        border-color: #94a3b8;
        background-color: #ffffff;
    }
    
    .filter-control:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }
    
    .filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
        border-top: 1px solid #f1f5f9;
        padding-top: 18px;
    }
    
    .btn-reset {
        padding: 10px 20px;
        border-radius: 8px;
        background: #64748b;
        color: white;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-reset:hover {
        background: #475569;
        transform: translateY(-1px);
    }
    
    .btn-submit {
        padding: 10px 20px;
        border-radius: 8px;
        background: #3b82f6;
        color: white;
        border: none;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
    }
    
    .btn-submit:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 8px 12px -3px rgba(59, 130, 246, 0.15);
    }
    
    .btn-print {
        padding: 10px 20px;
        border-radius: 8px;
        background: #ea580c;
        color: white;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-print:hover {
        background: #c2410c;
        transform: translateY(-1px);
    }

    .btn-primary {
        padding: 10px 20px;
        border-radius: 8px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    /* Diagram & Info Grid */
    .dashboard-charts-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 25px;
        margin-bottom: 30px;
        align-items: stretch;
    }
    
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
        display: flex;
        flex-direction: column;
    }
    
    .chart-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 20px 0;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
    }
    
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    
    .table-basic {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    
    .table-basic th {
        background: #f8fafc;
        padding: 14px 16px;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 2px solid #edf2f7;
        text-transform: uppercase;
    }
    
    .table-basic td {
        padding: 16px;
        color: #334155;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    .table-basic tr:hover td {
        background: #f8fafc;
    }

    /* Badges */
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }
    
    .badge-status.aktif {
        background-color: #ecfdf5;
        color: #10b981;
        border: 1px solid #a7f3d0;
    }
    
    .badge-status.arsip {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .badge-category {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        text-transform: uppercase;
    }

    .badge-category.critical {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fee2e2;
    }
    
    .badge-category.warning {
        background: #fff7ed;
        color: #f97316;
        border: 1px solid #ffedd5;
    }
    
    .badge-category.normal {
        background: #eff6ff;
        color: #3b82f6;
        border: 1px solid #dbeafe;
    }

    /* Action Buttons */
    .btn-action-edit {
        background: #eff6ff;
        color: #3b82f6;
        border: 1px solid #dbeafe;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-action-edit:hover {
        background: #3b82f6;
        color: white;
    }
    
    .btn-action-delete {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fee2e2;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-action-delete:hover {
        background: #ef4444;
        color: white;
    }

    .btn-action-detail {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-action-detail:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* Modals overlays & boxes */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .modal-overlay.active, .modal-overlay.show {
        display: flex !important;
    }
    
    .modal-box {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 550px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        animation: slideDown 0.3s ease-out;
        border: 1px solid #f1f5f9;
    }

    @keyframes slideDown {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    
    .modal-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .btn-close-modal {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #94a3b8;
        transition: color 0.2s;
    }
    
    .btn-close-modal:hover {
        color: #64748b;
    }
    
    .modal-body {
        padding: 24px;
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: #f8fafc;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 18px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        outline: none;
        color: #334155;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 90px;
    }

    .alert-success {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 15px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Layout Responsiveness */
    @media (max-width: 992px) {
        .dashboard-charts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main-content">
    
    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h2 class="page-title">⚠️ Laporan Kendala Loket Bermasalah</h2>
            <p class="page-subtitle">Pantau tingkat keparahan, riwayat masalah, dan solusi pelayanan di setiap loket MPP.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="kpi-info">
                <p>Total Kendala</p>
                <h2>{{ $totalKendala }} Kasus</h2>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon red">
                <i class="fa-solid fa-fire"></i>
            </div>
            <div class="kpi-info">
                <p>Kategori Critical</p>
                <h2>{{ $totalCritical }} Kasus</h2>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon orange">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="kpi-info">
                <p>Kategori Warning</p>
                <h2>{{ $totalWarning }} Kasus</h2>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="kpi-info">
                <p>Status Aktif / Arsip</p>
                <h2>{{ $totalAktif }} Aktif / {{ $totalArsip }} Arsip</h2>
            </div>
        </div>
    </div>

    <!-- BOX FILTER & SEARCH -->
    <div class="filter-card">
        <div class="filter-header">
            <h3><i class="fa-solid fa-filter" style="color: #3b82f6;"></i> Filter Kendala & Cetak Laporan</h3>
        </div>
        <form method="GET" action="{{ route('data-loket-bermasalah.index') }}" id="filterForm">
            <div class="filter-grid">
                <!-- Tanggal Mulai -->
                <div class="filter-group">
                    <label><i class="fa-regular fa-calendar"></i> Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $start_date }}" class="filter-control">
                </div>
                
                <!-- Tanggal Selesai -->
                <div class="filter-group">
                    <label><i class="fa-regular fa-calendar"></i> Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $end_date }}" class="filter-control">
                </div>

                <!-- Loket -->
                <div class="filter-group">
                    <label><i class="fa-solid fa-building"></i> Pilih Loket</label>
                    <select name="id_loket" class="filter-control" onchange="this.form.submit()">
                        <option value="">Semua Loket</option>
                        @foreach($lokets as $lkt)
                            <option value="{{ $lkt->id_loket }}" {{ $id_loket == $lkt->id_loket ? 'selected' : '' }}>
                                {{ $lkt->nama_loket }} {{ $lkt->nama_pelayanan ? '- ' . $lkt->nama_pelayanan : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori -->
                <div class="filter-group">
                    <label><i class="fa-solid fa-triangle-exclamation"></i> Kategori</label>
                    <select name="kategori_info" class="filter-control" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <option value="critical" {{ $kategori_info == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="warning" {{ $kategori_info == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="normal" {{ $kategori_info == 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="filter-group">
                    <label><i class="fa-solid fa-toggle-on"></i> Status</label>
                    <select name="status_info" class="filter-control" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ $status_info == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="arsip" {{ $status_info == 'arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <div style="flex-grow: 1; max-width: 300px;">
                    <input type="text" name="search" placeholder="Cari kata kunci judul/masalah..." value="{{ $search }}" class="filter-control" style="height: 40px;">
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                <a href="{{ route('data-loket-bermasalah.index') }}" class="btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
                <a href="{{ route('data-loket-bermasalah.cetak', request()->query()) }}" target="_blank" onclick="return confirmPrint(this, event)" class="btn-print">
                    <i class="fa-solid fa-print"></i> Cetak Laporan (PDF)
                </a>
            </div>
        </form>
    </div>

    <!-- DIAGRAM & HELPER INFO GRID -->
    <div class="dashboard-charts-grid">
        <!-- Doughnut Chart: Kategori Kendala -->
        <div class="chart-card">
            <h4 class="chart-card-title"><i class="fa-solid fa-chart-pie" style="color: #3b82f6;"></i> Distribusi Kategori Kendala</h4>
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; min-height: 200px; position: relative;">
                @if($totalKendala > 0)
                    <div style="width: 100%; max-width: 180px; height: 180px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                @else
                    <div style="text-align: center; color: #94a3b8; font-style: italic;">
                        Tidak ada data kendala untuk dianalisis
                    </div>
                @endif
            </div>
        </div>

        <!-- Helper Guide Card -->
        <div class="chart-card">
            <h4 class="chart-card-title"><i class="fa-solid fa-shield-halved" style="color: #f97316;"></i> Petunjuk Operasional Kendala Pelayanan</h4>
            <div style="font-size: 13.5px; color: #475569; line-height: 1.6; display: flex; flex-direction: column; gap: 12px;">
                <p style="margin: 0;">Super Admin mengawasi seluruh laporan kendala dari operator loket. Gunakan tingkat keparahan berikut untuk penanganan masalah:</p>
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <span class="badge-category critical" style="margin-top: 3px;">CRITICAL</span>
                    <p style="margin: 0; font-size: 13px;">**Kerusakan Sistem Total / Gangguan Fatal**. Loket harus ditutup sementara dan dialihkan, membutuhkan penanganan segera (Misal: Listrik padam, Aplikasi Kasir mati).</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <span class="badge-category warning" style="margin-top: 3px;">WARNING</span>
                    <p style="margin: 0; font-size: 13px;">**Gangguan Operasional Parsial**. Layanan berjalan lambat atau ada hardware penunjang rusak. Penanganan max 1x24 jam (Misal: Printer tiket macet, Internet lambat).</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                    <span class="badge-category normal" style="margin-top: 3px;">NORMAL</span>
                    <p style="margin: 0; font-size: 13px;">**Kendala Ringan / Administratif**. Tidak menghentikan pelayanan loket, cukup dicatat untuk perbaikan berkala (Misal: Pengaturan AC, Kekurangan formulir).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA TABLE -->
    <div class="table-card">
        <div class="table-header">
            <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b;">📋 Log Riwayat Masalah Loket</h4>
        </div>
        
        <div class="table-container">
            <table class="table-basic">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 120px;">Tanggal</th>
                        <th style="width: 180px;">Nama Loket</th>
                        <th>Judul Kendala</th>
                        <th style="width: 120px; text-align: center;">Kategori</th>
                        <th style="width: 120px; text-align: center;">Status</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($problems as $key => $p)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $key + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_info)->format('d-m-Y') }}</td>
                        <td>
                            <strong>{{ $p->loket->nama_loket ?? '-' }}</strong><br>
                            <span style="font-size: 11.5px; color: #64748b;">{{ $p->loket->nama_pelayanan ?? '' }}</span>
                        </td>
                        <td>{{ $p->judul_info }}</td>
                        <td style="text-align: center;">
                            <span class="badge-category {{ $p->kategori_info }}">
                                {{ $p->kategori_info }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $p->status_info }}">
                                {{ $p->status_info }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-action-detail btnDetail" 
                                    data-loket="{{ $p->loket->nama_loket ?? '-' }} {{ $p->loket->nama_pelayanan ? '- ' . $p->loket->nama_pelayanan : '' }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($p->tanggal_info)->format('d-m-Y') }}"
                                    data-judul="{{ $p->judul_info }}"
                                    data-kategori="{{ $p->kategori_info }}"
                                    data-status="{{ $p->status_info }}"
                                    data-deskripsi="{{ $p->deskripsi_info }}"
                                    data-solusi="{{ $p->solusi_info ?: 'Belum ada solusi/penanganan yang dicatat.' }}"
                                    data-dibuat="{{ $p->created_at ? $p->created_at->format('d-m-Y H:i') . ' WITA' : '-' }}"
                                    title="Detail">Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94a3b8; font-style: italic; padding: 30px;">Tidak ada data kendala pelayanan loket ditemukan...</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL DETAIL PROBLEM -->
<div id="modalDetailProblem" class="modal-overlay">
    <div class="modal-box" style="max-width: 600px;">
        <div class="modal-header" style="background:#eff6ff;">
            <h4 id="detail_title_header">Detail Kendala Loket</h4>
            <button class="btn-close-modal btnCloseDetail">&times;</button>
        </div>
        <div class="modal-body" style="font-family: 'Poppins', sans-serif;">
            <!-- Metadata Info -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; background: #f8fafc; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f1f5f9;">
                <div>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Loket Pelayanan</span>
                    <strong id="detail_nama_loket" style="display: block; font-size: 14px; color: #1e293b; margin-top: 2px;">-</strong>
                </div>
                <div>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Tanggal Kejadian</span>
                    <strong id="detail_tanggal" style="display: block; font-size: 14px; color: #1e293b; margin-top: 2px;">-</strong>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; background: #f8fafc; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f1f5f9;">
                <div>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Kategori Kendala</span>
                    <div style="margin-top: 4px;">
                        <span id="detail_kategori" class="badge-category normal">-</span>
                    </div>
                </div>
                <div>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Status Kendala</span>
                    <div style="margin-top: 4px;">
                        <span id="detail_status" class="badge-status aktif">-</span>
                    </div>
                </div>
            </div>
            <!-- Waktu Laporan Ditambahkan -->
            <div style="background: #f8fafc; padding: 12px 18px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #f1f5f9;">
                <span style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Waktu Dilaporkan</span>
                <strong id="detail_dibuat" style="display: block; font-size: 13.5px; color: #475569; margin-top: 2px;">-</strong>
            </div>

            <!-- Problem Title & Content -->
            <div class="form-group" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 18px; margin-bottom: 18px;">
                <label style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase;">Judul Kendala</label>
                <h3 id="detail_judul" style="margin:5px 0 0 0; font-size:17px; font-weight:600; color:#1e293b;">-</h3>
            </div>

            <div class="form-group" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 18px; margin-bottom: 18px;">
                <label style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase;">Deskripsi Masalah</label>
                <p id="detail_deskripsi" style="margin:5px 0 0 0; color:#334155; line-height:1.6; white-space: pre-wrap; font-size:13.5px;">-</p>
            </div>

            <div class="form-group">
                <label style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase;">Solusi / Penanganan</label>
                <p id="detail_solusi" style="margin:5px 0 0 0; color:#1e293b; font-weight:500; line-height:1.6; white-space: pre-wrap; font-size:13.5px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px;">-</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-submit btnCloseDetail">Tutup Detail</button>
        </div>
    </div>
</div>

<script>
    // SweetAlert2 Confirmation for Print Report
    function confirmPrint(element, event) {
        event.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Cetak Laporan',
            text: 'Apakah Anda ingin mencantumkan QR Code verifikasi tanda tangan digital?',
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

    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM fully loaded and parsed");
        
        const modalDetail = document.getElementById('modalDetailProblem');
        console.log("modalDetail element found:", modalDetail);

        // Close actions (using delegation for maximum safety)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btnCloseDetail')) {
                if (modalDetail) {
                    modalDetail.classList.remove('active');
                    modalDetail.classList.remove('show');
                }
            }
            if (e.target === modalDetail) {
                if (modalDetail) {
                    modalDetail.classList.remove('active');
                    modalDetail.classList.remove('show');
                }
            }
        });

        // Event delegation to catch clicks on any element matching .btnDetail
        document.addEventListener('click', function(e) {
            const btnDetail = e.target.closest('.btnDetail');
            if (btnDetail) {
                e.preventDefault();
                console.log("Detail button clicked via delegation!");
                
                const loket = btnDetail.getAttribute('data-loket') || '-';
                const tanggal = btnDetail.getAttribute('data-tanggal') || '-';
                const judul = btnDetail.getAttribute('data-judul') || '-';
                const kategori = btnDetail.getAttribute('data-kategori') || 'normal';
                const status = btnDetail.getAttribute('data-status') || 'aktif';
                const deskripsi = btnDetail.getAttribute('data-deskripsi') || '-';
                const solusi = btnDetail.getAttribute('data-solusi') || '-';
                const dibuat = btnDetail.getAttribute('data-dibuat') || '-';

                const nameEl = document.getElementById('detail_nama_loket');
                if (nameEl) nameEl.textContent = loket;

                const dateEl = document.getElementById('detail_tanggal');
                if (dateEl) dateEl.textContent = tanggal;

                const titleEl = document.getElementById('detail_judul');
                if (titleEl) titleEl.textContent = judul;

                const descEl = document.getElementById('detail_deskripsi');
                if (descEl) descEl.textContent = deskripsi;

                const solEl = document.getElementById('detail_solusi');
                if (solEl) solEl.textContent = solusi;

                const madeEl = document.getElementById('detail_dibuat');
                if (madeEl) madeEl.textContent = dibuat;

                // Kategori Badge styling class
                const katEl = document.getElementById('detail_kategori');
                if (katEl) {
                    katEl.textContent = kategori;
                    katEl.className = 'badge-category ' + kategori;
                }

                // Status Badge styling class
                const statEl = document.getElementById('detail_status');
                if (statEl) {
                    statEl.textContent = status;
                    statEl.className = 'badge-status ' + status;
                }

                if (modalDetail) {
                    console.log("Showing modalDetail");
                    modalDetail.classList.add('active');
                    modalDetail.classList.add('show');
                } else {
                    console.error("modalDetail element is null!");
                }
            }
        });

        // Initialize Doughnut Chart (Distribus Kategori Kendala)
        @if($totalKendala > 0)
        const canvasChart = document.getElementById('categoryChart');
        if (canvasChart) {
            const ctx = canvasChart.getContext('2d');
            const categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        data: @json($chartValues),
                        backgroundColor: [
                            '#ef4444', // Critical (Red)
                            '#f97316', // Warning (Orange)
                            '#3b82f6'  // Normal (Blue)
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                font: { family: 'Poppins', size: 10.5 },
                                color: '#64748b',
                                padding: 10
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { family: 'Poppins', weight: '600' },
                            bodyFont: { family: 'Poppins' },
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} kasus`;
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif
    });
</script>
@endsection

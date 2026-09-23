@php
    $logoPath = 'img/super_admin_logo/logo.jpeg';
    $adminProfil = \App\Models\Profil::where('id_user', session('id_user'))->first();

    
    $search = request('search');

    $tgl_mulai = request('start_date');
    $tgl_selesai = request('end_date');
    $periodeFilterName = 'Semua Waktu';
    if ($tgl_mulai && $tgl_selesai) {
        $periodeFilterName = \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y');
    } elseif ($tgl_mulai) {
        $periodeFilterName = 'Sejak ' . \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y');
    } elseif ($tgl_selesai) {
        $periodeFilterName = 'Hingga ' . \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y');
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jadwal Kerja Karyawan</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 11px; 
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        .kop-surat {
            display: table;
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .kop-logo {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
        }
        .kop-logo img {
            height: 65px;
            width: auto;
            max-width: 80px;
            object-fit: contain;
        }
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-right: 80px; /* offset logo width to center text */
        }
        .main-title {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sub-title {
            font-size: 11px;
            font-weight: bold;
            margin: 3px 0 0 0;
            text-transform: uppercase;
            color: #555;
        }
        .instansi-info {
            font-size: 9px;
            margin: 2px 0 0 0;
            color: #777;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin: 20px 0 8px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            color: #1e293b;
        }
        .filter-info-container {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
            background: #fcfcfc;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
            box-sizing: border-box;
        }
        .filter-table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-table td {
            border: none;
            padding: 2px 5px;
        }
        .filter-label {
            font-weight: bold;
            width: 25%;
        }
        .filter-value {
            width: 25%;
        }
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px; 
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #ccc; 
            padding: 6px 8px; 
            text-align: left; 
        }
        table.data-table th { 
            background-color: #f7f7f7; 
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        table.data-table tr.total-row {
            background-color: #f1f5f9 !important;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer-container {
            margin-top: 35px;
            width: 100%;
            display: table;
            page-break-inside: avoid;
        }
        .footer-left {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            font-size: 10px;
            color: #555;
            line-height: 1.5;
        }
        .footer-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
            line-height: 1.5;
            padding-right: 20px;
        }
        .signature-box {
            display: inline-block;
            text-align: left;
        }
        .signature-space {
            height: 55px;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signer-role {
            font-size: 10px;
            color: #555;
        }
        .badge-status {
            font-weight: bold;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- KOP SURAT -->
    <div class="kop-surat">
        <div class="kop-logo">
            <img src="{{ asset($logoPath) }}" alt="Logo">
        </div>
        <div class="kop-text">
            <h1 class="main-title">Laporan Jadwal Kerja Karyawan</h1>
            <h2 class="sub-title">Sistem Antrean Pelayanan Publik (MPP) BANJARBARU</h2>
            <p class="instansi-info">Daftar Jadwal Penugasan dan Shift Karyawan Loket</p>
        </div>
    </div>

    <!-- METADATA & FILTER INFO -->
    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Periode Laporan:</td>
                <td class="filter-value"><strong>{{ $periodeFilterName }}</strong></td>
                <td class="filter-label">Kriteria Pencarian:</td>
                <td class="filter-value"><strong>{{ $search ? $search : 'Semua Karyawan' }}</strong></td>
            </tr>
            <tr>
                <td class="filter-label">Tanggal Cetak:</td>
                <td class="filter-value" colspan="3">{{ date('d-m-Y H:i') }} WITA</td>
            </tr>
        </table>
    </div>

    <!-- SEKSI 1: TABEL DATA JADWAL -->
    <div class="section-title">I. Daftar Jadwal Karyawan Loket</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 30%">Nama Karyawan</th>
                <th style="width: 20%; text-align: center;">Tanggal</th>
                <th style="width: 15%; text-align: center;">Jam Masuk</th>
                <th style="width: 15%; text-align: center;">Jam Pulang</th>
                <th style="width: 15%; text-align: center;">Shift</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwal as $key => $row)
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                <td style="font-weight: bold;">{{ $row->profil->nama_user ?? '-' }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                <td style="text-align: center;">{{ $row->jam_masuk }}</td>
                <td style="text-align: center;">{{ $row->jam_pulang }}</td>
                <td style="text-align: center; text-transform: capitalize;">{{ $row->shift }}</td>
                <td style="text-align: center; text-transform: capitalize;">
                    @php
                        $status = strtolower($row->status ?? $row->setatus);
                        $color = '#333';
                        if ($status == 'aktif' || $status == 'hadir') $color = '#10b981';
                        elseif ($status == 'libur') $color = '#ef4444';
                        elseif ($status == 'izin' || $status == 'sakit') $color = '#f59e0b';
                    @endphp
                    <span style="color: {{ $color }}; font-weight: bold;">
                        {{ ucfirst($status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: #777;">Tidak ada data jadwal ditemukan untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SIGNATURE AREA -->
    <div class="footer-container">
        <div class="footer-left">
            <strong>Sistem Pelayanan Antrean MPP</strong><br>
            Dicetak oleh: {{ $adminProfil?->nama_user ?? 'Admin Loket' }}<br>
            Waktu Cetak: {{ date('d-m-Y H:i') }} WITA
        </div>
        <div class="footer-right">
            <!-- Signature box dihilangkan sesuai permintaan -->
        </div>
    </div>
</body>
</html>

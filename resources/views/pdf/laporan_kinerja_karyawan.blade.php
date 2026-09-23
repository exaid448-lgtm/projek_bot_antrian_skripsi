@php
    $logoPath = 'img/super_admin_logo/logo.jpeg';
    if (isset($adminProfil) && isset($adminProfil->loket) && $adminProfil->loket->logo) {
        $logoPath = str_contains($adminProfil->loket->logo, 'logo_loket') 
            ? 'img/' . $adminProfil->loket->logo 
            : 'img/logo_loket/' . $adminProfil->loket->logo;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja & Kedisiplinan Karyawan Loket</title>
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
            <h1 class="main-title">Laporan Kinerja & Kedisiplinan Karyawan Loket</h1>
            <h2 class="sub-title">Sistem Antrean Pelayanan Publik (MPP) BANJARBARU</h2>
            <p class="instansi-info">Laporan Evaluasi Poin Kedisiplinan Petugas Pelayanan</p>
        </div>
    </div>

    <!-- METADATA & FILTER INFO -->
    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Periode Filter:</td>
                <td class="filter-value"><strong>{{ $periodeFilterName }}</strong></td>
                <td class="filter-label">Loket Filter:</td>
                <td class="filter-value"><strong>{{ $loketFilterName }}</strong></td>
            </tr>
            <tr>
                <td class="filter-label">Tipe Laporan:</td>
                <td class="filter-value">
                    @if($tipe_laporan === 'semua')
                        Rekapitulasi Kinerja & Log Pelanggaran
                    @elseif($tipe_laporan === 'rekap_kinerja')
                        Hanya Rekapitulasi Kinerja
                    @else
                        Hanya Log Rincian Pelanggaran
                    @endif
                </td>
                <td class="filter-label">Kriteria Pencarian Nama:</td>
                <td class="filter-value"><strong>{{ $search ? $search : 'Semua Karyawan' }}</strong></td>
            </tr>
            <tr>
                <td class="filter-label">Tanggal Cetak:</td>
                <td class="filter-value" colspan="3">{{ date('d-m-Y H:i') }} WITA</td>
            </tr>
        </table>
    </div>

    <!-- SEKSI 1: REKAPITULASI KINERJA KARYAWAN -->
    @if($tipe_laporan === 'semua' || $tipe_laporan === 'rekap_kinerja')
    <div class="section-title">I. Rekapitulasi Nilai Kinerja & Poin Kedisiplinan Karyawan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Nama Karyawan</th>
                <th style="width: 20%">Loket Bertugas</th>
                <th style="width: 15%">Devisi</th>
                <th style="width: 10%; text-align: center;">Pelanggaran</th>
                <th style="width: 10%; text-align: center;">Sisa Poin</th>
                <th style="width: 10%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawanKinerja as $idx => $row)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $row['nama_user'] }}</td>
                <td>{{ $row['nama_loket'] }}</td>
                <td>{{ $row['status_devisi'] }}</td>
                <td style="text-align: center; color: #e53935; font-weight: bold;">{{ $row['total_pelanggaran'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $row['sisa_poin'] }}%</td>
                <td>
                    <span class="badge-status">
                        {{ $row['status_kinerja'] }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: #777;">Tidak ada data karyawan ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SEKSI 2: RATA-RATA KINERJA PER LOKET -->
    <div class="section-title">II. Rata-rata Skor Kedisiplinan per Loket Pelayanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%">No</th>
                <th style="width: 50%">Nama Loket Pelayanan</th>
                <th style="width: 20%; text-align: center;">Total Karyawan</th>
                <th style="width: 20%; text-align: center;">Rata-rata Kedisiplinan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loketKinerja as $idx => $row)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $row['nama_loket'] }}</td>
                <td style="text-align: center;">{{ $row['total_karyawan'] }} Orang</td>
                <td style="text-align: center; font-weight: bold; color: {{ $row['average_score'] >= 90 ? '#2e7d32' : ($row['average_score'] >= 75 ? '#f57f17' : '#c62828') }};">
                    {{ $row['average_score'] }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic; color: #777;">Tidak ada data loket pelayanan bertugas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- SEKSI 3: LOG DETAIL PELANGGARAN -->
    @if($tipe_laporan === 'semua' || $tipe_laporan === 'log_pelanggaran')
    <div class="section-title">@if($tipe_laporan === 'semua') III. @else I. @endif Rincian Log Catatan Pelanggaran Karyawan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Nama Karyawan</th>
                <th style="width: 15%">Loket</th>
                <th style="width: 15%">Tanggal & Waktu</th>
                <th style="width: 15%">Jenis Pelanggaran</th>
                <th style="width: 8%; text-align: center;">Potongan</th>
                <th style="width: 17%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logPelanggaran as $idx => $log)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $log->profil->nama_user ?? 'Tidak Diketahui' }}</td>
                <td>{{ $log->profil->loket->nama_loket ?? '-' }}</td>
                <td>
                    {{ Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}
                    <br>
                    <span style="font-size: 9px; color: #777;">Pukul {{ Carbon\Carbon::parse($log->waktu_kejadian)->format('H:i') }}</span>
                </td>
                <td>{{ $log->jenis_pelanggaran === 'terlambat_absen' ? 'Terlambat Absen' : 'Terlambat Buka Loket' }}</td>
                <td style="text-align: center; color: #c62828; font-weight: bold;">-{{ $log->poin_dipotong }}</td>
                <td style="font-size: 10px;">{{ $log->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: #777;">Tidak ada catatan pelanggaran ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- SIGNATURE AREA -->
    <div class="footer-container">
        <div class="footer-left">
            <strong>Sistem Pelayanan Antrean MPP</strong><br>
            Dicetak oleh: {{ $adminProfil?->nama_user ?? session('nama', 'Administrator') }}<br>
            Level Jabatan: {{ ucfirst($adminProfil?->status_devisi ?? session('role', 'Administrator')) }}<br>
            Waktu Cetak: {{ date('d-m-Y H:i') }} WITA
        </div>
        <div class="footer-right">
            <div class="signature-box">
                Banjarbaru, {{ date('d F Y') }}<br>
                Verifikator / Administrator,
                <div class="signature-space" style="height: auto; margin: 8px 0; text-align: left;">
                    @if(isset($with_qr) && $with_qr == 1)
                        @php
                            $docData = [
                                'dokumen' => 'Laporan Kinerja & Kedisiplinan Karyawan Loket',
                                'petugas' => $adminProfil?->nama_user ?? session('nama', 'Administrator'),
                                'id_petugas' => $adminProfil?->id_user ?? session('id_user', '-'),
                                'loket' => $adminProfil?->loket?->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Admin MPP'),
                                'waktu_cetak' => date('Y-m-d H:i:s')
                            ];
                            $encryptedToken = \Illuminate\Support\Facades\Crypt::encryptString(json_encode($docData));
                            $validationUrl = route('validasi.dokumen', ['token' => $encryptedToken]);
                            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&ecc=L&margin=0&data=" . urlencode($validationUrl);
                        @endphp
                        <img src="{{ $qrCodeUrl }}" alt="QR Code Digital Signature" style="width: 70px; height: 70px; display: inline-block; margin-left: 5px; margin-top: 2px;">
                    @else
                        <div style="width: 70px; height: 70px; display: inline-block; margin-left: 5px; margin-top: 2px;"></div>
                    @endif
                </div>
                <span class="signer-name">{{ $adminProfil?->nama_user ?? session('nama', '____________________') }}</span><br>
                <span class="signer-role">NIP/ID: {{ $adminProfil?->id_user ?? session('id_user', '-') }}</span>
            </div>
        </div>
    </div>
</body>
</html>


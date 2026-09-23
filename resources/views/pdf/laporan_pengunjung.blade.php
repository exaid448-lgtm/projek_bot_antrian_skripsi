@php
    $logoPath = 'img/super_admin_logo/logo.jpeg';
    if (isset($profil) && isset($profil->loket) && $profil->loket->logo) {
        $logoPath = str_contains($profil->loket->logo, 'logo_loket') 
            ? 'img/' . $profil->loket->logo 
            : 'img/logo_loket/' . $profil->loket->logo;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Kepadatan & Demografi Pengunjung</title>
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
            <h1 class="main-title">Laporan Analisis Kepadatan & Demografi Pengunjung</h1>
            <h2 class="sub-title">Mal Pelayanan Publik (MPP) - Tahun {{ $yearVal }}</h2>
            <p class="instansi-info">Sistem Informasi Antrean Pelayanan Publik</p>
        </div>
    </div>

    <!-- DATA RINGKASAN METADATA -->
    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Total Pengunjung Terdaftar:</td>
                <td class="filter-value"><strong>{{ number_format($totalPengunjung) }} Orang</strong></td>
                <td class="filter-label">Metode Tiket Online:</td>
                <td class="filter-value">
                    {{ $totalAntrean > 0 ? number_format(($onlineCount / $totalAntrean) * 100, 1) : 0 }}% 
                    ({{ number_format($onlineCount) }} Tiket)
                </td>
            </tr>
            <tr>
                <td class="filter-label">Total Kunjungan Antrean:</td>
                <td class="filter-value"><strong>{{ number_format($totalAntrean) }} Antrean</strong></td>
                <td class="filter-label">Metode Tiket Offline:</td>
                <td class="filter-value">
                    {{ $totalAntrean > 0 ? number_format(($offlineCount / $totalAntrean) * 100, 1) : 0 }}% 
                    ({{ number_format($offlineCount) }} Tiket)
                </td>
            </tr>
            <tr>
                <td class="filter-label" style="border-top: 1px dashed #ddd; padding-top: 5px;">Filter Periode:</td>
                <td class="filter-value" style="border-top: 1px dashed #ddd; padding-top: 5px;">{{ $periodeFilterName }}</td>
                <td class="filter-label" style="border-top: 1px dashed #ddd; padding-top: 5px;">Filter Loket:</td>
                <td class="filter-value" style="border-top: 1px dashed #ddd; padding-top: 5px;">{{ $loketFilterName }}</td>
            </tr>
            <tr>
                <td class="filter-label">Tipe Laporan:</td>
                <td class="filter-value" colspan="3"><strong>{{ $tipeLaporanName }}</strong></td>
            </tr>
        </table>
    </div>

    @if($tipe_laporan === 'semua' || $tipe_laporan === 'kepadatan_bulanan')
    <!-- SEKSI 1: KEPADATAN PENGUNJUNG BULANAN (TREN TAHUNAN) -->
    <div class="section-title">I. Kepadatan Pengunjung Bulanan (Tahun {{ $yearVal }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Bulan Pelayanan</th>
                <th style="width: 15%; text-align: center;">Volume Kunjungan</th>
                <th style="width: 15%; text-align: center;">Rasio Bulanan</th>
                <th style="width: 15%; text-align: center;">Status Kepadatan</th>
                <th style="width: 30%">Rekomendasi Manajemen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $mNum => $mRow)
            @php
                $ratio = $totalKunjunganTahun > 0 ? ($mRow['total'] / $totalKunjunganTahun) * 100 : 0;
                if ($ratio > 10) {
                    $statusName = 'Padat';
                    $statusColor = '#dc2626';
                    $recom = 'Siagakan petugas tambahan & optimalkan waktu layanan.';
                } elseif ($ratio >= 5) {
                    $statusName = 'Sedang';
                    $statusColor = '#d97706';
                    $recom = 'Pelayanan normal & pemantauan standar.';
                } else {
                    $statusName = 'Sepi';
                    $statusColor = '#059669';
                    $recom = 'Optimalkan waktu untuk penyelesaian administrasi back-office & evaluasi layanan.';
                }
            @endphp
            <tr>
                <td>{{ $mNum }}</td>
                <td style="font-weight: bold;">{{ $mRow['nama_bulan'] }}</td>
                <td style="text-align: center;">{{ number_format($mRow['total']) }}</td>
                <td style="text-align: center;">{{ number_format($ratio, 1) }}%</td>
                <td style="text-align: center;">
                    <span style="color: {{ $statusColor }}; font-weight: bold;">{{ $statusName }}</span>
                </td>
                <td style="font-size: 10px; color: #555;">{{ $recom }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Volume Kunjungan Tahunan:</td>
                <td style="text-align: center;">{{ number_format($totalKunjunganTahun) }}</td>
                <td style="text-align: center;">100%</td>
                <td style="text-align: center; font-weight: bold; color: #64748b;">-</td>
                <td style="font-weight: bold; color: #64748b;">-</td>
            </tr>
        </tbody>
    </table>
    @endif


    @if($tipe_laporan === 'semua' || $tipe_laporan === 'detail_pengunjung')
    <!-- SEKSI 2: DETAIL DATA PENGUNJUNG -->
    <div class="section-title" style="page-break-before: auto;">II. Detail Data Pengunjung yang Beraktivitas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Nama Lengkap</th>
                <th style="width: 25%">Email</th>
                <th style="width: 20%">No. Whatsapp</th>
                <th style="width: 10%; text-align: center;">Umur</th>
                <th style="width: 10%; text-align: center;">Gender</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengunjungs as $idx => $p)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $p->nama }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->nomor_whatsapp }}</td>
                <td style="text-align: center;">
                    @if($p->tanggal_lahir)
                        {{ \Carbon\Carbon::parse($p->tanggal_lahir)->age }} Thn
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center;">
                    @php
                        $gk = strtolower($p->jenis_kelamin);
                        $badgeText = ($gk == 'laki-laki' || $gk == 'pria') ? 'L' : 'P';
                    @endphp
                    {{ $badgeText }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; color: #777;">Tidak ada data pengunjung yang cocok dengan kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($tipe_laporan === 'semua' || $tipe_laporan === 'evaluasi')
    <!-- SEKSI 3: RATA-RATA WAKTU EVALUASI PER LOKET -->
    <div class="section-title">III. Rata-rata Waktu Evaluasi Pelayanan per Loket</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Nama Loket Pelayanan</th>
                <th style="width: 15%; text-align: center;">Total Pelayanan Selesai</th>
                <th style="width: 20%; text-align: center;">Rata-rata Waktu Evaluasi</th>
                <th style="width: 15%; text-align: center;">Status Efisiensi</th>
                <th style="width: 20%">Rekomendasi Operasional</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluations as $idx => $eval)
            @php
                $avg = $eval['avg_seconds'];
                $totalSelesai = $eval['total_selesai'] ?? 0;
                
                if ($totalSelesai == 0) {
                    $statusName = 'Belum Ada Pelayanan';
                    $statusColor = '#64748b'; // Grey
                    $recom = 'Tidak ada rekomendasi.';
                } elseif ($avg <= 120) {
                    $statusName = 'Sangat Cepat';
                    $statusColor = '#059669'; // Green
                    $recom = 'Pertahankan efisiensi pelayanan.';
                } elseif ($avg <= 300) {
                    $statusName = 'Optimal';
                    $statusColor = '#3b82f6'; // Blue
                    $recom = 'Waktu pelayanan stabil & optimal.';
                } else {
                    $statusName = 'Lambat (Overlimit)';
                    $statusColor = '#dc2626'; // Red
                    $recom = 'Perlu evaluasi kendala loket / pelatihan petugas.';
                }
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $eval['nama_loket'] }}</td>
                <td style="text-align: center;">{{ number_format($totalSelesai) }} Pelayanan</td>
                <td style="text-align: center; font-weight: bold;">
                    @if($totalSelesai > 0)
                        @php
                            $min = floor($avg / 60);
                            $sec = round($avg % 60);
                            $formatted = $min > 0 ? "{$min} Menit {$sec} Detik" : "{$sec} Detik";
                        @endphp
                        {{ $formatted }} ({{ number_format($avg, 1) }} Detik)
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center;">
                    <span style="color: {{ $statusColor }}; font-weight: bold;">{{ $statusName }}</span>
                </td>
                <td style="font-size: 10px; color: #555;">{{ $recom }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; color: #777;">Tidak ada data waktu evaluasi yang tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($tipe_laporan === 'semua' || $tipe_laporan === 'jam_sibuk')
    <!-- SEKSI 4: ANALISIS JAM SIBUK PENGUNJUNG PER LOKET -->
    <div class="section-title">IV. Analisis Jam Sibuk Pengunjung per Loket</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%">Nama Loket Pelayanan</th>
                @foreach($hoursRange as $hr)
                <th style="text-align: center; width: 6.5%;">{{ sprintf('%02d:00', $hr) }}</th>
                @endforeach
                <th style="text-align: center; width: 10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($busyReports as $report)
            <tr>
                <td style="font-weight: bold;">{{ $report['nama_loket'] }}</td>
                @foreach($hoursRange as $hr)
                <td style="text-align: center;">
                    {{ $report['data'][$hr] > 0 ? number_format($report['data'][$hr]) : '-' }}
                </td>
                @endforeach
                <td style="text-align: center; font-weight: bold; background-color: #f1f5f9;">
                    {{ number_format($report['total']) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center; font-style: italic; color: #777;">Tidak ada data jam sibuk yang tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($tipe_laporan === 'semua' || $tipe_laporan === 'saluran_loket')
    <!-- SEKSI 5: LAPORAN SALURAN ANTREAN PER LOKET (ONLINE VS OFFLINE) -->
    <div class="section-title">V. Laporan Saluran Antrean per Loket (Online vs Offline)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Loket Pelayanan</th>
                <th style="width: 15%; text-align: center;">Online (Web/QR)</th>
                <th style="width: 15%; text-align: center;">Manual (Sentuh)</th>
                <th style="width: 10%; text-align: center;">Voice (Suara)</th>
                <th style="width: 15%; text-align: center;">Offline Total</th>
                <th style="width: 10%; text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalOnline = 0;
                $grandTotalManual = 0;
                $grandTotalVoice = 0;
                $grandTotalOffline = 0;
            @endphp
            @forelse($loketChannels as $idx => $channel)
            @php
                $grandTotalOnline += $channel['online'];
                $grandTotalManual += $channel['manual'] ?? 0;
                $grandTotalVoice += $channel['voice'] ?? 0;
                $grandTotalOffline += $channel['offline'];
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $channel['nama_loket'] }}</td>
                <td style="text-align: center;">{{ number_format($channel['online']) }}</td>
                <td style="text-align: center;">{{ number_format($channel['manual'] ?? 0) }}</td>
                <td style="text-align: center;">{{ number_format($channel['voice'] ?? 0) }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($channel['offline']) }}</td>
                <td style="text-align: center; font-weight: bold; background-color: #fcfcfc;">{{ number_format($channel['total']) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: #777;">Tidak ada data saluran antrean loket yang tersedia.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Keseluruhan:</td>
                <td style="text-align: center;">{{ number_format($grandTotalOnline) }}</td>
                <td style="text-align: center;">{{ number_format($grandTotalManual) }}</td>
                <td style="text-align: center;">{{ number_format($grandTotalVoice) }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($grandTotalOffline) }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($grandTotalOnline + $grandTotalOffline) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($tipe_laporan === 'semua' || $tipe_laporan === 'status_antrian')
    <!-- SEKSI 6: LAPORAN DISTRIBUSI STATUS ANTREAN PER LOKET -->
    <div class="section-title">VI. Laporan Distribusi Status Antrean per Loket</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Nama Loket Pelayanan</th>
                <th style="width: 11%; text-align: center; color: #059669;">Selesai</th>
                <th style="width: 11%; text-align: center; color: #dc2626;">Batal</th>
                <th style="width: 11%; text-align: center; color: #d97706;">Aktif</th>
                <th style="width: 11%; text-align: center;">Total Masuk</th>
                <th style="width: 10%; text-align: center;">Rasio</th>
                <th style="width: 16%; text-align: center;">Risiko Sepi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandSelesai = 0;
                $grandBatal = 0;
                $grandAktif = 0;
                $grandTotal = 0;
            @endphp
            @forelse($statusLoketReports as $idx => $row)
            @php
                $grandSelesai += $row['selesai'];
                $grandBatal += $row['batal'];
                $grandAktif += $row['aktif'];
                $grandTotal += $row['total'];
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $row['nama_loket'] }}</td>
                <td style="text-align: center;">{{ number_format($row['selesai']) }}</td>
                <td style="text-align: center;">{{ number_format($row['batal']) }}</td>
                <td style="text-align: center;">{{ number_format($row['aktif']) }}</td>
                <td style="text-align: center; font-weight: bold; background-color: #fcfcfc;">{{ number_format($row['total']) }}</td>
                <td style="text-align: center;">
                    {{ $totalAntrean > 0 ? number_format(($row['total'] / $totalAntrean) * 100, 1) : 0 }}%
                </td>
                <td style="text-align: center; font-size: 10px;">
                    @php
                        $pct = $totalAntrean > 0 ? ($row['total'] / $totalAntrean) * 100 : 0;
                        if ($pct < 10) {
                            $riskText = 'Tinggi (Sepi)';
                            $riskColor = '#dc2626';
                        } elseif ($pct <= 25) {
                            $riskText = 'Sedang (Cukup)';
                            $riskColor = '#d97706';
                        } else {
                            $riskText = 'Rendah (Ramai)';
                            $riskColor = '#059669';
                        }
                    @endphp
                    <span style="color: {{ $riskColor }}; font-weight: bold;">{{ $riskText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; font-style: italic; color: #777;">Tidak ada data status antrean per loket yang tersedia.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Keseluruhan:</td>
                <td style="text-align: center; color: #059669;">{{ number_format($grandSelesai) }}</td>
                <td style="text-align: center; color: #dc2626;">{{ number_format($grandBatal) }}</td>
                <td style="text-align: center; color: #d97706;">{{ number_format($grandAktif) }}</td>
                <td style="text-align: center;">{{ number_format($grandTotal) }}</td>
                <td style="text-align: center;">100%</td>
                <td style="text-align: center; font-weight: bold; color: #64748b;">-</td>
            </tr>
        </tbody>
    </table>
    @endif



    <!-- SIGNATURE AREA -->
    <div class="footer-container">
        <div class="footer-left">
            <strong>Sistem Informasi Pelayanan Publik</strong><br>
            Dicetak oleh: {{ $profil?->nama_user ?? session('nama', 'Administrator') }}<br>
            Unit Kerja: {{ $profil?->loket?->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket') }}<br>
            Waktu Cetak: {{ date('d-m-Y H:i') }} WITA
        </div>
        <div class="footer-right">
            <div class="signature-box">
                Banjarbaru, {{ date('d F Y') }}<br>
                Petugas Pelayanan,
                <div class="signature-space" style="height: auto; margin: 8px 0; text-align: left;">
                    @if(request('qr', '1') === '1')
                    @php
                        $docData = [
                            'dokumen' => 'Laporan Analisis Kepadatan & Demografi Pengunjung',
                            'petugas' => $profil?->nama_user ?? session('nama', 'Administrator'),
                            'id_petugas' => $profil?->id_user ?? session('id_user', '-'),
                            'loket' => $profil?->loket?->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket'),
                            'waktu_cetak' => date('Y-m-d H:i:s')
                        ];
                        $encryptedToken = \Illuminate\Support\Facades\Crypt::encryptString(json_encode($docData));
                        $validationUrl = route('validasi.dokumen', ['token' => $encryptedToken]);
                        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&ecc=L&margin=0&data=" . urlencode($validationUrl);
                    @endphp
                    <img src="{{ $qrCodeUrl }}" alt="QR Code Digital Signature" style="width: 70px; height: 70px; display: inline-block; margin-left: 5px; margin-top: 2px;">
                    @else
                    <div style="height: 75px;"></div>
                    @endif
                </div>
                <span class="signer-name">{{ $profil?->nama_user ?? session('nama', '____________________') }}</span><br>
                <span class="signer-role">NIP/ID: {{ $profil?->id_user ?? session('id_user', '-') }}</span>
            </div>
        </div>
    </div>
</body>
</html>


@php
    $adminProfil = \Illuminate\Support\Facades\DB::table('profil_karyawan')->where('id_user', session('id_user'))->first();
    $logoPath = 'img/super_admin_logo/logo.jpeg';
    
    $search = request('q');
    $start_date = request('start_date');
    $end_date = request('end_date');
    $year = request('year');
    $loketFilterName = request('id_loket') ? \Illuminate\Support\Facades\DB::table('loket')->where('id_loket', request('id_loket'))->value('nama_loket') : 'Semua Loket';

    $periodeFilterName = 'Bulan Ini';
    if ($start_date && $end_date) {
        $periodeFilterName = \Carbon\Carbon::parse($start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($end_date)->format('d/m/Y');
    } elseif ($start_date) {
        $periodeFilterName = 'Sejak ' . \Carbon\Carbon::parse($start_date)->format('d/m/Y');
    } elseif ($end_date) {
        $periodeFilterName = 'Hingga ' . \Carbon\Carbon::parse($end_date)->format('d/m/Y');
    } elseif ($year) {
        $periodeFilterName = 'Tahun ' . $year;
    }

    $with_qr = request('qr') === 'true' ? 1 : 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Beban Kerja Karyawan</title>
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
            <h1 class="main-title">Laporan Beban Kerja Karyawan</h1>
            <h2 class="sub-title">Sistem Antrean Pelayanan Publik (MPP) BANJARBARU</h2>
            <p class="instansi-info">Evaluasi Performa, Kinerja, dan Jumlah Antrean yang Ditangani</p>
        </div>
    </div>

    <!-- METADATA & FILTER INFO -->
    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Periode Laporan:</td>
                <td class="filter-value"><strong>{{ $periodeFilterName }}</strong></td>
                <td class="filter-label">Loket Filter:</td>
                <td class="filter-value"><strong>{{ $loketFilterName }}</strong></td>
            </tr>
            <tr>
                <td class="filter-label">Kriteria Pencarian:</td>
                <td class="filter-value"><strong>{{ $search ? $search : 'Semua Data' }}</strong></td>
                <td class="filter-label">Tanggal Cetak:</td>
                <td class="filter-value">{{ date('d-m-Y H:i') }} WITA</td>
            </tr>
        </table>
    </div>

    @php $tipe = request('tipe_laporan', 'semua'); @endphp

    @if($tipe === 'semua' || $tipe === 'detail_individu')
    <div class="section-title">I. Rincian Data Beban Kerja Karyawan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%">Nama Karyawan</th>
                <th style="width: 20%">Loket Operasional</th>
                <th style="width: 15%; text-align: center;">Total Ditangani</th>
                <th style="width: 15%; text-align: center;">Tuntas (Selesai)</th>
                <th style="width: 10%; text-align: center;">Terlewat</th>
                <th style="width: 10%; text-align: center;">Rata-rata Waktu</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumDitangani = 0;
                $sumSelesai = 0;
                $sumTerlewat = 0;
            @endphp
            @forelse($bebanKerjaRaw as $idx => $row)
                @php
                    $sumDitangani += $row->total_ditangani;
                    $sumSelesai += $row->selesai_count;
                    $sumTerlewat += $row->terlewat_count;
                    
                    $menit = floor($row->avg_seconds / 60);
                    $detik = round($row->avg_seconds % 60);
                @endphp
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $row->nama_user }}</td>
                <td>{{ $row->nama_loket }} {{ $row->nama_pelayanan ? '- ' . $row->nama_pelayanan : '' }}</td>
                <td style="text-align: center; color: #3b82f6; font-weight: bold;">{{ $row->total_ditangani }}</td>
                <td style="text-align: center; color: #10b981; font-weight: bold;">{{ $row->selesai_count }}</td>
                <td style="text-align: center; color: #ef4444; font-weight: bold;">{{ $row->terlewat_count }}</td>
                <td style="text-align: center;">{{ $menit }}m {{ $detik }}s</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: #777;">Tidak ada data beban kerja ditemukan pada periode dan kriteria ini.</td>
            </tr>
            @endforelse
            @if(count($bebanKerjaRaw) > 0)
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 15px;">TOTAL AKUMULASI NASIONAL</td>
                <td style="text-align: center;">{{ $sumDitangani }}</td>
                <td style="text-align: center;">{{ $sumSelesai }}</td>
                <td style="text-align: center;">{{ $sumTerlewat }}</td>
                <td style="text-align: center;">-</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    @if($tipe === 'semua' || $tipe === 'riwayat_harian')
    <div class="section-title">{{ $tipe === 'semua' ? 'II.' : 'I.' }} Catatan Riwayat Harian Karyawan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%">Nama Karyawan</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%">Loket Operasional</th>
                <th style="width: 12%; text-align: center;">Selesai (Per Hari)</th>
                <th style="width: 12%; text-align: center;">Terlewat (Per Hari)</th>
                <th style="width: 11%; text-align: center;">Rata-rata Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatHarian as $idx => $row)
                @php
                    $menit = floor($row->avg_seconds / 60);
                    $detik = round($row->avg_seconds % 60);
                    $tanggalIndo = \Carbon\Carbon::parse($row->tanggal_kerja)->translatedFormat('d M Y');
                @endphp
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $row->nama_user }}</td>
                <td style="font-weight: bold; color: #3b82f6;">{{ $tanggalIndo }}</td>
                <td>{{ $row->nama_loket }} {{ $row->nama_pelayanan ? '- ' . $row->nama_pelayanan : '' }}</td>
                <td style="text-align: center; color: #10b981; font-weight: bold;">{{ $row->selesai_count }}</td>
                <td style="text-align: center; color: #ef4444; font-weight: bold;">{{ $row->terlewat_count }}</td>
                <td style="text-align: center;">{{ $menit }}m {{ $detik }}s</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; color: #777;">Tidak ada catatan riwayat harian pada periode dan kriteria ini.</td>
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
                Administrator,
                <div class="signature-space" style="height: auto; margin: 8px 0; text-align: left;">
                    @if(isset($with_qr) && $with_qr == 1)
                        @php
                            $docData = [
                                'dokumen' => 'Laporan Beban Kerja Karyawan',
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


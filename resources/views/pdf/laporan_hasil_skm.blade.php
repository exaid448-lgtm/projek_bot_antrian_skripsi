@php
    $logoPath = 'img/super_admin_logo/logo.jpeg';
    if (isset($profil->loket) && $profil->loket->logo) {
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
    <title>Laporan Hasil SKM</title>
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
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sub-title {
            font-size: 12px;
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
            width: 15%;
        }
        .filter-value {
            width: 35%;
        }
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #ccc; 
            padding: 8px 10px; 
            text-align: left; 
        }
        table.data-table th { 
            background-color: #f7f7f7; 
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        .skm-badge-success { 
            background-color: #e6fffa; 
            color: #234e52; 
            border: 1px solid #b2f5ea; 
        }
        .skm-badge-warning { 
            background-color: #fffaf0; 
            color: #7b341e; 
            border: 1px solid #feebc8; 
        }
        .footer-container {
            margin-top: 40px;
            width: 100%;
            display: table;
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
    <div class="kop-surat">
        <div class="kop-logo">
            <img src="{{ asset($logoPath) }}" alt="Logo">
        </div>
        <div class="kop-text">
            <h1 class="main-title">Laporan Survei Kepuasan Masyarakat (SKM)</h1>
            <h2 class="sub-title">LOKET: {{ $loketNama }}</h2>
            <p class="instansi-info">Sistem Antrean Pelayanan Publik</p>
        </div>
    </div>

    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Periode Mulai:</td>
                <td class="filter-value">{{ $tglMulai ? \Carbon\Carbon::parse($tglMulai)->format('d-m-Y') : 'Semua Tanggal' }}</td>
                <td class="filter-label">Total Responden:</td>
                <td class="filter-value">{{ number_format($totalResponden) }} Orang</td>
            </tr>
            <tr>
                <td class="filter-label">Periode Selesai:</td>
                <td class="filter-value">{{ $tglSelesai ? \Carbon\Carbon::parse($tglSelesai)->format('d-m-Y') : 'Semua Tanggal' }}</td>
                <td class="filter-label">Rata-rata Poin:</td>
                <td class="filter-value">{{ number_format($rataRataPoin, 2) }} / 4.0 ({{ $predikatUmum }})</td>
            </tr>
            <tr>
                <td class="filter-label">Tahun Perbandingan:</td>
                <td class="filter-value" colspan="3">{{ implode(', ', $filterYears ?? []) }}</td>
            </tr>
        </table>
    </div>

    <!-- TABEL RINCIAN PERBANDINGAN TAHUN -->
    @if(in_array(request('tipe_cetak', 'semua'), ['semua', 'tren']))
    <h3 style="font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">Rincian Perbandingan Kepuasan Bulanan</h3>
    <table class="data-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 20%">Bulan</th>
                @foreach($filterYears ?? [] as $y)
                    <th style="text-align: center;">Tahun {{ $y }}</th>
                @endforeach
                <th style="text-align: center; width: 15%">Tren Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chartLabels ?? [] as $bulan)
                <tr>
                    <td>{{ $bulan }}</td>
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
                        <td style="text-align: center;">{{ number_format($tableRincianData[$bulan][$y] ?? 0, 2) }}</td>
                    @endforeach
                    
                    <td style="text-align: center;">
                        @if($countYears >= 2)
                            @if($lastVal > $secondLastVal)
                                Meningkat
                            @elseif($lastVal < $secondLastVal)
                                Menurun
                            @else
                                Stabil
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(in_array(request('tipe_cetak', 'semua'), ['semua', 'detail']))
    <h3 style="font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">Detail Responden SKM</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nomor Antrian</th>
                <th style="width: 25%">Loket</th>
                <th style="width: 25%">Tanggal Pengisian</th>
                <th style="width: 12%; text-align: center;">Rata-rata Poin</th>
                <th style="width: 13%; text-align: center;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tableData as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['nomor_antrian'] }}</td>
                <td>{{ $row['loket'] }}</td>
                <td>{{ $row['tanggal'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $row['avg_score'] }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $row['badge_class'] }}">{{ $row['predikat'] }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; color: #777; padding: 20px;">Tidak ada data survei.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div class="footer-container">
        <div class="footer-left">
            <strong>Sistem Informasi Antrean</strong><br>
            Dicetak oleh: {{ $profil->nama_user ?? session('nama', 'Administrator') }}<br>
            Unit Kerja: {{ $profil->loket->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket') }}<br>
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
                            'dokumen' => 'Laporan Hasil SKM',
                            'petugas' => $profil->nama_user ?? session('nama', 'Administrator'),
                            'id_petugas' => $profil->id_user ?? session('id_user', '-'),
                            'loket' => $profil->loket->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket'),
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
                <span class="signer-name">{{ $profil->nama_user ?? session('nama', '____________________') }}</span><br>
                <span class="signer-role">NIP/ID: {{ $profil->id_user ?? session('id_user', '-') }}</span>
            </div>
        </div>
    </div>
</body>
</html>


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
            padding-bottom: 5px;
            margin-bottom: 5px;
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
            padding: 4px 6px; 
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
            margin-top: 5px; /* Dikurangi agar hemat tempat */
            width: 100%;
            display: table;
            page-break-inside: avoid; /* Mencegah tanda tangan terpotong ke halaman berikutnya */
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
        
        /* Pengaturan Cetak (Print) */
        @media print {
            @page {
                size: landscape; /* Otomatis mengatur ke mode Landscape */
                margin: 10mm;
            }
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            /* Mencegah baris tabel terpotong di tengah halaman */
            table.data-table tr {
                page-break-inside: avoid;
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
            <h1 class="main-title">Laporan Analisis Global Survei Kepuasan Masyarakat (SKM)</h1>
            <h2 class="sub-title">LOKET: {{ $selectedLoket->nama_loket ?? 'Semua Loket' }}</h2>
            <p class="instansi-info">Sistem Antrean Pelayanan Publik</p>
        </div>
    </div>

    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Total Responden:</td>
                <td class="filter-value">{{ number_format($selectedLoketId === 'all' ? $totalRespondenAll : $totalRespondenSelected) }} Orang</td>
                <td class="filter-label">Rata-rata Skor:</td>
                <td class="filter-value">{{ number_format($selectedLoketId === 'all' ? $avgScoreAll : $avgScoreSelected, 2) }} / 4.0</td>
            </tr>
            <tr>
                <td class="filter-label">Tahun Perbandingan:</td>
                <td class="filter-value" colspan="3">{{ implode(', ', $filterYears ?? []) }}</td>
            </tr>
        </table>
    </div>

    <!-- TABEL RINCIAN PERBANDINGAN TAHUN -->
    @if(in_array($tipeCetak, ['semua', 'tren']))
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

    @if(in_array($tipeCetak, ['semua', 'detail']))
    <h3 style="font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">Peringkat Skor SKM Per Loket</h3>
    <table class="data-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">Peringkat</th>
                <th style="width: 45%">Nama Loket</th>
                <th style="width: 20%; text-align: center;">Total Responden</th>
                <th style="width: 25%; text-align: center;">Rata-rata Skor (SKM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loketSummary ?? [] as $index => $ls)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>{{ $ls['nama_loket'] }}</td>
                    <td style="text-align: center;">{{ number_format($ls['total_responden']) }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($ls['avg_score'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #777; padding: 20px;">Belum ada data penilaian loket.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if(in_array($tipeCetak, ['semua', 'detail']))
        <div style="page-break-inside: avoid;">
            <h3 style="font-size: 11px; margin-bottom: 5px; text-transform: uppercase;">Distribusi Feedback Responden</h3>
            <table class="data-table" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="width: 30%">Kategori Nilai</th>
                        <th style="width: 30%">Pilihan Jawaban</th>
                        <th style="width: 20%; text-align: center;">Bobot</th>
                        <th style="width: 20%; text-align: center;">Jumlah Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sangat Baik (Mutu A)</td>
                        <td>Sangat Bagus</td>
                        <td style="text-align: center;">4</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($breakdown['sangat_bagus'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Baik (Mutu B)</td>
                        <td>Bagus</td>
                        <td style="text-align: center;">3</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($breakdown['bagus'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Kurang Baik (Mutu C)</td>
                        <td>Kurang</td>
                        <td style="text-align: center;">2</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($breakdown['kurang'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Tidak Baik (Mutu D)</td>
                        <td>Sangat Kurang</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format($breakdown['sangat_kurang'] ?? 0) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc;">
                        <td colspan="3" style="text-align: right; font-weight: bold;">Total Jawaban:</td>
                        <td style="text-align: center; font-weight: bold;">{{ number_format(($breakdown['sangat_bagus'] ?? 0) + ($breakdown['bagus'] ?? 0) + ($breakdown['kurang'] ?? 0) + ($breakdown['sangat_kurang'] ?? 0)) }}</td>
                    </tr>
                </tfoot>
            </table>
    @else
        <div style="page-break-inside: avoid;">
    @endif

        <div class="footer-container">
            <div class="footer-left">
                <strong>Sistem Informasi Antrean</strong><br>
                Dicetak oleh: {{ $profil?->nama_user ?? session('nama', 'Administrator') }}<br>
                Unit Kerja: {{ $profil?->loket?->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket') }}<br>
                Waktu Cetak: {{ date('d-m-Y H:i') }} WITA
            </div>
            <div class="footer-right">
                <div class="signature-box">
                    Banjarbaru, {{ date('d F Y') }}<br>
                    Administrator Pelayanan,
                    <div class="signature-space" style="height: auto; margin: 8px 0; text-align: left;">
                        @if(request('qr', '1') === '1')
                        @php
                            $docData = [
                                'dokumen' => 'Laporan SKM Global',
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
    </div>
</body>
</html>


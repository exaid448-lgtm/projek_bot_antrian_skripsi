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
    <title>Laporan Kendala Pelayanan Loket Bermasalah</title>
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
            width: 20%;
        }
        .filter-value {
            width: 30%;
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
            font-size: 9.5px;
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
        
        /* Badge Categories */
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .bg-danger { background-color: #ffebee; color: #c62828; }
        .bg-warning { background-color: #fff8e1; color: #f57f17; }
        .bg-info { background-color: #e3f2fd; color: #1565c0; }
        
        .status-aktif { color: #2e7d32; font-weight: bold; }
        .status-arsip { color: #78909c; font-weight: bold; }

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
            <h1 class="main-title">Laporan Kendala Pelayanan Loket Bermasalah</h1>
            <h2 class="sub-title">Mal Pelayanan Publik (MPP)</h2>
            <p class="instansi-info">Sistem Informasi Antrean Pelayanan Publik</p>
        </div>
    </div>

    <!-- DATA RINGKASAN METADATA -->
    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Filter Periode:</td>
                <td class="filter-value"><strong>{{ $periodeFilterName }}</strong></td>
                <td class="filter-label">Filter Loket:</td>
                <td class="filter-value"><strong>{{ $loketFilterName }}</strong></td>
            </tr>
            <tr>
                <td class="filter-label">Filter Kategori:</td>
                <td class="filter-value"><strong>{{ $kategoriFilterName }}</strong></td>
                <td class="filter-label">Filter Status:</td>
                <td class="filter-value"><strong>{{ $statusFilterName }}</strong></td>
            </tr>
            <tr>
                <td class="filter-label" style="border-top: 1px dashed #ddd; padding-top: 5px;">Total Kendala Ditemukan:</td>
                <td class="filter-value" colspan="3" style="border-top: 1px dashed #ddd; padding-top: 5px;"><strong>{{ count($problems) }} Kendala</strong></td>
            </tr>
        </table>
    </div>

    <!-- TABEL DATA LOKET BERMASALAH -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 10%; text-align: center;">Tanggal</th>
                <th style="width: 15%;">Nama Loket</th>
                <th style="width: 20%;">Judul Kendala</th>
                <th style="width: 25%;">Deskripsi Masalah</th>
                <th style="width: 18%;">Solusi / Tindakan</th>
                <th style="width: 8%; text-align: center;">Kategori</th>
                <th style="width: 8%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($problems as $idx => $p)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($p->tanggal_info)->format('d-m-Y') }}</td>
                <td>
                    <strong>{{ $p->loket->nama_loket ?? '-' }}</strong><br>
                    <span style="font-size: 8px; color: #555;">{{ $p->loket->nama_pelayanan ?? '' }}</span>
                </td>
                <td>{{ $p->judul_info }}</td>
                <td>{!! nl2br(e($p->deskripsi_info)) !!}</td>
                <td>{!! nl2br(e($p->solusi_info ?: '-')) !!}</td>
                <td style="text-align: center;">
                    @if($p->kategori_info === 'critical')
                        <span class="badge bg-danger">Critical</span>
                    @elseif($p->kategori_info === 'warning')
                        <span class="badge bg-warning">Warning</span>
                    @else
                        <span class="badge bg-info">Normal</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <span class="status-{{ $p->status_info }}">
                        {{ ucfirst($p->status_info) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; font-style: italic; color: #777; padding: 20px;">Tidak ada data kendala loket yang cocok dengan kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SIGNATURE AREA -->
    <div class="footer-container">
        <div class="footer-left">
            <strong>Sistem Informasi Pelayanan Publik</strong><br>
            Dicetak oleh: {{ $profil?->nama_user ?? session('nama', 'Administrator') }}<br>
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
                            'dokumen' => 'Laporan Kendala Pelayanan Loket Bermasalah',
                            'petugas' => $profil?->nama_user ?? session('nama', 'Administrator'),
                            'id_petugas' => $profil?->id_user ?? session('id_user', '-'),
                            'loket' => $profil?->loket?->nama_loket ?? (session('role') === 'administrator' ? 'Administrator' : 'Admin MPP'),
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
                <span class="signer-name">{{ $profil?->nama_user ?? session('nama', 'Administrator') }}</span><br>
                <span class="signer-role">NIP/ID: {{ $profil?->id_user ?? session('id_user', '-') }}</span>
            </div>
        </div>
    </div>
</body>
</html>


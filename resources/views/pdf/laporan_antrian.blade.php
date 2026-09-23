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
    <title>Laporan Riwayat Antrian</title>
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
            width: 12%;
        }
        .filter-value {
            width: 38%;
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
        .badge.selesai {
            background-color: #e6fffa;
            color: #234e52;
            border: 1px solid #b2f5ea;
        }
        .badge.dipanggil {
            background-color: #fffaf0;
            color: #7b341e;
            border: 1px solid #feebc8;
        }
        .badge.menunggu {
            background-color: #f7fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }
        .badge.batal {
            background-color: #fff5f5;
            color: #c53030;
            border: 1px solid #fed7d7;
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
            <h1 class="main-title">Laporan Riwayat Antrian</h1>
            <h2 class="sub-title">LOKET: {{ $profil?->loket ? $profil->loket->nama_loket . ($profil->loket->nama_pelayanan ? ' - ' . $profil->loket->nama_pelayanan : '') : 'Semua Loket' }}</h2>
            <p class="instansi-info">Sistem Antrean Pelayanan Publik</p>
        </div>
    </div>

    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Periode:</td>
                <td class="filter-value">{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y') : 'Semua Tanggal' }} s/d {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y') : 'Semua Tanggal' }}</td>
                <td class="filter-label">Status:</td>
                <td class="filter-value">{{ ucfirst(request('status') ?? 'Semua') }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nomor Antrian</th>
                <th style="width: 25%">Waktu Masuk</th>
                <th style="width: 20%">Waktu Panggil</th>
                <th style="width: 20%">Waktu Selesai</th>
                <th style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($antrian as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $row->nomor_antrian }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($row->waktu_voice)->format('d-m-Y H:i') }}</td>
                <td>{{ $row->waktu_panggil ? \Carbon\Carbon::parse($row->waktu_panggil)->format('H:i') : '-' }}</td>
                <td>{{ $row->waktu_selesai ? \Carbon\Carbon::parse($row->waktu_selesai)->format('H:i') : '-' }}</td>
                <td>
                    @if($row->status_antrian == 'batal')
                        <span class="badge batal">Batal</span>
                    @elseif($row->waktu_selesai)
                        <span class="badge selesai">Selesai</span>
                    @elseif($row->waktu_panggil)
                        <span class="badge dipanggil">Dipanggil</span>
                    @else
                        <span class="badge menunggu">Menunggu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; padding: 20px;">Belum ada data antrian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-container">
        <div class="footer-left">
            <strong>Sistem Informasi Antrean</strong><br>
            Dicetak oleh: {{ $profil?->nama_user ?? session('nama', 'Administrator') }}<br>
            Unit Kerja: {{ $profil?->loket ? $profil->loket->nama_loket . ($profil->loket->nama_pelayanan ? ' - ' . $profil->loket->nama_pelayanan : '') : (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket') }}<br>
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
                            'dokumen' => 'Laporan Riwayat Antrian',
                            'petugas' => $profil?->nama_user ?? session('nama', 'Administrator'),
                            'id_petugas' => $profil?->id_user ?? session('id_user', '-'),
                            'loket' => $profil?->loket ? $profil->loket->nama_loket . ($profil->loket->nama_pelayanan ? ' - ' . $profil->loket->nama_pelayanan : '') : (session('role') === 'administrator' ? 'Administrator' : 'Semua Loket'),
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

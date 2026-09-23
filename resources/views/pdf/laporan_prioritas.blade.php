@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $profil = \App\Models\Profil::where('id_user', $user->id_user ?? session('id_user'))->first();
    
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
    <title>Laporan Riwayat Validasi Antrean Prioritas</title>
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
        .status-setuju { color: #166534; font-weight: bold; }
        .status-tolak { color: #991b1b; font-weight: bold; }

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
            <h1 class="main-title">Laporan Riwayat Validasi Antrean Prioritas</h1>
            <h2 class="sub-title">Sistem Antrean Terpadu</h2>
            <p class="instansi-info">Mal Pelayanan Publik (MPP)</p>
        </div>
    </div>

    <!-- DATA RINGKASAN METADATA -->
    <div class="filter-info-container">
        <table class="filter-table">
            <tr>
                <td class="filter-label">Waktu Cetak:</td>
                <td class="filter-value"><strong>{{ date('d-m-Y H:i') }} WITA</strong></td>
                <td class="filter-label">Total Data:</td>
                <td class="filter-value"><strong>{{ count($profilRiwayat) }} Riwayat</strong></td>
            </tr>
        </table>
    </div>

    <!-- TABEL DATA LOKET BERMASALAH -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Nama Pengunjung</th>
                <th style="width: 20%;">Jenis Prioritas</th>
                <th style="width: 15%;">Tanggal Diproses</th>
                <th style="width: 15%;">Batas Berlaku</th>
                <th style="width: 20%;">Status Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($profilRiwayat as $index => $r)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $r->nama ?? '-' }}</strong><br>
                    <span style="font-size: 8px; color: #555;">Email: {{ $r->email ?? '-' }}</span>
                </td>
                <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $r->jenis_prioritas) }}</td>
                <td>-</td>
                <td>{{ $r->tanggal_berakhir_prioritas ? \Carbon\Carbon::parse($r->tanggal_berakhir_prioritas)->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($r->status_prioritas == 'disetujui')
                        <span class="status-setuju">Disetujui</span>
                    @else
                        <span class="status-tolak">Batal / Ditolak</span>
                        @if($r->alasan_penolakan_prioritas)
                        <br><span style="font-size: 8px; color: #666;">Alasan: {{ $r->alasan_penolakan_prioritas }}</span>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; color: #777; padding: 20px;">Tidak ada data riwayat validasi prioritas.</td>
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
                    @if($useQr)
                    @php
                        $docData = [
                            'tipe_dokumen' => 'Laporan Validasi Prioritas',
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
                <span class="signer-name">{{ $profil?->nama_user ?? session('nama', 'Administrator') }}</span><br>
                <span class="signer-role">NIP/ID: {{ $profil?->id_user ?? session('id_user', '-') }}</span>
            </div>
        </div>
    </div>
</body>
</html>


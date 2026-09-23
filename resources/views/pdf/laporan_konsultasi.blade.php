<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konsultasi Loket {{ $profil->id_loket }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        /* HEADER LAPORAN */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4318ff;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
            color: #2b3674;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #707eae;
        }

        /* INFO FILTER */
        .info-filter {
            margin-bottom: 20px;
            font-size: 12px;
            color: #555;
        }

        /* TABEL DATA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* Menjaga lebar kolom tetap */
        }

        th {
            background-color: #4318ff;
            color: white;
            text-transform: uppercase;
            font-size: 11px;
            padding: 10px;
            border: 1px solid #e0e5f2;
        }

        td {
            padding: 10px;
            font-size: 11px;
            border: 1px solid #e0e5f2;
            word-wrap: break-word; /* Membungkus teks panjang */
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f4f7fe;
        }

        /* BADGE STATUS UNTUK PDF */
        .badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .status-sudah { color: #05cd99; }
        .status-belum { color: #ffb547; }

        /* FOOTER PENANDATANGAN */
        .footer-signature {
            margin-top: 50px;
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Data Konsultasi</h2>
        <p>Loket Pelayanan: {{ $profil->id_loket }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="info-filter">
        <strong>Periode:</strong> 
        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Awal' }} 
        s/d 
        {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Sekarang' }}
        <br>
        <strong>Status:</strong> {{ request('status') ? strtoupper(request('status')) : 'SEMUA STATUS' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 120px;">Nama Pengunjung</th>
                <th style="width: 150px;">Kontak (Email/HP)</th>
                <th>Isi Konsultasi</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $row->nama_pengunjung }}</strong></td>
                <td>
                    {{ $row->email }}<br>
                    <small>{{ $row->no_hp }}</small>
                </td>
                <td>{{ $row->konsultasi }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($row->tanggal_konsul)->format('d/m/Y') }}</td>
                <td style="text-align: center;">
                    @if($row->pelayanan_status == 'sudah')
                        <span class="badge status-sudah">SUDAH</span>
                    @else
                        <span class="badge status-belum">BELUM</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data ditemukan dalam periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-signature">
        <p>{{ date('d F Y') }}</p>
        <p>Petugas Loket {{ $profil->id_loket }}</p>
        <div class="signature-space"></div>
        <p><strong>( ________________ )</strong></p>
    </div>

    <script>
        // Otomatis membuka jendela print saat halaman dimuat
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

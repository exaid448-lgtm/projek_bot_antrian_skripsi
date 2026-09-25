<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Antrean Mendekati Giliran</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f1f5f9;
            padding: 30px 0;
        }
        .main-container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header .badge {
            display: inline-block;
            background-color: #f59e0b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #94a3b8;
        }
        .content {
            padding: 32px 28px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 16px;
            color: #334155;
        }
        .ticket-box {
            background: linear-gradient(to bottom right, #f8fafc, #f1f5f9);
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
        }
        .ticket-box .label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .ticket-box .number {
            font-size: 42px;
            font-weight: 900;
            color: #0f172a;
            margin: 4px 0 12px 0;
            letter-spacing: 1px;
        }
        .status-pill {
            display: inline-block;
            background-color: #dbeafe;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            margin-bottom: 14px;
            border: 1px solid #bfdbfe;
        }
        .detail-grid {
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            margin-top: 8px;
            display: table;
            width: 100%;
        }
        .detail-row {
            display: table-row;
        }
        .detail-cell {
            display: table-cell;
            padding: 6px 4px;
            font-size: 13px;
        }
        .detail-cell.title {
            color: #64748b;
            text-align: left;
            width: 45%;
        }
        .detail-cell.value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
            width: 55%;
        }
        .notice-box {
            background-color: #fefce8;
            border-left: 4px solid #f59e0b;
            padding: 14px 16px;
            border-radius: 0 8px 8px 0;
            margin: 24px 0;
            font-size: 13.5px;
            color: #854d0e;
            line-height: 1.5;
        }
        .btn-wrapper {
            text-align: center;
            margin: 28px 0 10px 0;
        }
        .btn {
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-container">
            {{-- Header --}}
            <div class="header">
                <div class="badge">PEMBERITAHUAN GILIRAN</div>
                <h1>Mall Pelayanan Publik</h1>
                <p>Kota Banjarbaru - Layanan Antrean Terpadu</p>
            </div>

            {{-- Content --}}
            <div class="content">
                <p class="greeting">
                    Yth. <strong>{{ $profilPengunjung->nama ?? 'Pengunjung' }}</strong>,
                </p>

                <p style="font-size: 14px; color: #475569; margin-top: 0;">
                    Nomor antrean Anda akan segera dipanggil pada loket pelayanan. Berikut adalah informasi status antrean Anda:
                </p>

                {{-- Box Nomor Antrean --}}
                <div class="ticket-box">
                    <div class="label">Nomor Antrean Anda</div>
                    <div class="number">{{ $antreanTujuan->nomor_antrian }}</div>
                    
                    <div class="status-pill">
                        ⏳ Giliran Berikutnya
                    </div>

                    <div class="detail-grid">
                        <div class="detail-row">
                            <div class="detail-cell title">Sedang Dilayani:</div>
                            <div class="detail-cell value" style="color: #2563eb;">
                                Nomor {{ $antreanDipanggil->nomor_antrian }}
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell title">Loket Tujuan:</div>
                            <div class="detail-cell value">
                                Loket {{ $loket->id_loket ?? '-' }} ({{ $loket->nama_loket ?? 'Layanan' }})
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-cell title">Kategori:</div>
                            <div class="detail-cell value">
                                {{ $antreanTujuan->is_prioritas ? '⭐ Prioritas Khusus' : 'Antrean Reguler' }}
                            </div>
                        </div>
                        @if($antreanTujuan->kode_booking_unik)
                        <div class="detail-row">
                            <div class="detail-cell title">Kode Booking:</div>
                            <div class="detail-cell value" style="font-family: monospace;">
                                {{ $antreanTujuan->kode_booking_unik }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Pesan Pengingat --}}
                <div class="notice-box">
                    <strong>⚠️ Mohon Bersiap-siap:</strong><br>
                    Nomor antrean Anda adalah <strong>1 antrean berikutnya</strong> setelah nomor <strong>{{ $antreanDipanggil->nomor_antrian }}</strong>. 
                    Silakan segera menuju dan bersiap di depan <strong>Loket {{ $loket->id_loket ?? '' }} ({{ $loket->nama_loket ?? '' }})</strong> agar saat nomor Anda dipanggil, proses pelayanan dapat langsung dilakukan.
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('dashboard.pengunjung') }}" class="btn">
                        Buka Dashboard Antrean
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <p style="margin: 0 0 6px 0;">
                    Email ini dikirim secara otomatis oleh Sistem Antrean Mall Pelayanan Publik Kota Banjarbaru.
                </p>
                <p style="margin: 0;">
                    &copy; {{ date('Y') }} MPP Kota Banjarbaru. Seluruh hak cipta dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-card {
            border: 3px solid #007bff;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
        }
        .nomor-antrian {
            font-size: 80px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }
        .status-badge {
            font-size: 20px;
            padding: 10px 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        @if (isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @else
            <div class="status-card">
                <h2>Status Antrian Anda</h2>
                
                <div class="nomor-antrian">
                    {{ str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT) }}
                </div>

                <div>
                    @if ($antrian->antrian_selesai)
                        <span class="badge status-badge bg-success">✓ SELESAI</span>
                    @elseif ($antrian->antrian_mulai)
                        <span class="badge status-badge bg-warning text-dark">⏱ SEDANG DILAYANI</span>
                    @else
                        <span class="badge status-badge bg-info">⏳ MENUNGGU</span>
                    @endif
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Loket</div>
                        <div class="info-value">{{ $antrian->loket->nama_loket }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Layanan</div>
                        <div class="info-value">{{ $antrian->loket->nama_pelayanan }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nama Pengunjung</div>
                        <div class="info-value">{{ $antrian->konsul->nama_pengunjung }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Waktu Diberikan</div>
                        <div class="info-value">{{ $antrian->waktu_diberikan->format('H:i') }}</div>
                    </div>
                    @if ($antrian->antrian_mulai)
                    <div class="info-item">
                        <div class="info-label">Mulai Dilayani</div>
                        <div class="info-value">{{ $antrian->antrian_mulai->format('H:i') }}</div>
                    </div>
                    @endif
                    @if ($antrian->antrian_selesai)
                    <div class="info-item">
                        <div class="info-label">Selesai</div>
                        <div class="info-value">{{ $antrian->antrian_selesai->format('H:i') }}</div>
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="/" class="btn btn-secondary">Kembali ke Beranda</a>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Auto-refresh setiap 5 detik untuk status terbaru
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>

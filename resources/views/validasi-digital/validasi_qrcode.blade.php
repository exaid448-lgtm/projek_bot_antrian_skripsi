<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen Digital - Sistem Antrean</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #1e293b;
            --light: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: var(--dark);
        }

        /* Animated Background Elements */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            animation: float 10s infinite ease-in-out alternate;
        }
        .shape-1 {
            width: 300px;
            height: 300px;
            background: rgba(79, 70, 229, 0.4);
            top: -50px;
            left: -50px;
        }
        .shape-2 {
            width: 400px;
            height: 400px;
            background: rgba(16, 185, 129, 0.3);
            bottom: -100px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }

        /* Glassmorphism Card */
        .validation-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
            overflow: hidden;
            text-align: center;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s ease forwards;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        .header-section {
            margin-bottom: 30px;
        }
        
        .status-icon-container {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s forwards;
            transform: scale(0);
        }

        @keyframes scaleIn {
            to { transform: scale(1); }
        }

        .status-valid .status-icon-container { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .status-invalid .status-icon-container { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        .status-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px;
        }
        .status-valid .status-title { color: var(--success); }
        .status-invalid .status-title { color: var(--danger); }

        .status-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .data-list {
            text-align: left;
            background: rgba(255,255,255,0.6);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.8);
        }

        .data-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #cbd5e1;
        }
        .data-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .data-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .data-value {
            font-size: 15px;
            font-weight: 500;
            color: var(--dark);
            word-break: break-word;
        }

        .btn-action {
            display: inline-block;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        /* Decor lines */
        .decor-line {
            height: 4px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
        .status-valid .decor-line { background: var(--success); }
        .status-invalid .decor-line { background: var(--danger); }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 15px;
        }

        @media (max-width: 480px) {
            .validation-card {
                padding: 30px 20px;
                border-radius: 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Background Animation Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="validation-card {{ $isValid ? 'status-valid' : 'status-invalid' }}">
        <div class="decor-line"></div>
        
        <div class="header-section">
            <div class="status-icon-container">
                @if($isValid)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-times"></i>
                @endif
            </div>
            
            <h1 class="status-title">
                {{ $isValid ? 'DOKUMEN VALID' : 'DOKUMEN TIDAK VALID' }}
            </h1>
            <p class="status-subtitle">
                {{ $isValid ? 'Sistem menyatakan bahwa dokumen ini resmi dan asli.' : 'QR Code tidak dikenali, rusak, atau telah dimanipulasi.' }}
            </p>

            @if($isValid)
            <div class="verification-badge">
                <i class="fas fa-shield-check"></i> Tanda Tangan Digital Terverifikasi
            </div>
            @endif
        </div>

        @if($isValid && isset($dataValidasi))
            <div class="data-list">
                <div class="data-item">
                    <div class="data-label">Jenis Dokumen</div>
                    <div class="data-value">{{ $dataValidasi['dokumen'] ?? 'Tidak diketahui' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Diterbitkan Oleh</div>
                    <div class="data-value">{{ $dataValidasi['petugas'] ?? '-' }} <span style="font-size:12px; color:#94a3b8;">(ID: {{ $dataValidasi['id_petugas'] ?? '-' }})</span></div>
                </div>
                <div class="data-item">
                    <div class="data-label">Unit Kerja / Loket</div>
                    <div class="data-value">{{ $dataValidasi['loket'] ?? '-' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Waktu Cetak</div>
                    <div class="data-value">
                        {{ isset($dataValidasi['waktu_cetak']) ? \Carbon\Carbon::parse($dataValidasi['waktu_cetak'])->format('d F Y, H:i') . ' WITA' : '-' }}
                    </div>
                </div>
            </div>
        @else
            <div class="data-list" style="text-align: center; color: var(--danger); background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2);">
                <i class="fas fa-exclamation-triangle" style="font-size: 30px; margin-bottom: 10px;"></i>
                <p style="margin:0; font-size: 14px; font-weight: 500;">Sistem gagal memverifikasi keaslian dokumen ini. Harap berhati-hati terhadap pemalsuan dokumen.</p>
            </div>
        @endif

        <a href="/" class="btn-action">
            <i class="fas fa-home"></i> Kembali ke Beranda
        </a>
    </div>

</body>
</html>
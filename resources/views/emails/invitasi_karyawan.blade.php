<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Undangan Registrasi Akun Karyawan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #2c3e50; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .content p { margin-bottom: 20px; font-size: 15px; }
        .btn-container { text-align: center; margin: 35px 0; }
        .btn { background-color: #3498db; color: #ffffff !important; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 13px; color: #7f8c8d; border-top: 1px solid #eeeeee; }
        .highlight { font-weight: bold; color: #2c3e50; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MPP Kota Banjarbaru</h1>
        </div>
        
        <div class="content">
            <p>Halo,</p>
            <p>Anda telah diundang oleh Administrator untuk mendaftarkan akun karyawan baru di sistem <strong>Mall Pelayanan Publik (MPP) Kota Banjarbaru</strong>.</p>
            
            <p>Anda akan ditugaskan untuk mengelola layanan pada loket: <br>
            <span class="highlight" style="font-size: 18px; display: inline-block; margin-top: 5px;">{{ strtoupper($loket->nama_loket) }}</span></p>
            
            <p>Untuk menyelesaikan pendaftaran akun Anda, silakan klik tombol di bawah ini. Tautan ini bersifat unik dan hanya dapat digunakan sekali.</p>
            
            <div class="btn-container">
                <a href="{{ url('/register_karyawan/' . $invitasi->token) }}" class="btn">Registrasi Akun Sekarang</a>
            </div>
            
            <p style="font-size: 13px; color: #e74c3c; margin-top: 30px;">
                <em>Perhatian: Tautan undangan ini akan kedaluwarsa pada {{ \Carbon\Carbon::parse($invitasi->expires_at)->format('d M Y H:i') }}. Harap segera menyelesaikan registrasi Anda.</em>
            </p>
        </div>
        
        <div class="footer">
            <p>Pesan ini dikirimkan secara otomatis oleh sistem MPP Kota Banjarbaru. Harap tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>

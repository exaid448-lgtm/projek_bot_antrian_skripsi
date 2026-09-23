<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riset Akun Karyawan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #e74c3c; padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .content p { margin-bottom: 20px; font-size: 15px; }
        .btn-container { text-align: center; margin: 35px 0; }
        .btn { background-color: #e74c3c; color: #ffffff !important; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 13px; color: #7f8c8d; border-top: 1px solid #eeeeee; }
        .highlight { font-weight: bold; color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MPP Kota Banjarbaru</h1>
        </div>
        
        <div class="content">
            <p>Halo, <strong>{{ $user->profil->nama_user ?? 'Karyawan' }}</strong></p>
            <p>Administrator telah memicu permintaan untuk meriset/mengatur ulang kredensial login Anda di sistem <strong>Mall Pelayanan Publik (MPP) Kota Banjarbaru</strong>.</p>
            
            <p>Jika Anda merasa tidak membutuhkan ini, Anda bisa mengabaikan email ini. Namun jika Anda memang lupa password atau ingin mengganti kredensial, silakan klik tombol di bawah ini.</p>
            
            <div class="btn-container">
                <a href="{{ url('/riset_akun/' . $token) }}" class="btn">Riset Akun Sekarang</a>
            </div>
            
            <p style="font-size: 13px; color: #e74c3c; margin-top: 30px;">
                <em>Perhatian: Tautan ini bersifat rahasia dan hanya berlaku selama 24 jam. Jangan berikan tautan ini kepada siapapun!</em>
            </p>
        </div>
        
        <div class="footer">
            <p>Pesan ini dikirimkan secara otomatis oleh sistem MPP Kota Banjarbaru. Harap tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>

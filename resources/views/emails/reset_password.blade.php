<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password - MPP Kota Banjarbaru</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 40px 10px;">
                <!-- Card Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid #eef2f5;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #ffb6b6 0%, #ff7c7c 100%); padding: 30px 20px;">
                            <div style="background-color: rgba(255, 255, 255, 0.2); width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; text-align: center; line-height: 60px;">
                                <span style="font-size: 28px; color: #ffffff; font-weight: bold; line-height: 60px; vertical-align: middle;">🔑</span>
                            </div>
                            <h2 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">Atur Ulang Password</h2>
                            <p style="margin: 5px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 13px;">Mall Pelayanan Publik Kota Banjarbaru</p>
                        </td>
                    </tr>
                    
                    <!-- Content Body -->
                    <tr>
                        <td style="padding: 30px 25px; color: #4a5568;">
                            <p style="margin-top: 0; font-size: 15px; line-height: 1.6; color: #2d3748;">Halo,</p>
                            <p style="font-size: 14px; line-height: 1.6; color: #4a5568;">Kami menerima permintaan untuk mengatur ulang kata sandi (password) akun MPP Kota Banjarbaru Anda.</p>
                            <p style="font-size: 14px; line-height: 1.6; color: #4a5568;">Silakan klik tombol di bawah ini untuk mengatur ulang kata sandi baru Anda. Link ini akan kedaluwarsa dalam waktu <strong>60 menit</strong>.</p>
                            
                            <!-- CTA Button -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" target="_blank" style="background-color: #ff7c7c; color: #ffffff; text-decoration: none; padding: 12px 30px; font-size: 14px; font-weight: bold; border-radius: 8px; display: inline-block; box-shadow: 0 4px 10px rgba(255, 124, 124, 0.3); transition: all 0.3s ease;">Atur Ulang Password Baru</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="font-size: 13px; line-height: 1.6; color: #718096; background-color: #f7fafc; padding: 12px; border-radius: 8px; border-left: 4px solid #ff7c7c; margin: 20px 0;">
                                Jika Anda tidak merasa mengajukan permintaan ini, abaikan saja email ini. Password Anda tidak akan berubah.
                            </p>
                            
                            <hr style="border: 0; border-top: 1px solid #edf2f7; margin: 25px 0;">
                            
                            <p style="font-size: 11px; line-height: 1.6; color: #a0aec0; margin: 0;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:<br>
                                <a href="{{ $link }}" style="color: #ff7c7c; text-decoration: underline; word-break: break-all;">{{ $link }}</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 20px; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0; font-size: 12px; color: #a0aec0;">&copy; {{ date('Y') }} MPP Kota Banjarbaru. All rights reserved.</p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/register_pengunjung.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Document</title>
</head>
<body>
    <div id="page-loader" class="page-transition"></div>
    <div class="video-container">
        <img src="{{ asset('img/backgraund_login/mpp_login.jpg') }}" alt="Background Image" />
    </div>

    <div class="container">
        <div class="login-box register-mode">
            <div class="login-header">
                <h3 data-text="MALL PELAYANAN PUBLIK">MALL PELAYANAN PUBLIK</h3>
                <p>(MPP) Kota Banjarbaru</p>
            </div>
            <h2 id="step-title">Daftar Akun (1/2)</h2>   
            <form action="{{ route('register.submit') }}" method="POST" id="regForm">
                @csrf 
                <div id="step-1">
                    <div class="input-group">
                        <input type="text" name="nama" id="nama" required />
                        <label for="nama">Nama Lengkap</label>
                    </div>
                    <div class="input-group-radio">
                        <label class="label-radio">Jenis Kelamin</label>
                        <div class="radio-container">
                            <label class="radio-item">
                                <input type="radio" name="jenis_kelamin" value="laki-laki" required>
                                <span>Laki-laki</span>
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="jenis_kelamin" value="wanita" required>
                                <span>Wanita</span>
                            </label>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" name="tanggal_lahir" id="tanggal_lahir" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'" required />
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                    </div>
                    <div class="button-group">
                        <button type="button" id="btn-next">Selanjutnya <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <div id="step-2" style="display: none;">
                    <div class="input-group">
                        <input type="text" name="email" id="email" required />
                        <label for="email">Email Aktif</label>
                    </div>
                    <div class="input-group">
                        <input type="tel" name="no_wa" id="no_wa" required />
                        <label for="no_wa">Nomor WhatsApp</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="username" id="username" required />
                        <label for="username">Username</label>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" required />
                        <label for="password">Password</label>
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </div>
                    <div class="button-group">
                        <button type="button" id="btn-prev" class="btn-register">Kembali</button>
                        <button type="submit" class="btn-login">Daftar Sekarang</button>
                    </div>
                </div>
                <div class="register-link">
                    Sudah punya akun? <a href="{{ route('login_pengunjung') }}">Login di sini</a>
                </div>
            </form>
        </div>

        <div class="blank-side"></div>
    </div>

    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>
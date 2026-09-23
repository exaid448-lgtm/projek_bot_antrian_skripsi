<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Karyawan - MPP Banjarbaru</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="video-container">
      <img src="{{ asset('img/backgraund_login/backgraund.jpg') }}" alt="Background Image" />
  </div>

  <div class="container">
    <div class="login-box">
      <div class="brand-header">
        <div class="logo-wrapper">
          <i class="fa-solid fa-building-user"></i>
        </div>
        <h1>MPP Online</h1>
        <p>Kota Banjarbaru</p>
      </div>
      
      <form action="{{ route('login.proses') }}" method="POST">
        @csrf 
        
        <div class="input-group">
          <i class="fa-solid fa-user input-icon"></i>
          <input type="text" name="username" id="username" required autocomplete="off" />
          <label for="username">Username</label>
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock input-icon"></i>
          <input type="password" name="password" id="password" required />
          <label for="password">Password</label>
          <i class="fas fa-eye" id="togglePassword"></i>
        </div>

        <button type="submit">Masuk Ke Aplikasi</button>
      </form>

      @if(session('error'))
        <div class="error-msg">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
      @endif

      <div class="login-footer">
        <p>&copy; {{ date('Y') }} Mal Pelayanan Publik<br>Kota Banjarbaru</p>
      </div>
    </div>

    <div class="blank-side"></div>
  </div>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
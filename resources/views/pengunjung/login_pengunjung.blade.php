<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - MPP Banjarbaru</title>
  <link rel="stylesheet" href="{{ asset('css/login_pengunjung.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div id="page-loader" class="page-transition"></div>
  <div class="video-container">
      <img src="{{ asset('img/backgraund_login/mpp_login.jpg') }}" alt="Background Image" />
  </div>

  <div class="container">
    <div class="login-box">
      <div class="login-header">
        <h3 data-text="MALL PELAYANAN PUBLIK">MALL PELAYANAN PUBLIK</h3>
        <p>(MPP) Kota Banjarbaru</p>
    </div>
      <h2>Login pengunjung</h2>
      <form action="{{ route('login.proses.pengunjung') }}" method="POST">
        @csrf 
        
        <div class="input-group">
          <input type="text" name="username" id="username" required placeholder=" " />
          <label for="username">Username</label>
        </div>

        <div class="input-group">
          <input type="password" name="password" id="password" required placeholder=" " />
          <label for="password">Password</label>
          <i class="fas fa-eye" id="togglePassword"></i>
        </div>  


        <div class="button-group">
            <button type="button" class="btn-register" onclick="window.location.href='{{ route('register') }}'">
                register
            </button>
            <button type="submit" class="btn-login">login</button>
        </div>
        
        <div class="register-link">
            lupa password dan username? <a href="javascript:void(0)" onclick="openResetModal()">riset akun</a>
        </div>
      </form>

</div> <div class="blank-side"></div>
  </div> <div id="resetModal" class="modal">
      <div class="modal-content">
          <span class="close-btn" onclick="closeResetModal()">&times;</span>
          <h3>Riset Akun</h3>
          <p>Masukkan email yang terdaftar untuk menerima instruksi pemulihan.</p>
          <div class="input-group" style="width: 100%;">
              <input type="email" id="resetEmail" required placeholder=" " style="width: 100%;" />
              <label for="resetEmail">Alamat Email</label>
          </div>
          <div class="button-group">
              <button type="button" onclick="handleReset()">Kirim Link Riset</button>
          </div>
      </div>
  </div>

  <script src="{{ asset('js/login.js') }}"></script>
  <script src="{{ asset('js/riset_akun.js') }}"></script>
  @if(session('success'))
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: "{{ session('success') }}",
                  confirmButtonColor: '#3085d6',
              });
          });
      </script>
  @endif
  @if(session('error'))
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  icon: 'error',
                  title: 'Gagal!',
                  text: "{{ session('error') }}",
                  confirmButtonColor: '#d33',
              });
          });
      </script>
  @endif
</body>
</html>
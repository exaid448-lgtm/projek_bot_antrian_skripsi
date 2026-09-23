<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Konsultasi MPP - Kota Banjarbaru</title>
    <link rel="stylesheet" href="{{ asset('css/konsul.css') }}">
</head>
<body>
    <div class="navbar">
        <img src="{{ asset('img/logo/logo.png') }}" alt="Logo MPP">
        <div class="taxt-nav">
            <h1>Konsultasi MPP (Mall Pelayanan Publik)</h1>
            <h2>Kota Banjarbaru</h2>
        </div>
    </div>

    @if(session('success') || session('error'))
        <div class="alert-container" id="notif">
            <div class="alert-custom {{ session('success') ? 'alert-success' : 'alert-danger' }}">
                <div class="alert-icon">{{ session('success') ? '✓' : '✕' }}</div>
                <div class="alert-message">
                    <h4>{{ session('success') ? 'Berhasil!' : 'Gagal!' }}</h4>
                    <p>{{ session('success') ?? session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('konsul.store') }}" method="POST">
        @csrf
        <div class="container">
            <div class="data-konsultasi-full">
                <div style="margin-bottom: 20px;">
                    <a href="{{ route('pengunjung.dashboard') }}" class="btn-back-modern">
                        <svg class="icon-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>
                <h3>Formulir Konsultasi Layanan</h3>
                <p class="info-text text-gray-400">Silakan pilih instansi dan tuliskan kendala atau pertanyaan Anda di bawah ini.</p>
                <div class="user-profile-info">
                    <div class="profile-icon">👤</div>
                    <div class="profile-details">
                        <span>Mengirim sebagai:</span>
                        {{-- Mengambil kolom 'nama' dari tabel profil_pengunjung --}}
                        <strong>{{ $profil->nama ?? 'Nama Tidak Terdeteksi' }}</strong> 
                        <small>({{ $profil->email ?? auth()->user()->email ?? '-' }})</small>
                    </div>
                </div>
                <div class="dropdown-wrapper">
                    <div class="dropdown-custom" id="dropdown-loket">
                        <div class="dropdown-header">
                            <div class="icon-circle"><i class="arrow-up"></i></div>
                            <span>pilih loket / instansi</span>
                        </div>
                        <ul class="dropdown-list">
                            @foreach($loket_unik as $l)
                                <li data-value="{{ $l->nama_loket }}">{{ $l->nama_loket }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="dropdown-custom disabled-style" id="dropdown-layanan">
                        <div class="dropdown-header">
                            <div class="icon-circle"><i class="arrow-up"></i></div>
                            <span>pilih jenis layanan</span>
                        </div>
                        <ul class="dropdown-list">
                            @foreach($semua_data as $data)
                                <li data-parent="{{ $data->nama_loket }}" data-value="{{ $data->nama_pelayanan }}" style="display: none;">
                                    {{ $data->nama_pelayanan }}
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="layanan" id="hidden-layanan" required>
                    </div>
                </div>

                <textarea class="input-konsul" name="konsultasi" id="konsultasi" placeholder="Tulis rincian konsultasi Anda di sini... (Contoh: Menanyakan persyaratan perpanjangan izin usaha)" required></textarea>
                
                <div class="footer-form">
                    <p class="note">Data identitas Anda akan dikirim otomatis berdasarkan profil akun.</p>
                    <button type="submit" class="submit-btn">Kirim Konsultasi</button>
                </div>
            </div>
        </div>
    </form>

    <script src="{{ asset('js/konsul.js') }}"></script>
    <script>
        // Script sederhana untuk menghilangkan notif otomatis
        setTimeout(() => {
            const notif = document.getElementById('notif');
            if(notif) notif.classList.add('fade-out');
        }, 3000);
    </script>
</body>
</html>
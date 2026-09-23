<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/navbar_super_admin.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/data_loket.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <title>Super Admin - Data Loket</title>
</head>
<body>
    <aside class="sidebar">
        <div class="logo-loket">
            <div class="logo-text">
                <img src="{{ asset('img/super_admin_logo/logo.jpeg') }}" alt="Logo Super Admin" class="img-logo">
            </div>
        </div>
        <nav>
            <a href="{{ route('super.dashboard') }}" class="sidebar-item {{ request()->routeIs('super.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('data_absensi.index') }}" class="sidebar-item {{ request()->routeIs('data_absensi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i> <span>Data Absensi</span>
            </a>
            <a href="{{ route('loket.index') }}" class="sidebar-item {{ request()->routeIs('loket.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> <span>Data Loket</span>
            </a>
            <a href="{{ route('data-akun.index') }}" class="sidebar-item {{ request()->routeIs('data-akun.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield"></i> <span>Data Akun</span>
            </a>
            <a href="{{ route('super.jadwal.index') }}" class="sidebar-item {{ request()->routeIs('super.jadwal.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock"></i> <span>Data Jadwal</span>
            </a>
            <a href="{{ route('super.pengunjung.index') }}" class="sidebar-item {{ request()->routeIs('super.pengunjung.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> <span>Data Pengunjung</span>
            </a>
            <a href="{{ route('data-skm.index') }}" class="sidebar-item {{ request()->routeIs('data-skm.*') ? 'active' : '' }}">
                <i class="fa-solid fa-star-half-stroke"></i> <span>Data SKM Loket</span>
            </a>
            <a href="{{ route('data-kinerja.index') }}" class="sidebar-item {{ request()->routeIs('data-kinerja.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> <span>Data Kinerja Karyawan</span>
            </a>
            <a href="{{ route('beban-kerja.index') }}" class="sidebar-item {{ request()->routeIs('beban-kerja.*') ? 'active' : '' }}">
                <i class="fa-solid fa-briefcase"></i> <span>Data Beban Kerja</span>
            </a>
            <a href="{{ route('superadmin.booking.index') }}" class="sidebar-item {{ request()->routeIs('superadmin.booking.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i> <span>Data Booking</span>
            </a>
            <a href="{{ route('admin.validasi_prioritas') }}" class="sidebar-item {{ request()->routeIs('admin.validasi_prioritas') ? 'active' : '' }}">
                <i class="fa-solid fa-user-check"></i> <span>Data Validasi Prioritas</span>
            </a>
            <a href="{{ route('data-loket-bermasalah.index') }}" class="sidebar-item {{ request()->routeIs('data-loket-bermasalah.*') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i> <span>Data Loket Bermasalah</span>
            </a>
            <a href="{{ route('admin.log_sistem.index') }}" class="sidebar-item {{ request()->routeIs('admin.log_sistem.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-virus"></i> <span>Log Gangguan Sistem</span>
            </a>
            {{-- <a href="{{ route('super.training.index') }}" class="sidebar-item {{ request()->routeIs('super.training.*') ? 'active' : '' }}">
                <i class="fa-solid fa-brain"></i> <span>Data Training</span>
            </a> --}}
            <a href="{{ route('data.antrian') }}" class="sidebar-item {{ request()->routeIs('data.antrian') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> <span>Data Antrian</span>
            </a>
            <a href="#" class="sidebar-item" id="btnLogout">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
            </a>
        </nav>
    </aside>

                   <!-- MODAL LOGOUT -->
                <div class="logout-modal" id="logoutModal">
                    <div class="logout-box">
                        <h4>Konfirmasi Logout</h4>
                        <p>Anda yakin ingin keluar dari login ini?</p>

                        <div class="logout-actions">
                            <button class="btn-cancel" id="cancelLogout">Batal</button>
                            <a href="{{ route('logout') }}" class="btn-logout">Logout</a>
                        </div>
                    </div>
                </div>
    
    <div class="content">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/navbar_super_admin.js') }}"></script>

    @stack('scripts')
</body>
</html>
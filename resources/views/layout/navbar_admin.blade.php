<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/navbar_admin.css') }}"> 
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <title>Dashboard Admin - MPP</title>
</head>
<body>
    <script>
        // Immediate check to apply sidebar state before rendering elements
        (function() {
            const sidebarState = localStorage.getItem('sidebar-state');
            if (sidebarState === 'collapsed') {
                document.body.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <!-- Global Toast Notifications -->
    @if(session('success'))
        <div style="position: fixed; top: 25px; right: 25px; z-index: 10000; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); font-family: 'Outfit', sans-serif;" id="global-alert-success">
            <i class="fa-solid fa-circle-check" style="font-size: 16px;"></i>
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('global-alert-success').remove()" style="background: none; border: none; color: inherit; cursor: pointer; margin-left: 10px; font-size: 16px; display: inline-flex; align-items: center;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('global-alert-success');
                if (el) {
                    el.style.transition = 'opacity 0.5s ease';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }
            }, 4000);
        </script>
    @endif
    @if(session('error'))
        <div style="position: fixed; top: 25px; right: 25px; z-index: 10000; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); font-family: 'Outfit', sans-serif;" id="global-alert-error">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 16px;"></i>
            <span>{{ session('error') }}</span>
            <button type="button" onclick="document.getElementById('global-alert-error').remove()" style="background: none; border: none; color: inherit; cursor: pointer; margin-left: 10px; font-size: 16px; display: inline-flex; align-items: center;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('global-alert-error');
                if (el) {
                    el.style.transition = 'opacity 0.5s ease';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }
            }, 4000);
        </script>
    @endif

    <!-- Sidebar Kiri -->
    <aside class="sidebar">
        <div class="logo-loket">
            <div class="logo-img">
                <img 
                    src="{{ isset($loket) && $loket->logo 
                        ? asset('img/logo_loket/' . $loket->logo) 
                        : asset('img/logo_loket/default.png') }}" 
                    alt="Logo Loket">
            </div>

            <div class="logo-text">
                <span class="loket-nama">
                    {{ isset($loket) && $loket->nama_loket ? $loket->nama_loket : 'Nama Loket' }}
                </span>
            </div>
        </div>

        <nav>
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('profil.loket') }}" class="sidebar-item {{ request()->routeIs('profil.loket') ? 'active' : '' }}">
                <i class="fa-solid fa-id-card"></i> Profil Loket
            </a>
            <a href="{{ route('jadwal.index') }}" class="sidebar-item {{ request()->routeIs('jadwal.index') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i> Jadwal
            </a>
            <a href="{{ route('data.antrian') }}" class="sidebar-item {{ request()->routeIs('data.antrian') ? 'active' : '' }}">
                <i class="fa-solid fa-list-ol"></i> Data Antrian
            </a>
            <a href="{{ route('chatloket.index') }}" class="sidebar-item {{ request()->routeIs('chatloket.index') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i> Chat Konsultasi
            </a>
            <a href="{{ route('algoritma.index') }}" class="sidebar-item {{ request()->routeIs('algoritma.index') ? 'active' : '' }}">
                <i class="fa-solid fa-brain"></i> Data Algoritma
            </a>
            <a href="{{ route('absensi.index') }}" class="sidebar-item {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
                <i class="fa-solid fa-user-check"></i> Absensi
            </a>
            <a href="{{ route('laporan.skm') }}" class="sidebar-item {{ request()->routeIs('laporan.skm') ? 'active' : '' }}">
                <i class="fa-solid fa-square-poll-vertical"></i> Laporan SKM
            </a>
            <a href="{{ route('persyaratan.index') }}" class="sidebar-item {{ request()->routeIs('persyaratan.index') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-info"></i> Persyaratan Loket
            </a>
            <a href="{{ route('notif_bermasalah.index') }}" class="sidebar-item {{ request()->routeIs('notif_bermasalah.index') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i> Notif Masalah
            </a>
            <a href="#" class="sidebar-item sidebar-logout" id="btnLogout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Sidebar Toggle Pull-Tab Button -->
    <button class="sidebar-toggle" id="sidebarToggle" title="Buka/Tutup Sidebar">
        <i class="fa-solid fa-xmark" id="toggleIcon"></i>
    </button>
    <script>
        // Set initial toggle icon based on immediate load state
        if (document.body.classList.contains('sidebar-collapsed')) {
            document.getElementById('toggleIcon').className = 'fa-solid fa-bars';
        }
    </script>

    <!-- MODAL LOGOUT -->
    <div class="logout-modal" id="logoutModal">
        <div class="logout-box">
            <div class="logout-icon">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h4>Konfirmasi Logout</h4>
            <p>Anda yakin ingin keluar dari login ini?</p>

            <div class="logout-actions">
                <button class="btn-cancel" id="cancelLogout">Batal</button>
                <a href="{{ route('logout') }}" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/navbar_loket.js') }}"></script>
    <script src="{{ asset('js/loket_konsul.js') }}"></script>
    <script src="{{ asset('js/loket_konsul_search.js') }}"></script>
    @stack('scripts')
    <script>
        // Collapsible sidebar click handler
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
            
            const icon = document.getElementById('toggleIcon');
            // Save state to localStorage and update icon
            if (document.body.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebar-state', 'collapsed');
                icon.className = 'fa-solid fa-bars';
            } else {
                localStorage.setItem('sidebar-state', 'expanded');
                icon.className = 'fa-solid fa-xmark';
            }
        });
    </script>
</body>
</html>
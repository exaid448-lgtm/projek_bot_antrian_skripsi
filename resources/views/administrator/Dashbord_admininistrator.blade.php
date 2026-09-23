<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/home_super_admin.css') }}">
</head>
<body>
@extends('layout.navbar_administrator')

@section('content')
<div class="main-content">

    <!-- HEADER -->
    <div class="dashboard-header">
        @php
            $adminName = session('nama', 'Administrator');
            $adminFoto = session('foto', 'default-user.png');
            
            // Fallback dinamis jika session belum ter-refresh dari logout/login ulang
            if ($adminName === 'Administrator' && session()->has('id_user')) {
                $profil = DB::table('profil_karyawan')->where('id_user', session('id_user'))->first();
                if ($profil) {
                    $adminName = $profil->nama_user;
                    $adminFoto = $profil->img_user ?: 'default-user.png';
                }
            }
            
            $fotoPath = $adminFoto && file_exists(public_path('img/foto_karyawan/' . $adminFoto))
                ? asset('img/foto_karyawan/' . $adminFoto)
                : asset('img/foto_karyawan/default-user.png');
        @endphp
        <div class="header-left">
            <h2>Dashboard Administrator</h2>
            <p class="subtitle" style="color: #64748b; font-size: 14px; margin-top: 4px; font-weight: 500;">Selamat datang kembali, {{ $adminName }}</p>
        </div>

        <div class="header-right" style="display: flex; align-items: center; gap: 20px;">
            <form method="GET" action="{{ route('super.dashboard') }}" class="search-box">
                <input 
                    type="text" 
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari data loket..."
                >
                <button type="submit">Search</button>
            </form>

            <div class="user-profile-header" style="display: flex; align-items: center; gap: 12px; background: white; padding: 6px 18px; border-radius: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15,23,42,0.02);">
                <div class="user-avatar" style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border: 2px solid #3b82f6;">
                    <img src="{{ $fotoPath }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="user-info" style="text-align: left;">
                    <p class="user-name" style="font-size: 14px; font-weight: 600; color: #0f172a; line-height: 1.2;">{{ $adminName }}</p>
                    <span style="font-size: 11px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-top: 2px;">Administrator</span>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID UTAMA -->
    <div class="dashboard-grid">

        <!-- TABEL LOKET AKTIF -->
        <div class="card card-wide">
            <h4 class="card-title">Loket Aktif Hari Ini</h4>

            <div style="max-height: 350px; overflow-y: auto; padding-right: 5px; border-radius: 8px;">
                <table class="table-basic" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead style="position: sticky; top: 0; z-index: 10; background-color: #f8fafc; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <tr>
                            <th>No</th>
                            <th>Nama Loket</th>
                            <th>Tanggal</th>
                            <th style="text-align: center;">Status Pelayanan</th>
                        </tr>
                    </thead>
                <tbody>
                    @forelse($loketAktif as $i => $loket)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $loket->nama_loket }}</td>
                        <td>{{ \Carbon\Carbon::parse($loket->tanggal)->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $statusPelayanan = strtolower($loket->status_pelayanan);
                            @endphp
                            <div class="status-container">
                                <span class="service-badge {{ $statusPelayanan }}">
                                    <span class="service-dot"></span>
                                    <span class="status-text">{{ strtoupper($statusPelayanan) }}</span>
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty">Belum ada data loket hari ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- SUMMARY BOX -->
        <div class="summary-column">

            <div class="summary-card green">
                <i class="fa-solid fa-door-open" style="font-size: 28px; margin-bottom: 8px;"></i>
                <p>Layanan Loket Buka</p>
                <h1>{{ $layananBuka }}</h1>
            </div>

            <div class="summary-card red">
                <i class="fa-solid fa-door-closed" style="font-size: 28px; margin-bottom: 8px;"></i>
                <p>Layanan Loket Tutup</p>
                <h1>{{ $layananTutup }}</h1>
            </div>

            <div class="summary-card blue">
                <i class="fa-solid fa-user-check" style="font-size: 28px; margin-bottom: 8px;"></i>
                <p>Karyawan Aktif</p>
                <h1>{{ $totalKaryawanLogin }}</h1>
            </div>

        </div>

    </div>

    <!-- ========================= -->
    <!-- TABEL KARYAWAN LOGIN -->
    <!-- ========================= -->
    <div class="card mt-30">
        <h4 class="card-title">Status Keaktifan Karyawan Loket</h4>

        <table class="table-basic">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Karyawan</th>
                    <th>Penempatan Loket</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($karyawanLogin as $i => $k)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    @if($k->img_user)
                        <img src="{{ asset('img/foto_karyawan/' . $k->img_user) }}" alt="foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    @else
                        <img src="{{ asset('img/foto_karyawan/default-user.png') }}" alt="foto" style="width: 40px; height: 40px; border-radius: 50%;">
                    @endif
                </td>
                <td>{{ $k->nama_user }}</td>
                <td>{{ $k->nama_loket ?? 'Belum Diplot' }}</td>
                <td style="vertical-align: middle;">
                    @php
                        // Gunakan status_display hasil pengecekan waktu
                        $status = strtolower($k->status_display); 
                    @endphp
                    
                    <div class="status-container-login">
                        <span class="status-badge {{ $status }}">
                            @if($status == 'online')
                                <span class="status-dot-pulse"></span>
                            @else
                                <span class="status-dot-static"></span>
                            @endif
                            <span class="status-text">{{ strtoupper($status) }}</span>
                        </span>
                    </div>
                </td>
            </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection


</body>
</html>
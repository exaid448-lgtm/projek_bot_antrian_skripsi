@extends('layout.navbar_admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="update-status-url" content="{{ route('antrian.updateStatus') }}">
    <link rel="stylesheet" href="{{ asset('css/loket_admin.css') }}">

    @if(isset($no_loket) && $no_loket)
        <main class="main-content" style="display: flex; align-items: center; justify-content: center; min-height: 80vh;">
            <div class="warning-card" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; max-width: 500px; width: 100%;">
                <div style="background: #fef3c7; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: #d97706;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 36px;"></i>
                </div>
                <h2 style="color: #1f2937; margin-bottom: 12px; font-weight: 700;">Akses Dibatasi</h2>
                <p style="color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 24px;">
                    Akun Anda belum dikaitkan dengan loket mana pun di sistem.<br>
                    Silakan hubungi <strong>Administrator</strong> untuk mengatur penempatan loket Anda.
                </p>
                <div style="border-top: 1px solid #f3f4f6; padding-top: 20px;">
                    <p style="color: #9ca3af; font-size: 13px;">MPP Online Kota Banjarbaru</p>
                </div>
            </div>
        </main>
    @else
        <main class="main-content">
        <!-- Top Bar with breadcrumb and profile info -->
        <div class="top-bar">
            <div class="page-title">
                <h2>Layanan Loket</h2>
                <p>Dashboard manajemen antrian loket secara realtime</p>
            </div>
            <div class="user-profile-header">
                <div class="user-info">
                    <p class="user-name">{{ session('nama') }}</p>
                </div>
                <div class="user-avatar">
                    @if(session('foto') && file_exists(public_path('img/foto_karyawan/' . session('foto'))))
                        <img src="{{ asset('img/foto_karyawan/' . session('foto')) }}" alt="Profile">
                    @else
                        <img src="{{ asset('img/foto_karyawan/default.png') }}" alt="Default Profile">
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Metric Cards -->
        <div class="container-box">
            <div class="box-antrian card-masuk">
                <div class="card-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="card-info">
                    <p>Antrian Masuk</p>
                    <h1>{{ $total_antrian }}</h1>
                </div>
            </div>

            <div class="box-antrian card-selesai">
                <div class="card-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="card-info">
                    <p>Antrian Selesai</p>
                    <h1>{{ $antrian_selesai }}</h1>
                </div>
            </div>

            <div class="box-antrian card-total">
                <div class="card-icon">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <div class="card-info">
                    <p>Total Pelayanan</p>
                    <h1>{{ $total_pelayanan }}</h1>
                </div>
            </div>
        </div>
        
        <div class="content-body">
            <div class="controls-left">
                <!-- Search Card -->
                <div class="control-card">
                    <h3 class="control-card-title">Cari Antrian</h3>
                    <form method="GET" action="{{ route('dashboard') }}" class="search-form">
                        <div class="search-input-wrapper">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input 
                                type="text"
                                name="q"
                                placeholder="Nomor antrian..."
                                value="{{ $keyword ?? '' }}"
                                autocomplete="off"
                            >
                        </div>
                        <button type="submit">Cari</button>
                    </form>
                </div>

                <!-- Status Pelayanan Card -->
                <div class="control-card {{ $status_pelayanan === 'BUKA' ? 'status-buka' : 'status-tutup' }}">
                    <h3 class="control-card-title">Status Pelayanan</h3>
                    
                    <div class="status-display">
                        <span class="status-dot"></span>
                        <span class="status-label">Pelayanan {{ $status_pelayanan }}</span>
                    </div>

                    <form method="POST" action="{{ route('pelayanan.toggle') }}">
                        @csrf
                        <button type="submit" class="btn-toggle-status">
                            {{ $status_pelayanan === 'BUKA' ? 'Tutup Pelayanan' : 'Buka Pelayanan' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="table-area">
                <div class="table-card">
                    <h3 class="table-title"><i class="fa-solid fa-list-check"></i> Data Antrian Hari Ini</h3>

                    <table class="antrian-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Antrian</th>
                                <th>Prioritas</th>
                                <th>Waktu Antrian</th>
                                <th>Waktu Panggil</th>
                                <th>Waktu Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($antrianHariIni as $i => $a)
                            <tr style="{{ $a->is_prioritas ? 'background-color: #fffbeb;' : '' }}">
                                <td>{{ $i + 1 }}</td>
                                <td class="kode">
                                    {{ $a->nomor_antrian }}
                                    @if($a->is_prioritas)
                                        <i class="fa-solid fa-star" style="color: #f59e0b; margin-left: 5px;" title="Antrean Prioritas"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($a->is_prioritas)
                                        <div style="font-size: 11px; font-weight: bold; color: #d97706; text-transform: capitalize;">
                                            {{ str_replace('_', ' ', $a->jenis_prioritas) }}
                                        </div>
                                        @if($a->status_validasi_prioritas == 'menunggu' && in_array($a->jenis_prioritas, ['disabilitas_sementara', 'ibu_hamil']))
                                            <form action="{{ route('loket.antrian.tolakPrioritas', $a->id_antrian) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak status prioritas pengunjung ini? Tiket akan dikembalikan ke status reguler.');" style="margin-top: 5px;">
                                                @csrf
                                                <input type="hidden" name="alasan" value="Ditolak oleh petugas loket (Fisik tidak memenuhi syarat)">
                                                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 3px 8px; border-radius: 4px; font-size: 10px; cursor: pointer; font-weight: bold;">
                                                    <i class="fa-solid fa-xmark"></i> Tolak Prioritas
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span style="font-size: 11px; color: #9ca3af;">Reguler</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($a->waktu_voice)->format('H:i') }}</td>
                                <td id="panggil-{{ $a->id_antrian }}">{{ $a->waktu_panggil ? \Carbon\Carbon::parse($a->waktu_panggil)->format('H:i') : '-' }}</td>
                                <td id="selesai-{{ $a->id_antrian }}">{{ $a->waktu_selesai ? \Carbon\Carbon::parse($a->waktu_selesai)->format('H:i') : '-' }}</td>

                                <td>
                                    <span id="label-{{ $a->id_antrian }}" class="status {{ $a->status_antrian }}">
                                        {{ ucfirst($a->status_antrian) }}
                                    </span>
                                </td>

                                <td>
                                    <select onchange="gantiStatusAntrian('{{ $a->id_antrian }}', this.value)" class="dropdown-status-antrian">
                                        <option value="menunggu" {{ $a->status_antrian === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="dipanggil" {{ $a->status_antrian === 'dipanggil' ? 'selected' : '' }}>Dipanggil</option>
                                        <option value="selesai" {{ $a->status_antrian === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="terlewat" {{ $a->status_antrian === 'terlewat' ? 'selected' : '' }}>Terlewat</option>
                                    </select>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="kosong">Belum ada antrian hari ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    @endif

    <script src="{{ asset('js/home_loket.js') }}"></script>
@endsection
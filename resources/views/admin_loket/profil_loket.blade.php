<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
      <link rel="stylesheet" href="{{ asset('css/profil_loket.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<style>
.profile-main-card {
    background: white; 
    border-radius: 16px; 
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05); 
    border: 1px solid #f1f5f9; 
    display: grid; 
    grid-template-columns: 280px 1fr; 
    gap: 35px; 
    padding: 35px; 
    transition: all 0.3s ease;
    margin-bottom: 30px;
}
.profile-main-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}
.profile-details-grid {
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 30px; 
    align-items: start;
}
.profile-left-section {
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    text-align: center; 
    justify-content: center; 
    border-right: 1px solid #f1f5f9; 
    padding-right: 30px;
}
@media (max-width: 992px) {
    .profile-main-card {
        grid-template-columns: 1fr;
        padding: 25px;
    }
    .profile-left-section {
        border-right: none;
        border-bottom: 1px solid #f1f5f9;
        padding-right: 0;
        padding-bottom: 25px;
    }
    .profile-details-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
</style>
<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4f8e8; color: #27ae60; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #b2f5ea; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 class="page-title" style="margin-bottom: 0;">Profil Karyawan</h3>
        <button onclick="openModal()" class="btn-edit-profile" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; font-family: inherit; font-size: 14px; transition: all 0.2s;">
            <i class="fa-solid fa-user-pen"></i> Edit Profil
        </button>
    </div>

    <div class="profile-main-card">
        <!-- Bagian Kiri: Foto & Status -->
        <div class="profile-left-section">
            <div style="position: relative; margin-bottom: 20px;">
                <img 
                    src="{{ 
                        $profil->img_user 
                        ? asset('img/foto_karyawan/'.$profil->img_user) 
                        : asset('img/foto_karyawan/default-user.png') 
                    }}" 
                    style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #eff6ff; box-shadow: 0 8px 16px rgba(59, 130, 246, 0.08); transition: all 0.3s ease;"
                    alt="Foto Profil"
                    onmouseover="this.style.transform='scale(1.05)'; this.style.borderColor='#3b82f6'"
                    onmouseout="this.style.transform='scale(1)'; this.style.borderColor='#eff6ff'"
                >
            </div>
            <h4 style="font-size: 19px; font-weight: 600; color: #1e293b; margin: 0 0 6px 0; font-family: 'Poppins', sans-serif;">{{ $profil->nama_user }}</h4>
            <span style="font-size: 11px; color: #3b82f6; background: #eff6ff; border: 1px solid #dbeafe; padding: 4px 14px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Poppins', sans-serif; display: inline-block;">
                {{ $profil->status_devisi }}
            </span>
        </div>

        <!-- Bagian Kanan: Detail Informasi -->
        <div class="profile-details-grid">
            <!-- Informasi Pribadi -->
            <div>
                <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; font-family: 'Poppins', sans-serif; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-address-card" style="color: #3b82f6;"></i> Informasi Pribadi
                </h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; flex-direction: column; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                        <span style="color: #64748b; font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif;">Nama Lengkap</span>
                        <p style="font-weight: 600; color: #1e293b; margin: 2px 0 0 0; font-size: 13.5px; font-family: 'Poppins', sans-serif;">{{ $profil->nama_user }}</p>
                    </div>
                    <div style="display: flex; flex-direction: column; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                        <span style="color: #64748b; font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif;">Jenis Kelamin</span>
                        <p style="font-weight: 600; color: #1e293b; margin: 2px 0 0 0; font-size: 13.5px; font-family: 'Poppins', sans-serif;">{{ ucfirst($profil->jenis_kelamin) }}</p>
                    </div>
                    <div style="display: flex; flex-direction: column; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                        <span style="color: #64748b; font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif;">Email</span>
                        <p style="font-weight: 600; color: #1e293b; margin: 2px 0 0 0; font-size: 13.5px; font-family: 'Poppins', sans-serif;">{{ $profil->email }}</p>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="color: #64748b; font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif;">Tanggal Lahir</span>
                        <p style="font-weight: 600; color: #1e293b; margin: 2px 0 0 0; font-size: 13.5px; font-family: 'Poppins', sans-serif;">{{ \Carbon\Carbon::parse($profil->tanggal_lahir)->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Loket -->
            <div>
                <h4 style="font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; font-family: 'Poppins', sans-serif; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-door-open" style="color: #10b981;"></i> Informasi Loket
                </h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; flex-direction: column; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                        <span style="color: #64748b; font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif;">Nama Loket</span>
                        <p style="font-weight: 600; color: #1e293b; margin: 2px 0 0 0; font-size: 13.5px; text-transform: uppercase; font-family: 'Poppins', sans-serif;">{{ $profil->loket->nama_loket }}</p>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="color: #64748b; font-size: 12px; font-weight: 500; font-family: 'Poppins', sans-serif;">Lokasi Loket</span>
                        <p style="font-weight: 600; color: #1e293b; margin: 2px 0 0 0; font-size: 13.5px; font-family: 'Poppins', sans-serif;">{{ $profil->loket->lokasi_loket }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKTOR FILTER TANGGAL -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="fa-solid fa-chart-line"></i>
            <h4>Kinerja & Produktivitas</h4>
        </div>
        <form class="filter-form" method="GET" action="{{ route('profil.loket') }}">
            <div class="input-group">
                <label for="filter-periode">Periode Evaluasi:</label>
                <input type="month" id="filter-periode" name="periode" value="{{ $periode }}">
            </div>
            <button type="submit" class="btn-filter">Filter</button>
        </form>
    </div>

    <!-- SEKTOR KINERJA DETAIL (GRID) -->
    <div class="kinerja-wrapper">
        <!-- Skor Poin Kinerja -->
        <div class="kinerja-card score-card">
            <h4 class="card-section-title">Poin Kinerja Bulan Ini</h4>
            <div class="circular-progress-wrapper">
                @php
                    $progressDegree = ($total_poin / 100) * 360;
                @endphp
                <div class="circular-progress" style="background: conic-gradient(#3b82f6 {{ $progressDegree }}deg, #f1f5f9 0deg);">
                    <div class="inner-circle">
                        <span class="score-value">{{ $total_poin }}</span>
                        <span class="score-label">/ 100</span>
                    </div>
                </div>
            </div>
            <div class="score-summary">
                <p>Status: 
                    @if($total_poin >= 95)
                        <span class="status-badge excellent">Sangat Baik</span>
                    @elseif($total_poin >= 85)
                        <span class="status-badge good">Baik</span>
                    @else
                        <span class="status-badge warning">Cukup</span>
                    @endif
                </p>
                <small>Nilai direset setiap awal bulan ke 100 poin.</small>
            </div>
            
            <div class="riwayat-kesalahan">
                <h5>Riwayat Pengurangan Poin</h5>
                <ul class="kesalahan-list">
                    @forelse($pelanggaran as $p)
                        <li class="kesalahan-item">
                            <div class="kesalahan-info">
                                <strong>{{ $p->jenis_pelanggaran === 'terlambat_absen' ? 'Terlambat Absensi Masuk' : 'Terlambat Membuka Loket' }}</strong>
                                <span>{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }} · {{ \Carbon\Carbon::parse($p->waktu_kejadian)->format('H:i') }}</span>
                                @if($p->keterangan)
                                    <span style="font-size: 11px; color: #64748b; font-weight: normal; margin-top: 2px;">{{ $p->keterangan }}</span>
                                @endif
                            </div>
                            <span class="deduction">-{{ $p->poin_dipotong }} Pts</span>
                        </li>
                    @empty
                        <li style="text-align: center; color: #94a3b8; font-size: 13px; padding: 20px 0;">
                            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 20px; margin-bottom: 8px; display: block;"></i>
                            Tidak ada pelanggaran pada periode ini.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Grafik Tren Kinerja -->
        <div class="kinerja-card chart-card">
            <h4 class="card-section-title">Tren Kinerja Karyawan</h4>
            <div class="chart-container-wrapper">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js and Custom Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

    const performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Poin Kinerja',
                data: @json($chartData),
                borderColor: '#3b82f6',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `Skor: ${context.parsed.y} Poin`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 50,
                    max: 100,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Poppins',
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Poppins',
                            size: 11
                        }
                    }
                }
            }
        }
    });
});
</script>

<!-- Modal Edit Profil -->
<div id="editProfileModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; transition: all 0.3s ease;">
    <div class="modal-container" style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; gap: 15px; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">
            <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0; font-family: 'Poppins', sans-serif;">Edit Profil & Tanda Tangan</h4>
            <span style="font-size: 24px; color: #64748b; cursor: pointer;" onclick="closeModal()">&times;</span>
        </div>
        <form action="{{ route('profil.loket.update') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; font-family: 'Poppins', sans-serif;">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="nama_user" style="font-size: 13px; font-weight: 600; color: #475569;">Nama Lengkap</label>
                <input type="text" name="nama_user" id="nama_user" value="{{ $profil->nama_user }}" required style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; font-family: inherit; transition: all 0.2s;" onfocus="this.style.borderColor='#3b82f6'">
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="jenis_kelamin" style="font-size: 13px; font-weight: 600; color: #475569;">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" required style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; font-family: inherit; background: white; transition: all 0.2s;">
                    <option value="laki-laki" {{ $profil->jenis_kelamin == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="perempuan" {{ $profil->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="tanggal_lahir" style="font-size: 13px; font-weight: 600; color: #475569;">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ $profil->tanggal_lahir }}" required style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; font-family: inherit; transition: all 0.2s;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="img_user" style="font-size: 13px; font-weight: 600; color: #475569;">Foto Profil (Avatar)</label>
                <input type="file" name="img_user" id="img_user" accept="image/*" style="font-size: 13px;">
                <small style="font-size: 11px; color: #94a3b8;">Biarkan kosong jika tidak ingin mengubah foto profil</small>
            </div>


            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 15px; margin-top: 10px;">
                <button type="button" onclick="closeModal()" style="padding: 10px 20px; border: 1px solid #e2e8f0; background: white; color: #475569; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px; font-family: inherit;">Batal</button>
                <button type="submit" style="padding: 10px 20px; border: none; background: #3b82f6; color: white; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px; font-family: inherit;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    const modal = document.getElementById('editProfileModal');
    modal.style.display = 'flex';
}
function closeModal() {
    const modal = document.getElementById('editProfileModal');
    modal.style.display = 'none';
}
window.onclick = function(event) {
    const modal = document.getElementById('editProfileModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
@endsection

</body>
</html>
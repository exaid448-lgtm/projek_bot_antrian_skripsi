@extends('layout.navbar_administrator')

@section('content')
<link rel="stylesheet" href="{{ asset('css/data_kinerja_loket.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Hide the native calendar icon in date inputs since we use a custom SVG */
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: none;
        -webkit-appearance: none;
    }
</style>

<div class="main-content">
    <div class="card-title-container" style="margin-bottom: 20px;">
        <h2 class="page-title" style="margin: 0;">Data Kinerja & Kedisiplinan Karyawan</h2>
        <button id="btnOpenModal" class="btn-action-primary">
            <i class="fa-solid fa-plus"></i> Catat Pelanggaran Karyawan
        </button>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="kpi-container">
        <!-- Card 1: Total Karyawan -->
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="kpi-info">
                <h5>Total Karyawan</h5>
                <h2>{{ $totalKaryawan }} Orang</h2>
                <p>Aktif bertugas di loket</p>
            </div>
        </div>

        <!-- Card 2: Total Pelanggaran -->
        <div class="kpi-card">
            <div class="kpi-icon red">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="kpi-info">
                <h5>Total Pelanggaran</h5>
                <h2>{{ $totalPelanggaran }} Kali</h2>
                <p>Terhitung dalam periode</p>
            </div>
        </div>

        <!-- Card 3: Rata-rata Kedisiplinan -->
        <div class="kpi-card">
            <div class="kpi-icon green">
                <i class="fa-solid fa-gauge-simple-high"></i>
            </div>
            <div class="kpi-info">
                <h5>Rata-rata Kedisiplinan</h5>
                <h2>{{ $rataRataPoin }}%</h2>
                <p>Kategori: {{ $rataRataPoin >= 90 ? 'Sangat Baik' : ($rataRataPoin >= 80 ? 'Baik' : 'Cukup') }}</p>
            </div>
        </div>

        <!-- Card 4: Terbaik -->
        <div class="kpi-card">
            <div class="kpi-icon gold">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="kpi-info">
                <h5>Karyawan Terbaik</h5>
                <h2>{{ $terbaik ? $terbaik['sisa_poin'] : 100 }}%</h2>
                <p title="{{ $terbaik ? $terbaik['nama_user'] : '-' }}">{{ $terbaik ? $terbaik['nama_user'] : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- CHARTS GRID -->
    <div class="charts-grid">
        <!-- Chart 1: Rata-rata Kedisiplinan per Loket -->
        <div class="chart-card">
            <h4 class="chart-card-title">Rata-rata Kedisiplinan Karyawan per Loket (%)</h4>
            <div class="chart-canvas-wrapper">
                <canvas id="chartLoket"></canvas>
            </div>
        </div>

        <!-- Chart 2: Distribusi Jenis Pelanggaran -->
        <div class="chart-card">
            <h4 class="chart-card-title">Distribusi Jenis Pelanggaran</h4>
            <div class="chart-canvas-wrapper">
                @if(count($chartViolationCounts) > 0)
                    <canvas id="chartViolations"></canvas>
                @else
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #a3aed0; font-style: italic;">
                        Tidak ada data pelanggaran dalam periode ini
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DASHBOARD TABLES & FILTERS GRID -->
    <div class="dashboard-grid">
        <!-- LEFT COLUMN: TABLES -->
        <div class="table-column">
            <!-- Card 1: Rekapitulasi Kinerja Karyawan -->
            <div class="card">
                <h4 class="card-title" style="margin-bottom: 20px;">Rekapitulasi Kinerja Karyawan</h4>
                <div class="table-container">
                    <table class="table-basic">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Karyawan</th>
                                <th>Loket</th>
                                <th>Devisi</th>
                                <th style="text-align: center;">Jumlah Pelanggaran</th>
                                <th style="text-align: center;">Poin Kedisiplinan</th>
                                <th>Status Kinerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawanKinerja as $key => $row)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <div class="employee-cell">
                                        <img src="{{ asset($row['img_user'] && file_exists(public_path('img/foto_karyawan/'.$row['img_user'])) ? 'img/foto_karyawan/'.$row['img_user'] : 'img/default.png') }}" class="employee-avatar" alt="Avatar">
                                        <strong>{{ $row['nama_user'] }}</strong>
                                    </div>
                                </td>
                                <td>{{ $row['nama_loket'] }}</td>
                                <td>{{ $row['status_devisi'] }}</td>
                                <td style="text-align: center; font-weight: 600; color: #e53935;">{{ $row['total_pelanggaran'] }}</td>
                                <td style="text-align: center;">
                                    <strong style="color: {{ $row['sisa_poin'] >= 90 ? '#2e7d32' : ($row['sisa_poin'] >= 70 ? '#f57f17' : '#c62828') }}; font-size: 15px;">
                                        {{ $row['sisa_poin'] }}%
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge-status {{ $row['badge_class'] }}">
                                        {{ $row['status_kinerja'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-row">Tidak ada data karyawan ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 2: Log Rincian Pelanggaran -->
            <div class="card">
                <h4 class="card-title" style="margin-bottom: 20px;">Log Riwayat Pelanggaran</h4>
                <div class="table-container">
                    <table class="table-basic">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jenis Pelanggaran</th>
                                <th style="text-align: center;">Potongan Poin</th>
                                <th>Keterangan</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logPelanggaran as $key => $log)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $log->profil->nama_user ?? 'Tidak Diketahui' }}</strong>
                                    <br>
                                    <span style="font-size: 11px; color: #a3aed0;">{{ $log->profil->loket->nama_loket ?? '-' }}</span>
                                </td>
                                <td>
                                    {{ Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}
                                    <br>
                                    <span style="font-size: 11px; color: #a3aed0;">Pukul {{ Carbon\Carbon::parse($log->waktu_kejadian)->format('H:i') }}</span>
                                </td>
                                <td>
                                    <span class="badge-status {{ $log->jenis_pelanggaran === 'terlambat_absen' ? 'badge-kurang' : 'badge-cukup' }}">
                                        {{ $log->jenis_pelanggaran === 'terlambat_absen' ? 'Terlambat Absen' : 'Terlambat Buka Loket' }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: #c62828;">-{{ $log->poin_dipotong }}</td>
                                <td style="font-size: 12px; max-width: 200px; word-wrap: break-word;">{{ $log->keterangan }}</td>
                                <td style="text-align: center;">
                                    <form action="{{ route('data-kinerja.destroy', $log->id_poin) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus catatan pelanggaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-log">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-row">Tidak ada catatan pelanggaran ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: FILTER & EXPORT -->
        <div class="filter-column">
            <div class="card-filter">
                <h4 class="filter-title">Filter & Cetak Kinerja</h4>
                <form action="{{ route('data-kinerja.index') }}" method="GET">
                    <!-- Cari Karyawan -->
                    <div class="filter-group">
                        <p class="label-text">Nama Karyawan</p>
                        <div class="search-input-wrapper">
                            <input type="text" name="search" placeholder="Cari nama..." value="{{ $search }}">
                            <button type="submit" class="btn-search">Cari</button>
                        </div>
                    </div>

                    <!-- Filter Loket -->
                    <div class="filter-group">
                        <p class="label-text">Pilih Loket</p>
                        <div class="select-input-wrapper">
                            <select name="loket" onchange="this.form.submit()">
                                <option value="">Semua Loket</option>
                                @foreach($list_loket as $lkt)
                                    <option value="{{ $lkt->id_loket }}" {{ $id_loket == $lkt->id_loket ? 'selected' : '' }}>
                                        {{ $lkt->nama_loket }} {{ $lkt->nama_pelayanan ? '- ' . $lkt->nama_pelayanan : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dari Tanggal -->
                    <div class="filter-group">
                        <p class="label-text">Mulai Tanggal</p>
                        <div class="date-input-wrapper">
                            <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ $tgl_mulai }}">
                            <div class="calendar-btn-custom" onclick="document.getElementById('tgl_mulai').showPicker()">
                                <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="filter-group">
                        <p class="label-text">Sampai Tanggal</p>
                        <div class="date-input-wrapper">
                            <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ $tgl_selesai }}">
                            <div class="calendar-btn-custom" onclick="document.getElementById('tgl_selesai').showPicker()">
                                <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 12px;">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('data-kinerja.index') }}" class="btn-reset">Reset Filter</a>
                </form>

                <hr class="filter-divider">

                <!-- PRINT PDF FORM (INCORPORATING REPORT TYPE OPTION) -->
                <form id="formCetakPdf" action="{{ route('data-kinerja.cetak') }}" method="GET" target="_blank">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="loket" value="{{ $id_loket }}">
                    <input type="hidden" name="tgl_mulai" value="{{ $tgl_mulai }}">
                    <input type="hidden" name="tgl_selesai" value="{{ $tgl_selesai }}">
                    <input type="hidden" name="with_qr" id="with_qr_input" value="1">

                    <div class="filter-group" style="margin-bottom: 15px;">
                        <p class="label-text">Tabel Yang Ingin Dicetak</p>
                        <div class="select-input-wrapper">
                            <select name="tipe_laporan">
                                <option value="semua">Semua Data (Rekap & Log)</option>
                                <option value="rekap_kinerja">Hanya Rekapitulasi Poin</option>
                                <option value="log_pelanggaran">Hanya Log Pelanggaran</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn-print-custom" onclick="confirmCetak()">
                        🖨️ Cetak Laporan (PDF)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD VIOLATION -->
<div id="modalAddViolation" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h4>Catat Pelanggaran Karyawan Baru</h4>
            <button id="btnCloseModal" class="btn-close-modal">&times;</button>
        </div>
        <form action="{{ route('data-kinerja.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <!-- Select Karyawan -->
                <div class="filter-group">
                    <p class="label-text">Pilih Karyawan</p>
                    <div class="select-input-wrapper">
                        <select name="id_profil" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($all_karyawan as $kry)
                                <option value="{{ $kry->id_profil }}">
                                    {{ $kry->nama_user }} ({{ $kry->loket ? $kry->loket->nama_loket . ($kry->loket->nama_pelayanan ? ' - ' . $kry->loket->nama_pelayanan : '') : 'Tidak ada loket' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Jenis Pelanggaran -->
                <div class="filter-group">
                    <p class="label-text">Jenis Pelanggaran</p>
                    <div class="select-input-wrapper">
                        <select name="jenis_pelanggaran" required>
                            <option value="terlambat_absen">Terlambat Absen</option>
                            <option value="terlambat_buka_loket">Terlambat Buka Loket</option>
                        </select>
                    </div>
                </div>

                <!-- Tanggal & Waktu -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="filter-group">
                        <p class="label-text">Tanggal</p>
                        <input type="date" name="tanggal" class="form-control-custom" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="filter-group">
                        <p class="label-text">Jam Kejadian</p>
                        <input type="time" name="waktu_kejadian" class="form-control-custom" value="{{ date('H:i') }}" required>
                    </div>
                </div>

                <!-- Poin Dipotong -->
                <div class="filter-group">
                    <p class="label-text">Poin Dipotong</p>
                    <input type="number" name="poin_dipotong" class="form-control-custom" value="5" min="1" max="100" required>
                </div>

                <!-- Keterangan -->
                <div class="filter-group">
                    <p class="label-text">Keterangan / Alasan</p>
                    <textarea name="keterangan" class="form-control-custom" placeholder="Tuliskan keterangan detail di sini..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnCancelModal" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-action-primary">Simpan Pelanggaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmCetak() {
        Swal.fire({
            title: 'Konfirmasi Cetak Laporan',
            text: 'Apakah Anda ingin mencantumkan QR Code verifikasi pada laporan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Cantumkan',
            cancelButtonText: 'Tidak, Kosongkan'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('with_qr_input').value = '1';
                document.getElementById('formCetakPdf').submit();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                document.getElementById('with_qr_input').value = '0';
                document.getElementById('formCetakPdf').submit();
            }
        });
    }

    // Handle Modals Toggle
    const modalOverlay = document.getElementById('modalAddViolation');
    const btnOpen = document.getElementById('btnOpenModal');
    const btnClose = document.getElementById('btnCloseModal');
    const btnCancel = document.getElementById('btnCancelModal');

    if (btnOpen) {
        btnOpen.addEventListener('click', () => {
            modalOverlay.classList.add('active');
        });
    }

    [btnClose, btnCancel].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                modalOverlay.classList.remove('active');
            });
        }
    });

    // Close on overlay click
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    });

    // Charts Initialization
    document.addEventListener("DOMContentLoaded", function () {
        // Chart 1: Rata-rata Kedisiplinan per Loket
        const ctxLoket = document.getElementById('chartLoket').getContext('2d');
        const chartLoket = new Chart(ctxLoket, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($loketKinerja)->pluck('nama_loket')) !!},
                datasets: [{
                    label: 'Rata-rata Kedisiplinan (%)',
                    data: {!! json_encode(collect($loketKinerja)->pluck('average_score')) !!},
                    backgroundColor: 'rgba(67, 24, 255, 0.8)',
                    borderColor: '#4318ff',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + "%" },
                            color: '#a3aed0',
                            font: { family: 'Poppins' }
                        },
                        grid: { color: '#f1f4f9' }
                    },
                    x: {
                        ticks: {
                            color: '#a3aed0',
                            font: { family: 'Poppins' }
                        },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Kedisiplinan: ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });

        // Chart 2: Proporsi Pelanggaran Karyawan
        const canvasViolations = document.getElementById('chartViolations');
        if (canvasViolations) {
            const ctxViolations = canvasViolations.getContext('2d');
            const chartViolations = new Chart(ctxViolations, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartViolationLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartViolationCounts) !!},
                        backgroundColor: [
                            '#e53935', // red for terlambat absen
                            '#ffb300', // amber for terlambat buka loket
                            '#4318ff',
                            '#00b0ff'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { family: 'Poppins', size: 11 },
                                color: '#707eae',
                                padding: 15
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection

@extends('layout.navbar_administrator')

@section('content')
<link rel="stylesheet" href="{{ asset('css/data_kinerja_loket.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main-content">
    <div class="card-title-container" style="margin-bottom: 20px;">
        <h2 class="page-title" style="margin: 0;">Manajemen Booking Antrean</h2>
        <p style="color: #a3aed0; font-size: 14px; margin-top: 5px;">Pantau dan Kelola daftar pengunjung yang memesan jadwal layanan.</p>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- KPI STATS CARDS -->
    <div class="kpi-container">
        <!-- Card 1: Total Booking -->
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="kpi-info">
                <h5>Total Booking</h5>
                <h2>{{ $totalBooking }} Data</h2>
                <p>Periode pencarian ini</p>
            </div>
        </div>

        <!-- Card 2: Status Menunggu -->
        <div class="kpi-card">
            <div class="kpi-icon gold">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="kpi-info">
                <h5>Menunggu</h5>
                <h2>{{ $menunggu }} Antrean</h2>
                <p>Menunggu waktu kedatangan</p>
            </div>
        </div>

        <!-- Card 3: Selesai -->
        <div class="kpi-card">
            <div class="kpi-icon green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="kpi-info">
                <h5>Selesai Dilayani</h5>
                <h2>{{ $selesai }} Antrean</h2>
                <p>Status antrean selesai</p>
            </div>
        </div>

        <!-- Card 4: Batal -->
        <div class="kpi-card">
            <div class="kpi-icon red">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="kpi-info">
                <h5>Dibatalkan</h5>
                <h2>{{ $batal }} Antrean</h2>
                <p>Dibatalkan / Kadaluarsa</p>
            </div>
        </div>
    </div>

    <!-- CHARTS GRID -->
    <div class="charts-grid" style="grid-template-columns: 1fr;">
        <div class="chart-card" style="width: 100%;">
            <h4 class="chart-card-title">Distribusi Jumlah Booking per Loket Layanan</h4>
            <div class="chart-canvas-wrapper" style="height: 250px;">
                @if(array_sum($loketChartCounts) > 0)
                    <canvas id="chartBookingLoket"></canvas>
                @else
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #a3aed0; font-style: italic;">
                        Tidak ada data booking pada periode ini
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DASHBOARD TABLES & FILTERS GRID -->
    <div class="dashboard-grid">
        <!-- LEFT COLUMN: TABLES -->
        <div class="table-column">
            
            <!-- Card 1: Tabel Pengaturan Loket Booking -->
            <div class="card" style="margin-bottom: 25px;">
                <h4 class="card-title" style="margin-bottom: 20px;"><i class="fa-solid fa-gear text-blue-500 mr-2"></i> Pengaturan Kuota Booking per Loket</h4>
                <div class="table-container">
                    <table class="table-basic">
                        <thead>
                            <tr>
                                <th>Nama Loket</th>
                                <th style="text-align: center;">Kuota Harian</th>
                                <th style="text-align: center;">Sesi Layanan</th>
                                <th style="text-align: center;">Status Layanan</th>
                                <th style="text-align: center;">Aksi Pengaturan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list_loket as $loket)
                            <tr>
                                <td><strong>{{ $loket->nama_loket }} {{ $loket->nama_pelayanan ? '- ' . $loket->nama_pelayanan : '' }}</strong></td>
                                <td style="text-align: center; font-weight: bold; color: #4318ff;">
                                    {{ $loket->kuota_booking ?? 10 }} Orang
                                </td>
                                <td style="text-align: center;">
                                    @php $sesi = $loket->sesi_booking ?? 'pagi_siang'; @endphp
                                    @if($sesi == 'pagi')
                                        <span class="badge-status" style="background-color: #f59e0b; color: white;">Pagi (08:00-11:00)</span>
                                    @elseif($sesi == 'siang')
                                        <span class="badge-status" style="background-color: #8b5cf6; color: white;">Siang (13:00-15:00)</span>
                                    @else
                                        <span class="badge-status" style="background-color: #3b82f6; color: white;">Pagi & Siang</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if(($loket->status_booking ?? 'aktif') == 'aktif')
                                        <span class="badge-status badge-cukup">AKTIF</span>
                                    @else
                                        <span class="badge-status badge-kurang">TUTUP</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-action-primary" style="font-size: 11px; padding: 5px 10px;" onclick="openEditModal('{{ $loket->id_loket }}', '{{ $loket->nama_loket }} {{ $loket->nama_pelayanan ? '- ' . $loket->nama_pelayanan : '' }}', {{ $loket->kuota_booking ?? 10 }}, '{{ $loket->status_booking ?? 'aktif' }}', '{{ $loket->sesi_booking ?? 'pagi_siang' }}')">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 2: Tabel Daftar Pengunjung Booking -->
            <div class="card">
                <h4 class="card-title" style="margin-bottom: 20px;"><i class="fa-solid fa-list-check text-blue-500 mr-2"></i> Daftar Pengunjung Booking</h4>
                <div class="table-container">
                    <table class="table-basic">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pengunjung</th>
                                <th>Tujuan Loket</th>
                                <th>Kode Booking</th>
                                <th>Jadwal & Sesi</th>
                                <th style="text-align: center;">Nomor Antrean</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $key => $booking)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $booking->pengunjung->nama ?? 'Tidak Diketahui' }}</strong>
                                    <br>
                                    <span style="font-size: 11px; color: #a3aed0;"><i class="fa-solid fa-phone" style="font-size: 9px;"></i> {{ $booking->pengunjung->nomor_whatsapp ?? '-' }}</span>
                                </td>
                                <td>{{ $booking->loket ? $booking->loket->nama_loket . ($booking->loket->nama_pelayanan ? ' - ' . $booking->loket->nama_pelayanan : '') : '-' }}</td>
                                <td>
                                    <span style="font-family: monospace; font-weight: bold; background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                        {{ $booking->kode_booking_unik ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($booking->tanggal_booking ?? $booking->waktu_voice)->format('d-m-Y') }}</strong>
                                    @if($booking->slot_waktu)
                                        <br><span class="badge-status" style="background-color: #0284c7; color: white; font-size: 10px; padding: 2px 6px;">{{ $booking->slot_waktu }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span style="background-color: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: bold; color: #4318ff; border: 1px solid #e2e8f0;">
                                        {{ $booking->nomor_antrian ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($booking->status_antrian == 'menunggu')
                                        <span class="badge-status" style="background-color: #f59e0b; color: white;">MENUNGGU</span>
                                    @elseif($booking->status_antrian == 'booking')
                                        <span class="badge-status" style="background-color: #8b5cf6; color: white;">BOOKING</span>
                                    @elseif($booking->status_antrian == 'selesai')
                                        <span class="badge-status badge-cukup">SELESAI</span>
                                    @else
                                        <span class="badge-status badge-kurang">BATAL</span>
                                    @endif

                                    @if($booking->status_booking == 'check_in')
                                        <br><span class="badge-status badge-cukup" style="font-size: 9px; margin-top: 3px; display: inline-block;">CHECK-IN</span>
                                    @elseif($booking->status_booking == 'no_show')
                                        <br><span class="badge-status badge-kurang" style="font-size: 9px; margin-top: 3px; display: inline-block;">NO-SHOW</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-row">Tidak ada data pengunjung booking ditemukan</td>
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
                <h4 class="filter-title">Filter & Pencarian Data</h4>
                <form action="{{ route('superadmin.booking.index') }}" method="GET">
                    
                    <!-- Cari Pengunjung -->
                    <div class="filter-group">
                        <p class="label-text">Nama / No HP Pengunjung</p>
                        <div class="search-input-wrapper">
                            <input type="text" name="search" placeholder="Cari pengunjung..." value="{{ $search }}">
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
                        <p class="label-text">Tanggal Mulai Kedatangan</p>
                        <div class="date-input-wrapper">
                            <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ $tgl_mulai }}" class="form-control-custom">
                        </div>
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="filter-group">
                        <p class="label-text">Tanggal Akhir Kedatangan</p>
                        <div class="date-input-wrapper">
                            <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ $tgl_selesai }}" class="form-control-custom">
                        </div>
                    </div>

                    <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 12px;">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('superadmin.booking.index') }}" class="btn-reset">Reset Filter</a>
                </form>

                <hr class="filter-divider">

                <!-- PRINT PDF FORM -->
                <form id="formCetakPdf" action="{{ route('superadmin.booking.cetak') }}" method="GET" target="_blank">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="loket" value="{{ $id_loket }}">
                    <input type="hidden" name="tgl_mulai" value="{{ $tgl_mulai }}">
                    <input type="hidden" name="tgl_selesai" value="{{ $tgl_selesai }}">
                    <input type="hidden" name="with_qr" id="with_qr_input" value="1">

                    <div style="margin-bottom: 15px;">
                        <p style="font-size: 11px; color: #a3aed0; text-align: center;">Cetak data tabel pengunjung dengan filter yang sedang aktif saat ini.</p>
                    </div>

                    <button type="button" class="btn-print-custom" onclick="confirmCetak()">
                        🖨️ Cetak Laporan Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pengaturan Loket -->
<div id="modalEditLoket" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h4>Edit Pengaturan Booking Loket</h4>
            <button type="button" class="btn-close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <form action="{{ route('superadmin.booking.update') }}" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="id_loket" id="edit_id_loket">
                
                <div class="filter-group">
                    <p class="label-text">Nama Loket</p>
                    <input type="text" id="edit_nama_loket" class="form-control-custom" readonly style="background-color: #f1f5f9;">
                </div>
                
                <div class="filter-group">
                    <p class="label-text">Kuota Booking Harian</p>
                    <input type="number" name="kuota_booking" id="edit_kuota" class="form-control-custom" required min="1">
                </div>
                
                <div class="filter-group">
                    <p class="label-text">Status Layanan Booking</p>
                    <div class="select-input-wrapper">
                        <select name="status_booking" id="edit_status" required>
                            <option value="aktif">Aktif Buka</option>
                            <option value="nonaktif">Tutup Sementara</option>
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <p class="label-text">Sesi Operasional Booking</p>
                    <div class="select-input-wrapper">
                        <select name="sesi_booking" id="edit_sesi" required>
                            <option value="pagi_siang">Pagi & Siang (08:00 - 15:00)</option>
                            <option value="pagi">Hanya Pagi (08:00 - 11:00)</option>
                            <option value="siang">Hanya Siang (13:00 - 15:00)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 18px 25px 22px; border-top: 1px solid #f1f4f9; display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
                <button type="button" class="btn-cancel" onclick="closeEditModal()" style="flex: none; min-width: 100px; padding: 11px 22px; border-radius: 12px; font-weight: 600; font-size: 13px; background: #f4f7fe; color: #707eae; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; height: 42px;">
                    Batal
                </button>
                <button type="submit" class="btn-action-primary" style="flex: none; min-width: 140px; padding: 11px 24px; border-radius: 12px; font-weight: 600; font-size: 13px; background: #4318ff; color: #ffffff; border: none; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; height: 42px; box-shadow: 0 4px 12px rgba(67, 24, 255, 0.25);">
                    Simpan Perubahan
                </button>
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

    function openEditModal(id, nama, kuota, status, sesi) {
        document.getElementById('edit_id_loket').value = id;
        document.getElementById('edit_nama_loket').value = nama;
        document.getElementById('edit_kuota').value = kuota;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_sesi').value = sesi || 'pagi_siang';
        document.getElementById('modalEditLoket').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('modalEditLoket').classList.remove('active');
    }

    // Menutup modal jika klik di luar box
    document.getElementById('modalEditLoket').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    // Charts Initialization
    document.addEventListener("DOMContentLoaded", function () {
        const canvasBooking = document.getElementById('chartBookingLoket');
        if (canvasBooking) {
            const ctxBooking = canvasBooking.getContext('2d');
            new Chart(ctxBooking, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($loketChartNames) !!},
                    datasets: [{
                        label: 'Total Booking',
                        data: {!! json_encode($loketChartCounts) !!},
                        backgroundColor: 'rgba(67, 24, 255, 0.8)',
                        borderColor: '#4318ff',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.5,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
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
                                    return ' ' + context.raw + ' Booking';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection

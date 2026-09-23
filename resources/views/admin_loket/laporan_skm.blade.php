<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Manajemen SKM</title>
    <link rel="stylesheet" href="{{ asset('css/style_skm.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<style>
    .skm-main-wrapper {
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-sizing: border-box;
    }
    .skm-filter-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    /* TABS CSS */
    .analytics-tabs-container {
        background: white;
        border-radius: 10px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-top: 24px;
        box-sizing: border-box;
    }
    .analytics-tabs {
        display: flex;
        gap: 10px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
        margin-bottom: 20px;
        overflow-x: auto;
    }
    .analytics-tab-btn {
        background: transparent;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .analytics-tab-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }
    .analytics-tab-btn.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    .analytics-tab-content {
        display: none;
        animation: fadeIn 0.4s ease;
    }
    .analytics-tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    }
    .skm-filter-inputs {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .skm-badge-success { background-color: #2ec4b6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    .skm-badge-danger { background-color: #e71d36; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    .skm-badge-warning { background-color: #ff9f1c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
</style>

<div class="skm-main-wrapper">
    <div class="skm-header-flex page-title" style="margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1e293b;">📊 Laporan & Kepuasan Pelanggan (SKM)</h2>
    </div>
    
    <div class="skm-card skm-filter-box">
        <form action="{{ route('laporan.skm') }}" method="GET" class="skm-filter-form" id="formFilterSKM">
            <div class="skm-filter-inputs">
                <div class="skm-form-group-inline">
                    <label>Periode Mulai:</label>
                    <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ $tglMulai }}" style="height: 38px; box-sizing: border-box; padding: 0 10px; border-radius: 4px; border: 1px solid #ccc; outline: none; font-family: 'Poppins', sans-serif;">
                </div>
                <div class="skm-form-group-inline">
                    <label>Sampai:</label>
                    <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ $tglSelesai }}" style="height: 38px; box-sizing: border-box; padding: 0 10px; border-radius: 4px; border: 1px solid #ccc; outline: none; font-family: 'Poppins', sans-serif;">
                </div>
                
                <div class="skm-form-group-inline" style="margin-left: 10px; flex-direction: column; align-items: flex-start; gap: 5px;">
                    <label style="margin-bottom: 2px;">Tahun Perbandingan:</label>
                    <div style="position: relative;" class="skm-custom-dropdown">
                        <button type="button" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';" style="height: 38px; box-sizing: border-box; padding: 0 12px; background: white; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; text-align: left; min-width: 140px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-family: 'Poppins', sans-serif;">
                            <span>Pilih Tahun</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 8px;"></i>
                        </button>
                        <div style="display: none; position: absolute; background: white; border: 1px solid #ccc; border-radius: 4px; padding: 0; z-index: 100; min-width: 140px; margin-top: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;">
                            @php
                                $startYear = 2020;
                                $endYear = 2035;
                            @endphp
                            @for($y = $startYear; $y <= $endYear; $y++)
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; padding: 8px 12px; font-weight: normal; font-size: 13px; font-family: 'Poppins', sans-serif; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="filter_years[]" value="{{ $y }}" {{ in_array($y, $filterYears ?? []) ? 'checked' : '' }}>
                                    {{ $y }}
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>
                
                <script>
                    // Menutup dropdown jika klik di luar
                    document.addEventListener('click', function(event) {
                        const dropdowns = document.querySelectorAll('.skm-custom-dropdown');
                        dropdowns.forEach(dropdown => {
                            if (!dropdown.contains(event.target)) {
                                const content = dropdown.querySelector('div');
                                if (content) content.style.display = 'none';
                            }
                        });
                    });
                </script>
                
                @if(!session()->has('id_loket') && isset($lokets))
                <div class="skm-form-group-inline">
                    <label>Loket:</label>
                    <select name="id_loket_filter" id="id_loket_filter">
                        <option value="">Semua Loket</option>
                        @foreach($lokets as $l)
                            <option value="{{ $l->id_loket }}" {{ request('id_loket_filter') == $l->id_loket ? 'selected' : '' }}>
                                {{ $l->nama_loket }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            
            <div class="skm-filter-buttons" style="margin-top: 15px;">
                <button type="submit" class="skm-btn skm-btn-secondary" style="height: 38px; box-sizing: border-box; display: flex; align-items: center;">Filter</button>
                <a href="{{ route('laporan.skm') }}" class="skm-btn" style="background-color: #6c757d; color: white; text-decoration: none; padding: 0 12px; height: 38px; box-sizing: border-box; display: flex; align-items: center; border-radius: 4px; font-size: 14px;">🔄 Reset</a>
                
                <div style="position: relative;" class="skm-custom-dropdown">
                    <button type="button" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none';" class="skm-btn skm-btn-success" style="height: 38px; box-sizing: border-box; display: flex; align-items: center; gap: 8px;">
                        🖨️ Cetak Laporan <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                    </button>
                    <div style="display: none; position: absolute; background: white; border: 1px solid #ccc; border-radius: 4px; padding: 5px 0; z-index: 100; min-width: 220px; margin-top: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        <a href="{{ route('laporan.skm.cetak', array_merge(request()->query(), ['tipe_cetak' => 'semua'])) }}" target="_blank" class="dropdown-item" style="display: block; padding: 8px 15px; color: #333; text-decoration: none; font-size: 13px; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'" onclick="return confirmPrint(this, event)">🖨️ Cetak Semua Tabel</a>
                        <a href="{{ route('laporan.skm.cetak', array_merge(request()->query(), ['tipe_cetak' => 'tren'])) }}" target="_blank" class="dropdown-item" style="display: block; padding: 8px 15px; color: #333; text-decoration: none; font-size: 13px; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'" onclick="return confirmPrint(this, event)">📈 Cetak Tabel Tren Bulanan</a>
                        <a href="{{ route('laporan.skm.cetak', array_merge(request()->query(), ['tipe_cetak' => 'detail'])) }}" target="_blank" class="dropdown-item" style="display: block; padding: 8px 15px; color: #333; text-decoration: none; font-size: 13px; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'" onclick="return confirmPrint(this, event)">📋 Cetak Tabel Detail Responden</a>
                    </div>
                </div>
                <button type="button" class="skm-btn skm-btn-primary" id="btnBukaModal" style="height: 38px; box-sizing: border-box; display: flex; align-items: center;">📝 Tambah Soal Baru</button>
            </div>
        </form>
    </div>

    <div class="skm-panel-report">
        <div class="skm-summary-container">
            <div class="skm-card skm-stat-box">
                <span class="skm-stat-title">TOTAL RESPONDEN</span>
                <span class="skm-stat-value">{{ number_format($totalResponden) }}</span>
                <span class="skm-stat-desc">Orang pengisi SKM</span>
            </div>
            <div class="skm-card skm-stat-box">
                <span class="skm-stat-title">RATA-RATA POIN SKM</span>
                <span class="skm-stat-value">{{ number_format($rataRataPoin, 2) }} / 4.0</span>
                <span class="skm-stat-desc" style="color: {{ $predikatWarna ?? 'green' }}; font-weight: bold;">{{ $predikatUmum }}</span>
            </div>
        </div>

        <div class="skm-card">
            <h3>📊 Grafik Tren Kepuasan Masyarakat</h3>
            <hr class="skm-hr">
            <div class="skm-chart-container" style="position: relative; height:320px; width: 100%;">
                <canvas id="skmChart"></canvas>
            </div>
        </div>

        <!-- TABEL ANALITIK (TABS) -->
        <div class="analytics-tabs-container">
            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">📊 Tabel Analisis Detail SKM</h3>
            
            <div class="analytics-tabs">
                <button type="button" class="analytics-tab-btn active" onclick="openSkmTab(event, 'tab-tren')">Tren Kepuasan</button>
                <button type="button" class="analytics-tab-btn" onclick="openSkmTab(event, 'tab-soal')">Detail Soal SKM</button>
                <button type="button" class="analytics-tab-btn" onclick="openSkmTab(event, 'tab-nilai')">Nilai SKM Per Antrian</button>
            </div>

            <!-- TAB 1: TREN KEPUASAN -->
            <div id="tab-tren" class="analytics-tab-content active">
                <h4 style="font-size: 15px; font-weight: 600; color: #3b82f6; margin-bottom: 15px;">I. Rincian Perbandingan Tren Kepuasan ({{ implode(', ', $filterYears ?? []) }})</h4>
                <div class="skm-table-responsive">
                    <table class="skm-table-content">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                @foreach($filterYears ?? [] as $y)
                                    <th style="text-align: center;">Tahun {{ $y }}</th>
                                @endforeach
                                <th style="text-align: center;">Tren Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chartLabels ?? [] as $bulan)
                                <tr>
                                    <td>{{ $bulan }}</td>
                                    @php
                                        $lastVal = null;
                                        $secondLastVal = null;
                                        $countYears = count($filterYears ?? []);
                                        
                                        if ($countYears >= 2) {
                                            $lastYear = $filterYears[$countYears - 1];
                                            $secondLastYear = $filterYears[$countYears - 2];
                                            $lastVal = $tableRincianData[$bulan][$lastYear] ?? 0;
                                            $secondLastVal = $tableRincianData[$bulan][$secondLastYear] ?? 0;
                                        }
                                    @endphp

                                    @foreach($filterYears ?? [] as $y)
                                        <td style="text-align: center;">{{ number_format($tableRincianData[$bulan][$y] ?? 0, 2) }}</td>
                                    @endforeach
                                    
                                    <td style="text-align: center;">
                                        @if($countYears >= 2)
                                            @if($lastVal > $secondLastVal)
                                                <span class="skm-badge-success" style="background-color: #2ec4b6;">📈 Meningkat</span>
                                            @elseif($lastVal < $secondLastVal)
                                                <span class="skm-badge-danger" style="background-color: #e71d36;">📉 Menurun</span>
                                            @else
                                                <span class="skm-badge-warning" style="background-color: #ffb703; color: #000;">➖ Stabil</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: DETAIL SOAL -->
            <div id="tab-soal" class="analytics-tab-content">
                <h4 style="font-size: 15px; font-weight: 600; color: #3b82f6; margin-bottom: 15px;">II. Tabel Detail Soal SKM</h4>
                <div class="skm-table-responsive">
                    <table class="skm-table-content">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pertanyaan</th>
                                <th>Tanggal Upload</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pertanyaan as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->pertanyaan }}</td>
                                    <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i') : '-' }}</td>
                                    <td>
                                        @if($row->is_active == 1)
                                            <span class="skm-badge-success">Aktif</span>
                                        @else
                                            <span class="skm-badge-danger">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; gap: 5px; display: flex; justify-content: center;">
                                        <a href="{{ route('laporan.skm.edit', $row->id_soal) }}" class="skm-btn skm-btn-primary" style="font-size: 12px; padding: 5px 10px;">Pengubahan Status</a>
                                        <button type="button" class="skm-btn skm-btn-danger btn-hapus-soal" data-url="{{ route('laporan.skm.delete', $row->id_soal) }}" style="font-size: 12px; padding: 5px 10px;">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #a0aec0; font-style: italic; padding: 20px;">
                                        Belum ada data pertanyaan SKM yang terdaftar di loket ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: NILAI SKM PER ANTRIAN -->
            <div id="tab-nilai" class="analytics-tab-content">
                <h4 style="font-size: 15px; font-weight: 600; color: #3b82f6; margin-bottom: 15px;">III. Tabel Detail Nilai SKM Per Antrian</h4>
                <div class="skm-table-responsive">
                    <table class="skm-table-content">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nomor Antrian</th>
                                <th width="20%">Loket</th>
                                <th width="25%">Tanggal Pengisian</th>
                                <th width="15%" style="text-align: center;">Rata-rata Poin</th>
                                <th width="15%" style="text-align: center;">Predikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tableData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['nomor_antrian'] }}</td>
                                    <td>{{ $row['loket'] }}</td>
                                    <td>{{ $row['tanggal'] }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ number_format($row['avg_score'], 2) }}</td>
                                    <td style="text-align: center;">
                                        <span class="skm-badge {{ $row['badge_class'] ?? 'skm-badge-success' }}">{{ $row['predikat'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #a0aec0; font-style: italic; padding: 20px;">
                                        Belum ada data survei kepuasan yang terdata untuk filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>   </table>
            </div>
        </div>

    </div>
</div>

<div id="skmModalSoal" class="skm-modal-overlay">
    <div class="skm-modal-content">
        <div class="skm-modal-header">
            <h3>📝 Kelola Soal SKM Baru</h3>
            <button type="button" class="skm-modal-close-btn" id="btnTutupModalX">&times;</button>
        </div>
        <hr class="skm-hr" style="margin-bottom: 15px;">
        
        <form action="{{ route('laporan.skm.store-soal') }}" method="POST">
            @csrf
            @if(session('id_loket'))
                <input type="hidden" name="id_loket" value="{{ session('id_loket') }}">
            @else
                <div class="skm-form-group">
                    <label for="id_loket">Pilih Loket Layanan</label>
                    <select name="id_loket" id="id_loket" required>
                        <option value="">-- Pilih Loket --</option>
                        @foreach($lokets as $l)
                            <option value="{{ $l->id_loket }}">{{ $l->nama_loket }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="skm-form-group">
                <label for="pertanyaan">Pertanyaan Soal</label>
                <textarea name="pertanyaan" id="pertanyaan" rows="4" placeholder="Contoh (Unsur Waktu Penyelesaian): Bagaimana pendapat Saudara tentang ketepatan waktu pelayanan di loket ini?" required></textarea>
                <small style="color: #64748b; font-size: 11px;">*Disarankan membuat pertanyaan sesuai 9 Unsur SKM (Permenpan RB No.14 Th 2017)</small>
            </div>

            <div class="skm-form-group">
                <label for="status">Status Soal</label>
                <select name="is_active" id="status">
                    <option value="1">Aktif (Tampilkan)</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>

            <div class="skm-modal-footer">
                <button type="button" class="skm-btn skm-btn-secondary" id="btnTutupModalBatal">Batal</button>
                <button type="submit" class="skm-btn skm-btn-success">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#3085d6' });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({ icon: 'error', title: 'Akses Ditolak!', text: "{{ session('error') }}", confirmButtonColor: '#d33' });
        });
    </script>
@endif

<script>
    window.skmChartData = {
        labels: @json($chartLabels ?? []),
        datasets: @json($chartDatasets ?? [])
    };
</script>

<script>
function confirmPrint(element, event) {
    event.preventDefault();
    Swal.fire({
        title: 'Konfirmasi Cetak',
        text: 'Apakah Anda ingin mencantumkan QR Code verifikasi pada laporan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Cantumkan',
        cancelButtonText: 'Tidak, Kosongkan',
        background: '#ffffff',
        customClass: {
            popup: 'swal2-border-radius-custom'
        }
    }).then((result) => {
        let url = new URL(element.href);
        if (result.isConfirmed) {
            url.searchParams.set('qr', '1');
            window.open(url.toString(), '_blank');
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            url.searchParams.set('qr', '0');
            window.open(url.toString(), '_blank');
        }
    });
    return false;
}
</script>
<script>
    function openSkmTab(evt, tabId) {
        // Sembunyikan semua tab content
        const contents = document.querySelectorAll('.analytics-tab-content');
        contents.forEach(content => {
            content.classList.remove('active');
        });

        // Hapus class active dari semua tombol
        const buttons = document.querySelectorAll('.analytics-tab-btn');
        buttons.forEach(button => {
            button.classList.remove('active');
        });

        // Tampilkan tab yang dipilih
        document.getElementById(tabId).classList.add('active');

        // Tambahkan class active pada tombol yang diklik
        evt.currentTarget.classList.add('active');
    }
</script>
<script src="{{ asset('js/laporan_skm.js') }}"></script>
@endsection
</body>
</html>
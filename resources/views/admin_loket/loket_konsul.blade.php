<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/konsul_loket.css') }}">
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/data_antrian.css') }}">
<link rel="stylesheet" href="{{ asset('css/konsul_loket.css') }}">

<script>
    window.LOKET_KONSUL = {
        updateUrl: "{{ route('konsultasi.updateStatus') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>

<div class="main-content">
    <div class="header-flex">
        <h2 class="page-title">Data Konsultasi Loket {{ $profil->id_loket }}</h2>
    </div>

    <div class="dashboard-grid">
        {{-- SISI KIRI: TABEL --}}
        <div class="card card-table">
            <h4 class="card-inner-title">Daftar Pengunjung Konsultasi</h4>
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengunjung</th>
                            <th>Kontak</th>
                            <th>Konsultasi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Di dalam loket_konsul.blade.php --}}

                        @forelse ($konsultasi as $no => $k)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            
                            {{-- Ambil Nama dari relasi data_pengunjung --}}
                            <td>
                                <strong>{{ $k->data_pengunjung->nama ?? 'Pengunjung Tidak Ditemukan' }}</strong>
                            </td>
                            
                            {{-- Ambil Email & Nomor WhatsApp dari relasi data_pengunjung --}}
                            <td>
                                {{ $k->data_pengunjung->email ?? '-' }}<br>
                                <small class="text-muted">
                                    📱 {{ $k->data_pengunjung->nomor_whatsapp ?? '-' }}
                                </small>
                            </td>
                            
                            <td>{{ Str::limit($k->konsultasi, 50) }}</td>
                            <td>{{ \Carbon\Carbon::parse($k->tanggal_konsul)->format('d/m/Y') }}</td>
                            <td id="status-{{ $k->id_konsul }}">
                                @if ($k->pelayanan_status == 'belum')
                                    <button class="btn-status-belum" data-id="{{ $k->id_konsul }}">
                                        Belum
                                    </button>
                                @else
                                    <span class="badge selesai">SUDAH</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px;">Tidak ada data konsultasi ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SISI KANAN: FILTER --}}
        <div class="card card-filter">
            <h4 class="filter-title">Filter Konsultasi</h4>
            <form method="GET" action="{{ route('loket.konsultasi') }}">
                
                <div class="filter-group">
                    <p class="label-text">Cari Nama / Email</p>
                    <div class="search-input-wrapper">
                        <input type="text" name="q" placeholder="Ketik nama..." value="{{ request('q') }}">
                        <button type="submit" class="btn-search">Cari</button>
                    </div>
                </div>

                <div class="filter-group">
                    <p class="label-text">Status Pelayanan</p>
                    <div class="select-input-wrapper">
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Dilayani</option>
                            <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Dilayani</option>
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <p class="label-text">Dari Tanggal</p>
                    <div class="date-input-wrapper">
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}">
                        <div class="calendar-btn-custom" onclick="document.getElementById('start_date').showPicker()">
                            <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <p class="label-text">Sampai Tanggal</p>
                    <div class="date-input-wrapper">
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}">
                        <div class="calendar-btn-custom" onclick="document.getElementById('end_date').showPicker()">
                            <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-search-full">Terapkan Filter</button>
                <a href="{{ route('loket.konsultasi') }}" class="btn-reset">Reset Filter</a>
                <hr class="filter-divider">
                <a href="{{ route('loket.konsultasi.cetak', request()->all()) }}" target="_blank" class="btn-print-custom">
                    🖨️ Cetak Laporan (PDF)
                 </a>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalKonfirmasi">
    <div class="modal-box">
        <h3>Konfirmasi Pelayanan</h3>
        <p>Apakah pengunjung ini sudah selesai dilayani?</p>
        <div class="modal-actions">
            <button class="btn-modal btn-cancel" id="btnBatal">Batal</button>
            <button class="btn-modal btn-confirm" id="btnUbah">Ya, Sudah</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/loket_konsul.js') }}"></script>
@endpush


</body>
</html>
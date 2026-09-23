<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/jadwal_loket.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/data_antrian.css') }}"> {{-- Gunakan CSS yang sama agar konsisten --}}

<div class="main-content">
    <div class="header-flex">
        <h2 class="page-title">Jadwal Loket Saya</h2>
    </div>

    <div class="dashboard-grid">
        {{-- SISI KIRI: TABEL --}}
        <div class="card card-table">
            <h4 class="card-inner-title">Tabel Jadwal Kerja</h4>
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Shift</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwal as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong>{{ $row->profil->nama_user ?? '-' }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $row->jam_masuk }}</td>
                            <td>{{ $row->jam_pulang }}</td>
                            <td>{{ ucfirst($row->shift) }}</td>
                            <td>
                                {{-- Gunakan class badge yang sesuai dengan data_antrian.css --}}
                                <span class="badge {{ $row->setatus == 'masuk' ? 'selesai' : ($row->setatus == 'libur' ? 'dipanggil' : 'menunggu') }}">
                                    {{ ucfirst($row->setatus) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 30px;">Data tidak ditemukan untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SISI KANAN: FILTER (Disamakan desainnya) --}}
        <div class="card card-filter">
            <h4 class="filter-title">Filter Jadwal</h4>
            <form action="{{ route('jadwal.index') }}" method="GET">
                
                <div class="filter-group">
                    <p class="label-text">Cari Karyawan</p>
                    <div class="search-input-wrapper">
                        <input type="text" name="search" placeholder="Ketik nama..." value="{{ request('search') }}">
                        <button type="submit" class="btn-search">Cari</button>
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
                <a href="{{ route('jadwal.index') }}" class="btn-reset">Reset Filter</a>

                <hr class="filter-divider">

                <a href="{{ route('jadwal.cetak_loket', request()->all()) }}" target="_blank" class="btn-print-custom">
                    🖨️ Cetak Jadwal (PDF)
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
</body>
</html>
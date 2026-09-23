<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/data_antrian.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
@extends('layout.navbar_admin')
@section('content')
<link rel="stylesheet" href="{{ asset('css/data_antrian.css') }}">

<div class="main-content">
    <div class="header-flex">
        <h2 class="page-title">Data Riwayat Antrian</h2>
        <div class="header-right-placeholder"></div> 
    </div>

    <div class="dashboard-grid">
        {{-- SISI KIRI: TABEL --}}
        <div class="card card-table">
            <h4 class="card-inner-title">Tabel Riwayat Antrian</h4>
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Antrian</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Panggil</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($antrian as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $row->nomor_antrian }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->waktu_voice)->format('d/m/Y H:i') }}</td>
                            <td>{{ $row->waktu_panggil ? \Carbon\Carbon::parse($row->waktu_panggil)->format('H:i') : '-' }}</td>
                            <td>{{ $row->waktu_selesai ? \Carbon\Carbon::parse($row->waktu_selesai)->format('H:i') : '-' }}</td>
                            <td>
                                @if($row->status_antrian == 'batal')
                                    <span class="badge batal">Batal</span>
                                @elseif($row->waktu_selesai)
                                    <span class="badge selesai">Selesai</span>
                                @elseif($row->waktu_panggil)
                                    <span class="badge dipanggil">Dipanggil</span>
                                @else
                                    <span class="badge menunggu">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px;">Belum ada data antrian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SISI KANAN: FILTER (Disamakan dengan Jadwal Loket) --}}
        <div class="card card-filter">
            <h4 class="filter-title">Filter Data</h4>
            <form method="GET" action="{{ route('data.antrian') }}">
                
                {{-- Pencarian Nomor --}}
                <div class="filter-group">
                    <p class="label-text">Cari Nomor Antrian</p>
                    <div class="search-input-wrapper">
                        <input type="text" name="q" placeholder="Contoh: A001" value="{{ request('q') }}">
                        <button type="submit" class="btn-search">Cari</button>
                    </div>
                </div>

                {{-- Filter Status (Dropdown) --}}
                <div class="filter-group">
                    <p class="label-text">Status Antrian</p>
                    <div class="select-input-wrapper">
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="dipanggil" {{ request('status') == 'dipanggil' ? 'selected' : '' }}>Dipanggil</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                </div>

                {{-- Dari Tanggal --}}
                <div class="filter-group">
                    <p class="label-text">Dari Tanggal</p>
                    <div class="date-input-wrapper">
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}">
                        <div class="calendar-btn-custom" onclick="document.getElementById('start_date').showPicker()">
                            <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- Sampai Tanggal --}}
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
                <a href="{{ route('data.antrian') }}" class="btn-reset">Reset Filter</a>
            </form>

            <hr class="filter-divider">

            {{-- Tombol Cetak --}}
            <a href="{{ route('data.antrian.cetak', request()->all()) }}" target="_blank" class="btn-print-custom" onclick="return confirmPrint(this, event)">
                🖨️ Cetak Laporan (PDF)
            </a>
        </div>
    </div>
</div>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@endsection
</body>
</html>
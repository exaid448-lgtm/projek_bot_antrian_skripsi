<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/data_jadwal.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
@extends('layout.navbar_administrator')

@section('content')

<div class="main-content">
    <div class="header-flex">
        <h2 class="page-title">Data Jadwal Karyawan Loket</h2>
        <button class="btn-add-main" onclick="openModal('tambah', null, '{{ route('super.jadwal.store') }}')">+ Tambah Jadwal</button>
    </div>

<div class="dashboard-grid">
    {{-- SISI KIRI: TABEL --}}
    <div class="card card-table">
        <h4 class="card-inner-title">Tabel Jadwal Loket</h4>
        <div class="table-container">
            <table class="table-basic">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Nama Loket</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Shift</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $key => $row)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><strong>{{ $row->profil?->nama_user ?? '-' }}</strong></td>
                        <td>{{ $row->profil?->loket ? $row->profil->loket->nama_loket . ($row->profil->loket->nama_pelayanan ? ' - ' . $row->profil->loket->nama_pelayanan : '') : '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $row->jam_masuk }}</td>
                        <td>{{ $row->jam_pulang }}</td>
                        <td>{{ ucfirst($row->shift) }}</td>
                        <td>
                            <span class="badge {{ strtolower($row->setatus) }}">
                                {{ ucfirst($row->setatus) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-wrapper">
                                <button class="btn-edit-table" 
                                    onclick="openModal('edit', {{ json_encode($row->load('profil.loket')) }}, '{{ route('super.jadwal.update', $row->id_jadwal) }}')">
                                    Edit
                                </button>
                                <button type="button" class="btn-delete-table" onclick="confirmDelete('{{ route('super.jadwal.destroy', $row->id_jadwal) }}')">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center; padding: 30px;">Data jadwal tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SISI KANAN: FILTER (Desain Baru) --}}
    <div class="card card-filter">
        <h4 class="filter-title">Filter Data Jadwal</h4>
        <form action="{{ route('super.jadwal.index') }}" method="GET">
            
            {{-- Pencarian Nama --}}
            <div class="filter-group">
                <p class="label-text">Cari Nama Karyawan</p>
                <div class="search-input-wrapper">
                    <input type="text" name="search" placeholder="Ketik nama..." value="{{ request('search') }}">
                    <button type="submit" class="btn-search">Cari</button>
                </div>
            </div>

            {{-- Filter Loket (Dropdown) --}}
            <div class="filter-group">
                <p class="label-text">Pilih Loket</p>
                <div class="select-input-wrapper">
                    <select name="loket">
                        <option value="">Semua Loket</option>
                        @foreach($lokets as $lkt)
                            <option value="{{ $lkt->nama_loket }}" {{ request('loket') == $lkt->nama_loket ? 'selected' : '' }}>
                                {{ $lkt->nama_loket }} {{ $lkt->nama_pelayanan ? '- ' . $lkt->nama_pelayanan : '' }}
                            </option>
                        @endforeach
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
            <a href="{{ route('super.jadwal.index') }}" class="btn-reset">Reset Filter</a>
        </form>

        <hr class="filter-divider">

        {{-- Tombol Cetak --}}
        <button type="button" class="btn-print-custom" onclick="konfirmasiCetak(event)">
            🖨️ Cetak Jadwal (PDF)
        </button>
    </div>
</div>
</div>

<div id="modalJadwal" class="modal-overlay">
    <div class="modal-content">
        <h3 id="modalTitle">Tambah Jadwal</h3>
        <hr class="modal-line">
        <form id="formJadwal" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="input-group-modal">
                <label>Pilih Loket</label>
                <select name="id_loket" id="selectLoket" onchange="loadKaryawan(this.value)" required>
                    <option value="">-- Pilih Loket --</option>
                    @foreach($lokets as $l)
                        <option value="{{ $l->id_loket }}">{{ $l->nama_loket }} {{ $l->nama_pelayanan ? '- ' . $l->nama_pelayanan : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group-modal">
                <label>Nama Karyawan</label>
                <select name="id_karyawan" id="selectKaryawan" disabled required>
                    <option value="">-- Pilih Loket Dahulu --</option>
                </select>
            </div>

            <div class="input-group-modal">
                <label>Tanggal</label>
                <div class="modal-date-input-box">
                    <input type="date" name="tanggal" id="inputTanggal" required>
                    <div class="modal-calendar-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="input-group-modal">
                <label>Jam Masuk</label>
                <input type="time" name="jam_masuk" id="inputJamMasuk" required>
            </div>

            <div class="input-group-modal">
                <label>Jam Pulang</label>
                <input type="time" name="jam_pulang" id="inputJamPulang" required>
            </div>

            <div class="input-group-modal">
                <label>Shift</label>
                <select name="shift" id="selectShift" required>
                    <option value="pagi">Pagi</option>
                    <option value="siang">Siang</option>
                    <option value="sore">Sore</option>
                </select>
            </div>
            <div class="input-group-modal">
                <label>Status Kehadiran</label>
                <select name="status" id="selectStatus">
                    @foreach($statuses as $sts)
                        @if($sts != "") {{-- Menghindari opsi kosong jika ada di enum --}}
                            <option value="{{ $sts }}">{{ ucfirst($sts) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel-jadwal" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save-jawal">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDelete" class="modal-overlay">
    <div class="modal-content modal-delete-size">
        <div class="delete-icon-wrapper">
            <svg viewBox="0 0 24 24" class="warning-svg">
                <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path>
            </svg>
        </div>
        <h3 class="delete-title">Hapus Data Jadwal?</h3>
        <p class="delete-subtitle">Data yang dihapus tidak bisa dikembalikan.</p>
        
        <form id="formDelete" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-footer-delete">
                <button type="button" class="btn-cancel-delete" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn-confirm-delete">Lanjut Hapus</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/data_jadwal.js') }}"></script>
<script>
    function konfirmasiCetak(e) {
        e.preventDefault();
        
        // Ambil URL dasar dengan query string saat ini
        const baseUrl = "{{ route('super.jadwal.cetak', request()->query()) }}";
        
        // Cek apakah URL sudah memiliki tanda tanya (query string)
        const separator = baseUrl.includes('?') ? '&' : '?';

        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Cetak Laporan',
            text: 'Apakah Anda ingin mencantumkan QR Code verifikasi pada laporan?',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonText: 'Ya, Cantumkan',
            denyButtonText: 'Tidak, Kosongkan',
            confirmButtonColor: '#3b82f6',
            denyButtonColor: '#64748b',
            customClass: {
                title: 'swal-title-custom',
                htmlContainer: 'swal-text-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Ya, pakai QR
                window.open(baseUrl + separator + 'qr=true', '_blank');
            } else if (result.isDenied) {
                // Tidak, tanpa QR
                window.open(baseUrl + separator + 'qr=false', '_blank');
            }
        });
    }
</script>
@endsection
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/data_absensi.css') }}">
</head>
<body>
@extends('layout.navbar_administrator')

@section('content')
<link rel="stylesheet" href="{{ asset('css/data_absensi.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main-content">
    <h2 class="page-title">Data Absensi Karyawan Loket</h2>

    <div class="dashboard-grid">
        <div class="card card-table">
            <h4 class="card-inner-title">Tabel Absensi Loket</h4>
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Nama Loket</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                            <th>Bukti</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->nama_user }}</td>
                            <td>{{ $row->nama_loket }} {{ $row->nama_pelayanan ? '- ' . $row->nama_pelayanan : '' }}</td>
                            <td>{{ $row->lokasi_loket }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td>{{ $row->waktu_masuk }}</td>
                            <td>{{ $row->waktu_pulang ?? '-' }}</td>
                            <td>{{ ucfirst($row->status_absen) }}</td>
                            <td>
                                @if($row->surat_izin)
                                    <a href="{{ route('data_absensi.suratIzin', $row->surat_izin) }}" 
                                    target="_blank" 
                                    class="btn-view-file">
                                    📄 Lihat File
                                    </a>
                                @else
                                    <span style="color: #ccc;">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;">Data tidak ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

<div class="card card-filter">
    <h4 class="filter-title">Filter Data Absensi</h4>
    
    <form action="{{ route('data_absensi.index') }}" method="GET">
        
        {{-- Pencarian Nama --}}
        <div class="filter-group">
            <p class="label-text">Cari Nama Karyawan</p>
            <div class="search-input-wrapper">
                <input type="text" name="search" placeholder="Ketik nama..." value="{{ request('search') }}">
                <button type="submit" class="btn-search">Cari</button>
            </div>
        </div>

        {{-- Filter Loket --}}
        <div class="filter-group">
            <p class="label-text">Pilih Loket</p>
            <div class="select-input-wrapper">
                <select name="loket">
                    <option value="">Semua Loket</option>
                    @foreach($list_loket as $lkt)
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
                <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ request('tgl_mulai') }}">
                <div class="calendar-btn-custom" onclick="document.getElementById('tgl_mulai').showPicker()">
                    <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                </div>
            </div>
        </div>
        
        {{-- Sampai Tanggal --}}
        <div class="filter-group">
            <p class="label-text">Sampai Tanggal</p>
            <div class="date-input-wrapper">
                <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ request('tgl_selesai') }}">
                <div class="calendar-btn-custom" onclick="document.getElementById('tgl_selesai').showPicker()">
                    <svg class="calendar-icon-svg" viewBox="0 0 24 24"><path d="M19,4H18V2H16V4H8V2H6V4H5C3.89,4 3,4.9 3,6V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V6A2,2 0 0,0 19,4M19,19H5V9H19V19M5,6H19V7H5V6M14.53,11.03L13.47,10L10,13.47L8.53,12L7.47,13.06L10,15.59L14.53,11.03Z"></path></svg>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-search-full">Terapkan Filter</button>
        <a href="{{ route('data_absensi.index') }}" class="btn-reset">Reset Filter</a>
    </form>

    <hr class="filter-divider">

    {{-- Logika Cetak Tanpa JS - Sama dengan Data Antrian --}}
    <button type="button" class="btn-print-custom" onclick="konfirmasiCetak(event)">
        🖨️ Cetak Laporan (PDF)
    </button>
</div>
    </div>

    {{-- TABEL SURAT IZIN & PERTUKARAN SHIFT --}}
    <div class="dashboard-grid" style="margin-top: 30px;">
        <div class="card card-table" style="grid-column: 1 / -1;">
            <h4 class="card-inner-title">Tabel Surat Izin & Pertukaran Shift</h4>
            <div class="table-container">
                <table class="table-basic">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan (Izin)</th>
                            <th>Nama Loket</th>
                            <th>Tanggal Izin</th>
                            <th>Surat Izin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiIzin as $key => $izin)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $izin->nama_user }}</td>
                            <td>{{ $izin->nama_loket }} {{ $izin->nama_pelayanan ? '- ' . $izin->nama_pelayanan : '' }}</td>
                            <td>{{ $izin->tanggal }}</td>
                            <td>
                                @if($izin->surat_izin)
                                    <a href="{{ route('data_absensi.suratIzin', $izin->surat_izin) }}" target="_blank" class="btn-view-file">📄 Lihat File</a>
                                @else
                                    <span style="color: #ccc;">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn-edit-shift" 
                                    style="background-color: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-family: 'Poppins', sans-serif; display: flex; align-items: center; gap: 5px; font-size: 0.85rem;"
                                    onclick="window.openModalTukarShift(this)"
                                    data-idprofil="{{ $izin->id_profil }}"
                                    data-nama="{{ $izin->nama_user }}"
                                    data-loket="{{ $izin->nama_loket }} {{ $izin->nama_pelayanan ? '- ' . $izin->nama_pelayanan : '' }}"
                                    data-tanggal="{{ $izin->tanggal }}">
                                    <i class="fa-solid fa-right-left"></i> Tukar Shift
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;">Tidak ada data izin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/data_absensi.js') }}?v={{ time() }}"></script>

<script>
    window.openModalTukarShift = function(btn) {
        const idProfil = btn.getAttribute('data-idprofil');
        const nama = btn.getAttribute('data-nama');
        const loket = btn.getAttribute('data-loket');
        const tanggal = btn.getAttribute('data-tanggal');
        
        // Tampilkan loading popup
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Mencari karyawan pengganti di loket yang sama.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil data karyawan satu loket
        fetch(`/data-absensi/get-karyawan-satu-loket?id_profil_izin=${idProfil}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Pilih Karyawan Pengganti --</option>';
                if (data.length > 0) {
                    data.forEach(karyawan => {
                        options += `<option value="${karyawan.id_profil}">${karyawan.nama_user}</option>`;
                    });
                } else {
                    options = '<option value="">Tidak ada karyawan lain di loket ini.</option>';
                }

                // Tampilkan SweetAlert form
                Swal.fire({
                    title: 'Pertukaran Shift',
                    html: `
                        <form id="formTukarShiftSwal" action="{{ route('data_absensi.tukarShift') }}" method="POST" style="text-align: left; margin-top: 15px;">
                            @csrf
                            <input type="hidden" name="id_profil_izin" value="${idProfil}">
                            <input type="hidden" name="tanggal_izin" value="${tanggal}">
                            
                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 14px; font-weight: 600; color: #475569;">Karyawan Izin</label>
                                <input type="text" value="${nama}" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 5px; background: #f8fafc; margin-top: 5px;">
                            </div>
                            
                            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label style="font-size: 14px; font-weight: 600; color: #475569;">Loket</label>
                                    <input type="text" value="${loket}" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 5px; background: #f8fafc; margin-top: 5px;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="font-size: 14px; font-weight: 600; color: #475569;">Tanggal</label>
                                    <input type="text" value="${tanggal}" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 5px; background: #f8fafc; margin-top: 5px;">
                                </div>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 14px; font-weight: 600; color: #475569;">Pilih Karyawan Pengganti</label>
                                <select name="id_profil_pengganti" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 5px; margin-top: 5px;">
                                    ${options}
                                </select>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan Pertukaran',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#ef4444',
                    width: '500px',
                    preConfirm: () => {
                        const form = document.getElementById('formTukarShiftSwal');
                        const select = form.querySelector('select[name="id_profil_pengganti"]');
                        if (!select.value) {
                            Swal.showValidationMessage('Harap pilih karyawan pengganti!');
                            return false;
                        }
                        form.submit();
                    }
                });
            })
            .catch(err => {
                console.error('Error fetching employees:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal mengambil data karyawan pengganti. Pastikan server berjalan.'
                });
            });
    };

    function konfirmasiCetak(e) {
        e.preventDefault();
        
        // Ambil URL dasar dengan query string saat ini
        const baseUrl = "{{ route('data_absensi.cetak', request()->query()) }}";
        
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
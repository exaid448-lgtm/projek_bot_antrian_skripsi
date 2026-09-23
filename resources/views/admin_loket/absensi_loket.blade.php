<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/absensi_loket.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
</head>
<body>
@extends('layout.navbar_admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/absensi_loket.css') }}">

<div class="main-content">
    {{-- SweetAlert Notifikasi --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3b82f6'
                });
            });
        </script>
    @endif
    
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>
    @endif

    <div class="container-grid">
        {{-- Kiri: Form Absen --}}
        <div class="glass-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">Form Absen Hari Ini</h3>
                <button type="button" id="btnOpenModalIzin" style="background: #f59e0b; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; font-family: 'Poppins';">
                    <i class="fa-solid fa-envelope"></i> Ajukan Surat Izin
                </button>
            </div>
            
            @if($jadwalHariIni)
                <div style="background: rgba(0, 139, 224, 0.1); border-left: 4px solid #008be0; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px;">
                    <strong style="color: #008be0; display: block; margin-bottom: 4px;">Jadwal Aktif Hari Ini:</strong>
                    <span style="font-size: 0.95rem; color: #333;">
                        Shift: <strong>{{ ucfirst($jadwalHariIni->shift) }}</strong> | 
                        Jam Kerja: <strong>{{ \Carbon\Carbon::parse($jadwalHariIni->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalHariIni->jam_pulang)->format('H:i') }}</strong>
                    </span>
                </div>
                <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Silakan pilih status kehadiran Anda.</p>
            @else
                <div style="background: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px;">
                    <strong style="color: #e74c3c; display: block; margin-bottom: 4px;">Jadwal Tidak Ditemukan:</strong>
                    <span style="font-size: 0.95rem; color: #333;">
                        Anda tidak memiliki jadwal bertugas aktif hari ini. Absensi tidak dapat dilakukan.
                    </span>
                </div>
            @endif

            <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="absenForm">
                @csrf
                <div class="flex-form">
                    <div class="custom-dropdown @if(!$jadwalHariIni) disabled @endif" id="dropdownAbsen" style="@if(!$jadwalHariIni) pointer-events: none; opacity: 0.6; @endif">
                        <div class="selected" id="selectedText">Pilih Status</div>
                        @if($jadwalHariIni)
                        <ul class="dropdown-menu">
                            <li data-value="Hadir">Hadir</li>
                        </ul>
                        @endif
                    </div>
                    <input type="hidden" name="status_absen" id="statusValue" required>



                    <button type="submit" class="btn-submit" style="margin-top: 20px; @if(!$jadwalHariIni) opacity: 0.5; cursor: not-allowed; @endif" @if(!$jadwalHariIni) disabled @endif>Kirim Absen Masuk</button>
                </div>
            </form>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

            <form action="{{ route('absensi.pulang') }}" method="POST">
                @csrf
                <button type="submit" class="btn-submit" style="background:#2ecc71; @if(!$jadwalHariIni) opacity: 0.5; cursor: not-allowed; @endif" @if(!$jadwalHariIni) disabled @endif>Absen Pulang</button>
            </form>
        </div>

        {{-- Kanan: Statistik --}}
        <div class="side-stats">
            <div class="stat-item"><span>Hadir</span><strong>{{ $hadir }}</strong></div>
            <div class="stat-item"><span>Izin</span><strong>{{ $izin }}</strong></div>
            <div class="stat-item"><span>Telat</span><strong>{{ $telat }}</strong></div>
        </div>

        {{-- Bawah: Tabel Riwayat --}}
        <div class="glass-box table-container">
            <h4>Riwayat Absen</h4>
            <table width="100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $row)
                    <tr>
                        <td>{{ $row->tanggal }}</td>
                        <td>{{ $row->waktu_masuk }}</td>
                        <td>{{ $row->waktu_pulang ?? '--:--' }}</td>
                        <td><span class="badge {{ $row->status_absen }}">{{ strtoupper($row->status_absen) }}</span></td>
                        <td>
                            @if($row->surat_izin)
                               <a href="{{ asset('penyimpanan_dokumen/surat_izin/'.$row->surat_izin) }}" 
                                    target="_blank" 
                                    style="color: #008be0; font-weight: bold;">
                                    📄 Lihat Surat
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">Belum ada riwayat absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Ajukan Izin --}}
<div id="modalIzin" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1e293b;">Form Pengajuan Izin</h3>
            <button id="btnCloseModalIzin" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form action="{{ route('absensi.ajukanIzin') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">Tanggal Izin</label>
                <input type="date" name="tanggal_izin" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Poppins';">
            </div>
            <div style="margin-bottom: 20px; border: 2px dashed #cbd5e1; padding: 15px; border-radius: 8px; text-align: center;">
                <label for="suratIzinBaru" style="cursor: pointer; display: block;">
                    <span style="display: block; font-weight: 600; color: #475569; margin-bottom: 5px;">Upload Bukti Izin (PDF/JPG/PNG)</span>
                    <input type="file" name="surat_izin" id="suratIzinBaru" accept=".pdf,image/*" required style="max-width: 100%;">
                </label>
            </div>
            <button type="submit" style="width: 100%; background: #008be0; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-family: 'Poppins';">Kirim Pengajuan Izin</button>
        </form>
    </div>
</div>

<script src="{{ asset('js/absensi_loket.js') }}"></script>

@endsection
</body>
</html>
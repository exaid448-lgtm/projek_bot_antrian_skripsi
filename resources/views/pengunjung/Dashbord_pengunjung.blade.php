<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- WAJIB UNTUK AJAX --}}
    <title>Dashboard Pengunjung</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard_pengunjung.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">

    @include('layout.navbar_pengunjung')

    <div class="max-w-7xl mx-auto px-4 py-6">
        
        {{-- Header Banner --}}
        <div class="relative rounded-xl overflow-hidden h-40 mb-8 bg-black">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200" class="w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 flex flex-col justify-center items-center text-white">
                <h1 class="text-2xl font-bold tracking-[0.2em] uppercase text-center">Mal Pelayanan Publik</h1>
                <p class="text-sm font-light">Kota Banjarbaru</p>
            </div>
        </div>

        {{-- Kartu Antrian Aktif --}}
        <div class="flex flex-wrap gap-6 mb-10 items-start">
        @forelse($antrianSaya as $antrean)
            @php 
                // Deteksi apakah ini tiket booking untuk besok/ke depan
                $isBooking = \Carbon\Carbon::parse($antrean->waktu_voice)->isAfter(\Carbon\Carbon::today()->endOfDay());
                
                // Set warna border berdasarkan status
                $borderColor = 'border-green-500';
                
                if ($isBooking) {
                    $borderColor = 'border-purple-500';
                } elseif ($antrean->status_validasi_prioritas == 'menunggu') {
                    $borderColor = 'border-gray-400 border-dashed';
                } elseif ($antrean->is_prioritas == 1) {
                    $borderColor = 'border-amber-500';
                } elseif ($antrean->status_antrian == 'dipanggil') {
                    $borderColor = 'border-blue-500';
                }
            @endphp
            <div id="ticket-{{ $antrean->id_antrian }}" 
                class="ticket-card bg-white p-5 shadow-lg border-l-4 cursor-pointer active:scale-95 transition-transform {{ $borderColor }} w-full sm:w-[350px]"
                @if($antrean->status_validasi_prioritas == 'ditolak') title="Prioritas ditolak: {{ $antrean->alasan_penolakan_prioritas ?? 'Bukti tidak valid' }}" @endif
                onclick="downloadTicket('ticket-{{ $antrean->id_antrian }}', '{{ $antrean->nomor_antrian }}')">
                
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span style="display: inline-flex; align-items: center; justify-content: center; height: 22px; padding: 0 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; background-color: #f1f5f9; color: #334155;">
                            LOKET {{ $antrean->loket->id_loket ?? '-' }}
                        </span>
                        @if($isBooking)
                            <span style="display: inline-flex; align-items: center; justify-content: center; height: 22px; padding: 0 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff;">
                                📅 BOOKING 
                            </span>
                        @endif
                        @if($antrean->status_validasi_prioritas == 'menunggu')
                            <span style="display: inline-flex; align-items: center; justify-content: center; height: 22px; padding: 0 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                ⏳ VALIDASI BUKTI
                            </span>
                        @elseif($antrean->is_prioritas == 1)
                            <span style="display: inline-flex; align-items: center; justify-content: center; height: 22px; padding: 0 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                ⭐ PRIORITAS
                            </span>
                        @elseif($antrean->status_validasi_prioritas == 'ditolak')
                            <span style="display: inline-flex; align-items: center; justify-content: center; height: 22px; padding: 0 8px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;" title="{{ $antrean->alasan_penolakan_prioritas ?? 'Bukti tidak valid' }}">
                                ⚠️ NORMAL
                            </span>
                        @endif
                    </div>
                    <div class="w-12 h-12 flex items-center justify-center bg-gray-50 rounded-lg p-1 border border-gray-100 shadow-sm overflow-hidden">
                        <img src="{{ $antrean->loket->logo ? asset('img/logo_loket/'.$antrean->loket->logo) : 'https://via.placeholder.com/20' }}" 
                            style="display: inline-block !important;"
                            class="max-w-full max-h-full object-contain" alt="Logo Loket">
                    </div>
                </div>

                <h2 class="text-[10px] uppercase text-gray-400 font-bold">
                    {{ $antrean->loket->nama_loket ?? 'Layanan' }}
                </h2>

                <p class="text-4xl font-bold text-gray-800 my-2">
                    {{ $antrean->nomor_antrian }}
                </p>

            {{-- PENAMBAHAN BAGIAN ESTIMASI WAKTU DINAMIS / INFO BOOKING --}}
            @if($isBooking)
                @php
                    $jamBatas = $antrean->batas_check_in ? substr($antrean->batas_check_in, 0, 5) : (str_contains($antrean->slot_waktu ?? '', '15:00') ? '14:30' : '10:30');
                @endphp
                <div class="booking-info-box mt-3 p-3 rounded-xl border text-center" style="background-color: #faf5ff; border: 1px solid #e9d5ff;">
                    @if($antrean->kode_booking_unik)
                        <div style="margin-bottom: 8px; text-align: center;">
                            <span style="display: inline-block; height: 26px; line-height: 26px; padding: 0 12px; background-color: #ffffff; border: 1px solid #ddd6fe; border-radius: 6px; font-size: 10px; font-weight: 700; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: middle;">
                                Kode Booking: <strong style="font-family: monospace; font-size: 12px; color: #4c1d95; font-weight: 800; text-transform: none; margin-left: 4px;">{{ $antrean->kode_booking_unik }}</strong>
                            </span>
                        </div>
                    @endif

                    <div style="margin-top: 6px; margin-bottom: 6px; text-align: center;">
                        <p style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin: 0; line-height: 1.2;">Jadwal Kunjungan</p>
                        <p style="font-weight: 800; font-size: 13px; color: #1e293b; margin: 2px 0 0 0; line-height: 1.2;">
                            {{ \Carbon\Carbon::parse($antrean->tanggal_booking ?? $antrean->waktu_voice)->format('d M Y') }}
                        </p>
                        @if($antrean->slot_waktu)
                            <p style="font-weight: 700; font-size: 11px; color: #7c3aed; margin: 3px 0 0 0; line-height: 1.2;">
                                Sesi: {{ $antrean->slot_waktu }} WITA
                            </p>
                        @endif
                    </div>

                    {{-- Box Batas Check-In --}}
                    <div style="margin-top: 10px; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0 12px; text-align: center;">
                        <span style="font-size: 11px; color: #92400e; font-weight: 700;">
                            ⏰ Batas Check-in: <strong style="font-weight: 800; color: #78350f; margin-left: 4px;">{{ $jamBatas }} WITA</strong>
                        </span>
                    </div>

                    {{-- Status Check-In Display --}}
                    @if($antrean->status_booking == 'check_in')
                        <div style="margin-top: 8px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0 12px; text-align: center;">
                            <span style="font-size: 11px; color: #065f46; font-weight: 700;">
                                ✅ Sudah Check-In <span style="margin-left: 4px;">{{ $antrean->waktu_check_in ? '(' . \Carbon\Carbon::parse($antrean->waktu_check_in)->format('H:i') . ' WITA)' : '' }}</span>
                            </span>
                        </div>
                    @elseif($antrean->status_booking == 'no_show')
                        <div style="margin-top: 8px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0 12px; text-align: center;">
                            <span style="font-size: 11px; color: #991b1b; font-weight: 700;">
                                ❌ Tidak Hadir (No-Show)
                            </span>
                        </div>
                    @endif
                </div>
            @elseif($antrean->status_antrian == 'menunggu')
                <div class="estimasi-box mt-3 p-2 bg-amber-50 rounded-lg border border-amber-200 text-center">
                    <p class="text-[10px] text-amber-700 font-medium">
                        ⏱️ Estimasi Tunggu: 
                        <span class="waktu-menit font-bold text-sm text-amber-900" data-nomor="{{ $antrean->nomor_antrian }}">
                            Menghitung...
                        </span> 
                        Menit
                    </p>
                </div>
            @else
                <div class="estimasi-box mt-3 p-2 bg-blue-50 rounded-lg border border-blue-200 text-center">
                    <p class="text-[10px] text-blue-700 font-bold animate-pulse">
                        📢 SILAHKAN MENUJU LOKET
                    </p>
                </div>
            @endif

                <div class="border-t border-dashed mt-4 pt-2 flex justify-between items-center gap-2">
                    <div data-html2canvas-ignore="true" class="flex items-center gap-1.5 flex-wrap">
                        @php
                            $tglBooking = $antrean->tanggal_booking ? \Carbon\Carbon::parse($antrean->tanggal_booking) : \Carbon\Carbon::parse($antrean->waktu_voice);
                            $isHariH = $tglBooking->isToday();
                        @endphp
                        
                        {{-- Tombol Check-In untuk Booking --}}
                        @if($isBooking && $antrean->status_booking != 'check_in' && in_array($antrean->status_antrian, ['menunggu', 'booking']))
                            @if($isHariH)
                                <button onclick="checkInAntrian(event, {{ $antrean->id_antrian }})" 
                                        class="text-[10px] text-white bg-green-600 hover:bg-green-700 px-2.5 py-1 rounded font-bold uppercase tracking-wider transition-colors duration-200 flex items-center gap-1 shadow-sm active:scale-95"
                                        title="Konfirmasi Kehadiran di Lokasi">
                                    <i class="fa-solid fa-location-dot"></i> Check-In
                                </button>
                            @else
                                <span class="text-[9px] text-gray-500 bg-gray-100 px-2 py-1 rounded font-medium border border-gray-200" title="Check-in hanya dapat dilakukan pada tanggal kunjungan">
                                    <i class="fa-solid fa-calendar-day"></i> Hari H
                                </span>
                            @endif
                        @endif

                        @if(in_array($antrean->status_antrian, ['menunggu', 'booking']))
                            <button onclick="cancelAntrian(event, {{ $antrean->id_antrian }})" 
                                    class="text-[10px] text-red-500 hover:text-white border border-red-500 hover:bg-red-500 px-2.5 py-1 rounded font-bold uppercase tracking-wider transition-colors duration-200 flex items-center gap-1"
                                    title="Batalkan Antrean">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </button>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[9px] text-gray-400">Tanggal: {{ \Carbon\Carbon::parse($antrean->waktu_voice)->format('d M Y') }}</p>
                        <p class="text-[10px] font-bold uppercase {{ $antrean->status_antrian == 'dipanggil' ? 'text-blue-600' : 'text-green-600' }}">
                            Status: {{ $antrean->status_antrian }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="w-full bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-8 text-center">
                <p class="text-gray-400 text-sm italic">Anda belum memiliki antrean aktif saat ini.</p>
            </div>
        @endforelse
        </div>

        {{-- MENU TOMBOL UTAMA (KOTAK-KOTAK) --}}
        <div class="max-w-7xl mx-auto px-4 mb-14">
            <div class="flex flex-wrap justify-center md:justify-end gap-3 md:gap-9">
                
                <div class="menu-item-wrapper">
                    <div class="bubble-label">tiket</div>
                    <a href="{{ url('/') }}" class="action-btn">
                        <img src="{{ asset('img/icon_btn_pengunjung/ticket_15345749.png') }}" alt="Tiket">
                    </a>
                </div>

                <div class="menu-item-wrapper">
                    <div class="bubble-label">informasi</div>
                    <a href="{{ route('pusat.informasi') }}" class="action-btn">
                        <img src="{{ asset('img/icon_btn_pengunjung/notification-bell_6144558.png') }}" alt="Notif">
                    </a>
                </div>

                <div class="menu-item-wrapper">
                    <div class="bubble-label">konsul</div>
                    <a href="{{ route('chatkonsultasi.index') }}" class="action-btn">
                        <img src="{{ asset('img/icon_btn_pengunjung/advice_17659966.png') }}" alt="Konsul">
                    </a>
                </div>

                <div class="menu-item-wrapper">
                    <div class="bubble-label">syarat</div>
                    <a href="{{ route('informasi.syarat') }}" class="action-btn">
                        <img src="{{ asset('img/icon_btn_pengunjung/google-docs.png') }}" alt="Syarat">
                    </a>
                </div>

            </div>
        </div>

        {{-- Status Layanan Instansi --}}
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Status Layanan Instansi</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($groupedLokets as $loket)
                @php $status = strtolower($loket->status_pelayanan); @endphp
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden {{ $status !== 'buka' ? 'opacity-75' : '' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-100 p-1">
                            @if($loket->logo && file_exists(public_path('img/logo_loket/' . $loket->logo)))
                                <img src="{{ asset('img/logo_loket/' . $loket->logo) }}" class="w-full h-full object-contain">
                            @else
                                <div class="text-[10px] font-bold text-gray-400 uppercase">{{ substr($loket->nama_loket, 0, 2) }}</div>
                            @endif
                        </div>
                        
                        @if($status === 'buka')
                            <div class="flex items-center gap-1.5 bg-green-50 px-2 py-1 rounded-full">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                <span class="text-[10px] font-bold text-green-700 uppercase">Buka</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 bg-red-50 px-2 py-1 rounded-full">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                <span class="text-[10px] font-bold text-red-700 uppercase">Tutup</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase text-gray-800 leading-tight">{{ $loket->nama_loket }}</h4>
                        <p class="text-[10px] text-gray-400 mt-1">lokasi loket: <span class="text-gray-600 font-medium"> {{ $loket->lokasi_loket }}</span></p>
                        <div class="mt-2 space-y-1">
                            @foreach($loket->layanans as $lyn)
                                <div class="flex items-center justify-between bg-slate-50 px-2 py-1.5 rounded border border-slate-100">
                                    <span class="text-[9px] text-slate-600 line-clamp-1 font-semibold" title="{{ $lyn['nama_pelayanan'] }}">{{ $lyn['nama_pelayanan'] }}</span>
                                    <span class="text-[8px] font-bold px-1.5 py-0.5 rounded {{ strtolower($lyn['status']) === 'buka' ? 'text-green-600 bg-green-100/50' : 'text-red-500 bg-red-100/50' }}">
                                        {{ strtolower($lyn['status']) === 'buka' ? 'BUKA' : 'TUTUP' }} ({{ $lyn['prefix'] }})
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 {{ $status === 'buka' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                </div>
            @endforeach
        </div>

        {{-- Section Diagram Penilaian SKM --}}
        <div class="mt-12 mb-6">
            <h3 class="text-xs font-bold text-gray-450 uppercase tracking-widest">Diagram Penilaian SKM</h3>
            <p class="text-xs text-gray-400 mt-1">Hasil kepuasan pengunjung terhadap layanan di masing-masing instansi loket</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-14">
            @foreach($lokets as $loket)
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-100 p-1 shrink-0">
                            @if($loket->logo && file_exists(public_path('img/logo_loket/' . $loket->logo)))
                                <img src="{{ asset('img/logo_loket/' . $loket->logo) }}" class="w-full h-full object-contain">
                            @else
                                <div class="text-[10px] font-bold text-gray-400 uppercase">{{ substr($loket->nama_loket, 0, 2) }}</div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold uppercase text-gray-800 leading-tight line-clamp-1">{{ $loket->nama_loket }}</h4>
                            <p class="text-[10px] text-gray-500 font-semibold line-clamp-1 mt-0.5">{{ $loket->nama_pelayanan ?? 'Pelayanan Umum' }}</p>
                            <span class="text-[9px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded uppercase mt-1 inline-block">LOKET {{ $loket->prefix }}</span>
                        </div>
                    </div>

                    @if($loket->skm_stats['total'] > 0)
                        @php
                            $satisfactionRate = $loket->skm_stats['sangat_bagus'] + $loket->skm_stats['bagus'];
                        @endphp
                        <div class="flex items-center justify-between gap-2 mt-2">
                            <div class="w-32 h-32 shrink-0 relative flex items-center justify-center">
                                <canvas id="chart-{{ $loket->id_loket }}" 
                                        class="skm-chart-canvas w-full h-full"
                                        data-sangat-bagus="{{ $loket->skm_stats['sangat_bagus_count'] }}"
                                        data-bagus="{{ $loket->skm_stats['bagus_count'] }}"
                                        data-kurang="{{ $loket->skm_stats['kurang_count'] }}"
                                        data-sangat-kurang="{{ $loket->skm_stats['sangat_kurang_count'] }}">
                                </canvas>
                                <div class="absolute inset-0 flex flex-col justify-center items-center pointer-events-none pb-0.5">
                                    <span class="text-base font-black text-slate-800 leading-none">{{ $satisfactionRate }}%</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Puas</span>
                                </div>
                            </div>

                            <div class="flex-grow space-y-1 text-slate-600 min-w-0">
                                <div class="text-right mb-1">
                                    <span class="text-[11px] font-extrabold text-blue-600 block leading-tight">{{ $loket->skm_stats['total'] }} Responden</span>
                                    <span class="text-[8px] text-gray-400 block font-medium">Total survei masuk</span>
                                </div>
                                <div class="flex items-center justify-between text-[9px]">
                                    <span class="flex items-center truncate mr-1"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-1 shrink-0"></span>Sangat Baik</span>
                                    <span class="font-bold text-slate-700 whitespace-nowrap">{{ $loket->skm_stats['sangat_bagus'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between text-[9px]">
                                    <span class="flex items-center truncate mr-1"><span class="w-2 h-2 bg-blue-500 rounded-full mr-1 shrink-0"></span>Baik</span>
                                    <span class="font-bold text-slate-700 whitespace-nowrap">{{ $loket->skm_stats['bagus'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between text-[9px]">
                                    <span class="flex items-center truncate mr-1"><span class="w-2 h-2 bg-amber-500 rounded-full mr-1 shrink-0"></span>Kurang Baik</span>
                                    <span class="font-bold text-slate-700 whitespace-nowrap">{{ $loket->skm_stats['kurang'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between text-[9px]">
                                    <span class="flex items-center truncate mr-1"><span class="w-2 h-2 bg-rose-500 rounded-full mr-1 shrink-0"></span>Tidak Baik</span>
                                    <span class="font-bold text-slate-700 whitespace-nowrap">{{ $loket->skm_stats['sangat_kurang'] }}%</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 my-auto">
                            <i class="fa-solid fa-face-meh text-3xl text-slate-300 mb-2 block"></i>
                            <p class="text-xs text-slate-400 italic">Belum ada survei untuk instansi ini.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL SKM TAILWIND + ALPINEJS --}}
    <div id="modalSKM" 
        x-data="{ open: false }" 
        @open-modal-skm.window="open = true" 
        @close-modal-skm.window="open = false"
        x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
        
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4 text-center sm:p-0">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
                <div class="bg-blue-600 px-6 py-4 text-left">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider">Survei Kepuasan</h3>
                </div>
                
                <form id="form-skm" class="text-left">
                    <div class="p-6 max-h-[60vh] overflow-y-auto bg-white">
                        <input type="hidden" name="id_antrian" id="id_antrian_modal">
                        <input type="hidden" name="id_loket" id="id_loket_modal">
                        
                        <div id="isi-soal-skm" class="space-y-4">
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 border-t">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-95">
                            KIRIM PENILAIAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/skm_loket.js') }}"></script>
    <script src="{{ asset('js/dashboard-pengunjung.js') }}?v={{ time() }}"></script>

    {{-- KODE AJAX REALTIME ESTIMASI WAKTU --}}
    <script>
        function cancelAntrian(event, idAntrian) {
            event.stopPropagation(); // Stop click from downloading the ticket card
            
            Swal.fire({
                title: 'Batalkan Antrean?',
                text: 'Apakah Anda yakin ingin membatalkan nomor antrean ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/pengunjung/antrian/batal/' + idAntrian,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    confirmButtonColor: '#10b981'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message,
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat membatalkan antrean.',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    });
                }
            });
        }

        function checkInAntrian(event, idAntrian) {
            event.stopPropagation(); // Stop click from downloading the ticket card
            
            Swal.fire({
                title: 'Konfirmasi Check-In?',
                text: 'Pastikan Anda sudah berada di lokasi MPP. Lanjutkan konfirmasi kedatangan untuk antrean ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Saya Sudah Hadir',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/pengunjung/antrian/check-in/' + idAntrian,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Check-In Berhasil!',
                                    text: response.message,
                                    confirmButtonColor: '#10b981'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Check-In',
                                    text: response.message,
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memproses check-in kedatangan.',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    });
                }
            });
        }

        function ambilEstimasiWaktuRealtime() {
            $.ajax({
                url: '/api/estimasi-antrian-realtime',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Looping setiap elemen ber-class 'waktu-menit'
                        $('.waktu-menit').each(function() {
                            let nomorAntrian = $(this).data('nomor');
                            
                            // Jika nomor antrean terdaftar di dalam perhitungan backend
                            if (response.estimasi_data[nomorAntrian]) {
                                $(this).text(response.estimasi_data[nomorAntrian].estimasi_menit);
                            } else {
                                // Jika tidak ada, kemungkinan antrean sudah dipanggil/selesai
                                $(this).text('0');
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Gagal memperbarui waktu estimasi: ", error);
                }
            });
        }

        // Jalankan fungsi sesaat setelah dokumen selesai dimuat
        $(document).ready(function() {
            ambilEstimasiWaktuRealtime();

            // Set interval agar berjalan otomatis di background setiap 30 detik (30000 ms)
            setInterval(ambilEstimasiWaktuRealtime, 30000);
        });
    </script>
</body>
</html>
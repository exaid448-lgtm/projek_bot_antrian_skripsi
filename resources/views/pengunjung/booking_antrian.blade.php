<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Antrian - MPP Banjarbaru</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans min-h-screen flex flex-col justify-between"
      x-data="{
          isGroupModalOpen: false,
          groupName: '',
          groupLokets: [],
          handleCardClick(hasMultiple, dataStr) {
              let data = JSON.parse(atob(dataStr));
              if (data[0].status_booking === 'nonaktif') return;
              
              if (hasMultiple) {
                  this.groupLokets = data;
                  this.groupName = data[0].nama_loket;
                  this.isGroupModalOpen = true;
              } else {
                  window.triggerSelectLoket(data[0]);
              }
          }
      }">

    <!-- Header / Navbar -->
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/80 shadow-sm px-6 py-3.5">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="relative flex items-center justify-center">
                    <img src="{{ asset('img/super_admin_logo/logo.jpeg') }}" class="h-10 w-auto object-contain rounded-md shadow-2xs border border-slate-100" alt="Logo MPP">
                    <div class="absolute -bottom-1 -right-1 bg-green-500 h-2.5 w-2.5 rounded-full border-2 border-white"></div>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-800 tracking-tight leading-tight">Pemesanan Antrean Online</h1>
                    <p class="text-xs text-slate-500 font-medium">Kota Banjarbaru &bull; <span class="text-blue-600 font-semibold">Loket Online</span></p>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('dashboard.pengunjung') }}" class="inline-flex items-center bg-white hover:bg-slate-50 hover:border-slate-300 text-slate-600 hover:text-blue-600 text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-2xs border border-slate-200 active:scale-95 group">
                    <i class="fa-solid fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform text-slate-500 group-hover:text-blue-500"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-8">
        
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 font-medium text-sm flex items-center">
                <i class="fa-solid fa-circle-exclamation text-lg mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 font-medium text-sm">
                <div class="flex items-center mb-2">
                    <i class="fa-solid fa-circle-exclamation text-lg mr-2"></i> <strong>Terdapat Kesalahan:</strong>
                </div>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="text-center mb-10">
            <h2 class="text-3xl font-black tracking-tight text-slate-800">Pilih Loket Layanan</h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Pilih instansi yang ingin Anda kunjungi untuk melakukan pemesanan jadwal.</p>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_loket" id="id_loket" value="{{ request('id_loket') ?? (isset($id_loket) ? $id_loket : '') }}" required>
            
            <!-- Grid Kartu Loket Grouped -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @forelse($groupedLoket as $namaGrup => $lokets)
                    @php 
                        $mainLoket = $lokets->first(); 
                        $hasMultiple = $lokets->count() > 1;
                        $statusBooking = $mainLoket->status_booking ?? 'aktif'; 
                        $isNonaktif = $statusBooking == 'nonaktif';
                        
                        $groupData = $lokets->map(function($l) {
                            return [
                                'id_loket' => $l->id_loket,
                                'nama_loket' => $l->nama_loket,
                                'nama_pelayanan' => $l->nama_pelayanan ?: $l->nama_loket,
                                'status_booking' => $l->status_booking ?? 'aktif',
                                'sesi_booking' => $l->sesi_booking ?? 'pagi_siang',
                                'prefix' => $l->prefix,
                                'kuota' => $l->kuota_booking ?? 10
                            ];
                        })->toJson();
                    @endphp

                    <div @click="handleCardClick({{ $hasMultiple ? 'true' : 'false' }}, '{{ base64_encode($groupData) }}')" 
                            class="{{ $isNonaktif ? 'bg-slate-50/80 opacity-80 cursor-not-allowed' : 'bg-white hover:shadow-xl hover:border-blue-500 hover:-translate-y-1 cursor-pointer group' }} rounded-3xl shadow-sm border border-slate-100 transition-all duration-300 text-left flex flex-col overflow-hidden relative h-48">
                        
                        @if(!$isNonaktif)
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        @endif

                        <div class="p-6 flex flex-col h-full w-full relative z-10">
                            <div class="w-full flex items-start justify-between mb-4">
                                <div class="p-3 rounded-2xl bg-white shadow-xs border border-slate-100 w-16 h-16 flex items-center justify-center overflow-hidden {{ $isNonaktif ? 'grayscale' : 'group-hover:scale-110 transition-transform duration-300' }}">
                                    @if(!empty($mainLoket->logo) && file_exists(public_path('img/logo_loket/' . $mainLoket->logo)))
                                        <img src="{{ asset('img/logo_loket/' . $mainLoket->logo) }}" class="max-w-full max-h-full object-contain" alt="Logo {{ $mainLoket->nama_loket }}">
                                    @else
                                        <i class="fa-solid fa-building-flag text-2xl {{ $isNonaktif ? 'text-slate-400' : 'text-slate-300 group-hover:text-blue-500' }}"></i>
                                    @endif
                                </div>
                                
                                @if($isNonaktif)
                                    <span class="bg-red-100 text-red-700 font-extrabold text-[10px] px-3 py-1.5 rounded-xl tracking-widest uppercase border border-red-200">
                                        Booking Tutup
                                    </span>
                                @elseif($hasMultiple)
                                    <span class="bg-blue-50 text-blue-600 font-black text-[10px] px-3 py-1.5 rounded-xl tracking-widest uppercase border border-blue-100 flex items-center gap-1.5">
                                        <i class="fa-solid fa-list-ul"></i> {{ $lokets->count() }} Layanan
                                    </span>
                                @else
                                    <span class="bg-slate-50 text-slate-500 font-bold text-[10px] px-3 py-1.5 rounded-xl tracking-widest uppercase border border-slate-200">
                                        LOKET {{ $mainLoket->prefix ?? '0' }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-grow">
                                <h3 class="font-extrabold text-slate-800 text-lg leading-snug transition-colors line-clamp-2 {{ $isNonaktif ? 'text-slate-500' : 'group-hover:text-blue-600' }}">{{ strtoupper($namaGrup) }}</h3>
                                @if(!$hasMultiple)
                                    <p class="text-xs font-medium text-slate-400 mt-1 line-clamp-1">{{ $mainLoket->nama_pelayanan ?? 'Pemesanan Online' }}</p>
                                @else
                                    <p class="text-xs font-medium text-slate-400 mt-1 line-clamp-1">Pilih untuk melihat layanan</p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-slate-50/50 px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-semibold {{ $isNonaktif ? '' : 'group-hover:bg-blue-50/30 transition-colors' }}">
                            @if($isNonaktif)
                                <span class="text-red-500"><i class="fa-solid fa-ban mr-1"></i> Tidak melayani booking</span>
                                <i class="fa-solid fa-lock text-slate-300"></i>
                            @elseif($hasMultiple)
                                <span class="text-slate-500 group-hover:text-blue-600">Lihat Opsi Layanan</span>
                                <div class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </div>
                            @else
                                <span>Pilih loket ini</span>
                                <div class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-slate-400">
                        <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-folder-open text-3xl text-slate-300"></i>
                        </div>
                        <p class="font-bold text-slate-500 text-lg">Belum ada loket layanan online</p>
                    </div>
                @endforelse
            </div>

            <!-- Modal Waktu (jQuery Controller) -->
            <div id="waktu-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 hidden">
                <div id="waktu-container" class="bg-white/95 backdrop-blur-xl border border-white/50 rounded-[2rem] p-6 sm:p-8 shadow-[0_20px_50px_rgba(8,_112,_184,_0.15)] w-full max-w-2xl mx-auto transform scale-95 opacity-0 transition-all duration-300 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
                        
                        <button type="button" id="close-modal" class="absolute top-5 right-5 w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-500 rounded-full transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>

                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100 pr-8">
                            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl shadow-inner shrink-0 border border-blue-100">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 leading-tight">Tentukan Jadwal</h3>
                                <p class="text-sm text-slate-500 font-medium mt-1">Layanan: <span id="loket_terpilih_text" class="text-blue-600 font-bold uppercase tracking-tight"></span></p>
                                <p class="text-xs text-slate-400 mt-1"><i class="fa-solid fa-users mr-1"></i> Kuota Maksimal: <span id="kuota_loket_text" class="font-bold text-slate-600"></span> Pengunjung/Sesi</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Tanggal Kedatangan</label>
                            <input type="date" name="tanggal" id="tanggal" class="w-full bg-slate-50 border-2 border-slate-200 text-slate-800 rounded-xl px-4 py-4 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors font-medium cursor-pointer" required min="{{ date('Y-m-d') }}">
                            <p class="text-xs text-slate-400 mt-2 font-medium"><i class="fa-solid fa-circle-info mr-1"></i> Anda bisa mengambil antrean untuk hari ini atau hari ke depan.</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Sesi / Jam Kedatangan</label>
                            <select name="slot_waktu" id="slot_waktu" class="w-full bg-slate-50 border-2 border-slate-200 text-slate-800 rounded-xl px-4 py-4 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors font-medium cursor-pointer" required>
                                <option value="08:00 - 11:00">Sesi Pagi (08:00 - 11:00 WITA) - Batas Check-in: 10:30 WITA</option>
                                <option value="13:00 - 15:00">Sesi Siang (13:00 - 15:00 WITA) - Batas Check-in: 14:30 WITA</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-2 font-medium"><i class="fa-solid fa-clock mr-1"></i> Batas check-in kedatangan adalah 30 menit sebelum sesi berakhir.</p>
                        </div>

                        @if(isset($isPermanen) && $isPermanen || isset($isLansia) && $isLansia || isset($isPrioritasUmum) && $isPrioritasUmum)
                            <div class="mb-6 border border-amber-200 rounded-xl p-4 bg-amber-50">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-star text-amber-500 text-xl"></i>
                                    <div>
                                        <p class="font-bold text-amber-800 text-sm">Mode Prioritas Otomatis Aktif</p>
                                        <p class="text-xs text-amber-700 mt-0.5">Sistem mendeteksi Anda sebagai pengunjung Prioritas ({{ isset($profil) && $profil->jenis_prioritas ? ucwords(str_replace('_', ' ', $profil->jenis_prioritas)) : (isset($isLansia) && $isLansia ? 'Lansia' : 'Prioritas') }}).</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 border border-slate-200 rounded-xl p-4 bg-slate-50 flex items-start gap-3">
                                <i class="fa-solid fa-info-circle text-blue-500 mt-0.5 text-lg"></i>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">Butuh Antrean Prioritas?</p>
                                    <p class="text-xs text-slate-500 mt-1">Jika Anda Lansia, Ibu Hamil, atau Disabilitas, sistem akan otomatis memberlakukan antrean Prioritas. Pastikan Anda telah melakukan Validasi Hak Prioritas di halaman <a href="{{ route('dashboard.pengunjung') }}" class="text-blue-600 hover:underline font-bold">Profil Pengunjung</a>.</p>
                                </div>
                            </div>
                        @endif

                        <div id="status-kuota-container" class="hidden mb-6">
                            <div id="loading-kuota" class="flex items-center text-blue-500 font-bold mb-2 hidden">
                                <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memeriksa ketersediaan kuota...
                            </div>
                            <div id="status-kuota-text" class="text-sm font-bold p-4 rounded-xl border">
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <button type="button" id="btn-cancel" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 px-6 rounded-xl transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" id="btn-submit" disabled class="bg-blue-600 hover:bg-blue-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-bold py-3.5 px-8 rounded-xl shadow hover:shadow-lg active:scale-95 transition-all inline-flex items-center">
                                <i class="fa-solid fa-ticket mr-2"></i> Konfirmasi Booking
                            </button>
                        </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Modal Pilihan Group AlpineJS -->
    <div x-show="isGroupModalOpen" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div @click.away="isGroupModalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 md:p-8 border border-slate-100 transform transition-all flex flex-col max-h-[90vh]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight uppercase" x-text="groupName"></h3>
                    <p class="text-sm font-medium text-slate-500 mt-1">Silakan pilih spesifikasi layanan online</p>
                </div>
                <button @click="isGroupModalOpen = false" class="h-10 w-10 bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-500 rounded-full flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="overflow-y-auto pr-1 flex-grow space-y-3 custom-scrollbar" style="max-height: 60vh;">
                <template x-for="item in groupLokets" :key="item.id_loket">
                    <div @click="if(item.status_booking !== 'nonaktif') { isGroupModalOpen = false; setTimeout(() => window.triggerSelectLoket(item), 150); }"
                         :class="item.status_booking === 'nonaktif' ? 'opacity-60 bg-slate-50 cursor-not-allowed border-slate-200' : 'hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer group'"
                         class="p-5 rounded-2xl border-2 border-slate-100 transition-all flex items-center justify-between">
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-base" :class="item.status_booking !== 'nonaktif' ? 'group-hover:text-blue-700' : ''" x-text="item.nama_pelayanan || item.nama_loket"></h4>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="bg-slate-200 text-slate-600 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider" :class="item.status_booking !== 'nonaktif' ? 'group-hover:bg-blue-200 group-hover:text-blue-800' : ''" x-text="'LOKET ' + (item.prefix || '0')"></span>
                                <span class="text-xs font-bold" :class="item.status_booking === 'nonaktif' ? 'text-red-500' : 'text-slate-500 group-hover:text-blue-600'" x-text="item.status_booking === 'nonaktif' ? 'Booking Tutup' : 'Kuota: ' + (item.kuota || 10) + ' /sesi'"></span>
                            </div>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center transition-all"
                             :class="item.status_booking !== 'nonaktif' ? 'group-hover:border-blue-500 group-hover:bg-blue-600 group-hover:text-white' : ''">
                            <i class="fa-solid" :class="item.status_booking === 'nonaktif' ? 'fa-lock text-slate-400' : 'fa-chevron-right'"></i>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <footer class="text-center py-5 text-slate-400 text-[10px] font-bold tracking-wider border-t border-slate-200 bg-white/50 backdrop-blur-sm uppercase mt-auto">
        &copy; 2026 Sistem Antrian Kiosk MPP Banjarbaru &bull; IT Division
    </footer>

    <script>
    $(document).ready(function() {
        const $idLoket = $('#id_loket');
        const $tanggal = $('#tanggal');
        const $slotWaktu = $('#slot_waktu');
        const $statusContainer = $('#status-kuota-container');
        const $statusText = $('#status-kuota-text');
        const $loadingKuota = $('#loading-kuota');
        const $btnSubmit = $('#btn-submit');
        const $waktuModal = $('#waktu-modal');
        const $waktuContainer = $('#waktu-container');
        const $loketTerpilihText = $('#loket_terpilih_text');
        const $kuotaLoketText = $('#kuota_loket_text');

        // Ekspos fungsi ke window agar bisa dipanggil oleh AlpineJS
        window.triggerSelectLoket = function(loketData) {
            $idLoket.val(loketData.id_loket);
            $loketTerpilihText.text(loketData.nama_pelayanan || loketData.nama_loket);
            $kuotaLoketText.text(loketData.kuota || 10);
            
            // Atur pilihan slot waktu sesuai sesi operasional loket
            $slotWaktu.empty();
            const sesi = loketData.sesi_booking || 'pagi_siang';
            if (sesi === 'pagi') {
                $slotWaktu.append('<option value="08:00 - 11:00">Sesi Pagi (08:00 - 11:00 WITA) - Batas Check-in: 10:30 WITA</option>');
            } else if (sesi === 'siang') {
                $slotWaktu.append('<option value="13:00 - 15:00">Sesi Siang (13:00 - 15:00 WITA) - Batas Check-in: 14:30 WITA</option>');
            } else {
                $slotWaktu.append('<option value="08:00 - 11:00">Sesi Pagi (08:00 - 11:00 WITA) - Batas Check-in: 10:30 WITA</option>');
                $slotWaktu.append('<option value="13:00 - 15:00">Sesi Siang (13:00 - 15:00 WITA) - Batas Check-in: 14:30 WITA</option>');
            }

            openModal();

            if($tanggal.val()) {
                checkKuota();
            }
        };

        function openModal() {
            $waktuModal.removeClass('hidden');
            setTimeout(() => {
                $waktuContainer.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
            }, 10);
            $('body').addClass('overflow-hidden');
        }

        function closeModal() {
            $waktuContainer.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => {
                $waktuModal.addClass('hidden');
            }, 300);
            $('body').removeClass('overflow-hidden');
        }

        $('#close-modal, #btn-cancel').click(function() {
            closeModal();
            $idLoket.val('');
            $tanggal.val('');
            $statusContainer.addClass('hidden');
        });

        // Jika ID loket sudah ada dari URL, otomatis buka modal
        if ($idLoket.val()) {
            // Karena kita menggunakan grouping, kita tidak bisa dengan mudah mencari card. 
            // Kita harus cari data loket dari array server side, tapi untuk sementara kita hanya buka modal saja
            // Lebih baik kita refresh halaman tanpa id_loket jika tidak ditemukan, tapi kita biarkan dulu.
            // Fitur ini mungkin jarang dipakai jika melalui kiosk.
        }

        $tanggal.change(checkKuota);

        function checkKuota() {
            const loketVal = $idLoket.val();
            const tglVal = $tanggal.val();

            if (loketVal && tglVal) {
                $statusContainer.removeClass('hidden');
                $statusText.hide();
                $loadingKuota.removeClass('hidden').show();
                $btnSubmit.prop('disabled', true);

                const d = new Date(tglVal);
                if(d.getDay() === 0 || d.getDay() === 6) {
                    $loadingKuota.hide();
                    $statusText.html('<i class="fa-solid fa-triangle-exclamation mr-2"></i>Layanan tutup pada hari Sabtu & Minggu.')
                        .removeClass('bg-green-50 border-green-200 text-green-700')
                        .addClass('bg-red-50 border-red-200 text-red-600').show();
                    return;
                }

                $.ajax({
                    url: '{{ route("booking.kuota") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_loket: loketVal,
                        tanggal: tglVal
                    },
                    success: function(res) {
                        $loadingKuota.hide();
                        
                        if(res.is_full) {
                            $statusText.html(`<i class="fa-solid fa-xmark-circle mr-2"></i>Maaf, kuota antrean online untuk tanggal ini sudah habis (Maks: ${res.max_kuota} pengunjung).`)
                                .removeClass('bg-green-50 border-green-200 text-green-700')
                                .addClass('bg-red-50 border-red-200 text-red-600').show();
                        } else {
                            $statusText.html(`<i class="fa-solid fa-check-circle mr-2"></i>Kuota tersedia! (Sisa: ${res.sisa} tiket)`)
                                .removeClass('bg-red-50 border-red-200 text-red-600')
                                .addClass('bg-green-50 border-green-200 text-green-700').show();
                            
                            $btnSubmit.prop('disabled', false);
                        }
                    },
                    error: function() {
                        $loadingKuota.hide();
                        $statusText.html('<i class="fa-solid fa-triangle-exclamation mr-2"></i>Gagal memeriksa kuota server. Silakan coba lagi.')
                            .removeClass('bg-green-50 border-green-200 text-green-700')
                            .addClass('bg-red-50 border-red-200 text-red-600').show();
                    }
                });
            } else {
                $statusContainer.addClass('hidden');
                $btnSubmit.prop('disabled', true);
            }
        }

        $('#bookingForm').submit(function(e) {
            if(!$idLoket.val()) {
                e.preventDefault();
                Swal.fire('Oops', 'Silakan pilih Loket Layanan terlebih dahulu!', 'warning');
                return;
            }
            if(!$tanggal.val()) {
                e.preventDefault();
                Swal.fire('Oops', 'Silakan pilih Tanggal Kedatangan!', 'warning');
                return;
            }
            if(!$slotWaktu.val()) {
                e.preventDefault();
                Swal.fire('Oops', 'Silakan pilih Sesi / Jam Kedatangan!', 'warning');
                return;
            }
        });
    });
    </script>
</body>
</html>

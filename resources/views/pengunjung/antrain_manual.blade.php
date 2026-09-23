<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrian Manual - MPP Banjarbaru</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 font-sans min-h-screen flex flex-col justify-between" 
      x-data="{ 
          isModalOpen: false, 
          isGroupModalOpen: false,
          isModeKhusus: false,
          groupName: '',
          groupLokets: [],
          isSuccessModalOpen: false,
          isQrModalOpen: false,
          qrUrl: '',
          idLoketTerpilih: null,
          layananTerpilih: '',
          jumlahAntrean: 0,
          handleCardClick(hasMultiple, dataStr) {
              let data = JSON.parse(atob(dataStr));
              if (hasMultiple) {
                  this.groupLokets = data;
                  this.groupName = data[0].nama_loket;
                  this.isGroupModalOpen = true;
              } else {
                  this.bukaModal(data[0].id_loket, data[0].nama_pelayanan || data[0].nama_loket, data[0].jumlah_antrean, data[0].status_pelayanan);
              }
          },
          bukaModal(idLoket, namaLayanan, totalAntrean, statusPelayanan) {
              if (statusPelayanan === 'TUTUP') {
                  const urlBooking = `{{ url('/booking-antrian') }}?id_loket=${idLoket}`;
                  @auth
                      Swal.fire({
                          title: 'Loket Tutup!',
                          text: 'Apakah Anda ingin memesan antrean untuk hari esok?',
                          icon: 'info',
                          showCancelButton: true,
                          confirmButtonColor: '#3b82f6',
                          cancelButtonColor: '#94a3b8',
                          confirmButtonText: 'Ya, Booking Sekarang',
                          cancelButtonText: 'Batal'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              window.location.href = urlBooking;
                          }
                      });
                  @else
                      this.qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(urlBooking)}`;
                      this.isQrModalOpen = true;
                  @endauth
                  return;
              }

              this.idLoketTerpilih = idLoket;
              this.layananTerpilih = namaLayanan;
              this.jumlahAntrean = totalAntrean;
              this.isModalOpen = true;
          },
          bukaPopupSukses(data) {
              this.successData = data;
              this.isSuccessModalOpen = true;
          }
      }"
      @sukses-antrian.window="bukaPopupSukses($event.detail)"
      @set-mode-khusus.window="isModeKhusus = $event.detail.status">

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/80 shadow-sm px-6 py-3.5">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="relative flex items-center justify-center">
                    <img src="{{ asset('img/super_admin_logo/logo.jpeg') }}" class="h-10 w-auto object-contain rounded-md shadow-2xs border border-slate-100" alt="Logo MPP">
                    <div class="absolute -bottom-1 -right-1 bg-green-500 h-2.5 w-2.5 rounded-full border-2 border-white"></div>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-800 tracking-tight leading-tight">Mal Pelayanan Publik</h1>
                    <p class="text-xs text-slate-500 font-medium">Kota Banjarbaru &bull; <span class="text-blue-600 font-semibold">Loket Kiosk</span></p>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                @if(!session('id_user'))
                <button @click="toggleModeKhusus(isModeKhusus)" 
                        :class="isModeKhusus ? 'bg-amber-500 text-white border-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.5)]' : 'bg-slate-800 text-slate-300 border-slate-600'" 
                        class="px-4 py-2 rounded-xl text-xs font-bold tracking-wider transition-all duration-300 border-2 flex items-center gap-2">
                    <i class="fa-solid fa-star" :class="isModeKhusus ? 'text-white' : 'text-slate-400'"></i>
                    <span x-text="isModeKhusus ? 'Mode Khusus Aktif' : 'Mode Prioritas'"></span>
                </button>
                @endif
                @auth
                <span class="bg-blue-50 text-blue-800 text-xs px-3 py-2 rounded-xl font-bold border border-blue-200 flex items-center shadow-2xs">
                    <span class="h-2 w-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                    Mode Online ({{ Auth::user()->name }})
                </span>
                @else
                <span class="bg-slate-100 text-slate-600 text-xs px-3 py-2 rounded-xl font-bold border border-slate-200 flex items-center shadow-2xs">
                    <span class="h-2 w-2 bg-slate-400 rounded-full mr-2"></span>
                    Mode Offline
                </span>
                @endauth
            </div>
        </div>
    </nav>
    

    <main class="max-w-7xl mx-auto px-6 py-10 flex-grow w-full flex flex-col justify-center">
        <div class="mb-6 flex justify-start">
            <a href="{{ route('dashboard.pengunjung') }}" class="inline-flex items-center bg-white hover:bg-slate-50 hover:border-slate-300 text-slate-600 hover:text-blue-600 text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-2xs border border-slate-200 active:scale-95 group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform text-slate-500 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Pilih Instansi Tujuan Anda</h2>
            <p class="text-slate-500 text-sm mt-2 font-medium">Temukan layanan publik yang Anda butuhkan dengan cepat</p>
        </div>

        <form action="{{ route('antrian.manual.index') }}" method="GET" class="max-w-xl mx-auto w-full mb-12 flex gap-3">
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-500">
                    <i class="fa-solid fa-magnifying-glass text-md"></i>
                </span>
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama instansi atau layanan (cth: BPJS, Polres)..." class="w-full bg-white text-slate-800 pl-11 pr-10 py-4 rounded-2xl border-2 border-slate-100 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all font-semibold text-sm shadow-sm">
                @if(!empty($search))
                    <a href="{{ route('antrian.manual.index') }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-circle-xmark text-lg"></i>
                    </a>
                @endif
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-7 py-4 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.97] transition-all flex items-center text-sm gap-2 cursor-pointer">
                Cari
            </button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @forelse($groupedLoket as $namaGrup => $lokets)
                @php 
                    $mainLoket = $lokets->first(); 
                    $hasMultiple = $lokets->count() > 1;
                    $groupData = $lokets->map(function($l) {
                        return [
                            'id_loket' => $l->id_loket,
                            'nama_loket' => $l->nama_loket,
                            'nama_pelayanan' => $l->nama_pelayanan ?: $l->nama_loket,
                            'jumlah_antrean' => $l->jumlah_antrean,
                            'status_pelayanan' => $l->status_pelayanan,
                            'prefix' => $l->prefix,
                            'logo' => $l->logo,
                            'gambar' => $l->gambar ?? null,
                        ];
                    })->toJson();
                @endphp

                <button @click="handleCardClick({{ $hasMultiple ? 'true' : 'false' }}, '{{ base64_encode($groupData) }}')" 
                        class="group bg-white rounded-3xl shadow-sm hover:shadow-xl border border-slate-100 hover:border-blue-500 transition-all duration-300 text-left cursor-pointer flex flex-col overflow-hidden relative">
                    
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>

                    <div class="p-6 flex flex-col h-full w-full relative z-10">
                        <div class="w-full flex items-start justify-between mb-4">
                            <div class="p-3 rounded-2xl bg-white shadow-xs border border-slate-100 w-16 h-16 flex items-center justify-center overflow-hidden group-hover:scale-110 transition-transform duration-300">
                                @if(!empty($mainLoket->logo) && file_exists(public_path('img/logo_loket/' . $mainLoket->logo)))
                                    <img src="{{ asset('img/logo_loket/' . $mainLoket->logo) }}" class="max-w-full max-h-full object-contain" alt="Logo {{ $mainLoket->nama_loket }}">
                                @elseif(!empty($mainLoket->gambar) && file_exists(public_path('img/logo_loket/' . $mainLoket->gambar)))
                                    <img src="{{ asset('img/logo_loket/' . $mainLoket->gambar) }}" class="max-w-full max-h-full object-contain" alt="Logo {{ $mainLoket->nama_loket }}">
                                @else
                                    <i class="fa-solid fa-building-government text-2xl text-slate-300 group-hover:text-blue-500"></i>
                                @endif
                            </div>
                            
                            @if($hasMultiple)
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
                            <h3 class="font-extrabold text-slate-800 text-lg leading-snug group-hover:text-blue-600 transition-colors line-clamp-2">{{ strtoupper($namaGrup) }}</h3>
                            @if(!$hasMultiple)
                                <p class="text-xs font-medium text-slate-400 mt-1 line-clamp-1">{{ $mainLoket->nama_pelayanan ?? 'Pelayanan Instansi Resmi' }}</p>
                            @else
                                <p class="text-xs font-medium text-slate-400 mt-1 line-clamp-1">Pilih untuk melihat layanan</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-slate-50/50 px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-semibold group-hover:bg-blue-50/30 transition-colors">
                        @if($hasMultiple)
                            <span class="text-slate-500 group-hover:text-blue-600">Lihat Opsi Layanan</span>
                            <div class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </div>
                        @else
                            @if($mainLoket->status_pelayanan === 'TUTUP')
                                <span class="text-red-500"><i class="fa-solid fa-ban mr-1"></i> Sedang Tutup</span>
                                <i class="fa-solid fa-lock text-slate-300"></i>
                            @else
                                <span>Total antrean: <strong class="text-slate-700 font-black">{{ $mainLoket->jumlah_antrean }}</strong></span>
                                <div class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </div>
                            @endif
                        @endif
                    </div>
                </button>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-slate-400">
                    <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-folder-open text-3xl text-slate-300"></i>
                    </div>
                    <p class="font-bold text-slate-500 text-lg">Belum ada loket layanan</p>
                    <p class="text-sm mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-center mt-4">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 bg-slate-800 hover:bg-slate-900 text-white font-bold px-8 py-4 rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all group cursor-pointer text-sm">
                <div class="bg-white/20 p-2.5 rounded-full group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-microphone text-sm"></i>
                </div>
                <span>Gunakan Perintah Suara (Voice Command)</span>
            </a>
        </div>
    </main>

    <!-- Modal Pilihan Group -->
    <div x-show="isGroupModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
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
                    <p class="text-sm font-medium text-slate-500 mt-1">Silakan pilih spesifikasi layanan</p>
                </div>
                <button @click="isGroupModalOpen = false" class="h-10 w-10 bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-500 rounded-full flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="overflow-y-auto pr-1 flex-grow space-y-3" style="max-height: 60vh;">
                <template x-for="item in groupLokets" :key="item.id_loket">
                    <div @click="isGroupModalOpen = false; setTimeout(() => bukaModal(item.id_loket, item.nama_pelayanan, item.jumlah_antrean, item.status_pelayanan), 150)"
                         class="group p-5 rounded-2xl border-2 border-slate-100 hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer transition-all flex items-center justify-between">
                        <div>
                            <h4 class="font-extrabold text-slate-800 group-hover:text-blue-700 text-base" x-text="item.nama_pelayanan || item.nama_loket"></h4>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="bg-slate-200 group-hover:bg-blue-200 text-slate-600 group-hover:text-blue-800 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider" x-text="'LOKET ' + (item.prefix || '0')"></span>
                                <span class="text-xs font-bold" :class="item.status_pelayanan === 'TUTUP' ? 'text-red-500' : 'text-slate-500 group-hover:text-blue-600'" x-text="item.status_pelayanan === 'TUTUP' ? 'Sedang Tutup' : 'Antrean: ' + item.jumlah_antrean"></span>
                            </div>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white border border-slate-200 shadow-sm group-hover:border-blue-500 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-all">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div @click.away="isModalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 border border-slate-100 text-center transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4">
            
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-50 text-blue-600 mb-5 border-4 border-white shadow-sm">
                <i class="fa-solid fa-ticket text-3xl"></i>
            </div>

            <h3 class="text-xl font-black text-slate-800 line-clamp-2" x-text="layananTerpilih"></h3>
            <p class="text-sm font-medium text-slate-400 mt-2">Cetak tiket antrean untuk layanan ini?</p>

            <div class="bg-slate-50 rounded-2xl p-5 my-6 border border-slate-200 shadow-inner">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Antrean Menunggu</span>
                <span class="text-4xl font-black text-blue-600 block" x-text="jumlahAntrean"></span>
                <span class="text-xs font-bold text-slate-500 mt-1 block">Orang</span>
            </div>

            <div class="flex flex-col space-y-3">
                <button @click="prosesAmbilAntrian(idLoketTerpilih, isModeKhusus); isModalOpen = false" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.98] transition-all cursor-pointer text-sm">
                    <i class="fa-solid fa-print mr-2"></i> Cetak Antrean
                </button>
                <button @click="isModalOpen = false" 
                        class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-2xl transition-all cursor-pointer text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Sukses -->
    <div x-show="isSuccessModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-lg"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
        
        <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 border border-slate-100 relative overflow-hidden transform transition-all flex flex-col items-center"
             x-transition:enter="transition ease-out duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
             x-transition:enter-start="opacity-0 scale-75 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="w-full flex flex-col items-center mb-5">
                <div class="h-20 w-20 bg-emerald-500 text-white rounded-full flex items-center justify-center text-4xl shadow-lg shadow-emerald-500/30 mb-4 scale-in">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Berhasil!</h3>
                <p class="text-sm font-medium text-slate-500 mt-1 text-center">Silakan ambil struk cetak Anda di bawah mesin.</p>
            </div>

            <div class="w-full bg-white border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center relative my-2 shadow-sm">
                <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-slate-100 border-r-2 border-dashed border-slate-200 rounded-full"></div>
                <div class="absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-slate-100 border-l-2 border-dashed border-slate-200 rounded-full"></div>
                
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block" x-text="successData.nama_loket"></span>
                <span class="text-sm text-slate-600 font-bold block mt-1 line-clamp-1" x-text="successData.nama_pelayanan"></span>
                
                <hr class="border-dashed border-slate-200 my-4">
                
                <span class="text-[10px] font-bold text-slate-400 block tracking-widest">NOMOR ANTRIAN</span>
                <span class="text-6xl font-black text-blue-600 tracking-tighter my-3 block drop-shadow-sm" x-text="successData.nomor_antrian"></span>
                
                <hr class="border-dashed border-slate-200 my-4">
                
                <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold">
                    <span class="bg-slate-100 px-2 py-1 rounded-md" x-text="'MODE: ' + (successData.mode ? successData.mode.toUpperCase() : '')"></span>
                    <span>{{ date('d M Y') }}</span>
                </div>
            </div>

            <div class="w-full flex gap-2 mt-3" data-html2canvas-ignore="true">
                <button type="button" onclick="cetakStrukKiosk(this)" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-3 px-3 rounded-xl border border-blue-200 transition text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-print"></i> Tes / Cetak Struk Fisik
                </button>
            </div>

            <button @click="isSuccessModalOpen = false; window.location.reload();" 
                    class="w-full mt-3 bg-slate-800 hover:bg-slate-900 text-white font-black py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all active:scale-[0.98] text-sm cursor-pointer flex items-center justify-center gap-2">
                Selesai <i class="fa-solid fa-rotate-right text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Modal QR Booking (Offline Mode) -->
    <div x-show="isQrModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div @click.away="isQrModalOpen = false" 
             class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 border border-slate-100 text-center transform transition-all relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-orange-400 to-red-500"></div>

            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-orange-50 text-orange-500 mb-5 border-4 border-white shadow-sm mt-2">
                <i class="fa-solid fa-door-closed text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800">Loket Tutup</h3>
            <p class="text-sm font-medium text-slate-500 mt-2 mb-6 leading-relaxed">Silakan scan QR Code di bawah menggunakan HP Anda untuk mem-booking jadwal hari esok.</p>
            
            <div class="bg-slate-50 p-4 rounded-3xl shadow-inner border border-slate-200 inline-block">
                <img :src="qrUrl" class="w-48 h-48 rounded-xl" alt="QR Code Booking">
            </div>

            <button @click="isQrModalOpen = false" 
                    class="w-full mt-8 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-2xl transition-all cursor-pointer text-sm">
                Tutup
            </button>
        </div>
    </div>

    <!-- Modal Upload Dokumen Khusus (Untuk Online) -->
    <div id="uploadKhususModal" class="hidden fixed inset-0 bg-slate-900/80 flex items-center justify-center z-[9999] backdrop-blur-md">
        <div class="bg-white p-6 rounded-2xl max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-xl font-bold text-slate-800 mb-2">Upload Bukti Prioritas</h3>
            <p class="text-sm text-slate-500 mb-4">Harap unggah dokumen pendukung (Surat Keterangan Dokter/Bidan, dsb) untuk mengaktifkan Mode Prioritas Sementara.</p>
            
            <form id="form-upload-khusus">
                <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Prioritas</label>
                <select id="jenis-prioritas-sementara" class="w-full bg-white border-2 border-slate-200 text-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 mb-4" required>
                    <option value="">-- Pilih Jenis Prioritas --</option>
                    <option value="ibu_hamil">Ibu Hamil</option>
                    <option value="lansia">Lansia</option>
                    <option value="disabilitas_sementara">Disabilitas Sementara</option>
                </select>
                
                <input type="file" id="file-prioritas" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-4" required>
                <div id="upload-status-msg" class="text-xs text-red-500 mb-4 hidden">Gagal mengunggah file.</div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="batalUploadKhusus()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" id="btn-submit-upload" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Upload & Aktifkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Input for Offline Priority Type -->
    <input type="hidden" id="hidden-jenis-prioritas-sementara" value="disabilitas_sementara">

    <footer class="text-center py-5 text-slate-400 text-[10px] font-bold tracking-wider border-t border-slate-200 bg-white/50 backdrop-blur-sm uppercase">
        &copy; 2026 Sistem Antrian Kiosk MPP Banjarbaru &bull; Divisi TI
    </footer>

    <style>
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

    <script>
        // Variables dari Laravel
        window.isPermanen = {{ isset($isPermanen) && $isPermanen ? 'true' : 'false' }};
        window.isLansia = {{ isset($isLansia) && $isLansia ? 'true' : 'false' }};

        function toggleModeKhusus(isSaatIniAktif) {
            if (isSaatIniAktif) {
                // Matikan
                window.dispatchEvent(new CustomEvent('set-mode-khusus', { detail: { status: false } }));
                document.getElementById('hidden-jenis-prioritas-sementara').value = 'disabilitas_sementara';
            } else {
                // Hidupkan
                @auth
                    if (window.isPermanen || window.isLansia) {
                        window.dispatchEvent(new CustomEvent('set-mode-khusus', { detail: { status: true } }));
                        return;
                    } else {
                        // Online (Login) tapi bukan permanen/lansia -> HARUS UPLOAD BUKTI
                        document.getElementById('uploadKhususModal').classList.remove('hidden');
                    }
                @else
                    // Offline (Guest/Kiosk Fisik) -> TIDAK PERLU UPLOAD BUKTI, Cukup Pilih Jenis
                    Swal.fire({
                        title: 'Jenis Prioritas',
                        text: 'Silakan pilih jenis layanan prioritas Anda:',
                        icon: 'question',
                        input: 'select',
                        inputOptions: {
                            'ibu_hamil': 'Ibu Hamil',
                            'lansia': 'Lansia',
                            'disabilitas_sementara': 'Disabilitas Sementara'
                        },
                        inputPlaceholder: '-- Pilih Jenis Prioritas --',
                        showCancelButton: true,
                        confirmButtonText: 'Aktifkan Mode Khusus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#3b82f6',
                        inputValidator: (value) => {
                            return new Promise((resolve) => {
                                if (value !== '') {
                                    resolve();
                                } else {
                                    resolve('Anda harus memilih jenis prioritas');
                                }
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('hidden-jenis-prioritas-sementara').value = result.value;
                            window.dispatchEvent(new CustomEvent('set-mode-khusus', { detail: { status: true } }));
                        }
                    });
                @endauth
            }
        }

        function batalUploadKhusus() {
            document.getElementById('uploadKhususModal').classList.add('hidden');
            document.getElementById('file-prioritas').value = "";
            document.getElementById('upload-status-msg').classList.add('hidden');
        }

        document.getElementById('form-upload-khusus').addEventListener('submit', async function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('file-prioritas');
            if(fileInput.files.length === 0) return;

            const btnSubmit = document.getElementById('btn-submit-upload');
            const msgError = document.getElementById('upload-status-msg');
            btnSubmit.disabled = true;
            btnSubmit.innerText = 'Mengunggah...';
            msgError.classList.add('hidden');

            const formData = new FormData();
            formData.append('dokumen_prioritas_sementara', fileInput.files[0]);
            
            try {
                const response = await fetch('/upload-dokumen-kiosk', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const result = await response.json();
                if(result.success) {
                    document.getElementById('hidden-jenis-prioritas-sementara').value = document.getElementById('jenis-prioritas-sementara').value;
                    window.dispatchEvent(new CustomEvent('set-mode-khusus', { detail: { status: true } }));
                    batalUploadKhusus();
                } else {
                    msgError.innerText = result.message || 'Gagal upload file';
                    msgError.classList.remove('hidden');
                }
            } catch (error) {
                msgError.innerText = 'Terjadi kesalahan jaringan';
                msgError.classList.remove('hidden');
            }
            btnSubmit.disabled = false;
            btnSubmit.innerText = 'Upload & Aktifkan';
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function prosesAmbilAntrian(idLoket, isModeKhusus) {
            let payload = {
                id_loket: idLoket
            };
            if (isModeKhusus) {
                payload.is_prioritas = 1;
                payload.jenis_prioritas = document.getElementById('hidden-jenis-prioritas-sementara').value;
            }

            $.ajax({
                url: "{{ route('antrian.manual.store') }}",
                type: "POST",
                data: payload,
                success: function(response) {
                    if(response.success) {
                        window.dispatchEvent(new CustomEvent('sukses-antrian', { 
                            detail: response.data 
                        }));
                    }
                },
                error: function(xhr) {
                    let res = xhr.responseJSON;
                    alert("Gagal memproses: " + (res ? res.message : "Terjadi masalah jaringan server."));
                }
            });
        }

        // ============================================================
        // MEKANISME KEGAGALAN SISTEM: PRINTER STRUK (REVISI PENGUJI)
        // ============================================================
        function cetakStrukKiosk(btn) {
            Swal.fire({
                title: 'Menghubungkan Printer...',
                text: 'Sedang memproses instruksi cetak ke printer fisik...',
                timer: 1200,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                // Di kiosk nyata: jika printer offline / paper out, catat log dan tampilkan fallback
                catatGagalPrinter();
            });
        }

        function catatGagalPrinter() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('/api/catat-kegagalan-sistem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    komponen: 'Printer / Struk Tiket',
                    pesan_error: 'Printer fisik offline / kehabisan kertas struk',
                    aksi_fallback: 'Tiket Ditampilkan di Layar Kiosk',
                    tingkat: 'Warning'
                })
            });

            Swal.fire({
                icon: 'warning',
                title: 'Printer Fisik Offline',
                html: `
                    <div style="text-align: left; font-size: 13px; color: #475569;">
                        <p class="mb-3 text-slate-600">Perangkat printer fisik sedang offline atau kehabisan kertas struk.</p>
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px; border-radius: 12px; color: #166534;">
                            <strong style="font-size: 14px;">Nomor Antrean Anda Tetap Sah!</strong><br>
                            Nomor antrean telah tercatat di server. Silakan foto atau catat nomor antrean Anda untuk ditunjukkan ke loket tujuan.
                        </div>
                    </div>
                `,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Saya Mengerti'
            });
        }
    </script>
</body>
</html>
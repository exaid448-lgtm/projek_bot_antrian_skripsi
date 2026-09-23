<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persyaratan Layanan - MPP</title>
    <script src="{{ asset('js/syarat_layanan.js') }}" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/navbar_pengunjung.css') }}">
    <link rel="stylesheet" href="{{ asset('css/syarat_layanan.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50">
    @include('layout.navbar_pengunjung')

    {{-- PERBAIKAN: Menggunakan kutip tunggal pada x-init dan pastikan variabel $data_syarat terisi --}}
    <div class="main-container" 
         x-data="syaratLayanan" 
         x-init='initData(@json($data_syarat))'>
        
        <header class="page-header">
            <div class="header-content relative z-10">
                <h1 class="text-4xl font-extrabold tracking-tight">Pusat Informasi Syarat</h1>
                <p class="mt-4 text-blue-100 max-w-2xl mx-auto">Temukan informasi dokumen dan alur prosedur pelayanan Mal Pelayanan Publik Kota Banjarbaru dengan mudah.</p>
                
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" x-model="search" placeholder="Ketik nama instansi (contoh: KTP atau Samsat)...">
                </div>
            </div>
            <div class="header-bg-decoration"></div>
        </header>

        <div class="content-wrapper"style="max-width: 1280px;">
            <div class="grid-syarat">
                <template x-for="item in instansi" :key="item.id_loket">
                    <div class="card-loket" 
                        x-show="item.nama_loket.toLowerCase().includes(search.toLowerCase())"
                        @click="openModal(item)"
                        style="cursor: pointer">
                        
                        <div class="card-icon">
                            <img :src="item.logo ? '{{ asset('img/logo_loket') }}/' + item.logo : '{{ asset('img/default_logo.png') }}'" :alt="item.nama_loket">
                        </div>
                        <div class="card-info">
                            <h3 x-text="item.nama_loket" class="font-bold"></h3>
                            <p x-text="item.nama_pelayanan || 'Informasi layanan publik'"></p>
                        </div>
                        <div class="card-footer">
                            <span>Detail Persyaratan</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- MODAL OVERLAY --}}
        <div class="modal-overlay" x-show="showModal" x-cloak x-transition.opacity>
            <div class="modal-content" @click.away="showModal = false"
                 x-show="showModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-12">
                
                <div class="modal-header">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-xl p-2 shadow-sm">
                            <img :src="selectedData.logo ? '{{ asset('img/logo_loket') }}/' + selectedData.logo : '{{ asset('img/default_logo.png') }}'" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h2 x-text="selectedData.nama_loket" class="text-xl font-bold text-gray-800"></h2>
                            <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded font-bold uppercase">Info Resmi</span>
                        </div>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <h4 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-4">Dokumen Persyaratan:</h4>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <ul class="space-y-2">
                                <template x-for="s in (selectedData.syarat || [])" :key="s.id_syarat">
                                    <li class="text-sm text-gray-700 flex items-start gap-3">
                                        <i class="fas fa-check-circle mt-1 text-green-500"></i> 
                                        <div>
                                            <span class="font-semibold" x-text="s.nama_syarat"></span>
                                            <p class="text-xs text-gray-500" x-text="s.keterangan"></p>
                                        </div>
                                    </li>
                                </template>
                                <template x-if="!selectedData.syarat || selectedData.syarat.length === 0">
                                    <li class="text-sm text-gray-400 italic">Belum ada data persyaratan untuk loket ini.</li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 text-center">Alur Layanan</h4>
                        <div class="flex justify-between items-center px-4">
                            <div class="text-center">
                                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-1 text-xs font-bold border border-blue-100">1</div>
                                <p class="text-[9px] text-gray-500 font-medium uppercase">Antrian</p>
                            </div>
                            <div class="flex-1 h-[1px] bg-gray-200 mb-4 mx-2"></div>
                            <div class="text-center">
                                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-1 text-xs font-bold border border-blue-100">2</div>
                                <p class="text-[9px] text-gray-500 font-medium uppercase">Proses</p>
                            </div>
                            <div class="flex-1 h-[1px] bg-gray-200 mb-4 mx-2"></div>
                            <div class="text-center">
                                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-1 text-xs font-bold border border-blue-100">3</div>
                                <p class="text-[9px] text-gray-500 font-medium uppercase">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
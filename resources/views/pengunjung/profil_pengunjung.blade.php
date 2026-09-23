<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="{{ asset('js/profil_pengunjung.js') }}"></script>
    <!-- Import Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Profil & Statistik Kehadiran</title>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body class="bg-[#f0f4f8] antialiased text-gray-800">
    @include('layout.navbar_pengunjung')

    <!-- Modal Upload Foto -->
    <div id="photoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" aria-hidden="true" id="modalOverlay"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full glass-effect">
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Ubah Foto Profil</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500" id="closeModalBtn">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('profil.update_foto') }}" method="POST" enctype="multipart/form-data" id="uploadPhotoForm">
                        @csrf
                        <div class="mt-6 flex flex-col items-center">
                            <!-- Preview Image -->
                            <div class="relative w-40 h-40 mb-6 group">
                                <img id="previewImage" 
                                     src="{{ $profil->foto ? asset('storage/'.$profil->foto) : 'https://ui-avatars.com/api/?name='.urlencode($profil->nama).'&background=0D8ABC&color=fff' }}" 
                                     class="w-full h-full rounded-full border-4 border-blue-50 object-cover shadow-sm transition-all duration-300">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-full transition-all duration-300"></div>
                            </div>
                            
                            <input type="file" name="foto" id="fileInput" class="hidden" accept="image/*">
                            
                            <button type="button" id="selectFileBtn" class="flex items-center gap-2 px-6 py-2.5 bg-blue-50 text-blue-700 font-semibold rounded-xl border border-blue-100 hover:bg-blue-100 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12 a2 2 0 002-2v-1M16 8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Pilih Foto Baru
                            </button>
                            <p class="mt-3 text-xs text-gray-400 italic">Format: JPG, PNG, WEBP (Max. 5MB)</p>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-5">
                            <button type="button" id="cancelBtn" class="px-5 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-xl transition-all duration-200">
                                Batal
                            </button>
                            <button type="submit" class="px-7 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-200 rounded-xl transition-all duration-200">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Prioritas -->
    <div id="disabilitasModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('disabilitasModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full glass-effect">
                <div class="px-6 pt-6 pb-4 bg-white">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900">Upload Bukti Prioritas</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('disabilitasModal').classList.add('hidden')">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('profil.upload_prioritas') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-4">Silakan pilih jenis pengajuan dan unggah dokumen pendukung (Kartu Penyandang Disabilitas, Surat Keterangan Dokter/Hamil, dll). Admin akan memvalidasi pengajuan Anda.</p>
                            
                            <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Prioritas</label>
                            <select name="jenis_prioritas" required class="w-full bg-white border-2 border-slate-200 text-slate-700 rounded-xl px-4 py-3 mb-4 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium appearance-none cursor-pointer">
                                <option value="" disabled selected>-- Pilih Jenis Prioritas --</option>
                                <option value="disabilitas_permanen">Disabilitas Permanen</option>
                                <option value="disabilitas_sementara">Disabilitas Sementara</option>
                                <option value="ibu_hamil">Ibu Hamil</option>
                            </select>

                            <label class="block text-sm font-bold text-slate-700 mb-2">Dokumen Pendukung</label>
                            <input type="file" name="dokumen_prioritas" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept=".pdf, .jpg, .jpeg, .png">
                            <p class="mt-2 text-xs text-gray-400">Format: JPG, PNG, PDF (Max. 2MB)</p>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                            <button type="button" onclick="document.getElementById('disabilitasModal').classList.add('hidden')" class="px-5 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-xl">Batal</button>
                            <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl">Kirim Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-xl shadow-sm animate-pulse">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="ml-3">
                    <ul class="list-disc text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Profil</h1>
            <p class="mt-2 text-sm text-gray-500">Kelola informasi pribadi Anda dan pantau kepadatan pengunjung MPP.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Profil Card -->
            <div class="lg:col-span-1 glass-effect rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 p-8 flex flex-col items-center">
                <div class="relative group cursor-pointer mt-2" id="changePhotoBtn">
                    <img class="w-36 h-36 rounded-full border-4 border-white shadow-md object-cover transition-transform duration-300 group-hover:scale-105" 
                         src="{{ $profil->foto ? asset('storage/'.$profil->foto) : 'https://ui-avatars.com/api/?name='.urlencode($profil->nama).'&background=0D8ABC&color=fff' }}" alt="Profile" id="profileImageDisplay">
                    <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="text-white text-xs font-semibold">Ubah Foto</span>
                    </div>
                </div>

                <div class="mt-6 text-center w-full">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $profil->nama }}</h2>
                    <p class="text-sm font-medium text-blue-600 mt-1 border-b border-gray-100 pb-4">Pengunjung MPP</p>
                    
                    <div class="mt-6 text-left space-y-4">
                        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100/50">
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-3">Informasi Pribadi</p>
                            <div class="flex justify-between items-center text-sm py-1.5 border-b border-gray-200/50">
                                <span class="text-gray-500">Tanggal Lahir</span>
                                <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($profil->tanggal_lahir)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm py-1.5">
                                <span class="text-gray-500">Gender</span>
                                <span class="font-medium text-gray-800">{{ $profil->jenis_kelamin }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm py-1.5">
                                <span class="text-gray-500">Status Prioritas</span>
                                @php
                                    $umur = \Carbon\Carbon::parse($profil->tanggal_lahir)->age;
                                    $isLansia = $umur >= 60;
                                    $isExpired = false;
                                    if ($profil->status_prioritas == 'disetujui' && $profil->jenis_prioritas != 'disabilitas_permanen' && $profil->tanggal_berakhir_prioritas) {
                                        $isExpired = \Carbon\Carbon::today()->gt(\Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas));
                                    }
                                @endphp

                                @if($isLansia)
                                    <span class="font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded text-xs border border-green-200">Lansia (Otomatis)</span>
                                @elseif($profil->status_prioritas == 'disetujui' && !$isExpired)
                                    <div class="flex flex-col items-end">
                                        <div class="flex items-center gap-1">
                                            @if($profil->jenis_prioritas != 'disabilitas_permanen' && $profil->tanggal_berakhir_prioritas && \Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas)->diffInDays(now()) <= 7 && \Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas)->gt(now()))
                                                <i class="fa-solid fa-bell text-red-500 animate-bounce" title="Masa berlaku hampir habis!"></i>
                                            @endif
                                            <span class="font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded text-xs border border-green-200 capitalize">
                                                {{ str_replace('_', ' ', $profil->jenis_prioritas) }}
                                            </span>
                                        </div>
                                        @if($profil->jenis_prioritas != 'disabilitas_permanen' && $profil->tanggal_berakhir_prioritas)
                                            <span class="text-[10px] text-gray-500 mt-1">Berlaku s/d: {{ \Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas)->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                @elseif($profil->status_prioritas == 'menunggu_validasi')
                                    <span class="font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded text-xs border border-amber-200">Menunggu Validasi</span>
                                @elseif($profil->status_prioritas == 'ditolak')
                                    <div class="flex flex-col items-end">
                                        <span class="font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded text-xs border border-red-200 mb-1">Ditolak</span>
                                        @if($profil->alasan_penolakan_prioritas)
                                            <span class="text-[10px] text-red-400 max-w-[150px] text-right mb-1">Alasan: {{ $profil->alasan_penolakan_prioritas }}</span>
                                        @endif
                                        <button onclick="document.getElementById('disabilitasModal').classList.remove('hidden')" class="text-[10px] text-blue-600 hover:underline">Upload Ulang Bukti</button>
                                    </div>
                                @elseif($profil->status_prioritas == 'disetujui' && $isExpired)
                                    <div class="flex flex-col items-end">
                                        <span class="font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded text-xs border border-red-200 mb-1">Kadaluarsa</span>
                                        <span class="text-[10px] text-gray-400 mb-1">Berlaku s/d: {{ \Carbon\Carbon::parse($profil->tanggal_berakhir_prioritas)->format('d M Y') }}</span>
                                        <button onclick="document.getElementById('disabilitasModal').classList.remove('hidden')" class="text-[10px] text-blue-600 hover:underline">Upload Ulang Bukti</button>
                                    </div>
                                @else
                                    <div class="flex flex-col items-end">
                                        <span class="font-medium text-gray-800">Tidak Ada</span>
                                        <button onclick="document.getElementById('disabilitasModal').classList.remove('hidden')" class="text-[10px] text-blue-600 hover:underline">Ajukan Bukti Prioritas</button>
                                    </div>
                                @endif
                            </div>
                        </div>



                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">Kontak pengunjung</p>
                            <div class="flex justify-between items-center text-sm py-1.5 border-b border-gray-200/50">
                                <span class="text-gray-500">No. Telp</span>
                                <span class="font-medium text-gray-800">{{ $profil->nomor_whatsapp }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm py-1.5 border-b border-gray-200/50">
                                <span class="text-gray-500">Email</span>
                                <span class="font-medium text-gray-800">{{ $profil->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kepadatan Pengunjung Chart -->
            <div class="lg:col-span-2 glass-effect rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 p-8 flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            Waktu & Kepadatan Pengunjung
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Estimasi jam tersibuk per loket dan jumlah rata-rata kunjungannya.</p>
                    </div>
                    <div class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full text-xs font-semibold border border-blue-200 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Hari Ini (Realtime Mock)
                    </div>
                </div>
                
                <div class="flex-grow w-full relative min-h-[350px]">
                    <canvas id="densityChart"
                        data-labels="{{ json_encode($kepadatanData->pluck('nama_loket')) }}"
                        data-values="{{ json_encode($kepadatanData->pluck('total')) }}">
                    </canvas>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 mt-8">
            <!-- Riwayat Loket Chart -->
            <div class="glass-effect rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 p-8 flex flex-col lg:flex-row border border-gray-100">
                <div class="w-full lg:w-1/3 flex flex-col justify-center pr-0 lg:pr-8">
                    <h3 class="text-xl font-bold text-gray-800">
                        Loket Sering Dikunjungi
                    </h3>
                    <p class="text-sm text-gray-500 mt-2 mb-6">Persentase loket yang paling sering Anda ambil antriannya selama menggunakan layanan MPP.</p>
                    
                    <div class="space-y-4">
                        @php
                            $totalKunjungan = $seringDikunjungi->sum('total');
                            $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-cyan-400', 'bg-gray-200'];
                        @endphp

                        @forelse($seringDikunjungi as $index => $item)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full {{ $colors[$index] ?? 'bg-gray-400' }} shadow-sm"></span>
                                    {{-- Gunakan {{ $item->nama_loket }} pastikan nama kolom di DB sama --}}
                                    <span class="text-gray-700 font-medium">{{ $item->nama_loket ?? 'Nama Loket Tidak Ada' }}</span>
                                </div>
                                <span class="font-bold text-gray-900">
                                    {{ $totalKunjungan > 0 ? round(($item->total / $totalKunjungan) * 100) : 0 }}%
                                </span>
                            </div>
                        @empty
                            {{-- Jika ini muncul, berarti variabel $seringDikunjungi memang kosong dari Controller --}}
                            <p class="text-xs text-gray-400 italic">Belum ada riwayat kunjungan.</p>
                        @endforelse
                    </div>
                </div>
                
                <div class="w-full lg:w-2/3 flex justify-center items-center mt-10 lg:mt-0 min-h-[300px]">
                    <div style="width: 100%; max-width: 350px;">
                        <canvas id="loketChart"
                            data-labels="{{ json_encode($seringDikunjungi->pluck('nama_loket')) }}"
                            data-values="{{ json_encode($seringDikunjungi->pluck('total')) }}">
                        </canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
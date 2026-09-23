<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Online – Mal Pelayanan Publik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/chat_konsultasi.js') }}" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/chat_konsultasi.css') }}">
</head>
<body class="h-screen overflow-hidden bg-slate-900">

<div class="h-full w-full flex">

    <aside class="sidebar-bg w-80 shrink-0 flex flex-col border-r border-white/5">
        <div class="px-5 py-5 border-b border-white/8 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white">
                        <path fill-rule="evenodd" d="M10.415 1.966a3.25 3.25 0 0 1 3.17 0l5.25 2.766a3.25 3.25 0 0 1 1.665 2.825v1.23a1.75 1.75 0 0 1-1.75 1.75h-13.5A1.75 1.75 0 0 1 3.5 8.788v-1.23a3.25 3.25 0 0 1 1.665-2.825l5.25-2.766ZM4.25 12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75v5.25h1.5V12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75v5.25h1.5V12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75v5.25h.75a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1 0-1.5h.75V12Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white tracking-wide leading-tight">Mal Pelayanan Publik</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Kota Banjarbaru</p>
                </div>
            </div>
        </div>

        <div class="px-4 py-3 shrink-0">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" placeholder="Cari instansi..."
                     id="search-instansi" class="w-full bg-white/8 border border-white/10 text-black placeholder-slate-500 text-xs pl-9 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-indigo-500/60 focus:bg-white/12 transition-all">
            </div>
        </div>

        <div class="px-5 mb-2 shrink-0">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Percakapan Aktif</span>
        </div>

        <div id="instansi-list" class="flex-1 overflow-y-auto px-2 pb-4 space-y-0.5">
            @foreach($daftar_instansi as $item)
                @php
                    $isAktif = $instansi_aktif && $instansi_aktif->id_loket == $item->id_loket;
                    $isOpen = isset($item->status_pelayanan) && strtolower($item->status_pelayanan) === 'buka';
                @endphp
                <a href="{{ route('chatkonsultasi.index', ['id_loket' => $item->id_loket]) }}" 
                    data-nama="{{ strtolower($item->nama_loket ?? $item->nama_instansi) }}"
                    class="chat-item instansi-item {{ $isAktif ? 'chat-item-active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl transition-all cursor-pointer group">
                    <div class="relative shrink-0">
                        
                         <!-- bg-gradient-to-br from-indigo-400 -->
                        <div class="w-12 h-12 rounded-xl bg-white to-blue-500 flex items-center justify-center text-white font-black text-xs shadow-md uppercase">
                            <!-- {{ substr($item->nama_loket ?? $item->nama_instansi, 0, 4) }} -->
                            <img src="{{ asset('img/logo_loket/' . $item->logo) }}" class="w-9 h-9 max-w-full max-h-full object-contain" alt="Logo {{ $item->nama_loket }}">
                            </div>
                        @if($isOpen)
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-slate-900 badge-pulse"></span>
                        @else
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-slate-600 rounded-full border-2 border-slate-900"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm {{ $isAktif ? 'font-semibold text-white' : 'font-medium text-slate-300' }} truncate">
                                {{ $item->nama_loket ?? $item->nama_instansi }}
                            </h4>
                            @if($item->pesanTerakhir)
                                <span class="text-[10px] text-slate-500 shrink-0 ml-2">
                                    {{ $item->pesanTerakhir->created_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        @if(isset($item->nama_pelayanan) && $item->nama_pelayanan)
                            <p class="text-[10px] font-bold text-indigo-400 truncate mt-0.5">{{ $item->nama_pelayanan }}</p>
                        @endif
                        <p class="text-xs text-slate-400 truncate mt-0.5">
                            @if($item->pesanTerakhir)
                                @if($item->pesanTerakhir->pesan)
                                    {{ $item->pesanTerakhir->pesan }}
                                @else
                                    <i class="fa-solid fa-paperclip text-[10px] mr-1"></i> Lampiran berkas
                                @endif
                            @else
                                Belum ada percakapan.
                            @endif
                        </p>
                    </div>
                    @if($item->unread_count > 0)
                        <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-[9px] text-white font-bold">{{ $item->unread_count }}</span>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="px-4 py-4 border-t border-white/8 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-blue-500 flex items-center justify-center text-white font-bold text-xs shrink-0 uppercase">
                    {{ substr($user_pengunjung->nama ?? 'PG', 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ $user_pengunjung->nama ?? 'Pengunjung' }}</p>
                    <p class="text-[10px] text-emerald-400 font-medium flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        Online
                    </p>
                </div>
                <a href="{{ route('dashboard.pengunjung') }}" class="text-slate-500 hover:text-indigo-400 transition p-1.5 rounded-lg hover:bg-white/8" title="Kembali ke Dashboard">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                </a>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden bg-white">

            @if($instansi_aktif)
                @php
                    $loketBuka = isset($instansi_aktif->status_pelayanan) && strtolower($instansi_aktif->status_pelayanan) === 'buka';
                @endphp
            <div class="chat-header border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between shrink-0 z-10 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="relative">
                        <!-- bg-blue-500 -->
                        <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-white font-black text-xs shadow-md uppercase">
                            <!-- {{ substr($instansi_aktif->nama_loket ?? $instansi_aktif->nama_instansi, 0, 4) }} -->
                            <img src="{{ asset('img/logo_loket/' . $instansi_aktif->logo) }}" class="w-9 h-9 max-w-full max-h-full object-contain" alt="Logo {{ $instansi_aktif->nama_loket }}">
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 {{ $loketBuka ? 'bg-emerald-400' : 'bg-rose-500' }} rounded-full border-2 border-white {{ $loketBuka ? 'badge-pulse' : '' }}"></span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">{{ $instansi_aktif->nama_loket ?? $instansi_aktif->nama_instansi }}</h2>
                        @if(isset($instansi_aktif->nama_pelayanan) && $instansi_aktif->nama_pelayanan)
                            <p class="text-xs font-semibold text-indigo-600 mt-0.5">{{ $instansi_aktif->nama_pelayanan }}</p>
                        @endif
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 {{ $loketBuka ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full"></span>
                            <span class="text-[11px] {{ $loketBuka ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                                {{ $loketBuka ? 'Konsultasi Loket Online Dibuka' : 'Konsultasi Loket Tutup' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 chat-bg space-y-5 bg-slate-50">

                <div class="flex items-center gap-3 my-1">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-[11px] text-slate-400 font-semibold bg-white px-3 py-1 rounded-full shadow-sm border border-slate-100">
                        Awal Percakapan
                    </span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <div class="flex justify-center">
                    <div class="bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-medium px-4 py-2 rounded-full shadow-sm text-center max-w-sm">
                        🏥 Anda terhubung dengan petugas <strong>{{ $instansi_aktif->nama_loket ?? $instansi_aktif->nama_instansi }}</strong>. Silakan sampaikan pertanyaan Anda.
                    </div>
                </div>

                @foreach($riwayat_chat as $pesan)
                    @if($pesan->tipe_pengirim === 'pengunjung')
                        <div class="flex justify-end items-stretch bubble-right group relative gap-2">
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center shrink-0">
                                <div class="relative inline-block text-left">
                                    <button onclick="toggleDropdown('dropdown-{{ $pesan->id_chat }}', event)" class="w-7 h-7 bg-white rounded-full border border-slate-200 text-slate-500 hover:text-slate-700 shadow-sm flex items-center justify-center focus:outline-none">
                                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                    </button>
                                    
                                    <div id="dropdown-{{ $pesan->id_chat }}" class="hidden absolute right-full top-0 mr-2 w-28 bg-white border border-slate-200 rounded-lg shadow-xl z-30">
                                        <div class="py-1">
                                            <button type="button" onclick="openEditModal('{{ $pesan->id_chat }}', '{{ addslashes($pesan->pesan) }}')" class="w-full text-left px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                                                <i class="fa-solid fa-pen text-slate-400"></i> Edit
                                            </button>
                                            
                                            @php $deleteUrl = route('chatkonsultasi.hapus', $pesan->id_chat); @endphp
                                            <button type="button" onclick="openDeleteModal('{{ $deleteUrl }}')" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                <i class="fa-solid fa-trash text-red-400"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="max-w-sm lg:max-w-md">
                                <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow-md shadow-indigo-200/50 space-y-2">
                                    @if($pesan->file_lampiran)
                                        @php
                                            $ext = strtolower(pathinfo($pesan->file_lampiran, PATHINFO_EXTENSION));
                                            $fileUrl = Storage::url($pesan->file_lampiran);
                                            $previewType = $ext == 'pdf' ? 'pdf' : 'other';
                                        @endphp

                                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                             <div class="rounded-xl overflow-hidden border border-white/20 bg-white/5">
                                                 <a href="javascript:void(0)" onclick="openPreviewFile('{{ $fileUrl }}', 'image')">
                                                     <img src="{{ $fileUrl }}" alt="Lampiran" class="max-w-full h-auto object-cover hover:scale-105 transition duration-200" style="cursor: zoom-in;">
                                                 </a>
                                             </div>
                                         @else
                                             <div class="flex items-center gap-2">
                                                 <a href="javascript:void(0)" onclick="openPreviewFile('{{ $fileUrl }}', '{{ $previewType }}')" class="flex-1 flex items-center gap-3 bg-white/15 backdrop-blur-sm px-3 py-2.5 rounded-xl border border-white/20 hover:bg-white/20 transition">
                                                     <div class="w-9 h-9 {{ $ext == 'pdf' ? 'bg-red-500' : 'bg-blue-600' }} rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                                         <i class="fa-solid {{ $ext == 'pdf' ? 'fa-file-pdf' : 'fa-file-word' }} text-white text-sm"></i>
                                                     </div>
                                                     <div class="flex-1 min-w-0 text-left">
                                                         <p class="text-xs font-semibold truncate">{{ basename($pesan->file_lampiran) }}</p>
                                                         <p class="text-[10px] text-blue-200 mt-0.5">Klik untuk lihat berkas ({{ $pesan->ukuran_file ?? '?' }})</p>
                                                     </div>
                                                 </a>
                                                 <a href="{{ $fileUrl }}" target="_blank" class="w-9 h-9 bg-white/15 hover:bg-white/20 rounded-xl flex items-center justify-center border border-white/20 text-white shrink-0 transition" title="Unduh Berkas">
                                                     <i class="fa-solid fa-download text-xs"></i>
                                                 </a>
                                             </div>
                                         @endif
                                    @endif

                                    @if($pesan->pesan)
                                        <p class="text-sm leading-relaxed">{{ $pesan->pesan }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-end gap-1.5 mt-1 mr-1">
                                    <span class="text-[10px] text-slate-400">{{ $pesan->created_at->format('H:i') }}</span>
                                    <i class="fa-solid fa-check-double text-[10px] {{ $pesan->status_baca ? 'text-indigo-500' : 'text-slate-300' }}"></i>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start gap-2.5 bubble-left">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-[10px] shrink-0 mt-auto shadow-sm uppercase">
                                {{ substr($instansi_aktif->nama_loket ?? $instansi_aktif->nama_instansi, 0, 2) }}
                            </div>
                            <div class="max-w-sm lg:max-w-md">
                                <div class="bg-white text-slate-700 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm border border-slate-100/80 space-y-2">
                                    @if($pesan->file_lampiran)
                                        @php
                                            $ext = strtolower(pathinfo($pesan->file_lampiran, PATHINFO_EXTENSION));
                                            $fileUrl = Storage::url($pesan->file_lampiran);
                                            $previewType = $ext == 'pdf' ? 'pdf' : 'other';
                                        @endphp

                                         @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                             <div class="rounded-xl overflow-hidden border border-slate-200/40 bg-slate-50 max-w-[250px]">
                                                 <a href="javascript:void(0)" onclick="openPreviewFile('{{ $fileUrl }}', 'image')">
                                                     <img src="{{ $fileUrl }}" alt="Lampiran" class="max-w-full h-auto object-cover hover:scale-105 transition duration-200" style="cursor: zoom-in;">
                                                 </a>
                                             </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                 <a href="javascript:void(0)" onclick="openPreviewFile('{{ $fileUrl }}', '{{ $previewType }}')" class="flex-1 flex items-center gap-3 bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition">
                                                     <div class="w-9 h-9 {{ $ext == 'pdf' ? 'bg-red-500' : 'bg-blue-600' }} rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                                         <i class="fa-solid {{ $ext == 'pdf' ? 'fa-file-pdf' : 'fa-file-word' }} text-white text-sm"></i>
                                                     </div>
                                                     <div class="flex-1 min-w-0 text-left">
                                                         <p class="text-xs font-semibold truncate text-slate-800">{{ basename($pesan->file_lampiran) }}</p>
                                                         <p class="text-[10px] text-slate-400 mt-0.5">Klik untuk lihat berkas ({{ $pesan->ukuran_file ?? '?' }})</p>
                                                     </div>
                                                 </a>
                                                 <a href="{{ $fileUrl }}" target="_blank" class="w-9 h-9 bg-slate-50 hover:bg-slate-100 rounded-xl flex items-center justify-center border border-slate-200 text-slate-500 shrink-0 transition" title="Unduh Berkas">
                                                     <i class="fa-solid fa-download text-xs"></i>
                                                 </a>
                                             </div>
                                        @endif
                                    @endif

                                    @if($pesan->pesan)
                                        <p class="text-sm leading-relaxed">{{ $pesan->pesan }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 mr-1">
                                    <span class="text-[10px] text-slate-400">{{ $pesan->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="flex justify-start gap-2.5 hidden" id="typing-indicator">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-[10px] shrink-0 shadow-sm uppercase">
                        {{ substr($instansi_aktif->nama_loket ?? $instansi_aktif->nama_instansi, 0, 2) }}
                    </div>
                    <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm border border-slate-100/80 flex items-center gap-1.5">
                        <span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span>
                        <span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span>
                        <span class="typing-dot w-2 h-2 bg-slate-400 rounded-full inline-block"></span>
                    </div>
                </div>

            </div>

            <div class="chat-header border-t border-slate-200/80 px-5 py-3.5 shrink-0 bg-white">
                <div id="file-preview" class="hidden flex items-center gap-2.5 mb-3 p-2.5 bg-indigo-50 border border-indigo-100 rounded-xl">
                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file text-white text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 truncate" id="file-name">nama_file.pdf</p>
                        <p class="text-[10px] text-slate-400">Siap dikirim</p>
                    </div>
                    <button type="button" onclick="clearFile()" class="text-slate-400 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <form id="chat-form" action="{{ route('chatkonsultasi.kirim', ['id_loket' => $instansi_aktif->id_loket]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center gap-2.5">
                        <label class="w-9 h-9 cursor-pointer flex items-center justify-center bg-slate-100 rounded-xl text-slate-500 hover:bg-slate-200/80 transition shadow-sm" title="Lampirkan Berkas">
                            <input type="file" id="file-input" name="file_upload" 
                                accept="image/jpeg,image/png,image/gif,application/pdf,.docx"
                                class="hidden" onchange="handleFileSelect(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.924a3 3 0 1 1 4.243 4.243L8.904 18.828a1.5 1.5 0 0 1-2.122-2.122l7.926-7.925" />
                            </svg>
                        </label>

                        <div class="flex-1 relative">
                            <input type="text" id="msg-input" name="pesan" autoComplete="off"
                                   placeholder="Ketik pesan konsultasi Anda..."
                                   class="msg-input w-full px-5 py-2.5 bg-slate-100 border border-transparent rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-300 transition-all">
                        </div>

                        <button type="submit" id="send-btn"
                                class="send-btn w-10 h-10 bg-indigo-600 rounded-xl text-white shadow-lg shadow-indigo-300/40 hover:shadow-indigo-400/50 hover:bg-indigo-700 transition-all active:scale-95 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <p class="text-[10px] text-slate-400 mt-2 text-center">
                    <i class="fa-solid fa-lock text-[9px] mr-1 text-slate-300"></i>
                    Pesan terenkripsi · Didukung: JPG, PNG, PDF, DOCX (maks. 5MB)
                </p>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center bg-slate-50 text-slate-500">
                <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4 shadow-md">
                    <i class="fa-solid fa-comments text-2xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700">Selamat Datang di Online Konsultasi</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm text-center">Silakan pilih salah satu instansi aktif di sidebar kiri untuk memulai sesi tanya jawab dengan petugas pelayanan.</p>
            </div>
        @endif

    </main>
</div>

<div id="edit-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Ubah Pesan</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <textarea id="edit-textarea" name="pesan" rows="4" class="w-full text-sm text-slate-800 p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 text-xs">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition shadow-sm shadow-indigo-200">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="delete-modal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full overflow-hidden border border-slate-100 transform scale-95 transition-all duration-200" id="delete-modal-card">
        <div class="p-6 text-center">
            <div class="w-12 h-12 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100 text-rose-500">
                <i class="fa-solid fa-trash-can text-lg"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Hapus Pesan?</h3>
            <p class="text-xs text-slate-500 leading-relaxed px-2">Tindakan ini tidak bisa dibatalkan. Pesan dan berkas lampiran yang terikat akan dihapus secara permanen.</p>
        </div>
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2 text-xs border-t border-slate-100">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold transition shadow-sm shadow-rose-200">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
<script>
    function openPreviewFile(fileUrl, fileType) {
        if (fileType === 'image' || fileType === 'pdf') {
            const previewWindow = window.open('', '_blank');
            if (previewWindow) {
                let contentHtml = '';
                if (fileType === 'image') {
                    contentHtml = '<img src="' + fileUrl + '" style="max-width: 90%; max-height: 80vh; object-fit: contain; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55); margin-bottom: 25px;">';
                } else {
                    contentHtml = '<iframe src="' + fileUrl + '" style="width: 90%; height: 75vh; border-radius: 12px; border: none; background: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55); margin-bottom: 25px;" frameborder="0"></iframe>';
                }
                
                previewWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="id">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Pratinjau Lampiran</title>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
                        <style>
                            body {
                                margin: 0;
                                padding: 20px;
                                background-color: #0f172a;
                                font-family: 'Outfit', sans-serif;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                min-height: 100vh;
                                color: #ffffff;
                                overflow: hidden;
                                box-sizing: border-box;
                            }
                            .btn-close {
                                padding: 12px 28px;
                                background-color: #ef4444;
                                color: white;
                                border: none;
                                border-radius: 12px;
                                font-size: 14px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
                            }
                            .btn-close:hover {
                                background-color: #dc2626;
                                transform: translateY(-1px);
                            }
                            .btn-close:active {
                                transform: scale(0.95);
                            }
                        </style>
                    </head>
                    <body>
                ` + contentHtml + `
                        <div>
                            <button onclick="window.close()" class="btn-close">
                                <i class="fa-solid fa-xmark"></i> Tutup Halaman
                            </button>
                        </div>
                    </body>
                    </html>
                `);
                previewWindow.document.close();
                return;
            }
        }
        // Fallback jika window.open diblokir atau tipe file lain
        window.open(fileUrl, '_blank');
    }

    // Close modal on Escape key
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                closeDeleteModal();
            }
        });
    });
</script>

</body>
</html>
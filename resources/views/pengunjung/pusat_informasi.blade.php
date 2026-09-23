<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Informasi & Gangguan - MPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/navbar_pengunjung.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pusat_informasi.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @include('layout.navbar_pengunjung')
    
    <div class="main-content">
        <div class="container-info" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
            
            <div class="header-info" style="margin-bottom: 30px;">
                <h1 class="page-title text-2xl font-bold text-white">Pusat Informasi & Status Layanan</h1>
                <p class="page-subtitle text-gray-400">Pemberitahuan terkini mengenai kendala teknis dan pengumuman operasional loket.</p>
            </div>

            {{-- Wrapper Flexbox agar Sidebar dan Konten berdampingan --}}
            <div class="flex-container" style="display: flex; gap: 25px; align-items: flex-start;">
                
                {{-- Sisi Kiri: Sidebar --}}
                <div style="flex: 0 0 280px;">
                    @include('layout.sidebar_info')
                </div>

                {{-- Sisi Kanan: Daftar Informasi --}}
                <div class="info-main-content" style="flex: 1;">
                    @forelse($informasi as $info)
                        <div class="info-item {{ $info->kategori_info }}" style="margin-bottom: 20px; border-radius: 12px; overflow: hidden; background: #2d3748;">
                            <div class="info-accent"></div>
                            <div class="info-body" style="padding: 20px;">
                                <div class="info-top" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span class="instansi-label text-xs font-bold text-blue-400 uppercase">
                                        <i class="fas fa-building"></i> 
                                        {{ $info->loket->nama_loket ?? 'Instansi Umum' }}
                                    </span>
                                    <span class="info-date text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($info->tanggal_info)->format('d M Y') }}
                                    </span>
                                </div>
                                <h3 class="info-heading text-lg font-semibold text-white mb-2">{{ $info->judul_info }}</h3>
                                <p class="info-desc text-gray-300 text-sm leading-relaxed">{{ $info->deskripsi_info }}</p>
                                
                                @if($info->solusi_info)
                                <div class="info-footer" style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #4a5568;">
                                    <span class="solusi-tag font-bold text-red-400 text-sm">Solusi:</span>
                                    <p class="text-sm text-gray-300 italic">{{ $info->solusi_info }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 50px; background: #2d3748; border-radius: 12px;">
                            <p class="text-white italic">Tidak ada informasi untuk layanan ini.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</body>
</html>
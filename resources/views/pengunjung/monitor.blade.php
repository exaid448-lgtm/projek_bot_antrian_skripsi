<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrian - MPP Banjarbaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            color: white;
            min-height: 100vh;
            overflow: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .calling-card {
            animation: pulse-glow 2s infinite alternate;
            border-color: rgba(59, 130, 246, 0.5);
            background: rgba(59, 130, 246, 0.15);
        }

        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0 15px rgba(59, 130, 246, 0.3), 0 0 30px rgba(59, 130, 246, 0.1);
                transform: scale(1);
            }
            100% {
                box-shadow: 0 0 25px rgba(59, 130, 246, 0.6), 0 0 50px rgba(59, 130, 246, 0.3);
                transform: scale(1.01);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .number-glow {
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }

        .marquee-container {
            background: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="flex flex-col h-screen">
    @include('layout.navbar_pengunjung')
    {{-- Navbar Hidden on Monitor View usually, but we keep it or style it --}}
    {{-- We'll create a custom header instead for that "Terminal" feel --}}

    {{-- Header --}}
    <header class="glass p-6 px-10 flex justify-between items-center z-10">
        <div class="flex items-center gap-6">
            <div class="bg-white p-2 rounded-2xl shadow-lg">
                <img src="{{ asset('img/super_admin_logo/logo.jpeg') }}" class="h-16 rounded-lg" alt="Logo">
            </div>
            <div>
                <h1 class="text-4xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300 uppercase">
                    Antrian MPP
                </h1>
                <p class="text-blue-200/60 font-medium tracking-widest text-sm uppercase">Mal Pelayanan Publik Kota Banjarbaru</p>
            </div>
        </div>
        
        <div class="flex items-center gap-8">
            <div class="text-right">
                <div id="clock" class="text-5xl font-bold tracking-tighter text-white tabular-nums">00:00:00</div>
                <div class="text-blue-300/80 font-semibold uppercase tracking-widest text-sm">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 p-10 overflow-hidden relative">
        {{-- Background Decorative Elements --}}
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-[120px] -z-10"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] -z-10"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 h-full min-h-0 pb-4">
            @forelse($antrianDipanggil as $index => $antrean)
                <div class="glass-card rounded-[2.5rem] flex flex-col overflow-hidden animate-slide-up calling-card" 
                     style="animation-delay: {{ $index * 0.1 }}s">
                    
                    {{-- Counter Name --}}
                    <div class="bg-blue-600/40 px-6 py-4 border-b border-white/10">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-blue-300 uppercase tracking-widest">{{ $antrean->loket->lokasi_loket }}</span>
                            <span class="flex h-2 w-2 rounded-full bg-blue-400 animate-ping"></span>
                        </div>
                        <h2 class="text-2xl font-black uppercase text-white truncate mt-1">
                            {{ $antrean->loket->nama_loket }}
                        </h2>
                        <p class="text-xs font-semibold text-blue-200 mt-1 truncate">
                            {{ $antrean->loket->nama_pelayanan ?? 'Pelayanan Umum' }}
                        </p>
                    </div>

                    {{-- Queue Number --}}
                    <div class="flex-1 flex flex-col justify-center items-center relative py-6 min-h-0 overflow-hidden">
                        <div class="absolute text-[180px] font-black text-white/5 pointer-events-none select-none leading-none">
                            {{ substr($antrean->nomor_antrian, 0, 1) }}
                        </div>
                        <span class="text-sm font-bold text-blue-400 uppercase tracking-[0.4em] mb-4">Nomor Antrian</span>
                        <h3 class="text-9xl font-black leading-none text-white tracking-tighter number-glow">
                            {{ $antrean->nomor_antrian }}
                        </h3>
                    </div>

                    {{-- Footer Action --}}
                    <div class="bg-white/5 p-4 mt-auto text-center border-t border-white/5 shrink-0">
                        <p class="text-blue-400 font-extrabold uppercase tracking-widest flex items-center justify-center gap-3">
                            <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            Silakan Menuju Loket
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col justify-center items-center text-white/20">
                    <div class="relative mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v1m6 0H9" />
                        </svg>
                        <div class="absolute inset-0 bg-blue-500/20 blur-[60px] -z-10 rounded-full"></div>
                    </div>
                    <h2 class="text-4xl font-black uppercase tracking-widest mb-2">Belum Ada Panggilan</h2>
                    <p class="text-lg font-medium opacity-50">Menunggu antrian selanjutnya...</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- Footer Ticker --}}
    <footer class="marquee-container py-4">
        <div class="flex items-center overflow-hidden">
            <div class="bg-blue-600 px-6 py-1 text-sm font-black uppercase tracking-widest whitespace-nowrap z-10 shadow-[20px_0_40px_rgba(0,0,0,0.5)]">
                INFO TERKINI
            </div>
            <marquee behavior="scroll" direction="left" scrollamount="8" class="text-xl font-semibold uppercase tracking-wide text-blue-100">
                &nbsp;&nbsp;&nbsp;&nbsp; Selamat Datang di Mal Pelayanan Publik Kota Banjarbaru • Budayakan Antri Untuk Kenyamanan Bersama • Tetap Jaga Protokol Kesehatan &nbsp;&nbsp;&nbsp;&nbsp; • &nbsp;&nbsp;&nbsp;&nbsp; Pelayanan Kami Adalah Prioritas Utama &nbsp;&nbsp;&nbsp;&nbsp;
            </marquee>
        </div>
    </footer>

    <script>
        // Update Jam Realtime
        function updateClock() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

    </script>
</body>

</html>

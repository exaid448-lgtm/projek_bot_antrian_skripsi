<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        premium: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            primary: '#2563eb',
                            secondary: '#4f46e5',
                            accent: '#0ea5e9',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/catatan_konsul.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Arsip Konsultasi | MPP</title>
    <style>
        body {
            background-color: #f8fafc;
            background-image: radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.03) 0, transparent 50%),
                radial-gradient(at 50% 0%, rgba(79, 70, 229, 0.03) 0, transparent 50%),
                radial-gradient(at 100% 0%, rgba(14, 165, 233, 0.03) 0, transparent 50%);
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }

        .text-gradient {
            background: linear-gradient(to right, #1e293b, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes reveal {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal {
            animation: reveal 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased text-slate-700">
    @include('layout.navbar_pengunjung')

    <div class="container mx-auto py-10 px-6 max-w-7xl relative z-10">
        <div class="mb-12 reveal">
            <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit text-gradient">Riwayat Konsultasi
            </h1>
            <p class="text-slate-500 font-medium mt-2 max-w-2xl leading-relaxed">Arsip digital pertanyaan yang Anda
                ajukan di <span class="text-blue-600 font-bold">Mall Pelayanan Publik</span>. Kelola dan pantau status
                konsultasi Anda secara real-time.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-4 space-y-6">
                <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-blue-900/5 p-8 reveal"
                    style="animation-delay: 0.1s">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative mb-8 group">
                            <div
                                class="absolute -inset-4 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-full opacity-10 blur-2xl group-hover:opacity-20 transition-opacity">
                            </div>
                            <div
                                class="w-28 h-28 bg-gradient-to-tr from-slate-900 via-slate-800 to-slate-900 rounded-[2rem] flex items-center justify-center text-white text-4xl font-black relative shadow-2xl transform rotate-3 group-hover:rotate-0 transition-transform duration-500">
                                <span class="font-outfit">
                                    <img 
                                        src="{{ Auth::user()->profil_pengunjung && Auth::user()->profil_pengunjung->foto
                                            ? asset('storage/' . Auth::user()->profil_pengunjung->foto)
                                            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->profil_pengunjung->nama ?? 'User') }}"
                                        alt="User">
                                </span>
                                <div
                                    class="absolute -bottom-2 -right-2 w-8 h-8 bg-emerald-500 border-4 border-white rounded-full animate-pulse">
                                </div>
                            </div>
                        </div>
                        <h2
                            class="text-2xl font-black text-slate-800 uppercase tracking-tight font-outfit text-gradient">
                            {{ $profil->nama }}</h2>
                        <div
                            class="mt-2 mb-8 inline-flex items-center px-3 py-1 bg-slate-100 rounded-full border border-slate-200">
                            <span class="text-[9px] font-bold text-slate-500 tracking-wider uppercase">ID Visitor:
                                #MPP-{{ $profil->id_pengunjung }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="bg-gradient-to-b from-white to-slate-50 p-5 rounded-3xl border border-slate-100 text-center shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <span
                                class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-tight">Belum
                                di Konsultasikan</span>
                            <span class="text-2xl font-black text-slate-800 font-outfit">{{ $belumKonsultasi }}</span>
                        </div>
                        <div
                            class="bg-gradient-to-b from-white to-slate-50 p-5 rounded-3xl border border-slate-100 text-center shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <span
                                class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-tight">Sudah
                                di Konsultasikan</span>
                            <span class="text-2xl font-black text-slate-800 font-outfit">{{ $sudahKonsultasi }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-blue-900/5 p-8 text-center reveal"
                    style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Statistik
                            Layanan</h4>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    </div>
                    <div class="h-52">
                        <canvas id="catatanChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-8">

                <form action="{{ route('catatan.konsul') }}" method="GET"
                    class="glass-card rounded-[2rem] p-4 shadow-2xl shadow-blue-900/5 flex flex-col md:flex-row items-center gap-4 reveal relative z-20"
                    style="animation-delay: 0.3s">
                    <div class="relative w-full md:w-72" x-data="{ open: false, selected: '{{ request('layanan', 'Semua Layanan') }}' }">
                        <input type="hidden" name="layanan" :value="selected">
                        <label
                            class="absolute -top-2.5 left-4 px-2 bg-white text-[9px] font-bold text-slate-400 uppercase tracking-widest z-10">Pilih
                            Layanan</label>
                        <button @click="open = !open" type="button"
                            class="w-full bg-slate-50/50 border border-slate-200/60 text-slate-700 py-4 px-6 rounded-2xl text-[11px] font-bold flex justify-between items-center hover:border-blue-300 transition-all focus:ring-4 focus:ring-blue-500/5">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span x-text="selected"></span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-300"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5 font-bold">
                            <div class="py-2">
                                <a href="#" @click.prevent="selected = 'Semua Layanan'; open = false"
                                    class="flex items-center gap-3 px-5 py-3.5 text-[11px] text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-50 last:border-0">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-200"
                                        :class="selected === 'Semua Layanan' ? 'bg-blue-500' : ''"></div>
                                    <span>Semua Layanan</span>
                                </a>
                                @foreach ($lokets as $loket)
                                    <a href="#"
                                        @click.prevent="selected = '{{ $loket->nama_loket }}'; open = false"
                                        class="flex items-center gap-3 px-5 py-3.5 text-[11px] text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-50 last:border-0">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-200"
                                            :class="selected === '{{ $loket->nama_loket }}' ? 'bg-blue-500' : ''">
                                        </div>
                                        <span>{{ $loket->nama_loket }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-auto flex-grow relative">
                        <label
                            class="absolute -top-2.5 left-4 px-2 bg-white text-[9px] font-bold text-slate-400 uppercase tracking-widest z-10">Filter
                            Tanggal</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="w-full bg-slate-50/50 border border-slate-200/60 text-slate-600 py-4 px-6 rounded-2xl text-[11px] font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-300 transition-all cursor-pointer">
                    </div>

                    <button type="submit"
                        class="w-full md:w-auto px-10 py-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:from-blue-600 hover:to-indigo-600 transition-all shadow-xl shadow-slate-900/10 hover:shadow-blue-600/20 active:scale-95 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Tampilkan Data
                    </button>
                </form>

                <div class="space-y-6">
                    @foreach ($riwayat as $data)
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl mb-6">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="px-5 py-2 bg-blue-50 text-blue-600 text-[10px] font-black rounded-2xl uppercase">
                                            {{ $data->loket->nama_loket ?? 'LOKET ' . $data->id_loket }}
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-400">
                                            {{ \Carbon\Carbon::parse($data->tanggal_konsul)->format('d F Y') }}
                                        </span>
                                    </div>

                                    <div
                                        class="px-5 py-2 rounded-full text-[10px] font-black {{ $data->pelayanan_status == 'selesai' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ strtoupper($data->pelayanan_status) }}
                                    </div>
                                </div>

                                <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100">
                                    <p class="text-slate-600 font-medium italic">
                                        "{{ $data->konsultasi }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($riwayat->isEmpty())
                        <div class="text-center py-10 text-slate-400 font-bold">Belum ada riwayat konsultasi.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>

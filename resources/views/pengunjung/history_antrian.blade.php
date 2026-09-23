<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas - MPP</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/history_antrian.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="{{ asset('js/history_antrian.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-[#F8FAFC]">
    @include('layout.navbar_pengunjung')

    <div class="history-container" x-data="historyAntrian({{ $histories->toJson() }})"
        x-cloak>
        <aside class="sidebar-info">
            <div class="user-profile-summary">
                <div class="avatar-circle"><i class="fas fa-user-clock"></i></div>
                <h2>Aktivitas Saya</h2>
                <p>Kelola dan pantau seluruh riwayat antrian Anda dalam satu halaman.</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card completed">
                    <div class="stat-icon"><i class="fas fa-check"></i></div>
                    <div class="stat-content">
                        <span class="num" x-text="histories.filter(h => h.status === 'selesai').length">0</span>
                        <span class="lbl">Selesai</span>
                    </div>
                </div>
                <div class="stat-card cancelled">
                    <div class="stat-icon"><i class="fas fa-ban"></i></div>
                    <div class="stat-content">
                        <span class="num" x-text="histories.filter(h => h.status === 'batal' || h.status === 'terlewat').length">0</span>
                        <span class="lbl">Batal/Terlewat</span>
                    </div>
                </div>
            </div>

            <div class="filter-section" style="margin-bottom: 20px;">
                <div class="flex justify-between items-center mb-2">
                    <label class="section-label" style="margin-bottom: 0;">Filter Tanggal</label>
                    <button @click="resetFilter()" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded-md transition-colors">
                        <i class="fas fa-undo-alt mr-1"></i> Reset
                    </button>
                </div>
                <div class="space-y-2 mt-2">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Mulai Dari</span>
                        <input type="date" x-model="filterStartDate" 
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Sampai</span>
                        <input type="date" x-model="filterEndDate" 
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <label class="section-label">Filter Status</label>
                <div class="filter-pill-group">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'active' : ''"><i class="fas fa-stream"></i> Semua</button>
                    <button @click="filter = 'menunggu'" :class="filter === 'menunggu' ? 'active' : ''"><i class="fas fa-clock"></i> Menunggu</button>
                    <button @click="filter = 'dipanggil'" :class="filter === 'dipanggil' ? 'active' : ''"><i class="fas fa-bullhorn"></i> Dipanggil</button>
                    <button @click="filter = 'selesai'" :class="filter === 'selesai' ? 'active' : ''"><i class="fas fa-check-circle"></i> Selesai</button>
                    <button @click="filter = 'booking'" :class="filter === 'booking' ? 'active' : ''"><i class="far fa-calendar-check"></i> Booking</button>
                    <button @click="filter = 'batal'" :class="filter === 'batal' ? 'active' : ''"><i class="fas fa-times-circle"></i> Batal</button>
                    <button @click="filter = 'terlewat'" :class="filter === 'terlewat' ? 'active' : ''"><i class="fas fa-ban"></i> Terlewat</button>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="content-header">
                <h3>Log Antrian</h3>
                <span class="count-badge" x-text="filteredHistory.length + ' Data ditemukan'"></span>
            </div>
            <template x-if="filteredHistory.length === 0">
                <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-slate-100">
                    <i class="fas fa-clipboard-list text-5xl textc-slate-200 mb-4"></i>
                    <p class="text-slate-400 font-medium">Belum ada riwayat antrian ditemukan.</p>
                </div>
            </template>
            <div class="timeline-wrapper">
                <template x-for="item in filteredHistory" :key="item.id">
                    <div class="history-entry" :class="item.status.toLowerCase()">
                        <div class="indicator-col">
                            <div class="marker"></div>
                            <div class="v-line"></div>
                        </div>

                        <div class="entry-card">
                            <div class="card-left">
                                <div class="ticket-tag" x-text="item.nomor"></div>
                                <div class="entry-main-info">
                                    <h4 x-text="item.instansi"></h4>
                                    <p class="text-[11px] font-semibold text-slate-500 line-clamp-1 mt-0.5" x-text="item.nama_pelayanan"></p>
                                    <p class="text-[9px] text-slate-400 mt-1 uppercase" x-text="'Metode: ' + item.metode"></p>
                                </div>
                            </div>
                            <div class="card-right">
                                <div class="meta-info">
                                    <div class="meta-item"><i class="far fa-calendar-alt"></i> <span x-text="item.tanggal"></span></div>
                                    <div class="meta-item"><i class="far fa-clock"></i> <span x-text="item.jam"></span></div>
                                </div>
                                <div class="status-box">
                                    <span class="status-label" x-text="item.status"></span>
                                    <button @click="openDetail(item)" class="btn-action"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </main>

        <div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            
            <div @click.away="closeDetail()" id="ticket-to-print" class="ticket-modal w-full max-w-sm mx-4 p-8 shadow-2xl bg-white">
                
                <div class="text-center mb-6">
                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-700 text-[10px] font-bold uppercase rounded-full px-6 pt-[5px] pb-[10px] min-w-[100px] leading-none" 
                    x-text="'LOKET ' + selectedTicket.loket">
                </span>
                    <h2 class="text-gray-500 text-xs font-bold uppercase mt-3" x-text="selectedTicket.instansi"></h2>
                    <p class="text-[10px] text-gray-500 font-semibold" x-text="selectedTicket.nama_pelayanan"></p>
                    <h1 class="text-6xl font-black text-slate-800 my-4" x-text="selectedTicket.nomor"></h1>
                    <p class="text-sm text-gray-600 font-medium uppercase" x-text="'Metode Antrean ' + selectedTicket.metode"></p>
                </div>

                <div class="ticket-divider"></div>

                <div class="space-y-3">
                    <template x-if="selectedTicket.kode_booking">
                        <div class="flex justify-between text-xs items-center bg-purple-50 p-2 rounded-lg border border-purple-100">
                            <span class="text-purple-600 uppercase font-bold">Kode Booking</span>
                            <span class="text-purple-900 font-mono font-bold" x-text="selectedTicket.kode_booking"></span>
                        </div>
                    </template>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 uppercase font-bold">Waktu Kunjungan</span>
                        <span class="text-slate-700 font-bold" x-text="selectedTicket.tanggal + ' • ' + selectedTicket.jam"></span>
                    </div>
                    <template x-if="selectedTicket.slot_waktu">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400 uppercase font-bold">Sesi Layanan</span>
                            <span class="text-blue-700 font-bold" x-text="selectedTicket.slot_waktu"></span>
                        </div>
                    </template>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 uppercase font-bold">Status Antrean</span>
                        <span :class="selectedTicket.status === 'selesai' ? 'text-green-600' : (selectedTicket.status === 'batal' ? 'text-red-600' : 'text-blue-600')" 
                            class="font-bold uppercase" 
                            x-text="selectedTicket.status"></span>
                    </div>
                    <template x-if="selectedTicket.status_booking">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400 uppercase font-bold">Kehadiran Booking</span>
                            <span :class="selectedTicket.status_booking === 'check_in' ? 'text-green-600' : (selectedTicket.status_booking === 'no_show' ? 'text-red-600' : 'text-purple-600')" 
                                class="font-bold uppercase" 
                                x-text="selectedTicket.status_booking === 'check_in' ? 'Check-In (' + (selectedTicket.waktu_check_in || '') + ')' : (selectedTicket.status_booking === 'no_show' ? 'No-Show' : 'Booking')"></span>
                        </div>
                    </template>
                </div>

                <button data-html2canvas-ignore 
                    class="w-full mt-8 bg-slate-900 text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2"
                    @click="downloadTicket('ticket-to-print', selectedTicket.nomor)">
                    <i class="fas fa-print"></i> Cetak Ulang Tiket
                </button>
            </div>
        </div>
    </div>
</body>
</html>
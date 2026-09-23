@extends('layout.navbar_administrator')

@section('content')
<style>
    .main-content { padding: 20px; font-family: 'Poppins', sans-serif; background: #f4f7fe; min-height: 100vh; }
    .page-title { font-size: 24px; font-weight: 700; color: #2b3674; }
    
    .card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .card-title { font-size: 18px; font-weight: 600; color: #2b3674; border-bottom: 2px solid #f4f7fe; padding-bottom: 10px; margin-bottom: 15px;}
    
    .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .alert-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    
    .engine-mode-container { display: flex; gap: 20px; flex-wrap: wrap;}
    .mode-card { flex: 1; border: 2px solid #e0e5f2; border-radius: 10px; padding: 15px; cursor: pointer; transition: 0.3s; text-align: center; background: white;}
    .mode-card:hover { border-color: #4318FF; transform: translateY(-3px); }
    .mode-card.active { border-color: #4318FF; background: #f4f7fe; box-shadow: 0 4px 10px rgba(67, 24, 255, 0.15); }
    .mode-icon { font-size: 30px; margin-bottom: 10px; color: #4318FF; }
    
    .btn-train { background: linear-gradient(135deg, #4318FF, #868CFF); color: white; padding: 15px 30px; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.3s;}
    .btn-train:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(67, 24, 255, 0.3); }
    
    .table-basic { width: 100%; border-collapse: collapse; font-family: 'Poppins', sans-serif; }
    .table-basic th { background: #f8fafc; padding: 15px 12px; text-align: left; color: #64748b; font-size: 14px; border-bottom: 2px solid #e2e8f0;}
    .table-basic td { padding: 15px 12px; border-bottom: 1px solid #e2e8f0; color: #334155; font-size: 14px; }
    .table-basic tr:hover { background: #f1f5f9; }
    
    .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }

    /* TABS STYLE */
    .analytics-tabs-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px; font-family: 'Poppins', sans-serif;}
    .analytics-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; overflow-x: auto; }
    .analytics-tab-btn { padding: 10px 20px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px; color: #64748b; font-weight: 500; font-family: 'Poppins', sans-serif; cursor: pointer; transition: all 0.3s; white-space: nowrap; }
    .analytics-tab-btn:hover { background: #e2e8f0; color: #334155; }
    .analytics-tab-btn.active { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    .analytics-tab-content { display: none; animation: fadeIn 0.5s; }
    .analytics-tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* MODERN DATATABLES SEARCH STYLING */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }
    .dataTables_wrapper .dataTables_filter label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        font-family: 'Poppins', sans-serif;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        outline: none;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        margin-left: 10px;
        width: 280px;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4318FF;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(67, 24, 255, 0.1);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4318FF !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 10px rgba(67, 24, 255, 0.2);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        margin: 0 3px;
    }
</style>


<div class="main-content">
    <div style="margin-bottom: 20px;">
        <h2 class="page-title">Dashboard Machine Learning</h2>
        <p style="color: #a3aed0;">Kelola algoritma pencarian antrean dan latih kecerdasan buatan.</p>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <!-- PENGATURAN MODE ENGINE -->
    <div class="card">
        <h4 class="card-title"><i class="fa-solid fa-microchip"></i> Pengaturan Mode Engine (Logika Pencarian)</h4>
        <form action="{{ route('super.training.updateMode') }}" method="POST" id="formUpdateMode">
            @csrf
            <input type="hidden" name="engine_mode" id="engineModeInput" value="{{ $engineMode }}">
            <div class="engine-mode-container">
                <div class="mode-card {{ $engineMode == 'rule_based' ? 'active' : '' }}" onclick="selectMode('rule_based')">
                    <div class="mode-icon"><i class="fa-solid fa-book"></i></div>
                    <h4>Mode Kamus (Rule-Based)</h4>
                    <p style="font-size: 12px; color: #a3aed0;">Sistem murni menggunakan kata kunci statis dari tabel Algoritma. Cepat namun kaku.</p>
                </div>
                <div class="mode-card {{ $engineMode == 'ai_only' ? 'active' : '' }}" onclick="selectMode('ai_only')">
                    <div class="mode-icon"><i class="fa-solid fa-brain"></i></div>
                    <h4>Mode Full AI</h4>
                    <p style="font-size: 12px; color: #a3aed0;">Sistem 100% menggunakan file model.pkl. Murni mengandalkan Machine Learning.</p>
                </div>
                <div class="mode-card {{ $engineMode == 'hybrid' ? 'active' : '' }}" onclick="selectMode('hybrid')">
                    <div class="mode-icon"><i class="fa-solid fa-network-wired"></i></div>
                    <h4>Mode Hybrid (Rekomendasi)</h4>
                    <p style="font-size: 12px; color: #a3aed0;">Gabungan. Cek Kamus dulu, jika gagal akan dibantu tebak oleh AI Machine Learning.</p>
                </div>
            </div>
        </form>
    </div>

    <div class="charts-grid">
        <!-- TRAIN MODEL PANEL -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: center;">
            <h4 class="card-title"><i class="fa-solid fa-graduation-cap"></i> Pelatihan Model AI (Training)</h4>
            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                Saat melatih model, sangat disarankan untuk mengubah Mode Engine ke <strong>Mode Kamus</strong> terlebih dahulu untuk mencegah error pembacaan file jika ada pengunjung yang mendaftar secara bersamaan.
            </p>
            <form action="{{ route('super.training.train') }}" method="POST" onsubmit="return confirm('Mulai proses training? Proses ini mungkin memakan waktu beberapa detik.')">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 14px;">Pilih Algoritma ML:</label>
                    <select name="algoritma" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px;">
                        <option value="Naive Bayes">Naive Bayes (Lebih cepat)</option>
                        <option value="SVM">Support Vector Machine (Lebih akurat pada data kompleks)</option>
                    </select>
                </div>
                <button type="submit" class="btn-train" id="btnTrain">
                    <i class="fa-solid fa-play"></i> Mulai Training AI Sekarang
                </button>
            </form>
        </div>

        <!-- CHART DISTRIBUSI DATA -->
        <div class="card">
            <h4 class="card-title"><i class="fa-solid fa-chart-bar"></i> Distribusi Data Latih (Voice Training)</h4>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="chartDistribusi"></canvas>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT TRAINING -->
    <div class="card">
        <h4 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Log Riwayat Training Model</h4>
        <table class="table-basic">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Training</th>
                    <th>Algoritma</th>
                    <th style="text-align: center;">Jumlah Data</th>
                    <th>Akurasi</th>
                    <th>Waktu Proses</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d F Y, H:i') }}</td>
                    <td><strong>{{ $row->algoritma_dipakai }}</strong></td>
                    <td style="text-align: center;">{{ $row->jumlah_data }} baris</td>
                    <td>
                        <strong style="color: {{ $row->akurasi >= 80 ? '#2e7d32' : '#c62828' }}">
                            {{ $row->akurasi }}%
                        </strong>
                    </td>
                    <td>{{ $row->waktu_eksekusi }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #a3aed0; font-style: italic;">Belum ada riwayat training.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TABEL DATA LATIHAN & ALGORITMA (TABS) -->
    <div class="analytics-tabs-container">
        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0 0 20px 0;">📊 Tabel Analisis Detail Engine</h3>
        
        <div class="analytics-tabs">
            <button class="analytics-tab-btn active" onclick="openAnalyticsTab('tab-voice', this)">Dataset Suara (AI)</button>
            <button class="analytics-tab-btn" onclick="openAnalyticsTab('tab-algoritma', this)">Kamus Kata Kunci (Rule-Based)</button>
        </div>

        <!-- TAB 1: DATASET SUARA -->
        <div id="tab-voice" class="analytics-tab-content active">
            <h4 style="font-size: 15px; font-weight: 600; color: #3b82f6; margin-bottom: 5px;">I. Daftar Rekaman Suara Pengunjung (Voice Training)</h4>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Hapus data yang tidak relevan (sampah) sebelum melatih model agar akurasi tetap tinggi.</p>
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px; align-items: center;">
                <label style="font-size: 13px; font-weight: 600; color: #64748b;"><i class="fa-solid fa-filter"></i> Filter Loket:</label>
                <select id="filterLoketVoice" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-family: 'Poppins', sans-serif; font-size: 13px;">
                    <option value="">Semua Loket</option>
                    @foreach($labelsDistribusi as $loket)
                        <option value="{{ strtoupper($loket) }}">{{ strtoupper($loket) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-basic datatable" id="tableVoice">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Teks Transkripsi</th>
                            <th>Ditebak Sebagai Loket</th>
                            <th>Sumber</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voiceTraining as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong>"{{ $row->teks_transkripsi }}"</strong></td>
                            <td><span style="background: #e0e5f2; padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #2b3674; font-weight: 600;">{{ strtoupper($row->nama_loket) }}</span></td>
                            <td>{{ ucfirst($row->sumber_data) }}</td>
                            <td style="text-align: center;">
                                <form action="{{ route('super.training.destroyVoice', $row->id_training) }}" method="POST" class="delete-voice-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-delete-voice" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; font-style: italic;">Belum ada data rekaman suara.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: KAMUS ALGORITMA -->
        <div id="tab-algoritma" class="analytics-tab-content">
            <h4 style="font-size: 15px; font-weight: 600; color: #ea580c; margin-bottom: 5px;">II. Kamus Kata Kunci Pasti (Tabel Algoritma)</h4>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Digunakan oleh Mode Kamus dan Mode Hybrid tahap pertama. Pengubahan dilakukan oleh masing-masing Admin Loket.</p>
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px; align-items: center;">
                <label style="font-size: 13px; font-weight: 600; color: #64748b;"><i class="fa-solid fa-filter"></i> Filter Target Loket:</label>
                <select id="filterLoketAlgoritma" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-family: 'Poppins', sans-serif; font-size: 13px;">
                    <option value="">Semua Target</option>
                    @foreach($labelsDistribusi as $loket)
                        <option value="{{ strtoupper($loket) }}">{{ strtoupper($loket) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-basic datatable" id="tableAlgoritma">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kata Kunci / Frasa (Algoritma)</th>
                            <th>Target Loket</th>
                            <th>Tipe Target</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataAlgoritma as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong style="color: #4318FF;">{{ $row->algoritma }}</strong></td>
                            <td>{{ strtoupper($row->nama_loket) }}</td>
                            <td><span style="background: {{ $row->tipe_layanan == 'layanan' ? '#dcfce7; color: #166534' : '#ffedd5; color: #9a3412' }}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($row->tipe_layanan) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; font-style: italic;">Belum ada kata kunci terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize datatables if jQuery exists
        if(typeof $.fn.DataTable !== 'undefined') {
            var dtVoice = $('#tableVoice').DataTable({
                pageLength: 5,
                lengthChange: false, // Menghilangkan "Show entries"
                language: {
                    search: "Cari Data:",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data"
                }
            });

            var dtAlgo = $('#tableAlgoritma').DataTable({
                pageLength: 5,
                lengthChange: false, // Menghilangkan "Show entries"
                language: {
                    search: "Cari Data:",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data"
                }
            });

            // Fitur Filter Dropdown Loket untuk Tabel Voice (Kolom indeks ke-2)
            $('#filterLoketVoice').on('change', function() {
                dtVoice.column(2).search(this.value).draw();
            });

            // Fitur Filter Dropdown Loket untuk Tabel Algoritma (Kolom indeks ke-2)
            $('#filterLoketAlgoritma').on('change', function() {
                dtAlgo.column(2).search(this.value).draw();
            });
        }
    });

    function selectMode(mode) {
        document.getElementById('engineModeInput').value = mode;
        document.getElementById('formUpdateMode').submit();
    }

    function openAnalyticsTab(tabId, btn) {
        // Sembunyikan semua konten tab
        document.querySelectorAll('.analytics-tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Hapus class active dari semua tombol
        document.querySelectorAll('.analytics-tab-btn').forEach(button => {
            button.classList.remove('active');
        });

        // Tampilkan tab yang dipilih
        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    }

    // Render Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartDistribusi');
        if (ctx) {
            let labels = {!! json_encode($labelsDistribusi) !!};
            let data = {!! json_encode($dataDistribusi) !!};
            
            // Modern premium color palette
            let bgColors = [
                '#4318FF', // Deep Blue
                '#05CD99', // Emerald Green
                '#FFB547', // Warm Orange
                '#EE5D50', // Soft Red
                '#7A5AF8', // Purple
                '#00B8D9', // Cyan
                '#FF8A65', // Coral
                '#A3AED0'  // Grey/Blue
            ];

            let totalData = data.reduce((a, b) => a + b, 0);

            // Jika kosong, tampilkan diagram abu-abu
            if (data.length === 0) {
                labels = ['Belum ada data'];
                data = [1];
                bgColors = ['#e2e8f0'];
                totalData = 0;
            }

            // Plugin untuk teks di tengah Doughnut
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw: function(chart) {
                    if (chart.config.type !== 'doughnut') return;
                    if (!chart.getDatasetMeta(0).data.length) return;

                    var ctx = chart.ctx;
                    // Dapatkan titik tengah persis dari Doughnut, bukan seluruh canvas
                    var centerX = chart.getDatasetMeta(0).data[0].x;
                    var centerY = chart.getDatasetMeta(0).data[0].y;

                    ctx.restore();
                    
                    // Teks Total (Angka)
                    var fontSizeTotal = (chart.height / 100).toFixed(2);
                    ctx.font = "bold " + fontSizeTotal + "em 'Poppins', sans-serif";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#2b3674"; // Warna teks angka

                    var textTotal = totalData.toString();
                    var textXTotal = Math.round(centerX - (ctx.measureText(textTotal).width / 2));
                    var textYTotal = centerY - 10;

                    ctx.fillText(textTotal, textXTotal, textYTotal);
                    
                    // Teks Label (Suara)
                    var fontSizeLabel = (chart.height / 250).toFixed(2);
                    ctx.font = "500 " + fontSizeLabel + "em 'Poppins', sans-serif";
                    ctx.fillStyle = "#a3aed0"; // Warna teks label

                    var textLabel = "Rekaman Suara";
                    var textXLabel = Math.round(centerX - (ctx.measureText(textLabel).width / 2));
                    var textYLabel = centerY + 15;

                    ctx.fillText(textLabel, textXLabel, textYLabel);

                    ctx.save();
                }
            };

            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgColors,
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { 
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 25,
                                color: '#64748b',
                                font: { size: 12, family: "'Poppins', sans-serif" }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { family: "'Poppins', sans-serif", size: 13 },
                            bodyFont: { family: "'Poppins', sans-serif", size: 12 },
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    if(context.label === 'Belum ada data') return ' Belum ada data rekaman';
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed + ' rekaman';
                                    }
                                    return ' ' + label;
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });
        }
    });

    // Fitur SweetAlert2 untuk tombol Delete Voice
    document.querySelectorAll('.btn-delete-voice').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Data Suara?',
                text: "Yakin ingin menghapus data suara ini? Data yang salah dapat merusak akurasi AI.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    popup: 'swal2-border-radius-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Fitur SweetAlert2 untuk tombol Train Model
    document.getElementById('btnTrain').addEventListener('click', function(e) {
        e.preventDefault();
        const btn = this;
        const form = btn.closest('form');
        
        Swal.fire({
            title: 'Mulai Proses Training AI?',
            text: "Ini akan memakan waktu beberapa saat tergantung pada jumlah data latih. Apakah Anda ingin melanjutkan?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Mulai Training',
            cancelButtonText: 'Batal',
            background: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sedang Melatih Model...';
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
                form.submit();
            }
        });
    });
</script>
@endpush

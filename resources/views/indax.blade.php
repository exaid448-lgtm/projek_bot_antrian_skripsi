<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ session('id_user') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Loket Suara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/indax.css') }}">
</head>
<body class="p-4 bg-gray-900 flex items-center justify-center min-h-screen">
    
<div class="max-w-md w-full">
    <div class="flex justify-between items-center mb-5 w-full">
        <a href="{{ route('dashboard.pengunjung') }}" class="inline-flex items-center text-slate-400 hover:text-slate-200 text-sm font-semibold transition-all group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('antrian.manual.index') }}" class="inline-flex items-center bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-bold px-5 py-2.5 rounded-full transition-all shadow-md hover:shadow-lg hover:shadow-indigo-500/20 active:scale-95 group border border-indigo-500/20">
            <svg class="w-4 h-4 mr-2 text-white/90 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Antrian Manual
            <svg class="w-3.5 h-3.5 ml-1.5 transform group-hover:translate-x-1 transition-transform text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <div class="max-w-md w-full p-6 bg-gray-800 rounded-xl shadow-2xl text-center">
        <h1 class="text-3xl font-extrabold text-white mb-2">Simulasi Loket Suara MPP</h1>
        <p id="status-text" class="text-indigo-400 mb-8 h-6">Sistem siap.</p>
        
        <div id="error-message" class="hidden bg-red-800 text-white p-2 mb-4 rounded-lg text-sm">
            Server Python belum berjalan.
        </div>

        <div id="visualization-area" class="mx-auto mb-6 flex justify-center">
            <svg id="mic-icon" class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"></path>
                <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"></path>
            </svg>
        </div>

        <button id="input-button" class="w-full py-3 px-6 text-lg font-bold text-white transition duration-300 rounded-lg shadow-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 disabled:opacity-50">
            Tekan untuk Bicara
        </button>

        <!-- Panduan & Contoh Kalimat (Revisi Penguji) -->
        <div class="mt-5 text-left bg-gray-700/40 p-4 rounded-lg border border-gray-600">
            <h3 class="text-sm font-bold text-indigo-300 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Panduan Penggunaan
            </h3>
            <ul class="text-xs text-gray-300 list-disc list-inside space-y-1 mb-3">
                <li>Tekan tombol <strong class="text-white">"Tekan untuk Bicara"</strong>.</li>
                <li>Ucapkan layanan atau <strong class="text-indigo-300">Cukup Sebutkan Nama Instansi Tujuan Anda</strong> (misal: "Samsat", "Polres").</li>
                <li>Sistem akan mencarikan loket yang tepat secara otomatis.</li>
            </ul>
            
            <h3 class="text-sm font-bold text-teal-300 mb-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                Contoh Kalimat:
            </h3>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="bg-gray-800 text-teal-100 text-[11px] px-2.5 py-1 rounded-md border border-gray-600 font-medium">"Polres"</span>
                <span class="bg-gray-800 text-teal-100 text-[11px] px-2.5 py-1 rounded-md border border-gray-600 font-medium">"BPJS Kesehatan"</span>
                <span class="bg-gray-800 text-teal-100 text-[11px] px-2.5 py-1 rounded-md border border-gray-600 font-medium">"Bank Kalsel"</span>
                <span class="bg-gray-800 text-indigo-200 text-[11px] px-2.5 py-1 rounded-md border border-indigo-700/50 font-medium">"Saya ingin membuat SKCK"</span>
                <span class="bg-gray-800 text-indigo-200 text-[11px] px-2.5 py-1 rounded-md border border-indigo-700/50 font-medium">"Mau perpanjang SIM"</span>
            </div>
        </div>

        <div class="mt-5 text-left bg-gray-700 p-4 rounded-lg h-32 overflow-y-auto">
            <h2 class="text-sm font-semibold text-gray-400 mb-2">Log Interaksi:</h2>
            <div id="log-output" class="text-sm text-gray-200 space-y-1"></div>
        </div>
        
        <div class="mt-4 p-4 bg-gray-900 rounded-lg shadow-inner border border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase">Loket Tujuan Saat Ini</p>
            <div id="loket-simulasi" class="mt-1">
                <div id="loket-nama" class="text-xl font-semibold text-teal-300">-</div>
                <div id="loket-nomor" class="text-4xl font-extrabold text-teal-400">-</div>
            </div>
        </div>

        <div class="mt-6">
            @if(!session('id_user'))
            <button id="btn-mode-khusus" onclick="toggleModeKhusus()" class="w-full py-3 px-4 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-600 rounded-lg transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-wheelchair"></i>
                <span id="text-mode-khusus">Aktifkan Mode Khusus (Lansia / Disabilitas / Hamil)</span>
            </button>
            @endif
        </div>
    </div>
</div>

    <div id="qr-modal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-[9999] backdrop-blur-sm">
        <div class="bg-white p-8 rounded-3xl text-center max-w-sm mx-4 relative shadow-2xl border-4 border-indigo-500">
            <button onclick="closeQRModal()" class="absolute -top-4 -right-4 bg-red-500 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold hover:bg-red-600 transition shadow-xl text-xl">×</button>
            
            <h3 class="text-gray-900 font-extrabold text-xl mb-2">Layanan Sedang Tutup</h3>
            <p class="text-gray-500 text-sm mb-4">Mohon maaf, silahkan jika mau melakukan booking antrian bisa melalui QR Code di bawah ini:</p>
            
            <div id="qr-code-container" class="bg-white p-4 rounded-xl flex justify-center border-2 border-dashed border-gray-300">
                <img id="qr-image" src="" alt="QR Code Konsultasi" class="w-52 h-52">
            </div>
            
            <p class="text-indigo-600 font-bold mt-4 animate-pulse">Scan untuk booking antrian</p>
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

    <!-- Hidden input to store chosen jenis prioritas -->
    <input type="hidden" id="hidden-jenis-prioritas-sementara" value="disabilitas_sementara">

    <script src="{{ asset('js/index.js') }}"></script>
    <script>
        // Variables dari Laravel
        window.isPermanen = {{ isset($isPermanen) && $isPermanen ? 'true' : 'false' }};
        window.isLansia = {{ isset($isLansia) && $isLansia ? 'true' : 'false' }};
        window.isModeKhusus = false;
        
        function toggleModeKhusus() {
            if (!window.isModeKhusus) {
                // Mau mengaktifkan
                @auth
                    if (window.isPermanen || window.isLansia) {
                        // Jika lansia atau permanen, langsung aktifkan
                        setModeKhususAktif(true);
                    } else {
                        // Online: Harus upload dokumen
                        document.getElementById('uploadKhususModal').classList.remove('hidden');
                    }
                @else
                    // Offline: Minta pengunjung memilih jenis prioritas tanpa upload
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
                                if (value !== '') resolve();
                                else resolve('Anda harus memilih jenis prioritas');
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('hidden-jenis-prioritas-sementara').value = result.value;
                            setModeKhususAktif(true);
                        }
                    });
                @endauth
            } else {
                // Mau mematikan
                document.getElementById('hidden-jenis-prioritas-sementara').value = 'disabilitas_sementara';
                setModeKhususAktif(false);
            }
        }

        function batalUploadKhusus() {
            document.getElementById('uploadKhususModal').classList.add('hidden');
            document.getElementById('file-prioritas').value = "";
            document.getElementById('upload-status-msg').classList.add('hidden');
        }

        function setModeKhususAktif(status) {
            window.isModeKhusus = status;
            const btn = document.getElementById('btn-mode-khusus');
            const text = document.getElementById('text-mode-khusus');
            
            if (window.isModeKhusus) {
                btn.classList.remove('bg-slate-800', 'text-slate-300', 'border-slate-600');
                btn.classList.add('bg-amber-500', 'text-white', 'border-amber-400', 'shadow-[0_0_15px_rgba(245,158,11,0.5)]');
                text.innerText = "Mode Khusus Aktif";
            } else {
                btn.classList.remove('bg-amber-500', 'text-white', 'border-amber-400', 'shadow-[0_0_15px_rgba(245,158,11,0.5)]');
                btn.classList.add('bg-slate-800', 'text-slate-300', 'border-slate-600');
                text.innerText = "Aktifkan Mode Khusus (Lansia / Disabilitas / Hamil)";
            }
        }

        function closeQRModal() {
            document.getElementById('qr-modal').classList.add('hidden');
        }

        // Handle AJAX Upload
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                if(data.success) {
                    // Simpan path yang terupload ke variabel global supaya bisa dikirim saat submit suara
                    window.uploadedDokumenKhusus = data.path;
                    document.getElementById('hidden-jenis-prioritas-sementara').value = document.getElementById('jenis-prioritas-sementara').value;
                    batalUploadKhusus();
                    setModeKhususAktif(true);
                } else {
                    msgError.innerText = data.message || "Gagal mengunggah file.";
                    msgError.classList.remove('hidden');
                }
            } catch (err) {
                msgError.innerText = "Terjadi kesalahan server.";
                msgError.classList.remove('hidden');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Upload & Aktifkan';
            }
        });
    </script>
    
</body>
</html>
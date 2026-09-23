const API_URL = 'http://127.0.0.1:5500/api/process_speech';

const button = document.getElementById('input-button');
const logOutput = document.getElementById('log-output');
const statusText = document.getElementById('status-text');
const loketNama = document.getElementById('loket-nama');
const loketNomor = document.getElementById('loket-nomor');
const userId = document.querySelector('meta[name="user-id"]')?.content || null;

let modalTimer;

button.disabled = false;

// ============================================================
// FUNGSI PERBAIKAN LOG INTERAKSI (MENGHAPUS KATA BERULANG)
// ============================================================
function cleanDisplayWeb(text) {
    if (!text) return "";
    return text.replace(/\b(\w+)( \1\b)+/gi, '$1');
}

// ============================================================
// FUNGSI TEXT-TO-SPEECH (WEB BROWSER)
// ============================================================
function speakText(message) {
    if (!message) return;
    
    // Hentikan suara yang sedang berjalan jika ada
    window.speechSynthesis.cancel();
    
    const utterance = new SpeechSynthesisUtterance(message);
    utterance.lang = 'id-ID';
    utterance.rate = 1.0;  // Kecepatan normal
    utterance.pitch = 1.0;
    
    window.speechSynthesis.speak(utterance);
}

// ============================================================
// FUNGSI PENCATATAN KEGAGALAN SISTEM KE LARAVEL LOG
// ============================================================
async function catatKegagalanSistem(komponen, pesanError, aksiFallback = "Dialihkan ke Antrean Manual", tingkat = "Warning") {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        await fetch('/api/catat-kegagalan-sistem', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({
                komponen: komponen,
                pesan_error: pesanError,
                aksi_fallback: aksiFallback,
                tingkat: tingkat
            })
        });
    } catch (err) {
        console.warn("Gagal mengirim log kegagalan sistem:", err);
    }
}

// Helper untuk menampilkan SweetAlert peringatan fallback manual
function tampilkanFallbackManualModal(judul, pesan, komponen) {
    Swal.fire({
        icon: 'warning',
        title: judul,
        html: `
            <div style="text-align: left; font-size: 14px; color: #475569;">
                <p style="margin-bottom: 12px;">${pesan}</p>
                <div style="background: #f1f5f9; padding: 12px 14px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 15px;">
                    <strong style="color: #1e293b;">Mode Alternatif:</strong> Anda dapat langsung mengambil tiket layanan secara mandiri melalui <b>Mode Antrean Manual</b>.
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Beralih ke Antrean Manual',
        cancelButtonText: 'Coba Lagi'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/antrian-manual';
        }
    });
}

async function processSpeech() {
    // 0. Deteksi Koneksi Jaringan Offline
    if (typeof navigator.onLine !== 'undefined' && !navigator.onLine) {
        catatKegagalanSistem('Jaringan Internet', 'Koneksi jaringan terputus (Offline)', 'Dialihkan ke Antrean Manual', 'Warning');
        statusText.textContent = "Koneksi jaringan terputus";
        statusText.style.color = "red";
        tampilkanFallbackManualModal(
            'Koneksi Jaringan Terputus',
            'Perangkat Anda terdeteksi sedang offline. Silakan beralih ke Mode Antrean Manual.',
            'Jaringan Internet'
        );
        return;
    }

    button.disabled = true;
    statusText.textContent = "Mendengarkan suara...";

    try {
        const isKhusus = window.isModeKhusus ? 1 : 0;
        const jenisPrioritas = document.getElementById('hidden-jenis-prioritas-sementara') ? document.getElementById('hidden-jenis-prioritas-sementara').value : '';
        const res = await fetch(`${API_URL}?id_user=${userId}&mode_khusus=${isKhusus}&jenis_prioritas=${jenisPrioritas}`);
        const data = await res.json();
        console.log("Data diterima dari server:", data);

        // --- PERBAIKAN LOG INTERAKSI DISINI ---
        if (data.spoken_text) {
            // Bersihkan teks hasil transkripsi sebelum ditampilkan ke Log
            const cleanText = cleanDisplayWeb(data.spoken_text);
            logOutput.innerHTML = `<div>🗣️ "${cleanText}"</div>` + logOutput.innerHTML;
        }

        // 🔶 KONDISI 1A: BUTUH PILIHAN LOKET (AMBIGU)
        if (data.status === 'butuh_pilihan') {
            statusText.textContent = "Butuh Kepastian Loket";
            statusText.style.color = "#eab308";
            logOutput.innerHTML = `<div style="color:#eab308">⚠️ ${data.message}</div>` + logOutput.innerHTML;
            speakText(data.message);
            
            // Build tombol dinamis
            let buttonsHtml = '<div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">';
            if (data.pilihan_loket && Array.isArray(data.pilihan_loket)) {
                data.pilihan_loket.forEach(lok => {
                    buttonsHtml += `<button onclick="pilihLoket(${lok.id_loket})" style="padding: 15px; background: #eab308; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">${lok.nama_loket}</button>`;
                });
            }
            buttonsHtml += '</div>';

            Swal.fire({
                title: 'Pilih Loket Tujuan',
                html: `<p style="font-size: 16px;">${data.message}</p>${buttonsHtml}`,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Batal',
                cancelButtonColor: '#64748b'
            });
            return;
        }

        // 🔶 KONDISI 1B: BUTUH LOKASI (KHUSUS SAMSAT LAMA JIKA MASIH DIPAKAI)
        if (data.status === 'butuh_lokasi') {
            statusText.textContent = "Butuh lokasi SAMSAT";
            statusText.style.color = "orange";
            logOutput.innerHTML = `<div style="color:orange">⚠️ ${data.message}</div>` + logOutput.innerHTML;
            speakText(data.message);
            return;
        }

        // 🔶 KONDISI 2: LAYANAN TUTUP
        const statusSekarang = (data.status || "").toLowerCase();
        const pelayananSekarang = (data.status_pelayanan || "").toLowerCase();

        if (statusSekarang === 'closed' || pelayananSekarang === 'tutup') {
            statusText.textContent = "Layanan Tutup";
            statusText.style.color = "orange";
            loketNama.textContent = data.loket || "-";
            loketNomor.textContent = "TUTUP";

            const idLoket = data.id_loket ? `?id_loket=${data.id_loket}` : '';
            const urlKonsultasi = data.qr_url || `http://127.0.0.1:8000/booking-antrian${idLoket}`;

            showQRModal(urlKonsultasi, 10000);

            logOutput.innerHTML = `<div style="color:orange">⚠️ ${data.message}</div>` + logOutput.innerHTML;
            speakText(data.message);
            return;
        }

        // 🔶 KONDISI 3: GAGAL
        if (data.status === 'failed') {
            statusText.textContent = data.message || "Suara tidak dikenali";
            statusText.style.color = "red";
            logOutput.innerHTML = `<div style="color:red">❌ ${data.message}</div>` + logOutput.innerHTML;
            speakText(data.message);

            // Periksa jika kegagalan disebabkan mikrofon bermasalah atau timeout
            if (data.message && (data.message.toLowerCase().includes('mikrofon') || data.message.toLowerCase().includes('timeout'))) {
                catatKegagalanSistem('Mikrofon', data.message, 'Ditawarkan Antrean Manual', 'Warning');
                tampilkanFallbackManualModal(
                    'Gangguan Mikrofon',
                    data.message + '. Silakan periksa koneksi mikrofon Anda atau gunakan Antrean Manual.',
                    'Mikrofon'
                );
            } else {
                catatKegagalanSistem('Whisper ASR', data.message || 'Suara tidak terdeteksi atau kalimat belum dikenali', 'Ditawarkan Coba Lagi / Manual', 'Warning');
            }
            return;
        }

        // 🔶 KONDISI 4: BERHASIL
        if (data.status === 'success') {
            loketNama.textContent = data.loket;
            loketNomor.textContent = data.nomor;
            logOutput.innerHTML = `<div style="color:lime">✅ Antrian Berhasil: ${data.loket} - [${data.nomor}]</div>` + logOutput.innerHTML;
            statusText.textContent = "Antrian berhasil dibuat";
            statusText.style.color = "lime";
            
            // Putar suara nomor antrean
            if (data.voice_message) {
                speakText(data.voice_message);
            }
            return;
        }

        // 🔶 KONDISI 5: ERROR SERVER (Exception Python)
        if (data.status === 'error') {
            statusText.textContent = "Error Sistem Suara";
            statusText.style.color = "red";
            speakText(data.message || "Sistem antrian sedang bermasalah");

            catatKegagalanSistem('Whisper / Python AI', data.message || 'Error internal pada modul pemrosesan suara', 'Dialihkan ke Antrean Manual', 'Error');
            tampilkanFallbackManualModal(
                'Gangguan Sistem AI Suara',
                data.message || 'Layanan suara Whisper sedang mengalami gangguan teknis.',
                'Whisper / Python AI'
            );
            return;
        }

    } catch (e) {
        console.error("Terjadi Error:", e);
        statusText.textContent = "Server suara tidak merespon";
        statusText.style.color = "red";

        // Catat kegagalan koneksi Python ke backend Laravel
        catatKegagalanSistem('Server Python', 'Koneksi ke port 5500 gagal (Server Python Flask belum berjalan atau down)', 'Dialihkan ke Antrean Manual', 'Error');

        // Tampilkan modal opsi pengalihan langsung ke Antrean Manual
        tampilkanFallbackManualModal(
            'Server Suara Tidak Tersedia',
            'Server pengenalan suara (Python/Whisper) sedang tidak aktif atau terputus.',
            'Server Python'
        );
    } finally {
        button.disabled = false;
    }
}

// --- FUNGSI MODAL QR (TETAP SAMA) ---
function showQRModal(url, duration = 10000) {
    const modal = document.getElementById('qr-modal');
    const qrImage = document.getElementById('qr-image');

    if (modal && qrImage) {
        // Melakukan enkripsi URL agar aman dibaca oleh Generator QR API
        qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}`;
        modal.classList.remove('hidden');
        clearTimeout(modalTimer);
        modalTimer = setTimeout(() => {
            closeQRModal();
        }, duration);
    }
}

function closeQRModal() {
    const modal = document.getElementById('qr-modal');
    if (modal) {
        modal.classList.add('hidden');
        clearTimeout(modalTimer);
    }
}

// ============================================================
// FUNGSI UNTUK MENGIRIM PILIHAN LOKET AMBIGU KE BACKEND
// ============================================================
window.pilihLoket = async function(idLoket) {
    Swal.close();
    button.disabled = true;
    statusText.textContent = "Memproses tiket pilihan...";
    
    try {
        // Bypass Python, tembak langsung ke Laravel API (sama seperti yang dilakukan Python)
        const res = await fetch('http://127.0.0.1:8000/antrian/store-voice', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                loket: "BYPASS_AMBIGU", // Teks bebas, karena id_loket_pilihan yang akan dipakai
                id_user: userId,
                id_loket_pilihan: idLoket,
                mode_khusus: window.isModeKhusus ? 1 : 0,
                jenis_prioritas: document.getElementById('hidden-jenis-prioritas-sementara') ? document.getElementById('hidden-jenis-prioritas-sementara').value : ''
            })
        });
        
        const data = await res.json();
        
        if (data.success) {
            loketNama.textContent = data.loket;
            loketNomor.textContent = data.nomor;
            logOutput.innerHTML = `<div style="color:lime">✅ Antrian Berhasil (via Pilihan): ${data.loket} - [${data.nomor}]</div>` + logOutput.innerHTML;
            statusText.textContent = "Antrian berhasil dibuat";
            statusText.style.color = "lime";
            
            // Putar suara nomor antrean (sama seperti sukses biasa)
            if (data.voice_message) {
                speakText(data.voice_message);
            } else {
                speakText("Nomor antrean " + data.nomor + " menuju loket " + data.loket);
            }
        } else {
            throw new Error(data.message || "Gagal memproses pilihan loket");
        }
    } catch (e) {
        console.error("Terjadi Error saat pilih loket:", e);
        statusText.textContent = "Gagal memproses tiket";
        statusText.style.color = "red";
    } finally {
        button.disabled = false;
    }
}

button.addEventListener('click', processSpeech);
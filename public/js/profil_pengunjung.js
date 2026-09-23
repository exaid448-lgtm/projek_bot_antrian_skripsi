document.addEventListener('DOMContentLoaded', function() {
    
// ==========================================
// 1. LOGIKA BAR CHART (KEPADATAN)
// ==========================================
const densityCanvas = document.getElementById('densityChart');
if (densityCanvas) {
    const ctx = densityCanvas.getContext('2d');
    const labels = JSON.parse(densityCanvas.dataset.labels || '[]');
    const dataValues = JSON.parse(densityCanvas.dataset.values || '[]');

    let gradient = ctx.createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.85)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pengunjung',
                data: dataValues,
                backgroundColor: gradient,
                borderColor: '#3B82F6',
                borderWidth: 1.5,
                borderRadius: 8,
                borderSkipped: false,
                // --- PERUBAHAN DI SINI ---
                barThickness: 40,       // Paksa lebar batang jadi 40px saja
                maxBarThickness: 50,    // Batas maksimal lebar batang
                // -------------------------
                hoverBackgroundColor: '#2563EB'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: (context) => ` Total: ${context.parsed.y} Orang`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    // Tambahkan ini agar angka Y tidak koma jika datanya sedikit
                    ticks: { 
                        color: '#9CA3AF',
                        stepSize: 1 
                    },
                    grid: { color: 'rgba(243, 244, 246, 1)', drawBorder: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6B7280' }
                }
            }
        }
    });
}

    // ==========================================
    // 2. LOGIKA DOUGHNUT CHART (RIWAYAT)
    // ==========================================
    const loketCanvas = document.getElementById('loketChart');
    if (loketCanvas) {
        const labels = JSON.parse(loketCanvas.dataset.labels || '[]');
        const dataValues = JSON.parse(loketCanvas.dataset.values || '[]');

        new Chart(loketCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#3B82F6', '#6366F1', '#22D3EE', '#E5E7EB'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 12,
                        callbacks: {
                            label: (context) => ` ${context.label}: ${context.parsed} Kunjungan`
                        }
                    }
                }
            }
        });
    }

    // ==========================================
    // 3. LOGIKA MODAL FOTO
    // ==========================================
    const elements = {
        modal: document.getElementById('photoModal'),
        changeBtn: document.getElementById('changePhotoBtn'),
        closeBtn: document.getElementById('closeModalBtn'),
        cancelBtn: document.getElementById('cancelBtn'),
        overlay: document.getElementById('modalOverlay'),
        fileInput: document.getElementById('fileInput'),
        selectBtn: document.getElementById('selectFileBtn'),
        preview: document.getElementById('previewImage')
    };

    if (elements.changeBtn) {
        const toggleModal = (show) => {
            elements.modal.classList.toggle('hidden', !show);
            document.body.style.overflow = show ? 'hidden' : 'auto';
        };

        elements.changeBtn.onclick = () => toggleModal(true);
        [elements.closeBtn, elements.cancelBtn, elements.overlay].forEach(btn => {
            if(btn) btn.onclick = () => toggleModal(false);
        });

        if (elements.selectBtn) {
            elements.selectBtn.onclick = () => elements.fileInput.click();
        }

        elements.fileInput.onchange = function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => elements.preview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        };
    }
});
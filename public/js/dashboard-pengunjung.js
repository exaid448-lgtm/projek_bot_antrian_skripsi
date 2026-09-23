function downloadTicket(cardId, nomorAntrian) {
    // 1. Ambil elemen kartu berdasarkan ID-nya
    const kartuElement = document.getElementById(cardId);

    if (!kartuElement) return;

    // 2. Cari elemen estimasi tunggu (hanya untuk antrean normal) dan tombol batal
    const estimasiBox = kartuElement.querySelector('.estimasi-box');
    const batalBtn = kartuElement.querySelector('[title="Batalkan Antrean"]');

    // 3. Sembunyikan estimasi tunggu biasa sebelum html2canvas mengambil jepretan
    if (estimasiBox) {
        estimasiBox.style.display = 'none';
    }
    if (batalBtn) {
        batalBtn.style.display = 'none';
    }

    // 4. Jalankan proses cetak gambar menggunakan html2canvas dengan resolusi tinggi
    html2canvas(kartuElement, {
        scale: 3, // Resolusi 3x agar hasil download sangat tajam dan jernih
        backgroundColor: '#ffffff',
        useCORS: true,
        logging: false,
        scrollX: 0,
        scrollY: 0,
        onclone: (clonedDoc) => {
            const style = clonedDoc.createElement('style');
            style.innerHTML = `
                img { display: inline-block !important; }
                .ticket-card * { box-sizing: border-box; }
            `;
            clonedDoc.head.appendChild(style);

            const clonedCard = clonedDoc.getElementById(cardId);
            if (clonedCard) {
                clonedCard.style.transform = 'none';
                clonedCard.style.boxShadow = 'none';
                clonedCard.style.borderRadius = '16px';
            }
        }
    }).then(canvas => {
        // Kembalikan tampilan ke layar dashboard
        if (estimasiBox) {
            estimasiBox.style.display = 'block';
        }
        if (batalBtn) {
            batalBtn.style.display = '';
        }

        // Unduh file gambar tiket
        const link = document.createElement('a');
        link.download = `Tiket-Antrian-${nomorAntrian}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }).catch(error => {
        if (estimasiBox) {
            estimasiBox.style.display = 'block';
        }
        if (batalBtn) {
            batalBtn.style.display = '';
        }
        console.error("Gagal mencetak tiket:", error);
    });
}

// Inisialisasi Diagram Bulat (Donut Chart) SKM untuk masing-masing loket
document.addEventListener("DOMContentLoaded", function () {
    const chartElements = document.querySelectorAll('.skm-chart-canvas');

    chartElements.forEach(canvas => {
        const ctx = canvas.getContext('2d');

        // Ambil data nilai yang dikirim dari element HTML data attributes
        const sangatBagus = parseInt(canvas.getAttribute('data-sangat-bagus')) || 0;
        const bagus = parseInt(canvas.getAttribute('data-bagus')) || 0;
        const kurang = parseInt(canvas.getAttribute('data-kurang')) || 0;
        const sangatKurang = parseInt(canvas.getAttribute('data-sangat-kurang')) || 0;

        // Membuat element DOM Tooltip Kustom khusus untuk card ini saja jika belum ada
        const cardContainer = canvas.closest('.bg-white');
        let customTooltip = cardContainer.querySelector('.chartjs-custom-tooltip');

        if (!customTooltip) {
            customTooltip = document.createElement('div');
            customTooltip.className = 'chartjs-custom-tooltip absolute bg-slate-900/95 text-white p-2.5 rounded-lg shadow-xl text-[10px] pointer-events-none opacity-0 transition-opacity duration-200 z-30 font-sans border border-slate-700/50 min-w-[120px]';
            // Set posisi permanen di area kosong kanan-atas di atas teks legend
            customTooltip.style.top = '72px';
            customTooltip.style.left = '145px';
            cardContainer.appendChild(customTooltip);
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Bagus', 'Bagus', 'Kurang', 'Sangat Kurang'],
                datasets: [{
                    data: [sangatBagus, bagus, kurang, sangatKurang],
                    backgroundColor: [
                        '#10b981', // emerald-500
                        '#3b82f6', // blue-500
                        '#f59e0b', // amber-500
                        '#f43f5e'  // rose-500
                    ],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                cutout: '75%',
                layout: {
                    padding: 4
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false, // Nonaktifkan tooltip bawaan canvas agar tidak merusak grafis tengah
                        external: function (context) {
                            // Handler Fungsi Tooltip Kustom HTML
                            const { tooltip } = context;

                            // Jika kursor keluar dari area lingkaran donut, sembunyikan tooltip
                            if (tooltip.opacity === 0) {
                                customTooltip.style.opacity = '0';
                                return;
                            }

                            // Ambil data konten yang sedang di-hover
                            if (tooltip.body) {
                                const title = tooltip.title[0] || '';
                                const bodyLines = tooltip.body.map(b => b.lines);

                                let innerHtml = `<div class="font-bold text-slate-300 border-b border-slate-700 pb-1 mb-1 uppercase tracking-wider text-[9px]">${title}</div>`;

                                bodyLines.forEach((body) => {
                                    // Ambil nilai murni dan hitung persentase secara akurat
                                    const dataIndex = tooltip.dataPoints[0].dataIndex;
                                    const value = context.chart.data.datasets[0].data[dataIndex];
                                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;

                                    innerHtml += `
                                        <div class="flex items-center gap-1.5 font-semibold mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: ${tooltip.labelColors[0].backgroundColor}"></span>
                                            <span>Jumlah: ${value} survei</span>
                                        </div>
                                        <div class="text-emerald-400 font-bold mt-0.5 pl-3">Porsi: ${percentage}%</div>
                                    `;
                                });

                                customTooltip.innerHTML = innerHtml;
                            }

                            // Tampilkan komponen HTML tooltip kustom
                            customTooltip.style.opacity = '1';
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
});
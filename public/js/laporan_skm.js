/**
 * Laporan & Manajemen SKM - Core Script
 */

document.addEventListener("DOMContentLoaded", function () {
    // --- 1. LOGIKA POPUP MODAL TAMBAH SOAL ---
    const modal = document.getElementById('skmModalSoal');
    const btnBuka = document.getElementById('btnBukaModal');
    const btnTutupX = document.getElementById('btnTutupModalX');
    const btnTutupBatal = document.getElementById('btnTutupModalBatal');

    if (btnBuka && modal) {
        btnBuka.addEventListener('click', function () {
            modal.classList.add('aktif');
        });

        btnTutupX.addEventListener('click', function () {
            modal.classList.remove('aktif');
        });

        btnTutupBatal.addEventListener('click', function () {
            modal.classList.remove('aktif');
        });

        window.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('aktif');
            }
        });
    }

    // --- 2. LOGIKA POPUP SWEETALERT2 UNTUK HAPUS SOAL ---
    const tombolHapus = document.querySelectorAll('.btn-hapus-soal');

    tombolHapus.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const urlTujuan = this.getAttribute('data-url');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data soal SKM ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan;
                }
            });
        });
    });

    // --- 3. LOGIKA GRAFIK CHART.JS ---
    const ctx = document.getElementById('skmChart');
    if (ctx) {
        // Mengambil data JSON yang disuntikkan ke window object dari Blade
        const chartLabels = window.skmChartData ? window.skmChartData.labels : [];
        const rawDatasets = window.skmChartData && window.skmChartData.datasets ? window.skmChartData.datasets : [];

        // Array warna cantik untuk garis grafik
        const colors = [
            'rgba(54, 162, 235, 1)',  // Biru
            'rgba(255, 99, 132, 1)',  // Merah
            'rgba(75, 192, 192, 1)',  // Hijau
            'rgba(255, 159, 64, 1)',  // Oranye
            'rgba(153, 102, 255, 1)', // Ungu
        ];

        const chartDatasets = rawDatasets.map((ds, index) => {
            const color = colors[index % colors.length];
            return {
                label: ds.label,
                data: ds.data,
                borderColor: color,
                backgroundColor: color,
                borderWidth: 2,
                tension: 0.3, // Garis melengkung halus
                pointBackgroundColor: '#ffffff',
                pointBorderColor: color,
                pointRadius: 4,
                pointHoverRadius: 6
            };
        });

        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: chartDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
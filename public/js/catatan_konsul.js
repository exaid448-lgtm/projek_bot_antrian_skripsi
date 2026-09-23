document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('catatanChart').getContext('2d');
    
    if (ctx) {
        // Create a more vibrant gradient for the area fill
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');     // Blue 600 with transparency
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');       // Fully transparent at bottom

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.weight = '600';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'], // Example days for a smoother line
                datasets: [{
                    label: 'Jumlah Konsultasi',
                    data: [12, 19, 15, 25, 22, 30, 28],
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4 // Makes the line smooth/curvy
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 12, weight: '800' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Konsultasi';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(241, 245, 249, 1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 10 },
                            stepSize: 10
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
    }
});
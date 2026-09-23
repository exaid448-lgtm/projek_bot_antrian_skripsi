document.addEventListener('alpine:init', () => {
    Alpine.data('historyAntrian', (initialdata) => ({
        filter: 'all',
        filterStartDate: '', 
        filterEndDate: '',
        selectedInstansi: 'all',
        isModalOpen: false, 
        selectedTicket: {}, 
        histories: initialdata,

        get filteredHistory() {
            return this.histories.filter(h => {
                const matchStatus = (this.filter === 'all' || h.status === this.filter);
                const matchInstansi = (this.selectedInstansi === 'all' || h.instansi === this.selectedInstansi);
                
                // Logika Filter Rentang Tanggal
                let matchDate = true;
                if (this.filterStartDate || this.filterEndDate) {
                    if (h.tanggal_raw) {
                        const hDate = new Date(h.tanggal_raw);
                        const start = this.filterStartDate ? new Date(this.filterStartDate) : null;
                        const end = this.filterEndDate ? new Date(this.filterEndDate) : null;
                        
                        // Setel end date ke akhir hari agar data di hari yang sama terbaca
                        if (end) end.setHours(23, 59, 59, 999);
                        
                        if (start && end) {
                            matchDate = (hDate >= start && hDate <= end);
                        } else if (start) {
                            matchDate = (hDate >= start);
                        } else if (end) {
                            matchDate = (hDate <= end);
                        }
                    } else {
                        matchDate = false; // Jika history tidak ada tanggal dan rentang tanggal diisi
                    }
                }

                return matchStatus && matchInstansi && matchDate;
            });
        },

        resetFilter() {
            this.filter = 'all';
            this.filterStartDate = '';
            this.filterEndDate = '';
            this.selectedInstansi = 'all';
        },

        openDetail(item) {
            this.selectedTicket = { ...item };
            this.isModalOpen = true;
        },

        closeDetail() {
            this.isModalOpen = false;
        },

        // PINDAHKAN FUNGSI KE SINI
        downloadTicket(elementId, ticketNumber) {
            const element = document.getElementById(elementId);
            // Cari tombol di dalam elemen tersebut
            const btn = element.querySelector('button'); 

            if (btn) btn.style.display = 'none'; // Sembunyikan tombol sementara

            html2canvas(element, {
                scale: 2,
                backgroundColor: "#ffffff",
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL("image/png");
                link.download = `Tiket-Antrian-${ticketNumber}.png`;
                link.click();

                if (btn) btn.style.display = 'flex'; // Tampilkan kembali tombolnya
            });
        }
    }));
});
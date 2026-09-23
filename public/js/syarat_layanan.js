document.addEventListener('alpine:init', () => {
    Alpine.data('syaratLayanan', () => ({
        search: '',
        showModal: false,
        selectedData: { 
            nama_loket: '', 
            logo: '', 
            syarat: [] 
        },
        instansi: [],

        initData(data) {
            // Log ini untuk memastikan data masuk ke JS
            console.log("Data diterima Alpine dari Blade:", data);
            
            // Simpan data ke variabel instansi
            this.instansi = Array.isArray(data) ? data : Object.values(data);
        },

        openModal(item) {
            console.log("Membuka Modal untuk:", item);
            this.selectedData = item;
            this.showModal = true;
        }
    }));
});
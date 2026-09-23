$(function () {
    // Inisialisasi DataTable tetap sama
    const table = $('#tabelKonsultasi').DataTable({
        "dom": 't',          
        "paging": false,     
        "ordering": false,   
        "info": false,       
        "autoWidth": false,  
    });

    // 1. HAPUS atau KOMENTARI bagian 'keyup input' agar tidak mencari otomatis saat mengetik
    /*
    $('#customSearch').on('keyup input', function () {
        table.search(this.value).draw();
    });
    */

    // 2. TANGANI PENCARIAN HANYA SAAT TOMBOL DIKLIK
    $('#btnSearch').on('click', function (e) {
        e.preventDefault(); // Mencegah form reload
        
        const val = $('#customSearch').val(); // Ambil nilai dari input
        table.search(val).draw(); // Jalankan filter pada tabel
    });

    // 3. OPSIONAL: Jika ingin pencarian jalan saat tekan 'Enter' di keyboard
    $('#customSearch').on('keypress', function (e) {
        if (e.which == 13) { // 13 adalah kode tombol Enter
            e.preventDefault();
            const val = $(this).val();
            table.search(val).draw();
        }
    });
});
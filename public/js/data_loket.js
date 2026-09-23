document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formLoket');
    const modalTitle = document.getElementById('modalTitle');

    // MUNCULKAN MODAL TAMBAH
    window.showAddModal = function() {
        modalTitle.innerText = "Tambah Data Loket";
        form.action = "/data-loket/store"; // Sesuaikan route
        document.getElementById('methodField').innerHTML = '';
        form.reset();
        openModal('modalLoket');
    }

    // MUNCULKAN MODAL EDIT
    window.showEditModal = function(data) {
        const form = document.getElementById('formLoket');
        const modalTitle = document.getElementById('modalTitle');
        
        modalTitle.innerText = "Edit Data Loket";
        // Set action URL ke route update dengan ID yang benar
        form.action = "/data-loket/update/" + data.id_loket; 
        
        // Tambahkan input hidden method POST jika rute kamu pakai POST
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="POST">';
        
        // Isi field input
        document.getElementById('in_nama_loket').value = data.nama_loket;
        document.getElementById('in_nama_pelayanan').value = data.nama_pelayanan;
        document.getElementById('in_lokasi').value = data.lokasi_loket;
        document.getElementById('in_prefix').value = data.prefix;
        
        openModal('modalLoket');
    };


    // MUNCULKAN MODAL DELETE
    window.showDeleteModal = function(id) {
        document.getElementById('formDelete').action = "/data-loket/delete/" + id;
        openModal('modalDelete');
    }

    // Fungsi Global Open/Close
    window.openModal = (id) => document.getElementById(id).classList.add('show');
    window.closeModal = (id) => document.getElementById(id).classList.remove('show');
});
document.addEventListener('DOMContentLoaded', function () {
    // ... kode modal yang sudah ada tetap di sini ...

    const searchInput = document.getElementById('searchInput');
    const btnSearch = document.getElementById('btnSearch');
    const tableRows = document.querySelectorAll('.table-basic tbody tr:not(#noDataRow)');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        let hasMatch = false;

        tableRows.forEach(row => {
            // Mengambil teks dari kolom Nama Loket dan Nama Pelayanan
            const namaLoket = row.cells[1].textContent.toLowerCase();
            const namaPelayanan = row.cells[2].textContent.toLowerCase();

            if (namaLoket.includes(searchTerm) || namaPelayanan.includes(searchTerm)) {
                row.style.display = ""; 
                hasMatch = true;
            } else {
                row.style.display = "none"; 
            }
        });

        // Menampilkan pesan jika data tidak ditemukan
        const noDataRow = document.getElementById('noDataRow');
        if (noDataRow) {
            noDataRow.style.display = hasMatch ? "none" : "";
        }
    }

    // Jalankan pencarian saat tombol diklik
    if (btnSearch) {
        btnSearch.addEventListener('click', function () {
            performSearch();
        });
    }

    // (Opsional) Tetap jalankan pencarian jika user menekan tombol 'Enter' di keyboard
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
});
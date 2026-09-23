// Fungsi Pencarian
document.addEventListener('DOMContentLoaded', function () {
    const btnSearch = document.getElementById('btnSearch');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('algoritmaTableBody');

    if (btnSearch) {
        btnSearch.addEventListener('click', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const text = rows[i].textContent.toLowerCase();
                rows[i].style.display = text.includes(filter) ? "" : "none";
            }
        });
    }
});

// Fungsi Modal
window.openModal = function(mode, id = '', algo = '', tgl = '', tipe = '') {
    const modal = document.getElementById("modalAlgoritma");
    const form = document.getElementById("formAlgoritma");
    const title = document.getElementById("modalTitle");
    const methodField = document.getElementById("methodField");

    if (mode === 'edit') {
        title.innerText = "Edit Data Algoritma";
        form.action = "/algoritma/update/" + id;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById("in_algoritma").value = algo;
        if (tgl) {
            document.getElementById("in_tanggal").value = tgl.replace(' ', 'T').substring(0, 16);
        }
        document.getElementById("in_tipe").value = tipe;
    } else {
        title.innerText = "Tambah Algoritma Baru";
        form.action = "/algoritma/store";
        methodField.innerHTML = "";
        form.reset();
    }
    modal.style.display = "flex";
}

window.closeModal = function() {
    document.getElementById("modalAlgoritma").style.display = "none";
}

// Fungsi Pop-up Konfirmasi Hapus (SweetAlert2)
window.confirmDelete = function(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#bdc3c7',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}

window.onclick = function(event) {
    const modal = document.getElementById("modalAlgoritma");
    if (event.target == modal) {
        closeModal();
    }
}
/* public/js/notif_masalah.js */

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "flex";
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    }
}

function openModalEdit(data) {
    document.getElementById('edit_judul_info').value = data.judul_info;
    document.getElementById('edit_kategori_info').value = data.kategori_info;
    document.getElementById('edit_status_info').value = data.status_info;
    document.getElementById('edit_deskripsi_info').value = data.deskripsi_info;
    document.getElementById('edit_solusi_info').value = data.solusi_info;

    const formEdit = document.getElementById('formEditJadwal');
    if (formEdit) {
        formEdit.action = "/admin-loket/notif-bermasalah/update/" + data.id_informasi;
    }

    openModal('modalEdit');
}

/**
 * Fungsi untuk memicu kemunculan Modal Hapus Custom
 * @param {number} id - ID Informasi
 * @param {string} judul - Judul Pemberitahuan
 */
function openModalHapus(id, judul) {
    // Set teks judul informasi yang mau dihapus ke dalam modal konfirmasi
    document.getElementById('text_judul_hapus').innerText = `"${judul}"`;

    // Set route action secara dinamis ke form delete
    const formHapus = document.getElementById('formHapusData');
    if (formHapus) {
        formHapus.action = "/admin-loket/notif-bermasalah/delete/" + id;
    }

    // Buka modal hapus
    openModal('modalHapus');
}

window.addEventListener('click', function (event) {
    if (event.target.classList.contains('custom-modal')) {
        event.target.style.display = "none";
    }
});
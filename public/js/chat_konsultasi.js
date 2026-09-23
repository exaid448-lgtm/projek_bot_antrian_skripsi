// Auto-scroll ke chat terbawah saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    const chatContainer = document.getElementById('chat-messages');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
});

/**
 * Mengatur buka-tutup dropdown menu pada chat bubble
 */
function toggleDropdown(id, event) {
    event.stopPropagation();

    // Tutup semua dropdown lain yang sedang terbuka
    const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
    dropdowns.forEach(d => {
        if (d.id !== id) d.classList.add('hidden');
    });

    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Event listener global untuk menutup dropdown jika klik di luar area menu
document.addEventListener('click', function () {
    const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
    dropdowns.forEach(d => d.classList.add('hidden'));
});

/**
 * MODAL EDIT PESAN LOGIC
 */
function openEditModal(id, pesanText) {
    const modal = document.getElementById('edit-modal');
    const form = document.getElementById('edit-form');
    const textarea = document.getElementById('edit-textarea');

    if (modal && form && textarea) {
        // Atur action route Laravel secara dinamis
        form.action = `/chat-konsultasi/edit/${id}`;
        textarea.value = pesanText;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

/**
 * NEW: MODAL KONFIRMASI HAPUS PESAN LOGIC
 */
function openDeleteModal(deleteUrl) {
    const modal = document.getElementById('delete-modal');
    const form = document.getElementById('delete-form');
    const card = document.getElementById('delete-modal-card');

    if (modal && form) {
        form.action = deleteUrl; // Isi endpoint tujuan hapus
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Animasi pop-in efek halus
        setTimeout(() => {
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    const card = document.getElementById('delete-modal-card');

    if (modal && card) {
        card.classList.remove('scale-100');
        card.classList.add('scale-95');

        // Tunggu transisi selesai sebelum menutup container backdrop
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
    }
}

/**
 * LOGIK INPUT ATTACHMENT FILE PREVIEW
 */
function handleFileSelect(input) {
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');

    if (input.files && input.files[0] && filePreview && fileName) {
        fileName.textContent = input.files[0].name;
        filePreview.classList.remove('hidden');
    }
}

function clearFile() {
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');

    if (fileInput && filePreview) {
        fileInput.value = '';
        filePreview.classList.add('hidden');
    }
}

// Logika Fitur Pencarian Instansi / Loket
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-instansi');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const filterValue = this.value.toLowerCase().trim();
            const instansiItems = document.querySelectorAll('.instansi-item');

            instansiItems.forEach(function (item) {
                const namaInstansi = item.getAttribute('data-nama') || '';

                if (namaInstansi.includes(filterValue)) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        });
    }
});
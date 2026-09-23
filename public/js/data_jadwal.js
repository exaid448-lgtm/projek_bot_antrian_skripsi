document.addEventListener('DOMContentLoaded', function() {
    // Logic Custom Dropdown
    const dropdown = document.getElementById('dropdownLoket');
    if (dropdown) {
        const header = dropdown.querySelector('.dropdown-header');
        const items = dropdown.querySelectorAll('.dropdown-item');
        const hiddenInput = document.getElementById('selectedLoket');
        const selectedText = dropdown.querySelector('.selected-value');

        header.addEventListener('click', () => dropdown.classList.toggle('active'));

        items.forEach(item => {
            item.addEventListener('click', function() {
                let val = this.getAttribute('data-value');
                hiddenInput.value = val;
                selectedText.innerText = this.innerText;
                dropdown.classList.remove('active');
            });
        });
    }
});

// Logic Modal
function openModal(mode, data = null, url = '') {
    const modal = document.getElementById('modalJadwal');
    const form = document.getElementById('formJadwal');
    const methodField = document.getElementById('methodField');

    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);

    form.reset();
    form.action = url; // Gunakan URL yang dikirim dari Blade

    if (mode === 'tambah') {
        document.getElementById('modalTitle').innerText = "Tambah Jadwal";
        methodField.innerHTML = ''; 
        document.getElementById('selectKaryawan').disabled = true;
    } else {
        document.getElementById('modalTitle').innerText = "Edit Jadwal";
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Isi field lainnya
        document.getElementById('inputTanggal').value = data.tanggal;
        document.getElementById('inputJamMasuk').value = data.jam_masuk;
        document.getElementById('inputJamPulang').value = data.jam_pulang;
        document.getElementById('selectShift').value = data.shift;
        document.getElementById('selectStatus').value = data.setatus;

        if(data.profil && data.profil.id_loket) {
            document.getElementById('selectLoket').value = data.profil.id_loket;
            // Load karyawan dan set yang terpilih
            loadKaryawan(data.profil.id_loket, data.id_profil);
        }
    }
}

function closeModal() {
    const modal = document.getElementById('modalJadwal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

async function loadKaryawan(idLoket, selectedUserId = null) {
    const selectKaryawan = document.getElementById('selectKaryawan');

    if (!idLoket) {
        selectKaryawan.disabled = true;
        selectKaryawan.innerHTML = '<option value="">-- Pilih Loket Dahulu --</option>';
        return;
    }

    try {
        const response = await fetch(`/api/get-karyawan/${idLoket}`);

        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }

        const data = await response.json();

        selectKaryawan.disabled = false;
        selectKaryawan.innerHTML = '<option value="">-- Pilih Karyawan --</option>';

        if (data.length === 0) {
            selectKaryawan.innerHTML = '<option value="">Tidak ada karyawan</option>';
            return;
        }

        data.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = user.name;

            if (selectedUserId && user.id == selectedUserId) {
                option.selected = true;
            }

            selectKaryawan.appendChild(option);
        });

    } catch (error) {
        console.error(error);
        selectKaryawan.innerHTML = '<option value="">Gagal memuat data</option>';
        selectKaryawan.disabled = true;
    }
}

// Close on outside click
window.onclick = (e) => {
    if (e.target.classList.contains('modal-overlay')) closeModal();
};
// Tambahan agar klik di area manapun pada input tanggal modal membuka kalender
document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'inputTanggal') {
        try {
            e.target.showPicker(); // Fungsi standar browser modern untuk buka kalender
        } catch (err) {
            console.log("Browser tidak mendukung showPicker secara langsung.");
        }
    }
});
// Fungsi untuk membuka modal hapus
function confirmDelete(url) {
    const modal = document.getElementById('modalDelete');
    const form = document.getElementById('formDelete');
    
    form.action = url; 
    
    // Tampilkan modal
    modal.style.display = 'flex';
    // Beri jeda sedikit agar animasi CSS 'show' berjalan
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

// Fungsi untuk menutup modal hapus
function closeDeleteModal() {
    const modal = document.getElementById('modalDelete');
    modal.classList.remove('show');
    
    // Tunggu animasi selesai baru hilangkan display
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Tambahan: Update window.onclick agar bisa menutup modal hapus juga jika klik di luar
window.onclick = (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal();
        closeDeleteModal();
    }
    
};

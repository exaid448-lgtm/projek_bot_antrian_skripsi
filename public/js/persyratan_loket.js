document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('modalPersyaratan');
    const btnBuka = document.getElementById('btnBukaModalSyarat');
    const btnTutupX = document.getElementById('btnTutupX');
    const btnBatal = document.getElementById('btnBatal');

    const formPersyaratan = document.getElementById('formPersyaratan');
    const modalTitleText = document.getElementById('modalTitleText');
    const methodSpoofingContainer = document.getElementById('methodSpoofingContainer');
    const btnSubmitModal = document.getElementById('btnSubmitModal');

    const inputNama = document.getElementById('nama_syarat');
    const inputTipe = document.getElementById('tipe_syarat');
    const inputKeterangan = document.getElementById('keterangan');

    const inputUrlStore = document.getElementById('urlStoreRoute');
    const inputUrlUpdate = document.getElementById('urlUpdateRoute');

    const urlStore = inputUrlStore ? inputUrlStore.value : '';
    const urlUpdate = inputUrlUpdate ? inputUrlUpdate.value : '';

    // 1. MODAL TAMBAH DATA
    if (btnBuka && modal && formPersyaratan) {
        btnBuka.addEventListener('click', function () {
            formPersyaratan.reset();
            formPersyaratan.action = urlStore;
            if (methodSpoofingContainer) methodSpoofingContainer.innerHTML = '';

            if (modalTitleText) {
                modalTitleText.innerHTML = '<i class="fa fa-folder-plus" style="color: #3b82f6; margin-right: 8px;"></i> Input Persyaratan Baru';
            }
            if (btnSubmitModal) btnSubmitModal.innerText = 'Simpan Data';

            modal.classList.add('aktif');
        });
    }

    // 2. MODAL UPDATE / EDIT DATA
    document.querySelectorAll('.btn-edit-trigger').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const tipe = this.getAttribute('data-tipe');
            const keterangan = this.getAttribute('data-keterangan');

            if (inputNama) inputNama.value = nama;
            if (inputTipe) inputTipe.value = tipe;
            if (inputKeterangan) inputKeterangan.value = keterangan;

            if (formPersyaratan) formPersyaratan.action = `${urlUpdate}/${id}`;
            if (methodSpoofingContainer) {
                methodSpoofingContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            }

            if (modalTitleText) {
                modalTitleText.innerHTML = '<i class="fa fa-edit" style="color: #f59e0b; margin-right: 8px;"></i> Edit Persyaratan Layanan';
            }
            if (btnSubmitModal) btnSubmitModal.innerText = 'Perbarui Data';

            if (modal) modal.classList.add('aktif');
        });
    });

    // 3. POPUP CONFIRM HAPUS (SweetAlert2)
    document.querySelectorAll('.btn-hapus-trigger').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const namaSyarat = this.getAttribute('data-nama') || 'Data ini';
            const formHapus = document.getElementById('formHapusData');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Persyaratan "${namaSyarat}" akan dihapus secara permanen dari loket ini!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa fa-trash-alt"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-border-radius-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed && formHapus) {
                        formHapus.action = `/admin-loket/persyaratan/delete/${id}`;
                        formHapus.submit();
                    }
                });
            } else {
                // Fallback jika CDN SweetAlert2 gagal termuat
                if (confirm(`Apakah Anda yakin ingin menghapus "${namaSyarat}"?`)) {
                    if (formHapus) {
                        formHapus.action = `/admin-loket/persyaratan/delete/${id}`;
                        formHapus.submit();
                    }
                }
            }
        });
    });

    // FUNGSI TUTUP MODAL
    const tutupModal = () => {
        if (modal) modal.classList.remove('aktif');
    };

    if (btnTutupX) btnTutupX.addEventListener('click', tutupModal);
    if (btnBatal) btnBatal.addEventListener('click', tutupModal);

    window.addEventListener('click', function (e) {
        if (e.target === modal) tutupModal();
    });
});
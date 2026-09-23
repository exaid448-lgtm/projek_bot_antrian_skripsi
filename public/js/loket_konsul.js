document.addEventListener('DOMContentLoaded', () => {
    let selectedId = null;

    const modal = document.getElementById('modalKonfirmasi');
    const btnBatal = document.getElementById('btnBatal');
    const btnUbah = document.getElementById('btnUbah');

    // 1. Fungsi untuk Membuka Modal (Menggunakan Event Delegation agar tombol baru tetap berfungsi)
    document.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('btn-status-belum')) {
            selectedId = e.target.dataset.id;
            modal.classList.add('active'); // Menggunakan class active sesuai CSS
            modal.style.display = 'flex';
        }
    });

    // 2. Fungsi Batal
    btnBatal.addEventListener('click', () => {
        modal.classList.remove('active');
        modal.style.display = 'none';
        selectedId = null;
    });

    // 3. Klik di luar modal untuk menutup
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
            modal.style.display = 'none';
        }
    });

    // 4. Ubah Status via Fetch API
    btnUbah.addEventListener('click', () => {
        if (!selectedId) return;

        // Beri feedback loading pada tombol
        const originalText = btnUbah.innerText;
        btnUbah.innerText = 'Proses...';
        btnUbah.disabled = true;

        fetch(window.LOKET_KONSUL.updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.LOKET_KONSUL.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id_konsul: selectedId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // UPDATE DISINI: Menggunakan class yang sesuai dengan CSS (badge selesai)
                const statusContainer = document.getElementById('status-' + selectedId);
                if (statusContainer) {
                    statusContainer.innerHTML = '<span class="badge selesai">SUDAH</span>';
                }
            } else {
                alert('Gagal mengubah status: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan koneksi ke server.');
        })
        .finally(() => {
            // Kembalikan state tombol dan tutup modal
            btnUbah.innerText = originalText;
            btnUbah.disabled = false;
            modal.classList.remove('active');
            modal.style.display = 'none';
            selectedId = null;
        });
    });
});
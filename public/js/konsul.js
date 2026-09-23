document.addEventListener('DOMContentLoaded', function () {
    const dropdownLoket = document.getElementById('dropdown-loket');
    const dropdownLayanan = document.getElementById('dropdown-layanan');
    const hiddenLayanan = document.getElementById('hidden-layanan');

    // Ambil semua elemen dropdown di halaman
    const allDropdowns = document.querySelectorAll('.dropdown-custom');

    allDropdowns.forEach(dropdown => {
        const header = dropdown.querySelector('.dropdown-header');
        const list = dropdown.querySelector('.dropdown-list');
        const span = dropdown.querySelector('span');
        const items = dropdown.querySelectorAll('.dropdown-list li');

        // 1. Fungsi Klik Header Dropdown
        header.addEventListener('click', function (e) {
            // Jika dropdown layanan masih disabled, jangan lakukan apa-apa
            if (dropdown.classList.contains('disabled-style')) return;

            // Tutup dropdown lain yang sedang terbuka
            allDropdowns.forEach(other => {
                if (other !== dropdown) other.classList.remove('active');
            });

            // Toggle dropdown yang diklik
            dropdown.classList.toggle('active');
        });

        // 2. Fungsi Klik Item di dalam List
        items.forEach(item => {
            item.addEventListener('click', function () {
                const selectedValue = this.getAttribute('data-value');
                span.innerText = this.innerText; // Ubah teks tampilan header
                
                // Masukkan nilai ke hidden input di dalam dropdown tersebut (jika ada)
                const hiddenInput = dropdown.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = selectedValue;
                }

                dropdown.classList.remove('active');

                // 3. LOGIKA KHUSUS: Jika yang dipilih adalah LOKET
                if (dropdown.id === 'dropdown-loket') {
                    filterLayanan(selectedValue);
                }
            });
        });
    });

    // 4. Fungsi untuk Memfilter Layanan berdasarkan Loket
    function filterLayanan(namaLoket) {
        // Aktifkan dropdown layanan (Hapus style disabled)
        dropdownLayanan.classList.remove('disabled-style');

        // Reset pilihan layanan sebelumnya
        const spanLayanan = dropdownLayanan.querySelector('.dropdown-header span');
        spanLayanan.innerText = 'pilih layanan';
        hiddenLayanan.value = '';

        // Ambil semua list layanan
        const listItemsLayanan = dropdownLayanan.querySelectorAll('.dropdown-list li');

        listItemsLayanan.forEach(li => {
            const parentLoket = li.getAttribute('data-parent');

            // Jika nama_loket cocok, tampilkan. Jika tidak, sembunyikan.
            if (parentLoket === namaLoket) {
                li.style.display = 'block';
            } else {
                li.style.display = 'none';
            }
        });
    }

    // 5. Klik di luar area dropdown untuk menutup menu
    window.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown-custom')) {
            allDropdowns.forEach(d => d.classList.remove('active'));
        }
    });
});
// Tambahkan ini di bagian paling bawah file konsul.js
document.addEventListener('DOMContentLoaded', function() {
    const notif = document.getElementById('notif');
    if (notif) {
        // Tunggu 4 detik, lalu beri class fade-out
        setTimeout(() => {
            notif.classList.add('fade-out');
            // Hapus elemen dari DOM setelah animasi transisi selesai (0.5 detik)
            setTimeout(() => {
                notif.remove();
            }, 500);
        }, 4000);
    }
});
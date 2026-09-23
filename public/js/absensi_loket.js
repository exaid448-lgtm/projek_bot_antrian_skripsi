document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('dropdownAbsen');
    const selectedText = document.getElementById('selectedText');
    const statusValue = document.getElementById('statusValue');
    const options = document.querySelectorAll('.dropdown-menu li');

    if(dropdown) {
        // 1. Logika Klik Dropdown
        dropdown.addEventListener('click', function(e) {
            this.classList.toggle('active');
            e.stopPropagation();
        });

        // 2. Pilih Opsi
        options.forEach(option => {
            option.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                selectedText.innerText = val;
                statusValue.value = val;
            });
        });

        // 3. Klik di luar untuk menutup dropdown
        document.addEventListener('click', function() {
            dropdown.classList.remove('active');
        });
    }

    // Modal Ajukan Izin
    const btnOpenModalIzin = document.getElementById('btnOpenModalIzin');
    const btnCloseModalIzin = document.getElementById('btnCloseModalIzin');
    const modalIzin = document.getElementById('modalIzin');

    if(btnOpenModalIzin) {
        btnOpenModalIzin.addEventListener('click', () => {
            modalIzin.style.display = 'flex';
        });
    }

    if(btnCloseModalIzin) {
        btnCloseModalIzin.addEventListener('click', () => {
            modalIzin.style.display = 'none';
        });
    }

    // Close on outside click
    window.addEventListener('click', (e) => {
        if(e.target === modalIzin) {
            modalIzin.style.display = 'none';
        }
    });

    const fileInputBaru = document.getElementById('suratIzinBaru');
    if(fileInputBaru) {
        fileInputBaru.addEventListener('change', function() {
            // Optional display of selected file name could go here if needed
        });
    }
});
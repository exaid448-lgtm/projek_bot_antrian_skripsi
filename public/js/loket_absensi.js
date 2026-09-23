document.addEventListener('DOMContentLoaded', function() {
    // Referensi Elemen
    const dropdown = document.getElementById('dropdownAbsen');
    const selectedText = document.getElementById('selectedText');
    const statusValue = document.getElementById('statusValue');
    const options = dropdown.querySelectorAll('.dropdown-menu li');
    const uploadContainer = document.getElementById('uploadContainer');
    const fileInput = document.getElementById('suratIzin');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    // 1. Logika Klik Dropdown (Buka/Tutup)
    dropdown.addEventListener('click', function(e) {
        this.classList.toggle('active');
        e.stopPropagation();
    });

    // 2. Logika Pilih Opsi
    options.forEach(option => {
        option.addEventListener('click', function() {
            const val = this.getAttribute('data-value');
            const text = this.innerText;

            // Update Teks dan Input Hidden
            selectedText.innerText = text;
            statusValue.value = val;

            // Logika Tampilan Upload (Hanya jika "Izin")
            if (val.toLowerCase() === 'izin') {
                uploadContainer.style.display = 'block';
            } else {
                uploadContainer.style.display = 'none';
            }

            dropdown.classList.remove('active');
        });
    });

    // 3. Tutup dropdown jika klik di luar elemen
    document.addEventListener('click', function() {
        dropdown.classList.remove('active');
    });

    // 4. Update Nama File saat dipilih
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.innerText = "File: " + this.files[0].name;
                fileNameDisplay.style.color = "#00a2ff";
            }
        });
    }
});
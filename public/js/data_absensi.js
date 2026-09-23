document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.custom-dropdown');
    const header = document.querySelector('.dropdown-header');
    const items = document.querySelectorAll('.dropdown-item');
    const selectedValueText = document.querySelector('.selected-value');
    const hiddenInput = document.getElementById('selectedLoket'); // AMBIL INPUT HIDDEN

    if (header) {
        header.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });
    }

    items.forEach(item => {
        item.addEventListener('click', function() {
            const val = this.textContent;
            if (selectedValueText) selectedValueText.textContent = val;
            if (hiddenInput) hiddenInput.value = val; // MASUKKAN NILAI KE INPUT FORM
            dropdown.classList.remove('active');
        });
    });

    // Kalender (Tetap seperti milik Anda)
    const calendarBtns = document.querySelectorAll('.calendar-btn-custom');
    calendarBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const inputDate = this.parentElement.querySelector('input[type="date"]');
            if (inputDate) inputDate.showPicker();
        });
    });

    document.addEventListener('click', function(e) {
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

});
// Fungsi Membuka Modal
function openResetModal() {
    document.getElementById('resetModal').classList.add('show');
}

// Fungsi Menutup Modal
function closeResetModal() {
    document.getElementById('resetModal').classList.remove('show');
}

// Menutup modal jika klik di luar area kotak modal
window.onclick = function(event) {
    let modal = document.getElementById('resetModal');
    if (event.target == modal) {
        closeResetModal();
    }
}

// Simulasi Klik Tombol Riset (Fokus Frontend)
function handleReset() {
    const emailInput = document.getElementById('resetEmail');
    const email = emailInput.value.trim();
    if(!email) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan masukkan email terlebih dahulu.'
        });
        return;
    }

    // Validasi format email dasar
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Format email tidak valid.'
        });
        return;
    }

    // Tampilkan loading spinner
    Swal.fire({
        title: 'Mengirim...',
        text: 'Silakan tunggu, kami sedang mengirim link reset ke email Anda.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Ambil CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/reset-password/request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonColor: '#3085d6',
            });
            closeResetModal();
            emailInput.value = '';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan.',
                confirmButtonColor: '#d33',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan koneksi ke server atau email tidak terdaftar.',
            confirmButtonColor: '#d33',
        });
    });
}
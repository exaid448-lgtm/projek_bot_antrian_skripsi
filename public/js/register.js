document.addEventListener('DOMContentLoaded', function() {
    const regForm = document.getElementById('regForm');
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const btnNext = document.getElementById('btn-next');
    const btnPrev = document.getElementById('btn-prev');
    const stepTitle = document.getElementById('step-title');
    const password = document.querySelector('#password');
    const togglePassword = document.querySelector('#togglePassword');

    // --- 1. Logika Pindah Step ---
    btnNext.addEventListener('click', function() {
        const inputs = step1.querySelectorAll('input[required]');
        let valid = true;
        
        inputs.forEach(input => { 
            if(!input.value) {
                valid = false;
                input.style.borderBottom = "2px solid red";
            } else {
                input.style.borderBottom = "2px solid #ccc";
            }
        });

        // Cek Radio Jenis Kelamin
        const gender = document.querySelector('input[name="jenis_kelamin"]:checked');
        if(!gender) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Pilih jenis kelamin dulu ya!',
                confirmButtonColor: '#3085d6',
            });
            valid = false;
        }

        if(valid) {
            step1.style.display = 'none';
            step2.style.display = 'block';
            stepTitle.innerText = "Keamanan Akun (2/2)";
        }
    });

    btnPrev.addEventListener('click', function() {
        step2.style.display = 'none';
        step1.style.display = 'block';
        stepTitle.innerText = "Daftar Akun (1/2)";
    });

    // --- 2. Toggle Password ---
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // --- 3. PROSES SUBMIT (PENTING AGAR TIDAK LAYAR PUTIH) ---
    regForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah reload halaman

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                // Laravel butuh CSRF token, sudah diambil otomatis dari @csrf di form
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#3085d6',
                }).then(() => {
                    window.location.href = "/pengunjung"; // Redirect ke halaman login
                });
            } else {
                // Tampilkan error validasi (seperti password kurang dari 6 karakter)
                let errorMessage = "";
                for (let key in data.errors) {
                    errorMessage += data.errors[key][0] + "\n";
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Daftar',
                    text: errorMessage || data.message || 'Terjadi kesalahan saat mendaftar.',
                    confirmButtonColor: '#d33',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan koneksi ke server.',
                confirmButtonColor: '#d33',
            });
        });
    });
});
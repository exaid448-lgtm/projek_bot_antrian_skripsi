// public/js/navbar-pengunjung.js

document.addEventListener('DOMContentLoaded', function () {
    const logoutLink = document.getElementById('logout-link');
    const logoutForm = document.getElementById('logout-form');

    if (logoutLink && logoutForm) {
        logoutLink.addEventListener('click', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Logout?',
                text: "apa kah anda yakin ingin logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'logout',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    logoutForm.submit();
                }
            });
        });
    }
});
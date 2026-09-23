    const btnLogout = document.getElementById('btnLogout');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');

    btnLogout.addEventListener('click', function (e) {
        e.preventDefault();
        logoutModal.style.display = 'flex';
    });

    cancelLogout.addEventListener('click', function () {
        logoutModal.style.display = 'none';
    });


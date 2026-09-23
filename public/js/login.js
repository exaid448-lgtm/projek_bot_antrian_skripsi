

  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#password');

  togglePassword.addEventListener('click', function (e) {
    // toggle tipe input
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // toggle ikon mata
    this.classList.toggle('fa-eye-slash');
  });

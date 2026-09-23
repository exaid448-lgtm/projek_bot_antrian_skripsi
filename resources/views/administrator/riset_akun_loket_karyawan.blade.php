<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riset Akun Karyawan - MPP Kota Banjarbaru</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bg-mpp {
            background-image: url("{{ asset('img/backgraund_login/register.jpg') }}");
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-mpp h-screen w-screen flex items-center justify-center relative overflow-hidden font-sans">

    <div class="absolute inset-0 bg-black/50 z-0"></div>

    <div class="relative z-10 w-full max-w-md mx-4 p-8 bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/20">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 text-red-500 rounded-2xl mb-4 shadow-inner">
                <i class="fas fa-key text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Riset Akun Karyawan</h2>
            <p class="text-sm text-gray-500 mt-2">Atur ulang password atau username akun Anda demi keamanan.</p>
        </div>

        <form action="{{ route('riset_akun.proses') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="space-y-5">
                <div class="relative">
                    <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Konfirmasi Email Anda</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:bg-white transition-all text-gray-700"
                            placeholder="Masukkan email terdaftar">
                    </div>
                </div>

                <div class="relative">
                    <label for="username" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Username Baru (Opsional)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:bg-white transition-all text-gray-700"
                            placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                </div>

                <div class="relative">
                    <label for="password" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Password Baru</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:bg-white transition-all text-gray-700"
                            placeholder="Minimal 8 karakter">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-red-500 transition-colors" onclick="toggleView('password', 'eye-icon-1')">
                            <i class="fas fa-eye" id="eye-icon-1"></i>
                        </span>
                    </div>
                </div>

                <div class="relative">
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:bg-white transition-all text-gray-700"
                            placeholder="Ulangi password baru">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-red-500 transition-colors" onclick="toggleView('password_confirmation', 'eye-icon-2')">
                            <i class="fas fa-eye" id="eye-icon-2"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" 
                    class="w-full py-4 px-4 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 tracking-wide text-center flex justify-center items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Kredensial Baru
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleView(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
    
    {{-- Notifikasi Error --}}
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Riset Gagal!',
                html: '<ul style="text-align: left; font-size: 14px;">' +
                      '@foreach($errors->all() as $error)' +
                      '<li>- {{ $error }}</li>' +
                      '@endforeach' +
                      '</ul>',
                confirmButtonColor: '#ef4444',
            });
        </script>
    @endif
    
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
            });
        </script>
    @endif
</body>
</html>

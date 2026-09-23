<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MPP Kota Banjarbaru</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Background gambar yang sama dengan halaman login */
        .bg-mpp {
            background-image: url("{{ asset('img/backgraund_login/mpp_login.jpg') }}");
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-mpp h-screen w-screen flex items-center justify-center relative overflow-hidden font-sans">

    <div class="absolute inset-0 bg-black/30 backdrop-blur-md z-0"></div>

    <div class="relative z-10 w-full max-w-md mx-4 p-8 bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/20 transform transition-all duration-500 scale-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 text-[#ff7c7c] rounded-2xl mb-4 shadow-inner">
                <i class="fas fa-key text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Atur Ulang Password</h2>
            <p class="text-sm text-gray-500 mt-2">Halo <strong>{{ $namaPengguna ?? 'Pengguna' }}</strong>, silakan masukkan password baru Anda untuk akun MPP Kota Banjarbaru.</p>
        </div>

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-xl border border-red-200" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-xl border border-red-200" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">
            <input type="hidden" name="email" value="{{ $email ?? '' }}">
            <div class="relative">
                <label for="password" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#ff7c7c] focus:bg-white transition-all text-gray-700 placeholder-gray-400"
                        placeholder="Minimal 8 karakter">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-[#ff7c7c] transition-colors" onclick="toggleView('password', 'eye-icon-1')">
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
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#ff7c7c] focus:bg-white transition-all text-gray-700 placeholder-gray-400"
                        placeholder="Ulangi password baru">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-[#ff7c7c] transition-colors" onclick="toggleView('password_confirmation', 'eye-icon-2')">
                        <i class="fas fa-eye" id="eye-icon-2"></i>
                    </span>
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-[#ffb6b6] to-[#ff7c7c] hover:from-[#ff7c7c] hover:to-[#ff5252] text-white font-bold rounded-xl shadow-lg shadow-red-200 hover:shadow-xl hover:shadow-red-300 hover:-translate-y-0.5 transition-all duration-300 tracking-wide text-center">
                    <i class="fas fa-save mr-2"></i> Simpan Password Baru
                </button>
            </div>
        </form>

        <div class="text-center mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('login_pengunjung') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Login
            </a>
        </div>
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
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Karyawan - MPP Kota Banjarbaru</title>
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

    <div class="absolute inset-0 bg-black/40 z-0"></div>

    <div class="relative z-10 w-full max-w-2xl mx-4 p-8 bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/20 h-[90vh] overflow-y-auto custom-scrollbar">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-500 rounded-2xl mb-4 shadow-inner">
                <i class="fas fa-user-plus text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Registrasi Akun Karyawan</h2>
            <p class="text-sm text-gray-500 mt-2">Silakan lengkapi data diri Anda untuk menyelesaikan pembuatan akun karyawan baru MPP Kota Banjarbaru.</p>
        </div>

        <form action="{{ route('register_karyawan.proses') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="token" value="{{ $invitasi->token }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Kolom Kiri -->
                <div class="space-y-6">
                    <div class="relative">
                        <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Email Terdaftar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ $invitasi->email }}" readonly
                                class="w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Email dikunci sesuai undangan.</p>
                    </div>

                    <div class="relative">
                        <label for="nama_user" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" id="nama_user" name="nama_user" required value="{{ old('nama_user') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-gray-700"
                                placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <div class="relative">
                        <label for="jenis_kelamin" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-venus-mars"></i>
                            </span>
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-gray-700 appearance-none">
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <label for="tanggal_lahir" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required value="{{ old('tanggal_lahir') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-gray-700">
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-6">
                    <div class="relative">
                        <label for="username" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Username Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" id="username" name="username" required value="{{ old('username') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-gray-700"
                                placeholder="Buat username untuk login">
                        </div>
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Buat Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password" required minlength="8"
                                class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-gray-700"
                                placeholder="Minimal 8 karakter">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-blue-500 transition-colors" onclick="toggleView('password', 'eye-icon-1')">
                                <i class="fas fa-eye" id="eye-icon-1"></i>
                            </span>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-gray-700"
                                placeholder="Ulangi password">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-blue-500 transition-colors" onclick="toggleView('password_confirmation', 'eye-icon-2')">
                                <i class="fas fa-eye" id="eye-icon-2"></i>
                            </span>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="foto" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Foto Profil (Opsional)</label>
                        <div class="relative">
                            <input type="file" id="foto" name="foto" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" 
                    class="w-full py-4 px-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 tracking-wide text-center flex justify-center items-center">
                    <i class="fas fa-check-circle mr-2"></i> Buat Akun Karyawan
                </button>
            </div>
        </form>

        <div class="text-center mt-8 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-400">Sistem Informasi Manajemen Pelayanan - MPP Kota Banjarbaru</p>
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
    
    {{-- Notifikasi Error --}}
    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Registrasi Gagal!',
                html: '<ul style="text-align: left; font-size: 14px;">' +
                      '@foreach($errors->all() as $error)' +
                      '<li>- {{ $error }}</li>' +
                      '@endforeach' +
                      '</ul>',
                confirmButtonColor: '#3b82f6',
            });
        </script>
    @endif
    
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#3b82f6',
            });
        </script>
    @endif
</body>
</html>

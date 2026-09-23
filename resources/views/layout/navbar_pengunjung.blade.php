<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/navbar_pengunjung.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <title>MPP Online</title>
</head>
<body>
    <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-16 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/super_admin_logo/logo.jpeg') }}" alt="Logo">
                <span class="font-bold text-gray-700">MPP Online</span>
            </div>
            
            <div class="hidden md:flex gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('dashboard.pengunjung') }}" class="text-blue-600">Home</a>
                <a href="{{ route('profil.pengunjung') }}" class="hover:text-blue-600">Profil</a>
                <a href="{{ route('antrian.history') }}" class="hover:text-blue-600">History Antrian</a>
                <a href="{{ route('booking.index') }}" class="hover:text-blue-600">Booking Antrian</a>
                <a href="{{ route('monitor.antrian') }}" class="hover:text-blue-600">Monitor Antrian</a>
                
                <form id="logout-form" action="{{ route('logout.pengunjung') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <a href="javascript:void(0)" id="logout-link" class="hover:text-red-600">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold uppercase">
                        {{ Auth::user()->profil_pengunjung->nama ?? 'Nama Tidak Ditemukan' }}
                    </p>
                    <p class="text-[10px] text-green-500 font-semibold">Aktif</p>
                </div>
                <img class="h-9 w-9 rounded-full border-2 border-blue-400 object-cover" 
                    src="{{ (Auth::user()->profil_pengunjung && Auth::user()->profil_pengunjung->foto) 
                            ? asset('storage/' . Auth::user()->profil_pengunjung->foto) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->profil_pengunjung->nama ?? 'User') . '&background=0D8ABC&color=fff' }}" 
                    alt="User">
            </div>
        </div>
    </nav>

    <script src="{{ asset('js/navbar-pengunjung.js') }}"></script>
</body>
</html>
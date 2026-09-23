<link rel="stylesheet" href="{{ asset('css/sidebar_info.css') }}">

<aside class="info-sidebar">
    <div class="sidebar-card">
        <h3 class="sidebar-title">
            <i class="fas fa-filter text-blue-400 mr-2"></i> Filter Loket
        </h3>
        <ul class="filter-list">
            <li>
                <a href="{{ route('pusat.informasi') }}" 
                   class="filter-item {{ !request('loket') ? 'active' : '' }}">
                   Semua Layanan
                </a>
            </li>
            @foreach($lokets as $l)
            <li>
                <a href="{{ route('pusat.informasi', ['loket' => $l->nama_loket]) }}" 
                   class="filter-item {{ request('loket') == $l->nama_loket ? 'active' : '' }}">
                    {{ $l->nama_loket }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="sidebar-card mt-4">
        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Jam Operasional</p>
        <p class="text-xs text-white mt-1">Senin - Kamis: 08:00 - 15:00</p>
        <p class="text-xs text-white">Jumat: 08:00 - 11:00</p>
    </div>
</aside>
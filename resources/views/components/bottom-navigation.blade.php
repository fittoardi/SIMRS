<!-- Bottom Navigation Bar for Mobile (< 768px / md:hidden) -->
<nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg px-2 py-1.5 transition-all">
    <div class="grid grid-cols-5 gap-1 items-center justify-around text-center">
        <!-- Beranda -->
        <a href="{{ url('/') }}"
           class="flex flex-col items-center justify-center py-1 px-1 rounded-lg transition-colors {{ request()->is('/') ? 'text-teal-600 dark:text-teal-400 font-semibold' : 'text-gray-500 hover:text-teal-600 dark:text-gray-400' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[11px] leading-tight">Beranda</span>
        </a>

        <!-- Jadwal Dokter -->
        <a href="{{ url('/jadwal-dokter') }}"
           class="flex flex-col items-center justify-center py-1 px-1 rounded-lg transition-colors {{ request()->is('jadwal-dokter*') ? 'text-teal-600 dark:text-teal-400 font-semibold' : 'text-gray-500 hover:text-teal-600 dark:text-gray-400' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-[11px] leading-tight">Jadwal</span>
        </a>

        <!-- Booking Online (Center elevated button) -->
        <a href="{{ url('/akun/booking') }}"
           class="flex flex-col items-center justify-center -mt-4 group">
            <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:bg-teal-700 transition transform group-hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-[11px] font-semibold text-teal-700 dark:text-teal-400 mt-1">Booking</span>
        </a>

        <!-- Pantau Antrean -->
        <a href="{{ url('/layar-antrean') }}"
           class="flex flex-col items-center justify-center py-1 px-1 rounded-lg transition-colors {{ request()->is('layar-antrean*') || request()->is('akun/antrean*') ? 'text-teal-600 dark:text-teal-400 font-semibold' : 'text-gray-500 hover:text-teal-600 dark:text-gray-400' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="text-[11px] leading-tight">Antrean</span>
        </a>

        <!-- Akun Pasien / Login -->
        @auth
            <a href="{{ url('/akun') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-lg transition-colors {{ request()->is('akun*') && !request()->is('akun/booking*') ? 'text-teal-600 dark:text-teal-400 font-semibold' : 'text-gray-500 hover:text-teal-600 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[11px] leading-tight">Akun</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex flex-col items-center justify-center py-1 px-1 rounded-lg transition-colors {{ request()->routeIs('login') ? 'text-teal-600 dark:text-teal-400 font-semibold' : 'text-gray-500 hover:text-teal-600 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="text-[11px] leading-tight">Masuk</span>
            </a>
        @endauth
    </div>
</nav>


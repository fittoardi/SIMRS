<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-teal-100 dark:border-gray-700 sticky top-0 z-40 shadow-sm backdrop-blur-md bg-white/95 dark:bg-gray-800/95">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-8">
                <!-- Logo RS Aisyah Medika -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center shadow-md shadow-teal-500/20 group-hover:scale-105 transition transform">
                            <!-- Medical Cross Icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="font-bold text-lg leading-tight tracking-tight text-slate-900 dark:text-white group-hover:text-teal-600 transition">
                                RS Aisyah Medika
                            </span>
                            <span class="text-[10px] uppercase font-semibold tracking-wider text-teal-600 dark:text-teal-400">
                                Sistem Informasi Terpadu
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links (>= 1024px) -->
                <div class="hidden md:flex space-x-1 lg:space-x-3 items-center">
                    <a href="{{ url('/') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->is('/') ? 'bg-teal-50 text-teal-700 dark:bg-gray-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 hover:bg-slate-50 dark:hover:bg-gray-700' }}">
                        Beranda
                    </a>
                    <a href="{{ url('/jadwal-dokter') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->is('jadwal-dokter*') ? 'bg-teal-50 text-teal-700 dark:bg-gray-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 hover:bg-slate-50 dark:hover:bg-gray-700' }}">
                        Jadwal Dokter
                    </a>
                    <a href="{{ url('/ketersediaan-kamar') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->is('ketersediaan-kamar*') ? 'bg-teal-50 text-teal-700 dark:bg-gray-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 hover:bg-slate-50 dark:hover:bg-gray-700' }}">
                        Ketersediaan Bed
                    </a>
                    <a href="{{ url('/layar-antrean') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->is('layar-antrean*') ? 'bg-teal-50 text-teal-700 dark:bg-gray-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 hover:bg-slate-50 dark:hover:bg-gray-700' }}">
                        Layar Antrean
                    </a>
                    @auth
                        <a href="{{ route('patient.dashboard') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->is('akun*') ? 'bg-teal-50 text-teal-700 dark:bg-gray-700 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 hover:bg-slate-50 dark:hover:bg-gray-700' }}">
                            Area Pasien
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Right Controls: Staff Quick Access, Auth / User Menu -->
            <div class="hidden sm:flex sm:items-center sm:space-x-3">
                @auth
                    <!-- Tombol Cepat Panel Medis untuk Staf RS -->
                    @if(Auth::user()->hasAnyRole(['super_admin', 'doctor', 'nurse', 'admission', 'pharmacist', 'cashier']))
                        <a href="{{ url('/admin') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-teal-600 text-white shadow-sm hover:bg-teal-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Panel Medis / Staf
                        </a>
                    @endif

                    <!-- User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-1.5 border border-slate-200 dark:border-gray-700 text-sm font-medium rounded-lg text-slate-700 dark:text-slate-200 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 transition">
                                <div class="w-7 h-7 rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/60 dark:text-teal-300 flex items-center justify-center mr-2 font-semibold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="max-w-[140px] truncate text-left">{{ Auth::user()->name }}</span>
                                <svg class="ms-1.5 -me-0.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-gray-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400">Masuk sebagai</p>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('patient.dashboard')">
                                {{ __('Area Pasien') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('patient.booking')">
                                {{ __('Booking Antrean') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('patient.queues')">
                                {{ __('Daftar Antrean Saya') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('patient.family')">
                                {{ __('Anggota Keluarga') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('patient.profile')">
                                {{ __('Data Rekam Medis') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Pengaturan Akun') }}
                            </x-dropdown-link>

                            @if(Auth::user()->hasAnyRole(['super_admin', 'doctor', 'nurse', 'admission', 'pharmacist', 'cashier']))
                                <x-dropdown-link :href="url('/admin')">
                                    {{ __('Panel Staf Medis') }}
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-teal-600 px-3 py-1.5 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-lg shadow-sm transition">
                        Daftar Pasien
                    </a>
                @endauth
            </div>

            <!-- Hamburger Button for Mobile menu toggle -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-gray-700 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Nav Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-b border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/jadwal-dokter')" :active="request()->is('jadwal-dokter*')">
                {{ __('Jadwal Dokter') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/ketersediaan-kamar')" :active="request()->is('ketersediaan-kamar*')">
                {{ __('Ketersediaan Kamar') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/layar-antrean')" :active="request()->is('layar-antrean*')">
                {{ __('Layar Antrean') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('patient.dashboard')" :active="request()->is('akun*')">
                    {{ __('Area Pasien (/akun)') }}
                </x-responsive-nav-link>
                @if(Auth::user()->hasAnyRole(['super_admin', 'doctor', 'nurse', 'admission', 'pharmacist', 'cashier']))
                    <x-responsive-nav-link :href="url('/admin')" class="text-teal-600 font-semibold">
                        {{ __('Panel Staf Medis (/admin)') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-3 pb-3 border-t border-slate-200 dark:border-gray-700 px-4">
                <div class="font-medium text-sm text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-slate-500">{{ Auth::user()->email }}</div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Pengaturan Profil') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar (Log Out)') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-3 pb-3 border-t border-slate-200 dark:border-gray-700 px-4 space-y-2">
                <a href="{{ route('login') }}" class="block text-center py-2 text-sm font-semibold text-teal-700 bg-teal-50 rounded-lg">Masuk</a>
                <a href="{{ route('register') }}" class="block text-center py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg">Daftar Akun Pasien</a>
            </div>
        @endauth
    </div>
</nav>

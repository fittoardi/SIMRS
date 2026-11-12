@props(['maxWidth' => 'max-w-md', 'card' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Portal Pasien — RS Aisyah Medika' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col relative bg-slate-950">
        <!-- Full-Screen Hospital Background with Transparent Atmospheric Overlay -->
        <div class="fixed inset-0 -z-20 overflow-hidden pointer-events-none">
            <img
                src="{{ asset('images/rs-aisyah-medika-bg.jpg') }}"
                alt="RS Aisyah Medika"
                class="w-full h-full object-cover object-center scale-105"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/85 via-slate-900/70 to-teal-950/75 backdrop-blur-[2px]"></div>
        </div>

        <!-- Top Floating Header -->
        <header class="w-full max-w-6xl mx-auto px-4 sm:px-6 pt-5 pb-2 flex items-center justify-between gap-4 relative z-10">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-bold text-base sm:text-lg leading-tight tracking-tight text-white group-hover:text-teal-300 transition">
                        RS Aisyah Medika
                    </span>
                    <span class="text-[10px] uppercase font-semibold tracking-wider text-teal-300">
                        Sistem Informasi Terpadu
                    </span>
                </div>
            </a>

            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-xs sm:text-sm font-medium text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </header>

        <!-- Main Centered Auth Card Container -->
        <main class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 py-6 sm:py-10 relative z-10">
            <div class="w-full {{ $maxWidth }}">
                @if ($card)
                    <div class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/40 dark:border-slate-700/60 overflow-hidden">
                        {{ $slot }}
                    </div>
                @else
                    {{ $slot }}
                @endif
            </div>
        </main>

        <!-- Subtle Footer -->
        <footer class="w-full max-w-6xl mx-auto px-4 sm:px-6 pb-6 pt-2 text-center text-xs text-slate-300/80 relative z-10">
            <p>© {{ date('Y') }} RS Aisyah Medika • Layanan IGD & Ambulans 24 Jam: <span class="font-semibold text-white">(021) 8765-4322</span></p>
        </footer>
    </body>
</html>

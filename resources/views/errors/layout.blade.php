<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pemberitahuan Sistem') — RS Aisyah Medika</title>
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230d9488'><rect width='24' height='24' rx='6' fill='%230d9488'/><path d='M12 5v14M5 12h14' stroke='white' stroke-width='3' stroke-linecap='round'/></svg>">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Fallback styling jika assets Vite sedang re-bundle atau offline */
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #090d16;
            color: #f1f5f9;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-teal-500 selection:text-white relative overflow-x-hidden">
    <!-- Ambient Background Radial Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-slate-800/30 rounded-full blur-[140px]"></div>
    </div>

    <!-- Header RS -->
    <header class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-6 pt-6 sm:pt-8 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-teal-500/20 group-hover:scale-105 transition">
                +
            </div>
            <div>
                <span class="block font-black text-base tracking-tight text-white group-hover:text-teal-300 transition">
                    RS Aisyah Medika
                </span>
                <span class="block text-[11px] text-teal-400 font-medium tracking-wide">
                    Sistem Informasi Manajemen Rumah Sakit
                </span>
            </div>
        </a>

        <div class="hidden sm:flex items-center space-x-3 text-xs">
            <a href="tel:02187654322" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-300 font-semibold hover:bg-rose-500/20 transition">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span>IGD 24 Jam: (021) 8765-4322</span>
            </a>
        </div>
    </header>

    <!-- Main Content Box -->
    <main class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 my-4">
        <div class="max-w-lg w-full bg-slate-900/90 backdrop-blur-2xl border border-slate-800 p-6 sm:p-10 rounded-3xl text-center shadow-2xl relative overflow-hidden space-y-6">
            <!-- Huge Watermark Code in Background -->
            <div class="absolute -top-10 -right-4 text-9xl sm:text-[140px] font-black font-mono text-slate-800/20 select-none pointer-events-none leading-none z-0">
                @yield('code', 'ERR')
            </div>

            <!-- Foreground Content -->
            <div class="relative z-10 space-y-5">
                <!-- Icon Badge -->
                <div class="mx-auto w-20 h-20 rounded-2xl flex items-center justify-center shadow-inner border transition transform hover:scale-105 @yield('icon_wrapper_classes', 'bg-teal-500/10 border-teal-500/20 text-teal-400')">
                    @yield('icon')
                </div>

                <!-- Status Pill & Heading -->
                <div class="space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider @yield('badge_classes', 'bg-teal-500/10 text-teal-300 border border-teal-500/30')">
                        @yield('badge', 'Status ' . ($__env->yieldContent('code') ?: 'Error'))
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        @yield('heading', $__env->yieldContent('title', 'Pemberitahuan Sistem'))
                    </h1>
                </div>

                <!-- Message Description -->
                <div class="text-xs sm:text-sm text-slate-300/90 leading-relaxed max-w-md mx-auto">
                    @yield('message')
                </div>

                <!-- Action Buttons -->
                <div class="pt-3 flex flex-col sm:flex-row gap-2.5 justify-center items-center text-xs">
                    @yield('actions')
                </div>
            </div>
        </div>
    </main>

    <!-- Footer RS & Support Contact -->
    <footer class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-6 py-6 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
        <div class="flex items-center space-x-2">
            <span>© {{ date('Y') }} RS Aisyah Medika. Terakreditasi Paripurna KARS.</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ url('/') }}" class="hover:text-teal-400 transition">Beranda</a>
            <span class="text-slate-700">•</span>
            <a href="tel:02187654321" class="hover:text-teal-400 transition">Call Center: (021) 8765-4321</a>
            <span class="text-slate-700">•</span>
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-400 transition">WhatsApp RS</a>
        </div>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Kendala Teknis Sistem | RS Aisyah Medika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-xl border border-slate-700 p-8 rounded-3xl text-center shadow-2xl space-y-6">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-black text-2xl mx-auto shadow-inner">
            ⚙️
        </div>
        <div>
            <span class="text-xs uppercase font-extrabold tracking-wider text-amber-400">Status 500 • Server Error</span>
            <h1 class="text-2xl font-black text-white mt-1">Terjadi Kendala Teknis</h1>
            <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                Sistem kami sedang mengalami pemulihan atau kendala sementara. Tim IT RS Aisyah Medika telah diberitahu dan sedang menangani masalah ini secepat mungkin.
            </p>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row gap-2.5 justify-center text-xs">
            <button onclick="window.location.reload();" class="px-5 py-2.5 rounded-xl font-semibold bg-teal-600 text-white hover:bg-teal-500 transition shadow-sm">
                Muat Ulang Halaman
            </a>
            <a href="tel:02187654322" class="px-5 py-2.5 rounded-xl font-semibold bg-rose-600 text-white hover:bg-rose-500 transition shadow-sm">
                Hubungi IGD 24 Jam
            </a>
        </div>
    </div>
</body>
</html>

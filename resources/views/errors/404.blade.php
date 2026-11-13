<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | RS Aisyah Medika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-xl border border-slate-700 p-8 rounded-3xl text-center shadow-2xl space-y-6">
        <div class="w-16 h-16 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center font-black text-2xl mx-auto shadow-inner">
            🏥
        </div>
        <div>
            <span class="text-xs uppercase font-extrabold tracking-wider text-teal-400">Status 404 • Not Found</span>
            <h1 class="text-2xl font-black text-white mt-1">Halaman Tidak Ditemukan</h1>
            <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                Halaman, jadwal dokter, atau dokumen yang Anda cari mungkin telah dipindahkan, dinonaktifkan, atau alamat URL yang Anda masukkan keliru.
            </p>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row gap-2.5 justify-center text-xs">
            <a href="{{ url('/') }}" class="px-5 py-2.5 rounded-xl font-semibold bg-teal-600 text-white hover:bg-teal-500 transition shadow-sm">
                Ke Beranda Utama
            </a>
            <a href="{{ url('/jadwal-dokter') }}" class="px-5 py-2.5 rounded-xl font-semibold bg-slate-700 text-slate-200 hover:bg-slate-600 transition">
                Cari Jadwal Dokter
            </a>
        </div>
    </div>
</body>
</html>

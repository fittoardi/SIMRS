<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Layar Antrean Ruang Tunggu (TV Display)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">RS Aisyah Medika — Pantauan antrean live poliklinik & farmasi</p>
            </div>
            <a href="{{ url('/') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Kembali ke Beranda
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Live Call Banner -->
        <div class="rounded-3xl p-6 sm:p-10 bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-900 text-white shadow-xl text-center space-y-3 relative overflow-hidden">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-white border border-white/30 animate-pulse">
                Sedang Dipanggil
            </span>
            <div class="py-2">
                <p class="text-xs uppercase tracking-widest text-teal-200">Nomor Antrean</p>
                <p class="text-5xl sm:text-7xl font-mono font-extrabold tracking-wider text-white mt-1">U - 001</p>
                <p class="text-base sm:text-xl font-bold text-teal-100 mt-2">Menuju ke: Ruang Poliklinik Umum 1</p>
            </div>
            <p class="text-xs text-teal-200/80">Dokter: dr. Ahmad Fauzi</p>
        </div>

        <!-- Poli Queue Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Poli Umum -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200 dark:border-gray-700 shadow-sm text-center space-y-2">
                <span class="text-xs font-bold text-teal-700 uppercase">Poli Umum</span>
                <p class="text-3xl font-mono font-bold text-slate-900 dark:text-white">U-001</p>
                <p class="text-xs text-emerald-600 font-semibold">Sedang Diperiksa</p>
                <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-400">Sisa Antrean: 4 Pasien</div>
            </div>

            <!-- Poli Penyakit Dalam -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200 dark:border-gray-700 shadow-sm text-center space-y-2">
                <span class="text-xs font-bold text-emerald-700 uppercase">Poli Penyakit Dalam</span>
                <p class="text-3xl font-mono font-bold text-slate-900 dark:text-white">D-003</p>
                <p class="text-xs text-amber-600 font-semibold">Menunggu Giliran</p>
                <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-400">Sisa Antrean: 6 Pasien</div>
            </div>

            <!-- Poli Gigi -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200 dark:border-gray-700 shadow-sm text-center space-y-2">
                <span class="text-xs font-bold text-sky-700 uppercase">Poli Gigi & Mulut</span>
                <p class="text-3xl font-mono font-bold text-slate-900 dark:text-white">G-002</p>
                <p class="text-xs text-emerald-600 font-semibold">Sedang Tindakan</p>
                <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-400">Sisa Antrean: 2 Pasien</div>
            </div>

            <!-- Farmasi / Pengambilan Obat -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-teal-200 dark:border-teal-800/60 shadow-sm text-center space-y-2 bg-teal-50/20">
                <span class="text-xs font-bold text-teal-800 dark:text-teal-400 uppercase">Loket Farmasi</span>
                <p class="text-3xl font-mono font-bold text-teal-700 dark:text-teal-300">R-008</p>
                <p class="text-xs text-teal-600 font-semibold">Siap Diserahkan</p>
                <div class="pt-2 border-t border-teal-100 text-[11px] text-slate-400">Sedang Diracik: 3 Resep</div>
            </div>
        </div>
    </div>
</x-app-layout>


<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Antrean & Riwayat Kunjungan
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau status antrean aktif dan riwayat pemeriksaan Anda</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('patient.booking') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs rounded-xl shadow-sm transition">
                    + Ambil Antrean Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <livewire:patient.queue-history />
    </div>
</x-app-layout>

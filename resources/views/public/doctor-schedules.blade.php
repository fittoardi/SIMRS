<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Direktori Jadwal Dokter — RS Aisyah Medika
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Filter jadwal praktik dokter spesialis dan umum dengan sisa kuota</p>
            </div>
            <a href="{{ url('/') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Kembali ke Beranda
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:public.doctor-schedule-filter />
    </div>
</x-app-layout>

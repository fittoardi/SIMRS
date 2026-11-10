<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Kiosk Mandiri RS Aisyah Medika
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Konfirmasi kehadiran mandiri untuk antrean rawat jalan hari ini</p>
            </div>
            <a href="{{ url('/') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Kembali ke Beranda
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <livewire:public.kiosk-check-in />
    </div>
</x-app-layout>

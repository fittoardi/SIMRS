<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Tiket Antrean Digital
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tunjukkan e-tiket ini atau scan QR Code saat tiba di RS Aisyah Medika</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('patient.queues') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                    ← Daftar Antrean Saya
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <livewire:patient.digital-ticket :visit-id="$visit->id" />
    </div>
</x-app-layout>

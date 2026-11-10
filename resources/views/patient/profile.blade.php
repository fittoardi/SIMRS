<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Pengaturan Profil Pasien
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola identitas medis utama Anda di RS Aisyah Medika</p>
            </div>
            <a href="{{ route('patient.dashboard') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Kembali ke Area Pasien
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <livewire:patient.patient-profile-form />
    </div>
</x-app-layout>

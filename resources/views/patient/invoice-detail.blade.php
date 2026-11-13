<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    Rincian Tagihan Pasien
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Rincian item tagihan dan fasilitas pembayaran online mandiri</p>
            </div>
            <a href="{{ route('patient.invoices') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                ← Daftar Tagihan
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <livewire:patient.patient-invoice-detail :invoice-id="$invoice->id" />
    </div>
</x-app-layout>


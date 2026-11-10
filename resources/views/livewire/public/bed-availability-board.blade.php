<div wire:poll.15s class="space-y-6">
    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm text-center">
            <p class="text-xs text-slate-500">Total Kapasitas</p>
            <p class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $totalCount }}</p>
            <span class="text-[10px] text-slate-400">Tempat Tidur</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 shadow-sm text-center">
            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold">Tersedia (Kosong)</p>
            <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-300 mt-1">{{ $availableCount }}</p>
            <span class="text-[10px] text-emerald-600 font-medium">Siap Ditempati</span>
        </div>

        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 shadow-sm text-center">
            <p class="text-xs text-rose-700 dark:text-rose-400 font-semibold">Terisi Pasien</p>
            <p class="text-2xl font-extrabold text-rose-700 dark:text-rose-300 mt-1">{{ $occupiedCount }}</p>
            <span class="text-[10px] text-rose-600 font-medium">BOR {{ $bor }}%</span>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 shadow-sm text-center">
            <p class="text-xs text-amber-700 dark:text-amber-400 font-semibold">Sterilisasi</p>
            <p class="text-2xl font-extrabold text-amber-700 dark:text-amber-300 mt-1">{{ $cleaningCount }}</p>
            <span class="text-[10px] text-amber-600 font-medium">Dibersihkan</span>
        </div>

        <div class="col-span-2 lg:col-span-1 p-4 rounded-2xl bg-slate-100 dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm text-center">
            <p class="text-xs text-slate-500">Perbaikan</p>
            <p class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 mt-1">{{ $maintenanceCount }}</p>
            <span class="text-[10px] text-slate-400">Maintenance</span>
        </div>
    </div>

    <!-- Filter by Class Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 shrink-0">Filter Kelas:</span>
            <select
                wire:model.live="selectedClass"
                class="w-full sm:w-auto px-3 py-1.5 rounded-xl border border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-gray-700 text-xs focus:ring-teal-500 focus:border-teal-500 dark:text-white"
            >
                <option value="">Semua Kelas Kamar</option>
                <option value="VVIP">VVIP</option>
                <option value="VIP">VIP</option>
                <option value="Kelas 1">Kelas 1</option>
                <option value="Kelas 2">Kelas 2</option>
                <option value="Kelas 3">Kelas 3</option>
                <option value="ICU">ICU</option>
            </select>
        </div>

        <div class="flex items-center space-x-2 text-xs text-slate-500 w-full sm:w-auto justify-between sm:justify-end">
            <span class="inline-flex items-center space-x-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Pembaruan otomatis tiap 15 detik</span>
            </span>
        </div>
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700 text-left text-sm">
            <thead class="bg-slate-50 dark:bg-gray-700/50 text-slate-600 dark:text-slate-300 text-xs font-semibold uppercase">
                <tr>
                    <th class="px-6 py-4">Kamar & Bangsal</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Tarif / Hari</th>
                    <th class="px-6 py-4">Kapasitas</th>
                    <th class="px-6 py-4">Status Rinci Tempat Tidur (Bed)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                @forelse($rooms as $room)
                    @php
                        $roomAvailable = $room->beds->where('status', \App\Enums\BedStatus::Available)->count();
                        $roomTotal = $room->beds->count();
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $room->name }}</p>
                            <p class="text-xs text-slate-500">{{ $room->ward_name }} • Kode: {{ $room->code }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300">
                                {{ $room->class_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                            Rp {{ number_format($room->price_per_day, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold {{ $roomAvailable > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Sisa {{ $roomAvailable }} dari {{ $roomTotal }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($room->beds as $bed)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-mono font-semibold border {{ $bed->status === \App\Enums\BedStatus::Available ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/30 dark:border-emerald-800 dark:text-emerald-300' : ($bed->status === \App\Enums\BedStatus::Occupied ? 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/30 dark:border-rose-800 dark:text-rose-300' : 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/30 dark:border-amber-800 dark:text-amber-300') }}" title="{{ $bed->bed_number }}: {{ $bed->status->label() }}">
                                        {{ $bed->bed_number }} ({{ $bed->status->label() }})
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                            Tidak ada kamar yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Stacked Card View (< 768px) -->
    <div class="md:hidden space-y-4">
        @forelse($rooms as $room)
            @php
                $roomAvailable = $room->beds->where('status', \App\Enums\BedStatus::Available)->count();
                $roomTotal = $room->beds->count();
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200 dark:border-gray-700 shadow-sm space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-300 mb-1">
                            {{ $room->class_type }}
                        </span>
                        <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $room->name }}</h4>
                        <p class="text-xs text-slate-500">{{ $room->ward_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400">Tarif / Hari</span>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Rp {{ number_format($room->price_per_day, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-gray-700/50 flex justify-between items-center text-xs">
                    <span class="text-slate-600 dark:text-slate-400">Ketersediaan Bed:</span>
                    <span class="font-bold {{ $roomAvailable > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600' }}">
                        Tersedia {{ $roomAvailable }} dari {{ $roomTotal }}
                    </span>
                </div>

                <!-- Beds Pills -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    @foreach($room->beds as $bed)
                        <div class="p-2 rounded-lg border text-xs flex items-center justify-between {{ $bed->status === \App\Enums\BedStatus::Available ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800 dark:bg-emerald-950/30 dark:border-emerald-800 dark:text-emerald-300' : 'bg-rose-50/70 border-rose-200 text-rose-800 dark:bg-rose-950/30 dark:border-rose-800 dark:text-rose-300' }}">
                            <span class="font-mono font-bold">{{ $bed->bed_number }}</span>
                            <span class="text-[10px] uppercase font-semibold">{{ $bed->status->label() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-500 bg-white rounded-2xl border border-slate-200">
                Tidak ada data kamar.
            </div>
        @endforelse
    </div>
</div>


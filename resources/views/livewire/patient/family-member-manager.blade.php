<div class="space-y-6">
    <!-- Header & Action -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Anggota Keluarga</h3>
            <p class="text-xs text-slate-500 mt-1">Daftarkan anak, pasangan, atau orang tua untuk memudahkan pengambilan antrean berobat dalam satu akun.</p>
        </div>
        <button wire:click="openModal"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Tambah Anggota Keluarga
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Cards Grid: Diri Sendiri (Utama) & Anggota Keluarga -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Akun Utama (Diri Sendiri) -->
        <div class="bg-gradient-to-tr from-teal-50 to-emerald-50 dark:from-gray-800 dark:to-gray-800/80 rounded-3xl p-6 border-2 border-teal-500/30 relative flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-600 text-white">
                        Profil Utama (Anda)
                    </span>
                    <span class="text-xs font-mono font-bold text-teal-800 dark:text-teal-300">
                        {{ $primaryPatient?->medical_record_number ?? 'No. RM Belum Terbit' }}
                    </span>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white font-bold flex items-center justify-center text-lg shadow-md shadow-teal-600/20">
                        {{ strtoupper(substr($primaryPatient?->name ?? auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-slate-900 dark:text-white leading-tight">
                            {{ $primaryPatient?->name ?? auth()->user()->name }}
                        </h4>
                        <p class="text-xs text-slate-500">
                            {{ $primaryPatient?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} • {{ $primaryPatient?->birth_date?->age ? $primaryPatient->birth_date->age . ' Tahun' : '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-teal-100 dark:border-gray-700 text-xs space-y-1">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>NIK:</span>
                        <span class="font-mono font-semibold">{{ $primaryPatient?->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Gol. Darah:</span>
                        <span class="font-semibold">{{ $primaryPatient?->blood_type ?? '-' }}</span>
                    </div>
                </div>

                @if($primaryPatient?->allergies)
                    <div class="mt-3 p-2 rounded-xl bg-rose-100/80 dark:bg-rose-900/40 text-rose-800 dark:text-rose-200 text-[11px] font-medium flex items-center">
                        <span class="font-bold mr-1">Alergi:</span> {{ $primaryPatient->allergies }}
                    </div>
                @endif
            </div>

            <div class="pt-2">
                <a href="{{ route('patient.profile') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-gray-700 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-gray-600 hover:bg-teal-50 transition">
                    Edit Profil Saya
                </a>
            </div>
        </div>

        <!-- Daftar Anggota Keluarga -->
        @forelse($familyMembers as $member)
            @php $p = $member->patient; @endphp
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-slate-200 dark:border-gray-700 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300">
                            {{ ucfirst(str_replace('_', ' ', $member->relationship)) }}
                        </span>
                        <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">
                            {{ $p?->medical_record_number }}
                        </span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-lg">
                            {{ strtoupper(substr($p?->name ?? 'F', 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900 dark:text-white leading-tight">
                                {{ $p?->name }}
                            </h4>
                            <p class="text-xs text-slate-500">
                                {{ $p?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} • {{ $p?->birth_date?->age ? $p->birth_date->age . ' Tahun' : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-gray-700 text-xs space-y-1">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>NIK:</span>
                            <span class="font-mono font-semibold">{{ $p?->nik }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Gol. Darah:</span>
                            <span class="font-semibold">{{ $p?->blood_type ?? '-' }}</span>
                        </div>
                        @if($p?->bpjs_number)
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>No. BPJS:</span>
                                <span class="font-mono">{{ $p->bpjs_number }}</span>
                            </div>
                        @endif
                    </div>

                    @if($p?->allergies)
                        <div class="mt-3 p-2 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-[11px] font-medium flex items-center">
                            <span class="font-bold mr-1">Alergi:</span> {{ $p->allergies }}
                        </div>
                    @endif
                </div>

                <div class="pt-2 flex items-center space-x-2">
                    <a href="{{ url('/akun/booking?patient=' . $p->id) }}" class="flex-1 text-center py-2 rounded-xl text-xs font-semibold bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 hover:bg-teal-100 transition">
                        Ambil Antrean
                    </a>
                    <button wire:click="removeMember({{ $member->id }})"
                            wire:confirm="Yakin ingin menghapus anggota keluarga ini dari akun Anda?"
                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition"
                            title="Hapus dari daftar keluarga">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
        @endforelse
    </div>

    <!-- Modal Form Tambah Anggota Keluarga -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-gray-700">
                    <h4 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Anggota Keluarga</h4>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Hubungan Keluarga <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="relationship" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500">
                            <option value="anak">Anak</option>
                            <option value="suami">Suami</option>
                            <option value="istri">Istri</option>
                            <option value="orang_tua">Orang Tua (Ayah / Ibu)</option>
                            <option value="lainnya">Keluarga Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            NIK Anggota Keluarga <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="nik"
                               maxlength="16"
                               placeholder="16 digit NIK KTP / KIA"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 font-mono">
                        @error('nik') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="name"
                               placeholder="Nama lengkap sesuai identitas"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="gender" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Tanggal Lahir <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   wire:model="birth_date"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500">
                            @error('birth_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Gol. Darah</label>
                            <select wire:model="blood_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="">- Pilih -</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Kartu BPJS</label>
                            <input type="text"
                                   wire:model="bpjs_number"
                                   placeholder="Opsional"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-rose-600 dark:text-rose-400 mb-1">Riwayat Alergi (Jika Ada)</label>
                        <input type="text"
                               wire:model="allergies"
                               placeholder="Contoh: Alergi Penisilin, Susu Sapi"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                            Simpan Anggota Keluarga
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>


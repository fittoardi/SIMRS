<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-gray-700 shadow-sm space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100 dark:border-gray-700">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Data Rekam Medis & Identitas Pasien</h3>
            <p class="text-xs text-slate-500 mt-1">Lengkapi informasi identitas diri untuk kelancaran pendaftaran dan riwayat berobat.</p>
        </div>
        @if($medical_record_number)
            <div class="px-4 py-2 rounded-2xl bg-teal-50 dark:bg-teal-900/40 border border-teal-200 dark:border-teal-700/60">
                <span class="text-[10px] uppercase font-bold tracking-wider text-teal-600 dark:text-teal-400 block">Nomor Rekam Medis</span>
                <span class="font-mono font-bold text-base text-teal-800 dark:text-teal-200">{{ $medical_record_number }}</span>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <!-- Informasi Pokok Pasien -->
        <div>
            <h4 class="text-xs uppercase tracking-wider font-bold text-teal-700 dark:text-teal-400 mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-teal-600 mr-2"></span>
                Identitas Diri (Sesuai KTP/KK)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- NIK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="nik"
                           maxlength="16"
                           placeholder="16 digit NIK KTP"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                    @error('nik') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="name"
                           placeholder="Nama lengkap"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="gender" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="L">Laki-laki (L)</option>
                        <option value="P">Perempuan (P)</option>
                    </select>
                    @error('gender') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           wire:model="birth_date"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('birth_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Golongan Darah
                    </label>
                    <select wire:model="blood_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">-- Pilih Golongan Darah --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                        <option value="tidak_tahu">Tidak Tahu / Belum Cek</option>
                    </select>
                    @error('blood_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nomor HP Pasien -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nomor Handphone / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="tel"
                           wire:model="phone"
                           placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Alamat Domisili -->
            <div class="mt-4">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Alamat Domisili Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea wire:model="address"
                          rows="2"
                          placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota"
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Bagian Alergi dengan Penanda Merah Kontras -->
        <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
            <h4 class="text-xs uppercase tracking-wider font-bold text-rose-600 dark:text-rose-400 mb-2 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Riwayat Alergi (Obat / Makanan / Zat Lain)
            </h4>
            <p class="text-xs text-slate-500 mb-3">Informasi alergi sangat krusial demi keselamatan pemberian resep dokter & tindakan medis.</p>

            <textarea wire:model.live="allergies"
                      rows="2"
                      placeholder="Contoh: Alergi Amoksisilin (ruam merah), Parasetamol, Seafood, Debu"
                      class="w-full px-4 py-2.5 rounded-xl border {{ !empty($allergies) ? 'border-rose-300 bg-rose-50/40 dark:bg-rose-950/20' : 'border-slate-300 dark:border-gray-600' }} dark:bg-gray-700 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>

            @if(!empty($allergies))
                <div class="mt-2.5 p-3 rounded-xl bg-rose-100/80 dark:bg-rose-900/40 border border-rose-300 dark:border-rose-700 text-rose-800 dark:text-rose-200 text-xs flex items-center">
                    <span class="font-bold mr-1 uppercase">Perhatian Klinis:</span>
                    <span>Pasien tercatat memiliki riwayat alergi: "<strong>{{ $allergies }}</strong>". Data ini akan muncul otomatis di rekam medis dan panel dokter.</span>
                </div>
            @endif
        </div>

        <!-- Asuransi & BPJS -->
        <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
            <h4 class="text-xs uppercase tracking-wider font-bold text-teal-700 dark:text-teal-400 mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-teal-600 mr-2"></span>
                Penjamin Kesehatan & Asuransi (Opsional)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nomor Kartu BPJS Kesehatan
                    </label>
                    <input type="text"
                           wire:model="bpjs_number"
                           placeholder="Contoh: 0001234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nama Asuransi Swasta
                    </label>
                    <input type="text"
                           wire:model="insurance_name"
                           placeholder="Contoh: Prudential / Allianz"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nomor Polis Asuransi
                    </label>
                    <input type="text"
                           wire:model="insurance_number"
                           placeholder="Nomor kartu/polis"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                </div>
            </div>
        </div>

        <!-- Kontak Darurat -->
        <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
            <h4 class="text-xs uppercase tracking-wider font-bold text-teal-700 dark:text-teal-400 mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-teal-600 mr-2"></span>
                Kontak Darurat (Keluarga / Kerabat Dekat)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nama Kontak Darurat
                    </label>
                    <input type="text"
                           wire:model="emergency_contact_name"
                           placeholder="Nama kerabat"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nomor Telepon Kontak Darurat
                    </label>
                    <input type="tel"
                           wire:model="emergency_contact_phone"
                           placeholder="Contoh: 081298765432"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Hubungan Kerabat
                    </label>
                    <input type="text"
                           wire:model="emergency_contact_relation"
                           placeholder="Contoh: Suami / Istri / Orang Tua"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="pt-4 flex justify-end">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold text-sm text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 transition disabled:opacity-50">
                <span wire:loading.remove>Simpan Perubahan Data Rekam Medis</span>
                <span wire:loading>Menyimpan Data...</span>
            </button>
        </div>
    </form>
</div>

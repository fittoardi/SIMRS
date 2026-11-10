<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pendaftaran Pasien Baru</h2>
        <p class="text-xs text-slate-500 mt-1">Buat akun untuk booking antrean & riwayat rekam medis di RS Aisyah Medika</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap (Sesuai KTP/Identitas)" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NIK -->
        <div>
            <x-input-label for="nik" value="Nomor Induk Kependudukan (NIK 16 Digit)" />
            <x-text-input id="nik" class="block mt-1 w-full font-mono" type="text" name="nik" :value="old('nik')" maxlength="16" placeholder="16 digit angka KTP" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email (Untuk Login)" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="email@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" value="Nomor Handphone / WhatsApp" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" placeholder="Contoh: 081234567890" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Gender & Birth Date -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-input-label for="gender" value="Jenis Kelamin" />
                <select id="gender" name="gender" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="birth_date" value="Tanggal Lahir" />
                <x-text-input id="birth_date" class="block mt-1 w-full text-sm" type="date" name="birth_date" :value="old('birth_date')" />
                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
            </div>
        </div>

        <!-- BPJS & Gol Darah (Opsional) -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-input-label for="blood_type" value="Gol. Darah" />
                <select id="blood_type" name="blood_type" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="">- Pilih -</option>
                    <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B</option>
                    <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB</option>
                    <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O</option>
                </select>
            </div>

            <div>
                <x-input-label for="bpjs_number" value="No. BPJS (Opsional)" />
                <x-text-input id="bpjs_number" class="block mt-1 w-full font-mono text-sm" type="text" name="bpjs_number" :value="old('bpjs_number')" placeholder="No. Kartu BPJS" />
            </div>
        </div>

        <!-- Riwayat Alergi (Opsional) -->
        <div>
            <x-input-label for="allergies" value="Riwayat Alergi Obat/Makanan (Opsional)" />
            <x-text-input id="allergies" class="block mt-1 w-full" type="text" name="allergies" :value="old('allergies')" placeholder="Contoh: Penisilin, Parasetamol, Seafood" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi (Password)" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Ulangi Kata Sandi" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Ketik ulang kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
            <a class="underline text-xs text-gray-600 dark:text-gray-400 hover:text-teal-600" href="{{ route('login') }}">
                Sudah punya akun? Masuk
            </a>

            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition">
                Daftar Sebagai Pasien
            </button>
        </div>
    </form>
</x-guest-layout>

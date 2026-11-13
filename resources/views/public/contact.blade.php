<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">Kontak</span>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Hubungi Kami & Lokasi RS</h1>
            </div>
            <a href="tel:02187654322" class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-xl bg-rose-600 text-white hover:bg-rose-700 transition self-start sm:self-auto shadow-sm">
                🚨 Hotline IGD: (021) 8765-4322
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informasi Kontak & Operasional -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Alamat Kantor & Gedung</h2>
                    <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                        <div class="flex items-start gap-3">
                            <span class="text-teal-600 text-lg">📍</span>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">RS Aisyah Medika</p>
                                <p class="text-xs text-slate-500 mt-0.5">Jl. Kesehatan No. 45, Pancoran, Jakarta Selatan, DKI Jakarta 12810</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-teal-600 text-lg">📞</span>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">Call Center & Informasi</p>
                                <p class="text-xs text-slate-500 mt-0.5">(021) 8765-4321</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-rose-600 text-lg">🚨</span>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">IGD & Ambulans 24 Jam</p>
                                <p class="text-xs text-slate-500 mt-0.5">(021) 8765-4322 / 119</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-emerald-600 text-lg">💬</span>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">WhatsApp Customer Care</p>
                                <p class="text-xs text-slate-500 mt-0.5">0812-0000-0001</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-sky-600 text-lg">✉️</span>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">Email Resmi</p>
                                <p class="text-xs text-slate-500 mt-0.5">layanan@aisyahmedika.id</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-teal-800 to-emerald-900 text-white p-6 rounded-3xl shadow-sm space-y-3">
                    <h3 class="font-bold text-base">Jam Pelayanan Poliklinik</h3>
                    <div class="text-xs text-teal-100 space-y-1.5">
                        <p class="flex justify-between"><span>Senin – Jumat:</span> <span class="font-bold">08.00 – 21.00 WIB</span></p>
                        <p class="flex justify-between"><span>Sabtu:</span> <span class="font-bold">08.00 – 16.00 WIB</span></p>
                        <p class="flex justify-between"><span>Minggu & Hari Libur:</span> <span class="font-bold">Khusus IGD & Poli Spesialis Tertentu</span></p>
                    </div>
                </div>
            </div>

            <!-- Form Saran, Pertanyaan & Pengaduan Pasien -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-3xl border border-slate-200 dark:border-gray-700 shadow-sm space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Layanan Saran & Pengaduan Pasien</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Kritik dan saran Anda sangat berarti bagi peningkatan mutu keselamatan dan kenyamanan berobat di RS Aisyah Medika.
                        </p>
                    </div>

                    <form onsubmit="event.preventDefault(); alert('Terima kasih! Pesan dan saran Anda telah kami terima dan akan segera ditindaklanjuti oleh Unit Pengaduan Pasien.'); this.reset();" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                                <input type="text" required placeholder="Contoh: Muhammad Irfan" class="w-full text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP</label>
                                <input type="tel" required placeholder="Contoh: 081234567890" class="w-full text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Topik Layanan</label>
                            <select class="w-full text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:border-teal-500 focus:ring-teal-500">
                                <option>Informasi Layanan & Dokter</option>
                                <option>Pelayanan Admisi & Pendaftaran</option>
                                <option>Pelayanan Poliklinik Dokter</option>
                                <option>Instalasi Farmasi & Obat</option>
                                <option>Layanan Kasir & Tagihan</option>
                                <option>Saran & Apresiasi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Isi Pesan / Saran</label>
                            <textarea rows="4" required placeholder="Tuliskan pesan, pertanyaan, atau saran Anda secara rinci..." class="w-full text-xs rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:border-teal-500 focus:ring-teal-500"></textarea>
                        </div>

                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 text-white font-semibold text-xs hover:bg-teal-700 transition shadow-sm">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


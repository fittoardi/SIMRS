<div wire:poll.3s
     x-data="{
        audioEnabled: false,
        speechQueue: [],
        isSpeaking: false,
        playChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const now = ctx.currentTime;
                // High tone ding
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(880, now); // A5
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.5);

                // Low tone dong
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(659.25, now + 0.25); // E5
                gain2.gain.setValueAtTime(0.3, now + 0.25);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.25);
                osc2.stop(now + 0.8);
            } catch (e) {
                console.log('AudioContext error:', e);
            }
        },
        speak(text) {
            if (!this.audioEnabled || !('speechSynthesis' in window)) return;
            this.playChime();
            setTimeout(() => {
                const utter = new SpeechSynthesisUtterance(text);
                utter.lang = 'id-ID';
                utter.rate = 0.9;
                utter.pitch = 1.0;
                window.speechSynthesis.speak(utter);
            }, 600);
        },
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }
     }"
     x-init="
        $wire.on('play-queue-call', (event) => {
            const data = event[0] || event;
            if (data && data.code) {
                const formattedCode = data.code.split('').join(' ');
                const speechText = 'Nomor Antrean ' + data.code + ', silakan menuju ' + (data.room || data.poli);
                speak(speechText);
            }
        });
     "
     class="min-h-screen bg-slate-950 text-white flex flex-col justify-between selection:bg-teal-500 selection:text-white">

    <!-- Topbar TV: Logo RS, Clock, Fullscreen & Audio Toggles -->
    <header class="bg-slate-900/90 border-b border-slate-800 px-6 py-4 flex items-center justify-between backdrop-blur-md">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-teal-500/20">
                +
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white uppercase">RS Aisyah Medika</h1>
                <p class="text-xs text-teal-400 font-semibold tracking-wider uppercase">Layar Informasi Panggilan Antrean Poliklinik</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Audio Call Toggle -->
            <button type="button"
                    @click="audioEnabled = !audioEnabled; if(audioEnabled) playChime();"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2"
                    :class="audioEnabled ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 ring-2 ring-emerald-400/50' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
                <span x-text="audioEnabled ? 'Suara Aktif' : 'Aktifkan Suara'"></span>
            </button>

            <!-- Fullscreen Button -->
            <button type="button"
                    @click="toggleFullscreen()"
                    class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition"
                    title="Fullscreen Mode (TV)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button>

            <!-- Jam Real-time Digital WIB -->
            <div x-data="{ time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }"
                 x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)"
                 class="bg-slate-800/80 border border-slate-700/60 px-4 py-1.5 rounded-xl text-right">
                <span x-text="time + ' WIB'" class="font-mono font-bold text-base text-teal-300"></span>
                <span class="text-[10px] text-slate-400 block">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </header>

    <!-- Main Content: Split 60% Left (Current Call) vs 40% Right (All Polyclinics Grid) -->
    <main class="flex-1 p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- SISI KIRI: PANGGILAN ANTREAN UTAMA TERKINI -->
        <div class="lg:col-span-7 bg-gradient-to-br from-slate-900 via-slate-900 to-teal-950 rounded-3xl p-8 border-2 border-teal-500/40 shadow-2xl flex flex-col justify-between relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20 animate-pulse">
                    Panggilan Antrean Aktif
                </span>
                <span class="text-xs text-slate-400 font-mono">
                    {{ $currentCall ? 'Terakhir dipanggil: ' . $currentCall->called_at?->format('H:i:s') . ' WIB' : 'Menunggu Panggilan' }}
                </span>
            </div>

            <!-- Big Number Display -->
            <div class="text-center my-auto py-8">
                <p class="text-xs uppercase tracking-widest font-bold text-teal-400">Nomor Antrean</p>
                <div class="text-7xl sm:text-8xl lg:text-9xl font-black font-mono tracking-tight text-white drop-shadow-md my-2">
                    {{ $currentCall?->queue_code ?? '---' }}
                </div>
                <div class="inline-block mt-2 px-6 py-2 rounded-2xl bg-teal-500/20 border border-teal-400/30 text-teal-200 text-lg font-bold">
                    {{ $currentCall?->visit?->patient?->name ?? 'Belum ada antrean yang dipanggil' }}
                </div>
            </div>

            <!-- Destination Info Box -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl bg-slate-800/80 border border-slate-700/80">
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-semibold block">Tujuan Poliklinik</span>
                    <strong class="text-base text-white block">{{ $currentCall?->visit?->polyclinic?->name ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-semibold block">Ruangan</span>
                    <strong class="text-base text-teal-300 block">{{ $currentCall?->doctorSchedule?->room_name ?? 'Ruang Poliklinik' }}</strong>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-semibold block">Dokter Pemeriksa</span>
                    <strong class="text-base text-white block truncate">{{ $currentCall?->visit?->doctor?->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- SISI KANAN: STATUS SELURUH POLIKLINIK (GRID) -->
        <div class="lg:col-span-5 bg-slate-900 rounded-3xl p-6 border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold uppercase tracking-wider text-teal-400">Status Antrean Poliklinik</h3>
                <span class="text-xs text-slate-500">{{ $polyclinics->count() }} Poli Aktif</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4 flex-1">
                @foreach($polyclinics as $p)
                    <div class="bg-slate-800/70 rounded-2xl p-4 border border-slate-700/60 flex flex-col justify-between space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm text-white truncate">{{ $p['name'] }}</h4>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                                {{ $p['code'] }}
                            </span>
                        </div>

                        <div class="flex items-end justify-between py-1">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase block">Sedang Dilayani</span>
                                <span class="text-2xl font-black font-mono text-teal-400">{{ $p['current_code'] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase block">Berikutnya</span>
                                <span class="text-lg font-bold font-mono text-slate-300">{{ $p['next_code'] }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-700/60 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $p['location'] }}</span>
                            <span class="font-semibold text-amber-400">Menunggu: {{ $p['waiting_count'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3 rounded-xl bg-slate-800/40 text-center text-[11px] text-slate-400">
                Update status otomatis setiap 3 detik.
            </div>
        </div>
    </main>

    <!-- Bottombar: Marquee Running Text Pengumuman RS -->
    <footer class="bg-teal-900 border-t border-teal-700/60 text-white overflow-hidden py-2.5 px-4 flex items-center">
        <div class="shrink-0 px-3 py-1 rounded-lg bg-teal-800 text-[11px] font-black uppercase tracking-wider text-teal-200 mr-4 shadow-sm">
            Informasi RS
        </div>
        <div class="flex-1 overflow-hidden whitespace-nowrap">
            <div class="inline-block animate-marquee text-xs font-semibold tracking-wide text-teal-100">
                Selamat Datang di RS Aisyah Medika • Harap menjaga ketenangan di ruang tunggu poliklinik • Layanan IGD & Ambulans Siaga 24 Jam: (021) 8765-4322 • Check-in antrean mandiri dapat dilakukan di Kiosk Lobi Utama • Dilarang merokok di seluruh area rumah sakit • Terima kasih atas kepercayaan Anda.
            </div>
        </div>
    </footer>
</div>


@extends('errors.layout')

@section('title', '503 — Pemeliharaan Sistem')
@section('code', '503')
@section('badge', 'Status 503 • Pemeliharaan Sistem')
@section('badge_classes', 'bg-sky-500/10 text-sky-300 border border-sky-500/30')
@section('icon_wrapper_classes', 'bg-sky-500/10 border-sky-500/20 text-sky-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
    </svg>
@endsection

@section('heading', 'Sedang Dalam Pemeliharaan Berkala')

@section('message')
    <p>
        Sistem SIMRS RS Aisyah Medika sedang menjalani pemeliharaan berkala dan peningkatan kapasitas server untuk memberikan layanan yang lebih cepat, aman, dan andal.
    </p>
    <div class="mt-3 p-3 rounded-2xl bg-sky-950/30 border border-sky-900/60 text-sky-200 text-xs text-left">
        <p class="font-bold text-sky-300">Unit Gawat Darurat (IGD) Tetap Beroperasi Penuh:</p>
        <p class="mt-0.5 text-slate-300">Pelayanan IGD, rawat darurat, dan ambulans tetap siaga 24 jam nonstop di lokasi rumah sakit.</p>
    </div>
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-teal-600 text-white hover:bg-teal-500 transition shadow-lg shadow-teal-600/20">
        Periksa Kembali (Refresh)
    </button>
    <a href="tel:02187654322" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-rose-600 text-white hover:bg-rose-500 transition shadow-lg shadow-rose-600/20">
        IGD 24 Jam: (021) 8765-4322
    </a>
    <a href="tel:02187654321" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Call Center RS
    </a>
@endsection


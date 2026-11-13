@extends('errors.layout')

@section('title', '429 — Terlalu Banyak Permintaan')
@section('code', '429')
@section('badge', 'Status 429 • Batas Akses Terlampaui')
@section('badge_classes', 'bg-purple-500/10 text-purple-300 border border-purple-500/30')
@section('icon_wrapper_classes', 'bg-purple-500/10 border-purple-500/20 text-purple-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
    </svg>
@endsection

@section('heading', 'Terlalu Banyak Permintaan')

@section('message')
    <p>
        Sistem mendeteksi frekuensi pengiriman data atau pendaftaran antrean yang terlalu cepat dari alamat perangkat Anda (Rate Limiting).
    </p>
    <p class="mt-2 text-[11px] text-slate-400">
        Pembatasan ini dirancang untuk mencegah serangan spam bot dan menjaga stabilitas server rumah sakit. Silakan tunggu 1 menit lalu coba kembali.
    </p>
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-purple-600 text-white hover:bg-purple-500 transition shadow-lg shadow-purple-600/20">
        Coba Lagi Sekarang
    </button>
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Ke Beranda Utama
    </a>
    <a href="tel:02187654321" class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-medium text-slate-400 hover:text-white transition text-xs">
        Bantuan Call Center
    </a>
@endsection


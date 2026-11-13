@extends('errors.layout')

@section('title', '500 — Kendala Teknis Sistem')
@section('code', '500')
@section('badge', 'Status 500 • Server Error')
@section('badge_classes', 'bg-rose-500/10 text-rose-300 border border-rose-500/30')
@section('icon_wrapper_classes', 'bg-rose-500/10 border-rose-500/20 text-rose-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
@endsection

@section('heading', 'Terjadi Kendala Teknis Sistem')

@section('message')
    <p>
        Sistem SIMRS sedang mengalami gangguan teknis sementara. Tim IT RS Aisyah Medika telah menerima laporan otomatis ini dan sedang melakukan penanganan secepat mungkin.
    </p>
    <div class="mt-3 p-3 rounded-2xl bg-rose-950/30 border border-rose-900/60 text-rose-200 text-xs flex items-center space-x-2.5 text-left">
        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 animate-ping"></span>
        <span>Jika Anda memerlukan penanganan darurat segera, silakan hubungi IGD & Ambulans RS Aisyah Medika.</span>
    </div>
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-teal-600 text-white hover:bg-teal-500 transition shadow-lg shadow-teal-600/20">
        Muat Ulang Halaman
    </button>
    <a href="tel:02187654322" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-rose-600 text-white hover:bg-rose-500 transition shadow-lg shadow-rose-600/20">
        Hubungi IGD (021) 8765-4322
    </a>
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Ke Beranda
    </a>
@endsection

@extends('errors.layout')

@section('title', '419 — Sesi Kedaluwarsa')
@section('code', '419')
@section('badge', 'Status 419 • Sesi Kedaluwarsa')
@section('badge_classes', 'bg-amber-500/10 text-amber-300 border border-amber-500/30')
@section('icon_wrapper_classes', 'bg-amber-500/10 border-amber-500/20 text-amber-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endsection

@section('heading', 'Sesi Halaman Berakhir')

@section('message')
    <p>
        Demi keamanan data transaksi dan akun medis Anda, sesi formulir yang ditinggalkan tanpa aktivitas otomatis berakhir (CSRF Token Expired).
    </p>
    <p class="mt-2 text-[11px] text-slate-400">
        Silakan muat ulang halaman untuk memperbarui token keamanan sesi Anda dan kirimkan kembali data Anda.
    </p>
@endsection

@section('actions')
    <button type="button" onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-teal-600 text-white hover:bg-teal-500 transition shadow-lg shadow-teal-600/20 flex items-center justify-center space-x-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span>Muat Ulang Halaman</span>
    </button>
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Ke Beranda Utama
    </a>
@endsection


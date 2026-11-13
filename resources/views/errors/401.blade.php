@extends('errors.layout')

@section('title', '401 — Autentikasi Diperlukan')
@section('code', '401')
@section('badge', 'Status 401 • Sesi Diperlukan')
@section('badge_classes', 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/30')
@section('icon_wrapper_classes', 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
    </svg>
@endsection

@section('heading', 'Autentikasi Diperlukan')

@section('message')
    <p>
        Halaman atau layanan ini memerlukan autentikasi login aktif. Silakan masuk dengan akun pasien atau akun staf RS Aisyah Medika Anda untuk melanjutkan.
    </p>
@endsection

@section('actions')
    <a href="{{ route('login') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-teal-600 text-white hover:bg-teal-500 transition shadow-lg shadow-teal-600/20">
        Masuk ke Akun (Login)
    </a>
    <a href="{{ route('register') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Daftar Pasien Baru
    </a>
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-medium text-slate-400 hover:text-white transition text-xs">
        Ke Beranda
    </a>
@endsection


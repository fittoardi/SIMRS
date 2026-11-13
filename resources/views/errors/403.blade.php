@extends('errors.layout')

@section('title', '403 — Akses Ditolak')
@section('code', '403')
@section('badge', 'Status 403 • Akses Terbatas')
@section('badge_classes', 'bg-rose-500/10 text-rose-300 border border-rose-500/30')
@section('icon_wrapper_classes', 'bg-rose-500/10 border-rose-500/20 text-rose-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
@endsection

@section('heading', 'Akses Ditolak (Privasi Medis)')

@section('message')
    <p>
        {{ !empty($exception?->getMessage()) ? $exception->getMessage() : 'Anda tidak memiliki hak otorisasi untuk mengakses berkas rekam medis, billing, atau dokumen ini. Seluruh berkas pasien dilindungi oleh Kebijakan Privasi Rumah Sakit dan UU Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022).' }}
    </p>
    <p class="mt-2 text-[11px] text-slate-400">
        Jika Anda adalah pasien terdaftar atau penanggung jawab keluarga yang sah, pastikan Anda telah masuk dengan akun yang sesuai.
    </p>
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-teal-600 text-white hover:bg-teal-500 transition shadow-lg shadow-teal-600/20">
        Kembali ke Beranda
    </a>
    <a href="tel:02187654321" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Hubungi Call Center RS
    </a>
@endsection

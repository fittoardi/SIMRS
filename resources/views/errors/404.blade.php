@extends('errors.layout')

@section('title', '404 — Halaman Tidak Ditemukan')
@section('code', '404')
@section('badge', 'Status 404 • Not Found')
@section('badge_classes', 'bg-teal-500/10 text-teal-300 border border-teal-500/30')
@section('icon_wrapper_classes', 'bg-teal-500/10 border-teal-500/20 text-teal-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endsection

@section('heading', 'Halaman Tidak Ditemukan')

@section('message')
    <p>
        Halaman, jadwal dokter, atau dokumen yang Anda cari mungkin telah dipindahkan, dinonaktifkan, atau alamat URL yang Anda masukkan keliru.
    </p>
    <p class="mt-2 text-[11px] text-slate-400">
        Silakan periksa kembali penulisan tautan atau gunakan tombol navigasi cepat di bawah untuk menemukan layanan RS Aisyah Medika.
    </p>
@endsection

@section('actions')
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-teal-600 text-white hover:bg-teal-500 transition shadow-lg shadow-teal-600/20">
        Ke Beranda Utama
    </a>
    <a href="{{ url('/jadwal-dokter') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Cari Jadwal Dokter
    </a>
    <a href="{{ url('/ketersediaan-kamar') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-medium text-slate-400 hover:text-white transition text-xs">
        Kamar Rawat Inap
    </a>
@endsection

@extends('errors.layout')

@section('title', '400 — Permintaan Tidak Valid')
@section('code', '400')
@section('badge', 'Status 400 • Bad Request')
@section('badge_classes', 'bg-amber-500/10 text-amber-300 border border-amber-500/30')
@section('icon_wrapper_classes', 'bg-amber-500/10 border-amber-500/20 text-amber-400')

@section('icon')
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
@endsection

@section('heading', 'Permintaan Tidak Valid')

@section('message')
    <p>
        Data atau parameter yang dikirimkan oleh peramban Anda tidak sesuai dengan format yang diterima oleh sistem SIMRS. Silakan periksa kembali data isian Anda atau kembali ke halaman sebelumnya.
    </p>
    @if(!empty($exception?->getMessage()))
        <div class="mt-3 p-3 rounded-xl bg-amber-950/40 border border-amber-800/60 text-amber-200 text-xs font-mono text-left break-all">
            {{ $exception->getMessage() }}
        </div>
    @endif
@endsection

@section('actions')
    <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}';" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold bg-amber-500 text-slate-950 hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">
        ← Kembali ke Halaman Sebelumnya
    </button>
    <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white transition border border-slate-700">
        Ke Beranda Utama
    </a>
@endsection

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat Otomatis Jadwal Kunjungan H-1 via WhatsApp (Setiap Hari Pukul 08:00 WIB)
Schedule::command('hospital:send-reminders')
    ->dailyAt('08:00')
    ->description('Kirim notifikasi WhatsApp pengingat jadwal berobat poliklinik H-1');

<?php

use App\Http\Controllers\ProfileController;
use App\Models\Doctor;
use App\Models\Polyclinic;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Halaman Publik
Route::get('/jadwal-dokter', function () {
    return view('public.doctor-schedules');
})->name('public.doctor-schedules');

Route::get('/poliklinik/{polyclinic:slug}', function (Polyclinic $polyclinic) {
    $polyclinic->load(['doctors.schedules' => function ($q) {
        $q->where('is_active', true)->orderBy('day_of_week');
    }]);

    return view('public.polyclinic-detail', compact('polyclinic'));
})->name('public.polyclinic-detail');

Route::get('/dokter/{doctor:slug}', function (Doctor $doctor) {
    $doctor->load(['polyclinic', 'schedules' => function ($q) {
        $q->where('is_active', true)->orderBy('day_of_week');
    }]);

    return view('public.doctor-detail', compact('doctor'));
})->name('public.doctor-detail');

Route::get('/ketersediaan-kamar', function () {
    return view('public.bed-availability');
})->name('public.bed-availability');

Route::get('/layar-antrean', function () {
    return view('public.queue-display');
})->name('public.queue-display');

// Area Pasien Terautentikasi (/akun)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/akun', function () {
        $user = auth()->user();
        $patient = $user->patient;

        return view('patient.dashboard', compact('user', 'patient'));
    })->name('patient.dashboard');

    Route::get('/akun/booking', function () {
        return redirect()->route('patient.dashboard');
    })->name('patient.booking');

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('patient')) {
            return redirect()->route('patient.dashboard');
        }
        if ($user->hasAnyRole(['super_admin', 'doctor', 'nurse', 'admission', 'pharmacist', 'cashier'])) {
            return redirect('/admin');
        }

        return redirect()->route('patient.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use App\Enums\QueueStatus;
use App\Http\Controllers\PharmacyEtiketController;
use App\Http\Controllers\ProfileController;
use App\Models\Doctor;
use App\Models\Polyclinic;
use App\Models\Visit;
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

Route::get('/check-in', function () {
    return view('public.check-in');
})->name('public.check-in');

// Area Pasien Terautentikasi (/akun)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/akun', function () {
        $user = auth()->user();
        $patient = $user->patient;

        $patientIds = [];
        if ($patient) {
            $patientIds[] = $patient->id;
        }
        $familyPatientIds = $user->familyMembers()->pluck('patient_id')->toArray();
        $allPatientIds = array_unique(array_merge($patientIds, $familyPatientIds));

        $activeQueues = Visit::with(['patient', 'doctor.polyclinic', 'polyclinic', 'queue.doctorSchedule'])
            ->whereIn('patient_id', $allPatientIds)
            ->whereHas('queue', function ($q) {
                $q->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called, QueueStatus::Serving]);
            })
            ->orderBy('visit_date', 'asc')
            ->get();

        $familyCount = $user->familyMembers()->count();

        return view('patient.dashboard', compact('user', 'patient', 'activeQueues', 'familyCount'));
    })->name('patient.dashboard');

    Route::get('/akun/profil', function () {
        return view('patient.profile');
    })->name('patient.profile');

    Route::get('/akun/keluarga', function () {
        return view('patient.family');
    })->name('patient.family');

    Route::get('/akun/booking', function () {
        return view('patient.booking');
    })->name('patient.booking');

    Route::get('/akun/tiket/{visit}', function (Visit $visit) {
        return view('patient.ticket', compact('visit'));
    })->name('patient.ticket');

    Route::get('/akun/antrean', function () {
        return view('patient.queues');
    })->name('patient.queues');

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

    // Cetak Etiket Farmasi
    Route::get('/farmasi/etiket/{prescription}', PharmacyEtiketController::class)->name('pharmacy.etiket');
});

require __DIR__.'/auth.php';

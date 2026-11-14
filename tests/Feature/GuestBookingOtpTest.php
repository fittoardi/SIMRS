<?php

use App\Integrations\WhatsApp\WhatsAppProvider;
use App\Livewire\Public\GuestBooking;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\OtpVerification;
use App\Models\Patient;
use App\Models\Polyclinic;
use Carbon\Carbon;
use Livewire\Livewire;

test('guest booking allows patient to request and verify whatsapp otp before confirmation', function () {
    $capturedOtp = null;
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->atLeast()->once()
        ->withArgs(function ($phone, $message) use (&$capturedOtp) {
            if (preg_match('/🔐 \*(\d{6})\*/', $message, $matches)) {
                $capturedOtp = $matches[1];
            }

            return $phone === '6285608543495'
                && str_contains($message, 'KODE VERIFIKASI OTP');
        })
        ->andReturn(['success' => true]);

    app()->instance(WhatsAppProvider::class, $mockProvider);

    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Penyakit Dalam', 'code' => 'PD']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id, 'name' => 'dr. Hendra Sp.PD']);

    $tomorrow = Carbon::tomorrow();
    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id,
        'day_of_week' => $tomorrow->dayOfWeekIso,
        'start_time' => '09:00:00',
        'end_time' => '13:00:00',
        'max_quota' => 15,
        'is_active' => true,
    ]);

    $test = Livewire::test(GuestBooking::class)
        ->set('name', 'Budi Pratama')
        ->set('nik', '3501012345670001')
        ->set('phone', '085608543495')
        ->set('gender', 'L')
        ->set('birthDate', '1990-01-01')
        ->call('nextStep')
        ->set('selectedPolyclinicId', $polyclinic->id)
        ->set('selectedDoctorId', $doctor->id)
        ->call('nextStep')
        ->set('selectedDate', $tomorrow->format('Y-m-d'))
        ->set('selectedScheduleId', $schedule->id)
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        ->set('paymentType', 'general')
        // Request WhatsApp OTP
        ->call('requestBookingOtp')
        ->assertSet('otpSent', true)
        ->assertHasNoErrors();

    expect($capturedOtp)->not->toBeNull();
    $otpRecord = OtpVerification::where('phone', '6285608543495')->where('action', 'guest_booking')->first();
    expect($otpRecord)->not->toBeNull();

    // Verify entering wrong OTP fails
    $test->set('otpCode', '000000')
        ->call('verifyBookingOtp')
        ->assertHasErrors(['otpCode'])
        ->assertSet('otpVerified', false);

    // Verify entering correct OTP succeeds
    $test->set('otpCode', $capturedOtp)
        ->call('verifyBookingOtp')
        ->assertHasNoErrors()
        ->assertSet('otpVerified', true);

    // Finalize booking
    $test->call('confirmBooking')
        ->assertHasNoErrors();

    $patient = Patient::where('nik', '3501012345670001')->first();
    expect($patient)->not->toBeNull();
    expect($patient->phone)->toBe('085608543495');
});

<?php

use App\Integrations\WhatsApp\WhatsAppProvider;
use App\Models\OtpVerification;
use App\Models\Patient;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('registration step 1 validates input and redirects to whatsapp otp screen', function () {
    $capturedOtp = null;
    $mockProvider = Mockery::mock(WhatsAppProvider::class);
    $mockProvider->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($phone, $message) use (&$capturedOtp) {
            if (preg_match('/🔐 \*(\d{6})\*/', $message, $matches)) {
                $capturedOtp = $matches[1];
            }

            return $phone === '6281299998888'
                && str_contains($message, 'KODE VERIFIKASI OTP');
        })
        ->andReturn(['success' => true]);

    app()->instance(WhatsAppProvider::class, $mockProvider);

    $response = $this->post('/register', [
        'name' => 'Ahmad Fauzi',
        'email' => 'ahmad.fauzi@example.com',
        'nik' => '3171012345678999',
        'phone' => '081299998888',
        'gender' => 'L',
        'birth_date' => '1992-04-10',
        'blood_type' => 'B',
        'bpjs_number' => '0001999888777',
        'allergies' => 'Alergi Asam Mefenamat',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('register.otp'));

    // Verify OTP record created
    $otpRecord = OtpVerification::where('phone', '6281299998888')->first();
    expect($otpRecord)->not->toBeNull();
    expect($capturedOtp)->not->toBeNull();

    // Verify OTP Screen renders correctly
    $otpPageResponse = $this->get('/register/otp');
    $otpPageResponse->assertStatus(200);
    $otpPageResponse->assertSee('Verifikasi Nomor WhatsApp');
    $otpPageResponse->assertSee('0812-****-8888');

    // Test entering wrong OTP fails with error
    $wrongOtpResponse = $this->post('/register/otp/verify', [
        'otp_code' => '999999',
    ]);
    $wrongOtpResponse->assertSessionHasErrors(['otp_code']);
    $this->assertGuest();

    // Test entering valid OTP finishes registration & logs in user
    $validOtpResponse = $this->post('/register/otp/verify', [
        'otp_code' => $capturedOtp,
    ]);

    $validOtpResponse->assertSessionHasNoErrors();
    $this->assertAuthenticated();
    $validOtpResponse->assertRedirect(route('patient.dashboard', absolute: false));

    $user = User::where('email', 'ahmad.fauzi@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('patient'))->toBeTrue();

    $patient = Patient::where('user_id', $user->id)->first();
    expect($patient)->not->toBeNull();
    expect($patient->nik)->toBe('3171012345678999');
    expect($patient->allergies)->toBe('Alergi Asam Mefenamat');
    expect($patient->medical_record_number)->toStartWith('RM-');
});

test('registration validates 16 digit NIK format when provided', function () {
    $response = $this->post('/register', [
        'name' => 'Ahmad',
        'email' => 'ahmad@example.com',
        'phone' => '081234567890',
        'nik' => '123', // invalid length
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['nik']);
});

test('registration requires valid phone number for whatsapp otp verification', function () {
    $response = $this->post('/register', [
        'name' => 'Ahmad',
        'email' => 'ahmad2@example.com',
        'phone' => '', // missing phone
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['phone']);
});

test('otp screen redirects back if no registration session exists', function () {
    $response = $this->get('/register/otp');

    $response->assertRedirect(route('register'));
});

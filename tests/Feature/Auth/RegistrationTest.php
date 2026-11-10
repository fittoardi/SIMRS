<?php

use App\Models\Patient;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register as patient with full medical fields', function () {
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
    $this->assertAuthenticated();
    $response->assertRedirect(route('patient.dashboard', absolute: false));

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
        'nik' => '123', // invalid length
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['nik']);
});

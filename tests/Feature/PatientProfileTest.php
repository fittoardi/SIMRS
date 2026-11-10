<?php

use App\Livewire\Patient\PatientProfileForm;
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

test('guest cannot access patient profile page', function () {
    $response = $this->get('/akun/profil');

    $response->assertRedirect('/login');
});

test('authenticated user can view profile page and Livewire component', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/akun/profil');

    $response->assertOk();
    $response->assertSeeLivewire(PatientProfileForm::class);
});

test('patient can update their profile information and allergies', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PatientProfileForm::class)
        ->set('name', 'Budi Santoso')
        ->set('nik', '3171012345678901')
        ->set('gender', 'L')
        ->set('birth_date', '1990-05-15')
        ->set('blood_type', 'O')
        ->set('allergies', 'Alergi Penisilin dan Seafood')
        ->set('phone', '081234567890')
        ->set('address', 'Jl. Merdeka Barat No. 10 Jakarta')
        ->set('bpjs_number', '0001234567890')
        ->set('emergency_contact_name', 'Siti Aminah')
        ->set('emergency_contact_phone', '081298765432')
        ->set('emergency_contact_relation', 'Istri')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profil data rekam medis pasien berhasil disimpan');

    $patient = Patient::where('nik', '3171012345678901')->first();
    expect($patient)->not->toBeNull();
    expect($patient->name)->toBe('Budi Santoso');
    expect($patient->allergies)->toBe('Alergi Penisilin dan Seafood');
    expect($patient->bpjs_number)->toBe('0001234567890');
    expect($patient->medical_record_number)->toStartWith('RM-');
});

test('validation rejects invalid NIK length', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PatientProfileForm::class)
        ->set('name', 'Budi')
        ->set('nik', '12345') // Invalid: not 16 digits
        ->set('phone', '081234567890')
        ->set('address', 'Alamat')
        ->call('save')
        ->assertHasErrors(['nik']);
});

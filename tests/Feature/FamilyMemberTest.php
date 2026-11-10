<?php

use App\Livewire\Patient\FamilyMemberManager;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\User;
use Livewire\Livewire;

test('guest cannot access family member page', function () {
    $response = $this->get('/akun/keluarga');

    $response->assertRedirect('/login');
});

test('authenticated user can view family management page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/akun/keluarga');

    $response->assertOk();
    $response->assertSeeLivewire(FamilyMemberManager::class);
});

test('user can add family member and assign new medical record number', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(FamilyMemberManager::class)
        ->set('relationship', 'anak')
        ->set('nik', '3171098765432109')
        ->set('name', 'Ananda Pratama')
        ->set('gender', 'L')
        ->set('birth_date', '2018-08-20')
        ->set('blood_type', 'A')
        ->set('allergies', 'Alergi Susu Sapi')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Ananda Pratama berhasil didaftarkan');

    $patient = Patient::where('nik', '3171098765432109')->first();
    expect($patient)->not->toBeNull();
    expect($patient->name)->toBe('Ananda Pratama');
    expect($patient->medical_record_number)->toStartWith('RM-');

    $familyLink = PatientFamilyMember::where('user_id', $user->id)
        ->where('patient_id', $patient->id)
        ->first();
    expect($familyLink)->not->toBeNull();
    expect($familyLink->relationship)->toBe('anak');
});

test('user can remove a family member link', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create();

    $member = PatientFamilyMember::create([
        'user_id' => $user->id,
        'patient_id' => $patient->id,
        'relationship' => 'anak',
    ]);

    Livewire::actingAs($user)
        ->test(FamilyMemberManager::class)
        ->call('removeMember', $member->id)
        ->assertSee('telah dihapus dari daftar keluarga Anda');

    expect(PatientFamilyMember::find($member->id))->toBeNull();
});

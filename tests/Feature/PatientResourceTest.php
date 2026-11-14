<?php

use App\Filament\Resources\Patients\PatientResource;
use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admission', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('super admin and admission can access patient resource list in filament', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $patient = Patient::factory()->create([
        'name' => 'Budi Santoso',
        'medical_record_number' => 'RM-202611-0001',
    ]);

    $response = $this->actingAs($admin)->get('/admin/patients');

    $response->assertOk();
    $response->assertSee('Direktori Pasien');
    $response->assertSee('RM-202611-0001');
    $response->assertSee('Budi Santoso');
});

test('patient cannot access filament patient resource', function () {
    $patientUser = User::factory()->create();
    $patientUser->assignRole('patient');

    $response = $this->actingAs($patientUser)->get('/admin/patients');

    $response->assertForbidden();
});

test('rbac policy: admission can create patient but only super admin can delete', function () {
    $admission = User::factory()->create();
    $admission->assignRole('admission');

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');

    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');

    $patient = Patient::factory()->create();

    // Admission can create & view, but cannot delete
    $this->actingAs($admission);
    expect(PatientResource::canViewAny())->toBeTrue()
        ->and(PatientResource::canCreate())->toBeTrue()
        ->and(PatientResource::canDelete($patient))->toBeFalse();

    // Super Admin can do everything
    $this->actingAs($superAdmin);
    expect(PatientResource::canDelete($patient))->toBeTrue();

    // Doctor can view, but cannot delete
    $this->actingAs($doctor);
    expect(PatientResource::canViewAny())->toBeTrue()
        ->and(PatientResource::canDelete($patient))->toBeFalse();
});

test('hospital staff can view printable patient card', function () {
    $nurse = User::factory()->create();
    $nurse->assignRole('nurse');

    $patient = Patient::factory()->create([
        'name' => 'Ratna Sari',
        'medical_record_number' => 'RM-202611-0088',
        'nik' => '3171012345678901',
    ]);

    $response = $this->actingAs($nurse)->get("/pasien/kartu/{$patient->id}");

    $response->assertOk();
    $response->assertSee('RS Aisyah Medika');
    $response->assertSee('KARTU BEROBAT PASIEN');
    $response->assertSee('RM-202611-0088');
    $response->assertSee('Ratna Sari');
    $response->assertSee('SCAN KIOSK');
});

test('patient can view their own card and family member card', function () {
    $user = User::factory()->create();
    $user->assignRole('patient');

    $patient = Patient::factory()->create([
        'user_id' => $user->id,
        'name' => 'Ayah Pasien',
        'medical_record_number' => 'RM-202611-0101',
    ]);

    $childPatient = Patient::factory()->create([
        'user_id' => null,
        'name' => 'Anak Pasien',
        'medical_record_number' => 'RM-202611-0102',
    ]);

    PatientFamilyMember::create([
        'user_id' => $user->id,
        'patient_id' => $childPatient->id,
        'relationship' => 'anak',
    ]);

    // Kartu diri sendiri
    $this->actingAs($user)->get("/pasien/kartu/{$patient->id}")
        ->assertOk()
        ->assertSee('Ayah Pasien');

    // Kartu anak (keluarga terdaftar)
    $this->actingAs($user)->get("/pasien/kartu/{$childPatient->id}")
        ->assertOk()
        ->assertSee('Anak Pasien');
});

test('idor protection: patient A cannot access patient card of patient B', function () {
    $userA = User::factory()->create();
    $userA->assignRole('patient');
    $patientA = Patient::factory()->create(['user_id' => $userA->id]);

    $userB = User::factory()->create();
    $userB->assignRole('patient');
    $patientB = Patient::factory()->create(['user_id' => $userB->id]);

    // Pasien A mencoba mencetak kartu Pasien B
    $response = $this->actingAs($userA)->get("/pasien/kartu/{$patientB->id}");

    $response->assertForbidden();
});

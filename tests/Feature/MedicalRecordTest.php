<?php

use App\Models\Doctor;
use App\Models\MedicalAction;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\User;
use App\Models\Visit;
use App\Support\Icd10Catalog;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('doctor or super admin can access medical records list in filament', function () {
    $doctorUser = User::factory()->create();
    $doctorUser->assignRole('doctor');

    $response = $this->actingAs($doctorUser)->get('/admin/medical-records');

    $response->assertOk();
    $response->assertSee('Rekam Medis (RME)');
});

test('regular patient cannot access medical records admin page', function () {
    $patientUser = User::factory()->create();
    $patientUser->assignRole('patient');

    $response = $this->actingAs($patientUser)->get('/admin/medical-records');

    // Forbidden for patients in Filament panel
    expect($response->status())->toBeIn([403, 302]);
});

test('icd10 catalog returns relevant diagnoses and formatted options', function () {
    $results = Icd10Catalog::search('demam');

    expect($results)->not->toBeEmpty();
    expect($results)->toHaveKey('A01.0');
    expect($results['A01.0'])->toContain('Demam Tifoid');

    $label = Icd10Catalog::label('J06.9');
    expect($label)->toBe('J06.9 - Infeksi Saluran Pernapasan Akut (ISPA)');
});

test('medical record can be created with soap, vital signs, and actions', function () {
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Penyakit Dalam']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $patient = Patient::factory()->create([
        'name' => 'Ahmad Dahlan',
        'allergies' => 'Penisilin, Seafood',
    ]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => Carbon::today()->format('Y-m-d'),
    ]);

    $action = MedicalAction::factory()->create([
        'polyclinic_id' => $polyclinic->id,
        'name' => 'Elektrokardiografi (EKG)',
        'price' => 75000,
    ]);

    $medicalRecord = MedicalRecord::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'blood_pressure' => '130/85',
        'heart_rate' => 84,
        'respiratory_rate' => 20,
        'temperature' => 37.8,
        'weight' => 70.5,
        'height' => 172.0,
        'subjective' => 'Demam tinggi mendadak dan nyeri sendi sejak 2 hari.',
        'objective' => 'Suhu febris, uji torniket positif, tidak ada petekie spontan.',
        'assessment' => 'Demam Dengue / DHF Derajat 1',
        'icd10_code' => 'A90',
        'icd10_description' => 'Dengue fever [classical dengue]',
        'plan' => 'Cek darah rutin (Hb, Ht, Trombosit), hidrasi oral 2-3 liter/hari.',
    ]);

    // Attach medical record action
    $medicalRecord->actions()->create([
        'medical_action_id' => $action->id,
        'action_name_snapshot' => $action->name,
        'price_snapshot' => $action->price,
        'qty' => 1,
        'subtotal' => 75000,
    ]);

    expect($medicalRecord->id)->toBeGreaterThan(0);
    expect($medicalRecord->visit->patient->name)->toBe('Ahmad Dahlan');
    expect($medicalRecord->visit->patient->allergies)->toBe('Penisilin, Seafood');
    expect($medicalRecord->actions)->toHaveCount(1);
    expect($medicalRecord->actions->first()->action_name_snapshot)->toBe('Elektrokardiografi (EKG)');
    expect($medicalRecord->actions->first()->subtotal)->toBe('75000.00');
});

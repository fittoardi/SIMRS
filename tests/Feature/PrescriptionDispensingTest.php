<?php

use App\Enums\PrescriptionStatus;
use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
});

test('pharmacist can view prescriptions list in filament', function () {
    $pharmacist = User::factory()->create();
    $pharmacist->assignRole('pharmacist');

    $response = $this->actingAs($pharmacist)->get('/admin/prescriptions');

    $response->assertOk();
    $response->assertSee('Resep Obat & Dispensing');
});

test('dispensing medicines decrements stock and completes prescription and visit', function () {
    $pharmacist = User::factory()->create();
    $pharmacist->assignRole('pharmacist');

    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);
    $patient = Patient::factory()->create(['name' => 'Siti Khadijah']);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => Carbon::today()->format('Y-m-d'),
        'status' => VisitStatus::InExamination,
    ]);

    $medicine = Medicine::factory()->create([
        'name' => 'Paracetamol 500mg',
        'stock' => 50,
        'price' => 5000,
    ]);

    $prescription = Prescription::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'status' => PrescriptionStatus::Ready,
        'pharmacist_id' => $pharmacist->id,
    ]);

    PrescriptionItem::create([
        'prescription_id' => $prescription->id,
        'medicine_id' => $medicine->id,
        'medicine_name_snapshot' => $medicine->name,
        'qty' => 10,
        'dosage' => '3x1 tablet sesudah makan',
        'price_snapshot' => 5000,
        'subtotal' => 50000,
    ]);

    // Simulate dispensing process: decrement stock, mark handed over, complete visit
    $prescription->items->each(function ($item) {
        $med = Medicine::find($item->medicine_id);
        $med->decrement('stock', $item->qty);
    });

    $prescription->update([
        'status' => PrescriptionStatus::HandedOver,
    ]);

    $visit->update([
        'status' => VisitStatus::Completed,
    ]);

    expect($medicine->fresh()->stock)->toBe(40);
    expect($prescription->fresh()->status)->toBe(PrescriptionStatus::HandedOver);
    expect($visit->fresh()->status)->toBe(VisitStatus::Completed);
});

test('authorized staff can view thermal etiket page', function () {
    $pharmacist = User::factory()->create();
    $pharmacist->assignRole('pharmacist');

    $patient = Patient::factory()->create(['name' => 'Aisyah Binti Abu Bakar']);
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
        'visit_date' => Carbon::today()->format('Y-m-d'),
    ]);

    $medicine = Medicine::factory()->create([
        'name' => 'Amoxicillin 500mg',
        'unit' => 'Kapsul',
    ]);

    $prescription = Prescription::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'status' => PrescriptionStatus::Pending,
    ]);

    PrescriptionItem::create([
        'prescription_id' => $prescription->id,
        'medicine_id' => $medicine->id,
        'medicine_name_snapshot' => $medicine->name,
        'qty' => 15,
        'dosage' => '3x1 kapsul setiap 8 jam dihabiskan',
        'price_snapshot' => 12000,
        'subtotal' => 180000,
    ]);

    $response = $this->actingAs($pharmacist)->get("/farmasi/etiket/{$prescription->id}");

    $response->assertOk();
    $response->assertSee('RS AISYAH MEDIKA');
    $response->assertSee('INSTALASI FARMASI');
    $response->assertSee('Aisyah Binti Abu Bakar');
    $response->assertSee('Amoxicillin 500mg');
    $response->assertSee('3x1 kapsul setiap 8 jam dihabiskan');
    $response->assertSee("#RXP-{$prescription->id}");
});

test('patient is forbidden from accessing pharmacy etiket page', function () {
    $patientUser = User::factory()->create();
    $patientUser->assignRole('patient');

    $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $prescription = Prescription::create([
        'visit_id' => $visit->id,
        'doctor_id' => $doctor->id,
        'status' => PrescriptionStatus::Pending,
    ]);

    $response = $this->actingAs($patientUser)->get("/farmasi/etiket/{$prescription->id}");

    $response->assertForbidden();
});

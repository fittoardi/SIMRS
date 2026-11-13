<?php

use App\Enums\PaymentStatus;
use App\Livewire\Patient\PatientInvoiceDetail;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);

    $this->userA = User::factory()->create();
    $this->userA->assignRole('patient');
    $this->patientA = Patient::factory()->create(['user_id' => $this->userA->id]);

    $this->userB = User::factory()->create();
    $this->userB->assignRole('patient');
    $this->patientB = Patient::factory()->create(['user_id' => $this->userB->id]);

    $polyclinic = Polyclinic::factory()->create();
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id]);

    $this->visitA = Visit::factory()->create([
        'patient_id' => $this->patientA->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $this->invoiceA = Invoice::create([
        'number' => 'INV-TEST-0001',
        'visit_id' => $this->visitA->id,
        'patient_id' => $this->patientA->id,
        'subtotal' => 150000,
        'discount' => 0,
        'total' => 150000,
        'status' => PaymentStatus::Unpaid,
        'payment_status' => 'unpaid',
    ]);
    $this->invoiceA->items()->create([
        'item_type' => 'registration',
        'name_snapshot' => 'Konsultasi Dokter Umum',
        'qty' => 1,
        'price_snapshot' => 150000,
        'subtotal' => 150000,
    ]);

    $this->visitB = Visit::factory()->create([
        'patient_id' => $this->patientB->id,
        'doctor_id' => $doctor->id,
        'polyclinic_id' => $polyclinic->id,
    ]);

    $this->invoiceB = Invoice::create([
        'number' => 'INV-TEST-0002',
        'visit_id' => $this->visitB->id,
        'patient_id' => $this->patientB->id,
        'subtotal' => 200000,
        'discount' => 0,
        'total' => 200000,
        'status' => PaymentStatus::Unpaid,
        'payment_status' => 'unpaid',
    ]);
});

test('patient can access own invoices list', function () {
    $response = $this->actingAs($this->userA)->get('/akun/tagihan');

    $response->assertOk();
    $response->assertSee('Tagihan & Pembayaran');
    $response->assertSee($this->invoiceA->number);
    $response->assertDontSee($this->invoiceB->number);
});

test('patient can view own invoice detail page', function () {
    $response = $this->actingAs($this->userA)->get('/akun/tagihan/'.$this->invoiceA->id);

    $response->assertOk();
    $response->assertSee($this->invoiceA->number);
    $response->assertSee('Konsultasi Dokter Umum');
});

test('patient cannot view another patient invoice detail (IDOR prevention)', function () {
    $response = $this->actingAs($this->userA)->get('/akun/tagihan/'.$this->invoiceB->id);

    $response->assertForbidden();
});

test('patient can simulate digital payment on own invoice', function () {
    Livewire::actingAs($this->userA)
        ->test(PatientInvoiceDetail::class, ['invoiceId' => $this->invoiceA->id])
        ->set('selectedMethod', 'qris')
        ->call('payNow')
        ->assertSee('berhasil diverifikasi Lunas');

    expect($this->invoiceA->fresh()->status)->toBe(PaymentStatus::Paid);
});

test('patient can view thermal receipt of own invoice', function () {
    $response = $this->actingAs($this->userA)->get('/kasir/kwitansi/'.$this->invoiceA->id);

    $response->assertOk();
    $response->assertSee('RS AISYAH MEDIKA');
    $response->assertSee($this->invoiceA->number);
});

test('patient cannot view thermal receipt of another patient invoice', function () {
    $response = $this->actingAs($this->userA)->get('/kasir/kwitansi/'.$this->invoiceB->id);

    $response->assertForbidden();
});

test('cashier can view any thermal receipt', function () {
    $cashier = User::factory()->create();
    $cashier->assignRole('cashier');

    $response = $this->actingAs($cashier)->get('/kasir/kwitansi/'.$this->invoiceA->id);
    $response->assertOk();

    $response2 = $this->actingAs($cashier)->get('/kasir/kwitansi/'.$this->invoiceB->id);
    $response2->assertOk();
});

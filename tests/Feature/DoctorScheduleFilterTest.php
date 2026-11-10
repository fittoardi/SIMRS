<?php

use App\Livewire\Public\DoctorScheduleFilter;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Polyclinic;
use Livewire\Livewire;

beforeEach(function () {
    $this->poliUmum = Polyclinic::firstOrCreate(
        ['code' => 'POL-UMUM-TEST'],
        ['name' => 'Poli Umum Test', 'slug' => 'poli-umum-test', 'is_active' => true]
    );

    $this->poliGigi = Polyclinic::firstOrCreate(
        ['code' => 'POL-GIGI-TEST'],
        ['name' => 'Poli Gigi Test', 'slug' => 'poli-gigi-test', 'is_active' => true]
    );

    $this->docA = Doctor::firstOrCreate(
        ['slug' => 'dr-andi-spesialis'],
        [
            'polyclinic_id' => $this->poliUmum->id,
            'name' => 'dr. Andi Spesialis',
            'specialization' => 'Dokter Umum',
            'consultation_fee' => 50000,
            'is_active' => true,
        ]
    );

    $this->docB = Doctor::firstOrCreate(
        ['slug' => 'drg-budi-gigi'],
        [
            'polyclinic_id' => $this->poliGigi->id,
            'name' => 'drg. Budi Gigi',
            'specialization' => 'Dokter Gigi',
            'consultation_fee' => 100000,
            'is_active' => true,
        ]
    );

    DoctorSchedule::firstOrCreate(
        ['doctor_id' => $this->docA->id, 'day_of_week' => 1, 'start_time' => '08:00:00'],
        ['end_time' => '12:00:00', 'max_quota' => 20, 'is_active' => true]
    );

    DoctorSchedule::firstOrCreate(
        ['doctor_id' => $this->docB->id, 'day_of_week' => 3, 'start_time' => '13:00:00'],
        ['end_time' => '17:00:00', 'max_quota' => 15, 'is_active' => true]
    );
});

test('public doctor schedules page can be rendered', function () {
    $response = $this->get('/jadwal-dokter');

    $response->assertSuccessful();
    $response->assertSee('Direktori Jadwal Dokter');
});

test('can search doctor by name using livewire filter', function () {
    Livewire::test(DoctorScheduleFilter::class)
        ->set('search', 'Andi')
        ->assertSee('dr. Andi Spesialis')
        ->assertDontSee('drg. Budi Gigi');
});

test('can filter doctors by polyclinic', function () {
    Livewire::test(DoctorScheduleFilter::class)
        ->set('selectedPolyclinic', (string) $this->poliGigi->id)
        ->assertSee('drg. Budi Gigi')
        ->assertDontSee('dr. Andi Spesialis');
});

test('can filter doctors by day of week', function () {
    Livewire::test(DoctorScheduleFilter::class)
        ->set('selectedDay', '1') // Senin
        ->assertSee('dr. Andi Spesialis')
        ->assertDontSee('drg. Budi Gigi');
});

test('can view doctor detail profile page', function () {
    $response = $this->get('/dokter/'.$this->docA->slug);

    $response->assertSuccessful();
    $response->assertSee($this->docA->name);
    $response->assertSee('Physician'); // Schema.org JSON-LD
});

test('can view polyclinic detail page', function () {
    $response = $this->get('/poliklinik/'.$this->poliUmum->slug);

    $response->assertSuccessful();
    $response->assertSee($this->poliUmum->name);
});

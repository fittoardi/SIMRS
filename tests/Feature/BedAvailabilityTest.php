<?php

use App\Enums\BedStatus;
use App\Livewire\Public\BedAvailabilityBoard;
use App\Models\Bed;
use App\Models\Room;
use Livewire\Livewire;

beforeEach(function () {
    $this->roomVvip = Room::firstOrCreate(
        ['code' => 'ROOM-VVIP-TEST'],
        [
            'name' => 'Kamar VVIP Test',
            'ward_name' => 'Bangsal VVIP Test',
            'class_type' => 'VVIP',
            'price_per_day' => 1000000,
            'is_active' => true,
        ]
    );

    $this->roomK1 = Room::firstOrCreate(
        ['code' => 'ROOM-K1-TEST'],
        [
            'name' => 'Kamar K1 Test',
            'ward_name' => 'Bangsal K1 Test',
            'class_type' => 'Kelas 1',
            'price_per_day' => 400000,
            'is_active' => true,
        ]
    );

    Bed::firstOrCreate(
        ['room_id' => $this->roomVvip->id, 'bed_number' => 'VVIP-TEST-01'],
        ['status' => BedStatus::Available]
    );

    Bed::firstOrCreate(
        ['room_id' => $this->roomK1->id, 'bed_number' => 'K1-TEST-01'],
        ['status' => BedStatus::Occupied]
    );
});

test('public bed availability page can be rendered', function () {
    $response = $this->get('/ketersediaan-kamar');

    $response->assertSuccessful();
    $response->assertSee('Ketersediaan Tempat Tidur');
});

test('bed availability board calculates stats accurately and displays rooms', function () {
    Livewire::test(BedAvailabilityBoard::class)
        ->assertSee('Kamar VVIP Test')
        ->assertSee('Kamar K1 Test')
        ->assertSee('Tersedia')
        ->assertSee('Terisi');
});

test('can filter bed availability by class type', function () {
    Livewire::test(BedAvailabilityBoard::class)
        ->set('selectedClass', 'VVIP')
        ->assertSee('Kamar VVIP Test')
        ->assertDontSee('Kamar K1 Test');
});


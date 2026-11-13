<?php

use App\Models\Doctor;
use App\Models\Polyclinic;

test('public about page returns successful response with hospital info', function () {
    $response = $this->get('/tentang-kami');

    $response->assertOk();
    $response->assertSee('Profil & Visi RS Aisyah Medika', escape: false);
    $response->assertSee('Terakreditasi Paripurna KARS');
});

test('public patient flow page returns successful response', function () {
    $response = $this->get('/alur-berobat');

    $response->assertOk();
    $response->assertSee('Panduan Alur Pendaftaran & Pemeriksaan', escape: false);
    $response->assertSee('Check-In Mandiri');
});

test('public patient rights page returns successful response with law references', function () {
    $response = $this->get('/hak-kewajiban-pasien');

    $response->assertOk();
    $response->assertSee('Hak dan Kewajiban Pasien');
    $response->assertSee('Hak-Hak Pasien');
    $response->assertSee('Kewajiban Pasien');
});

test('public privacy policy page mentions uu pdp and rme regulations', function () {
    $response = $this->get('/kebijakan-privasi');

    $response->assertOk();
    $response->assertSee('Kebijakan Privasi & Perlindungan Data Medis', escape: false);
    $response->assertSee('Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)');
    $response->assertSee('Pengendalian Hak Akses Berjenjang');
});

test('public faq page returns common patient questions', function () {
    $response = $this->get('/faq');

    $response->assertOk();
    $response->assertSee('Tanya Jawab (FAQ) Pasien');
    $response->assertSee('BPJS Kesehatan');
    $response->assertSee('Kiosk Check-In');
});

test('public contact page returns official address and emergency numbers', function () {
    $response = $this->get('/kontak');

    $response->assertOk();
    $response->assertSee('Hubungi Kami & Lokasi RS', escape: false);
    $response->assertSee('(021) 8765-4321');
    $response->assertSee('(021) 8765-4322');
});

test('dynamic sitemap xml returns valid xml headers and hospital urls', function () {
    $polyclinic = Polyclinic::factory()->create(['name' => 'Poli Mata', 'slug' => 'poli-mata']);
    $doctor = Doctor::factory()->create(['polyclinic_id' => $polyclinic->id, 'slug' => 'dr-mata']);

    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee(url('/'));
    $response->assertSee(url('/jadwal-dokter'));
    $response->assertSee(url('/ketersediaan-kamar'));
    $response->assertSee(url('/tentang-kami'));
    $response->assertSee($doctor->slug);
    $response->assertSee($polyclinic->slug);
});

<?php

test('error 404 page renders custom hospital layout', function () {
    $response = $this->get('/halaman-acak-yang-tidak-ada-98765');

    $response->assertNotFound();
    $response->assertSee('RS Aisyah Medika');
    $response->assertSee('Halaman Tidak Ditemukan');
    $response->assertSee('Ke Beranda Utama');
    $response->assertSee('(021) 8765-4322');
});

test('error 400 view renders correctly', function () {
    $view = $this->view('errors.400', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 400 • Bad Request');
    $view->assertSee('Permintaan Tidak Valid');
    $view->assertSee('Kembali ke Halaman Sebelumnya');
});

test('error 401 view renders correctly', function () {
    $view = $this->view('errors.401', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 401 • Sesi Diperlukan');
    $view->assertSee('Autentikasi Diperlukan');
    $view->assertSee('Masuk ke Akun (Login)');
});

test('error 403 view renders correctly', function () {
    $view = $this->view('errors.403', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 403 • Akses Terbatas');
    $view->assertSee('Akses Ditolak (Privasi Medis)');
    $view->assertSee('Kembali ke Beranda');
});

test('error 419 view renders correctly', function () {
    $view = $this->view('errors.419', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 419 • Sesi Kedaluwarsa');
    $view->assertSee('Sesi Halaman Berakhir');
    $view->assertSee('Muat Ulang Halaman');
});

test('error 429 view renders correctly', function () {
    $view = $this->view('errors.429', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 429 • Batas Akses Terlampaui');
    $view->assertSee('Terlalu Banyak Permintaan');
    $view->assertSee('Coba Lagi Sekarang');
});

test('error 500 view renders correctly', function () {
    $view = $this->view('errors.500', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 500 • Server Error');
    $view->assertSee('Terjadi Kendala Teknis Sistem');
    $view->assertSee('Hubungi IGD (021) 8765-4322');
});

test('error 503 view renders correctly', function () {
    $view = $this->view('errors.503', ['exception' => null]);

    $view->assertSee('RS Aisyah Medika');
    $view->assertSee('Status 503 • Pemeliharaan Sistem');
    $view->assertSee('Sedang Dalam Pemeliharaan Berkala');
    $view->assertSee('Periksa Kembali (Refresh)');
    $view->assertSee('IGD 24 Jam: (021) 8765-4322');
});

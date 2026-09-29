<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Utama
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Modul 1: Perencanaan & Pelatihan peserta diklat (Diklat)
    Route::prefix('diklat')->name('diklat.')->group(function () {
        Route::livewire('/pelatihan', 'pages::diklat.pelatihan')->name('pelatihan');
        Route::redirect('/target', '/diklat/pelatihan');
        Route::livewire('/sertifikat', 'pages::diklat.sertifikat')->name('sertifikat');
        Route::livewire('/arsip', 'pages::diklat.arsip')
            ->middleware('role:admin_diklat,super_admin')
            ->name('arsip');
    });

    // Modul 2: Praktik Mahasiswa & Booking Unit RS (Diklit)
    Route::prefix('diklit')->name('diklit.')->group(function () {
        Route::livewire('/perguruan-tinggi', 'pages::diklit.perguruan-tinggi')->name('perguruan-tinggi');
        Route::livewire('/unit', 'pages::diklit.unit')->name('unit');
        Route::livewire('/booking', 'pages::diklit.booking')->name('booking');
        Route::redirect('/persetujuan', '/diklit/booking')->name('persetujuan');
        Route::livewire('/pembimbing', 'pages::diklit.pembimbing')->name('pembimbing');
        Route::livewire('/penilaian', 'pages::diklit.penilaian')->name('penilaian');
        Route::livewire('/kriteria', 'pages::diklit.kriteria')
            ->middleware('role:admin_diklat,super_admin')
            ->name('kriteria');
    });

    // Modul 3: Manajemen Akun & Hak Akses (RBAC)
    Route::prefix('pengguna')->name('pengguna.')->group(function () {
        // Akun Pengguna: Admin Diklat, Super Admin, dan Admin PT
        Route::livewire('/user', 'pages::pengguna.user')
            ->middleware('role:admin_diklat,super_admin,admin_pt')
            ->name('user');

        // Manajemen Role & Permissions Spatie: HANYA Super Admin
        Route::livewire('/role', 'pages::pengguna.role')
            ->middleware('role:super_admin')
            ->name('role');
    });
});

require __DIR__.'/settings.php';

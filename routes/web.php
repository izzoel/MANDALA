<?php

use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::get('/sso/redirect', [SsoController::class, 'redirect'])->name('sso.redirect');
Route::get('/callback', [SsoController::class, 'callback'])->name('sso.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sso/redirect/{nama}', [SsoController::class, 'ticket'])->name('sso.ticket');
    Route::get('/sso/redirect/{nama}/{ticket}', [SsoController::class, 'handoff'])->name('sso.handoff');

    // Dashboard Utama
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Modul 1: Perencanaan & Pelatihan peserta diklat (Diklat)
    Route::prefix('diklat')->name('diklat.')->group(function () {
        Route::livewire('/target', 'pages::diklat.target')->name('target');
        Route::livewire('/sertifikat', 'pages::diklat.sertifikat')->name('sertifikat');
        Route::livewire('/verifikasi', 'pages::diklat.verifikasi')
            ->middleware('role:admin_diklat,super_admin')
            ->name('verifikasi');
    });

    // Modul 2: Praktik Mahasiswa & Booking Unit RS (Diklit)
    Route::prefix('diklit')->name('diklit.')->group(function () {
        Route::livewire('/perguruan-tinggi', 'pages::diklit.perguruan-tinggi')->name('perguruan-tinggi');
        Route::livewire('/unit', 'pages::diklit.unit')->name('unit');
        Route::livewire('/booking', 'pages::diklit.booking')->name('booking');
        Route::livewire('/persetujuan', 'pages::diklit.persetujuan')
            ->middleware('role:admin_diklat,super_admin')
            ->name('persetujuan');
        Route::livewire('/pembimbing', 'pages::diklit.pembimbing')->name('pembimbing');
        Route::livewire('/penilaian', 'pages::diklit.penilaian')->name('penilaian');
        Route::livewire('/kriteria', 'pages::diklit.kriteria')
            ->middleware('role:admin_diklat,super_admin')
            ->name('kriteria');
    });

    // Modul 3: Manajemen Akun & Hak Akses (RBAC)
    Route::prefix('pengguna')->name('pengguna.')->group(function () {
        // Akun Pengguna: Admin Diklat & Super Admin
        Route::livewire('/user', 'pages::pengguna.user')
            ->middleware('role:admin_diklat,super_admin')
            ->name('user');

        // Manajemen Role & Permissions Spatie: HANYA Super Admin
        Route::livewire('/role', 'pages::pengguna.role')
            ->middleware('role:super_admin')
            ->name('role');
    });
});

require __DIR__.'/settings.php';

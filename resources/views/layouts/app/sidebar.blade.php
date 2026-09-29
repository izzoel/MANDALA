<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">

        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav class="space-y-2">
            <flux:sidebar.item wire:navigate icon="home" :href="route('dashboard')"
                :current="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </flux:sidebar.item>

            <!-- Modul 1: Diklat / Pelatihan Peserta Diklat -->
            @canany(['kelola-target-pelatihan', 'upload-sertifikat', 'arsip-sertifikat'])
                <flux:sidebar.group expandable heading="{{ __('Diklat (Pelatihan)') }}" class="grid">
                    @can('kelola-target-pelatihan')
                        <flux:sidebar.item :href="route('diklat.pelatihan')" :current="request()->routeIs('diklat.pelatihan')"
                            icon="academic-cap" wire:navigate>
                            {{ __('Pelatihan') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('upload-sertifikat')
                        <flux:sidebar.item :href="route('diklat.sertifikat')" :current="request()->routeIs('diklat.sertifikat')"
                            icon="document-check" wire:navigate>
                            {{ __('Sertifikat Peserta') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('arsip-sertifikat')
                        <flux:sidebar.item :href="route('diklat.arsip')" :current="request()->routeIs('diklat.arsip')"
                            icon="shield-check" wire:navigate>
                            {{ __('Arsip Sertifikat') }}
                        </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            @endcanany

            <!-- Modul 2: Diklit / Praktik Klinik & Mahasiswa -->
            @canany(['kelola-pt-mou', 'kelola-unit-rs', 'ajukan-booking-praktik', 'persetujuan-booking',
                'penunjukan-pembimbing', 'input-penilaian-praktik', 'lihat-rekap-nilai', 'kelola-kriteria-nilai'])
                <flux:sidebar.group expandable heading="{{ __('Diklit (Praktik RS)') }}" class="grid">
                    @can('kelola-pt-mou')
                        <flux:sidebar.item :href="route('diklit.perguruan-tinggi')"
                            :current="request()->routeIs('diklit.perguruan-tinggi')" icon="building-library" wire:navigate>
                            {{ __('Perguruan Tinggi (MoU)') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('kelola-unit-rs')
                        <flux:sidebar.item :href="route('diklit.unit')" :current="request()->routeIs('diklit.unit')"
                            icon="building-office-2" wire:navigate>
                            {{ __('Unit RS & Kuota') }}
                        </flux:sidebar.item>
                    @endcan

                    @canany(['ajukan-booking-praktik', 'persetujuan-booking'])
                        <flux:sidebar.item :href="route('diklit.booking')" :current="request()->routeIs('diklit.booking')"
                            icon="calendar-days" wire:navigate>
                            {{ __('Booking Permohonan') }}
                        </flux:sidebar.item>
                    @endcanany

                    @can('penunjukan-pembimbing')
                        <flux:sidebar.item :href="route('diklit.pembimbing')"
                            :current="request()->routeIs('diklit.pembimbing')" icon="user-group" wire:navigate>
                            {{ __('Penunjukan Pembimbing') }}
                        </flux:sidebar.item>
                    @endcan

                    @canany(['input-penilaian-praktik', 'lihat-rekap-nilai'])
                        <flux:sidebar.item :href="route('diklit.penilaian')" :current="request()->routeIs('diklit.penilaian')"
                            icon="star" wire:navigate>
                            {{ __('Penilaian Praktik') }}
                        </flux:sidebar.item>
                    @endcanany

                    @can('kelola-kriteria-nilai')
                        <flux:sidebar.item :href="route('diklit.kriteria')" :current="request()->routeIs('diklit.kriteria')"
                            icon="adjustments-horizontal" wire:navigate>
                            {{ __('Kriteria Evaluasi') }}
                        </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            @endcanany

            @canany(['kelola-user', 'kelola-akun-mahasiswa', 'kelola-role'])

                <flux:sidebar.group expandable heading="{{ __('Kelola Akun') }}" class="grid">
                    @canany(['kelola-user', 'kelola-akun-mahasiswa'])
                        <flux:sidebar.item :href="route('pengguna.user')" :current="request()->routeIs('pengguna.user')"
                            icon="users" wire:navigate>
                            {{ __('Manajemen Akun') }}
                        </flux:sidebar.item>
                    @endcanany

                    @can('kelola-role')
                        <flux:sidebar.item :href="route('pengguna.role')" :current="request()->routeIs('pengguna.role')"
                            icon="shield-check" wire:navigate>
                            {{ __('Manajemen Role') }}
                        </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            @endcanany

            <flux:sidebar.group expandable heading="{{ __('Pengaturan Akun') }}" class="grid">
                <flux:sidebar.item :href="route('profile.edit')" :current="request()->routeIs('profile.edit')"
                    icon="cog-6-tooth" wire:navigate>
                    {{ __('Profil & Keamanan') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <div class="mt-4 px-3 text-xs text-zinc-500 dark:text-zinc-400">
                <div class="font-semibold tracking-wide">MANDALA v1.0</div>
                <div class="text-[8px] text-zinc-400">Manajemen Diklat Akademik & Layanan Administrasi</div>

                <div class="mt-1">
                    © 2026 <a href="https://zetware.id" target="_blank"
                        class="text-primary-500 hover:underline"><i>zetware.id</i></a>
                </div>
            </div>
        </flux:sidebar.nav>

    </flux:sidebar>

    <!-- Mobile Header Navigation -->
    <flux:header class="block! border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 lg:bg-zinc-50">
        <flux:navbar scrollable>
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}</flux:navbar.item>

            {{-- @canany(['kelola-target-pelatihan', 'upload-sertifikat', 'verifikasi-sertifikat']) --}}
            @canany(['kelola-target-pelatihan', 'upload-sertifikat', 'arsip-sertifikat'])
                <flux:navbar.item :href="route('diklat.sertifikat')" :current="request()->routeIs('diklat.*')"
                    wire:navigate>
                    {{ __('Diklat') }}</flux:navbar.item>
            @endcanany

            @canany(['kelola-pt-mou', 'kelola-unit-rs', 'ajukan-booking-praktik', 'persetujuan-booking',
                'penunjukan-pembimbing', 'input-penilaian-praktik', 'lihat-rekap-nilai', 'kelola-kriteria-nilai'])
                <flux:navbar.item :href="route('diklit.booking')" :current="request()->routeIs('diklit.*')" wire:navigate>
                    {{ __('Diklit Praktik') }}</flux:navbar.item>
            @endcanany

            <flux:spacer />

            <flux:dropdown position="top" align="start">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}
                                        ({{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }})</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Pengaturan Akun') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.submenu heading="{{ __('Tema Tampilan') }}" icon="paint-brush">
                        <flux:menu.radio.group x-data x-model="$flux.appearance">
                            <flux:menu.radio value="light" icon="sun">{{ __('Light') }}</flux:menu.radio>
                            <flux:menu.radio value="dark" icon="moon">{{ __('Dark') }}</flux:menu.radio>
                            <flux:menu.radio value="system" icon="computer-desktop">{{ __('System') }}</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu.submenu>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:navbar>
    </flux:header>

    {{ $slot }}

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>

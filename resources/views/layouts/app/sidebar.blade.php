<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

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

            <!-- Modul 1: Diklat / Pelatihan Pegawai Non-ASN -->
            <flux:sidebar.group expandable heading="{{ __('Diklat (Pelatihan)') }}" class="grid">
                <flux:sidebar.item :href="route('diklat.target')"
                    :current="request()->routeIs('diklat.target')" icon="flag" wire:navigate>
                    {{ __('Target Pelatihan') }}
                </flux:sidebar.item>

                <flux:sidebar.item :href="route('diklat.sertifikat')"
                    :current="request()->routeIs('diklat.sertifikat')" icon="academic-cap" wire:navigate>
                    {{ __('Sertifikat Pegawai') }}
                </flux:sidebar.item>

                @if(auth()->user()->isAdminDiklat())
                    <flux:sidebar.item :href="route('diklat.verifikasi')"
                        :current="request()->routeIs('diklat.verifikasi')" icon="shield-check" wire:navigate>
                        {{ __('Verifikasi Sertifikat') }}
                    </flux:sidebar.item>
                @endif
            </flux:sidebar.group>

            <!-- Modul 2: Diklit / Praktik Klinik & Mahasiswa -->
            <flux:sidebar.group expandable heading="{{ __('Diklit (Praktik RS)') }}" class="grid">
                <flux:sidebar.item :href="route('diklit.perguruan-tinggi')"
                    :current="request()->routeIs('diklit.perguruan-tinggi')" icon="building-library" wire:navigate>
                    {{ __('Perguruan Tinggi (MoU)') }}
                </flux:sidebar.item>

                <flux:sidebar.item :href="route('diklit.unit')"
                    :current="request()->routeIs('diklit.unit')" icon="building-office-2" wire:navigate>
                    {{ __('Unit RS & Kuota') }}
                </flux:sidebar.item>

                <flux:sidebar.item :href="route('diklit.booking')"
                    :current="request()->routeIs('diklit.booking')" icon="calendar-days" wire:navigate>
                    {{ __('Booking Permohonan') }}
                </flux:sidebar.item>

                @if(auth()->user()->isAdminDiklat())
                    <flux:sidebar.item :href="route('diklit.persetujuan')"
                        :current="request()->routeIs('diklit.persetujuan')" icon="clipboard-document-check" wire:navigate>
                        {{ __('Persetujuan & Surat') }}
                    </flux:sidebar.item>
                @endif

                <flux:sidebar.item :href="route('diklit.pembimbing')"
                    :current="request()->routeIs('diklit.pembimbing')" icon="user-group" wire:navigate>
                    {{ __('Penunjukan Pembimbing') }}
                </flux:sidebar.item>

                <flux:sidebar.item :href="route('diklit.penilaian')"
                    :current="request()->routeIs('diklit.penilaian')" icon="star" wire:navigate>
                    {{ __('Penilaian Praktik') }}
                </flux:sidebar.item>

                @if(auth()->user()->isAdminDiklat())
                    <flux:sidebar.item :href="route('diklit.kriteria')"
                        :current="request()->routeIs('diklit.kriteria')" icon="adjustments-horizontal" wire:navigate>
                        {{ __('Kriteria Evaluasi') }}
                    </flux:sidebar.item>
                @endif
            </flux:sidebar.group>

            @if(auth()->user()->isAdminDiklat())
                <flux:sidebar.group expandable heading="{{ __('Pengguna & Akses') }}" class="grid">
                    <flux:sidebar.item :href="route('pengguna.user')"
                        :current="request()->routeIs('pengguna.user')" icon="users" wire:navigate>
                        {{ __('Manajemen Akun') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endif

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
                <div class="font-semibold tracking-wide">MANDALA RS v2.0</div>
                <div class="text-[10px] text-zinc-400">Sistem Diklat & Diklit Terintegrasi</div>

                <div class="mt-1">
                    © 2026 <a href="https://zetware.id" target="_blank" class="text-primary-500 hover:underline"><i>zetware.id</i></a>
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
            <flux:navbar.item :href="route('diklat.target')"
                :current="request()->routeIs('diklat.*')" wire:navigate>
                {{ __('Diklat') }}</flux:navbar.item>
            <flux:navbar.item :href="route('diklit.booking')"
                :current="request()->routeIs('diklit.*')" wire:navigate>
                {{ __('Diklit Praktik') }}</flux:navbar.item>

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
                                    <flux:text class="truncate">{{ auth()->user()->email }} ({{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }})</flux:text>
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

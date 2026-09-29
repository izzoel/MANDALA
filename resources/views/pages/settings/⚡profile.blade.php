<?php

use App\Concerns\ProfileValidationRules;
use App\Models\Mahasiswa;
use App\Models\PembimbingDosen;
use App\Models\PembimbingLapangan;
use App\Models\Peserta;
use App\Models\User;
/* @chisel-email-verification */
use Illuminate\Contracts\Auth\MustVerifyEmail;
/* @end-chisel-email-verification */
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan Profil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $status = 'aktif';

    // Form Fields Ekstensi Profil Peserta Diklat
    public string $no_pegawai = '';
    public string $unit_kerja = '';
    public string $jabatan = '';
    public string $tgl_mulai_kerja = '';

    // Form Fields Ekstensi Profil Mahasiswa
    public string $nim = '';
    public string $prodi = '';

    // Form Fields Ekstensi Profil Pembimbing Lapangan
    public string $nip_nik = '';
    public string $kontak = '';

    // Form Fields Ekstensi Profil Pembimbing Dosen
    public string $nidn = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->role = $user->role ?? '';
        $this->status = $user->status ?? 'aktif';

        if ($this->role === 'peserta_diklat' || $this->role === 'pegawai_non_asn') {
            $peserta = $user->peserta;
            $this->no_pegawai = $peserta?->no_pegawai ?? '';
            $this->unit_kerja = $peserta?->unit_kerja ?? '';
            $this->jabatan = $peserta?->jabatan ?? '';
            $this->tgl_mulai_kerja = $peserta?->tgl_mulai_kerja?->format('Y-m-d') ?? ($peserta?->tgl_mulai_kerja ? (string) $peserta->tgl_mulai_kerja : '');
        } elseif ($this->role === 'mahasiswa') {
            $mahasiswa = $user->mahasiswa;
            $this->nim = $mahasiswa?->nim ?? '';
            $this->prodi = $mahasiswa?->prodi ?? '';
        } elseif ($this->role === 'pembimbing_lapangan') {
            $pl = $user->pembimbingLapangan;
            $this->nip_nik = $pl?->nip_nik ?? '';
            $this->kontak = $pl?->kontak ?? '';
        } elseif ($this->role === 'pembimbing_dosen') {
            $pd = $user->pembimbingDosen;
            $this->nidn = $pd?->nidn ?? '';
            $this->kontak = $pd?->kontak ?? '';
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = $this->profileRules($user->id);

        if ($this->role === 'peserta_diklat' || $this->role === 'pegawai_non_asn') {
            $rules['no_pegawai'] = 'nullable|string|max:50';
            $rules['unit_kerja'] = 'nullable|string|max:100';
            $rules['jabatan'] = 'nullable|string|max:100';
            $rules['tgl_mulai_kerja'] = 'nullable|date';
        } elseif ($this->role === 'mahasiswa') {
            $rules['nim'] = 'nullable|string|max:50';
            $rules['prodi'] = 'nullable|string|max:100';
        } elseif ($this->role === 'pembimbing_lapangan') {
            $rules['nip_nik'] = 'nullable|string|max:50';
            $rules['kontak'] = 'nullable|string|max:50';
        } elseif ($this->role === 'pembimbing_dosen') {
            $rules['nidn'] = 'nullable|string|max:50';
            $rules['kontak'] = 'nullable|string|max:50';
        }

        $validated = $this->validate($rules);

        DB::transaction(function () use ($user, $validated) {
            $user->name = $this->name;
            $user->email = $this->email;

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Sync ekstensi profil jika ada
            if ($this->role === 'peserta_diklat' || $this->role === 'pegawai_non_asn') {
                Peserta::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama' => $user->name,
                        'no_pegawai' => $this->no_pegawai ?: ($user->peserta?->no_pegawai ?: 'PEG-' . rand(1000, 9999)),
                        'unit_kerja' => $this->unit_kerja ?: ($user->peserta?->unit_kerja ?: 'Unit Medis RS'),
                        'jabatan' => $this->jabatan ?: ($user->peserta?->jabatan ?: 'Staf Pelaksana'),
                        'tgl_mulai_kerja' => $this->tgl_mulai_kerja ?: ($user->peserta?->tgl_mulai_kerja ?: now()->toDateString()),
                    ]
                );
            } elseif ($this->role === 'mahasiswa' && $user->mahasiswa) {
                $user->mahasiswa->update([
                    'nama' => $user->name,
                    'nim' => $this->nim ?: $user->mahasiswa->nim,
                    'prodi' => $this->prodi ?: $user->mahasiswa->prodi,
                ]);
            } elseif ($this->role === 'pembimbing_lapangan' && $user->pembimbingLapangan) {
                $user->pembimbingLapangan->update([
                    'nama' => $user->name,
                    'nip_nik' => $this->nip_nik ?: $user->pembimbingLapangan->nip_nik,
                    'kontak' => $this->kontak ?: $user->pembimbingLapangan->kontak,
                ]);
            } elseif ($this->role === 'pembimbing_dosen' && $user->pembimbingDosen) {
                $user->pembimbingDosen->update([
                    'nama' => $user->name,
                    'nidn' => $this->nidn ?: $user->pembimbingDosen->nidn,
                    'kontak' => $this->kontak ?: $user->pembimbingDosen->kontak,
                ]);
            }
        });

        session()->flash('message', __('Profil berhasil diperbarui.'));
        Flux::toast(variant: 'success', text: __('Profil berhasil diperbarui.'));
    }

    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Pengaturan Profil') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profil')" :subheading="__('Perbarui nama dan alamat email akun Anda')">
        @if (session()->has('message'))
            <div
                class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field class="relative mb-2">
                    <flux:label>Nama Lengkap</flux:label>
                    <flux:input wire:model="name" placeholder="Nama Lengkap..." />
                    <flux:error name="name" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                </flux:field>

                <flux:field class="relative mb-2">
                    <flux:label>Alamat Email Login</flux:label>
                    <flux:input type="email" wire:model="email" placeholder="nama@mandala.test" />
                    <flux:error name="email" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                </flux:field>
            </div>

            @if ($role === 'peserta_diklat' || $role === 'pegawai_non_asn')
                <div
                    class="rounded-xl border border-blue-200 bg-blue-50/60 p-4 dark:border-blue-900/40 dark:bg-blue-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-blue-900 dark:text-blue-200">
                        Data Pegawai / Peserta Diklat
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:field class="relative mb-2">
                            <flux:label>Nomor Pegawai / NIK</flux:label>
                            <flux:input wire:model="no_pegawai" placeholder="Nomor Induk Pegawai..." />
                            <flux:error name="no_pegawai" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative mb-2">
                            <flux:label>Unit Kerja / Ruangan</flux:label>
                            <flux:input wire:model="unit_kerja" placeholder="Contoh: IGD, Rawat Inap Bedah, Farmasi" />
                            <flux:error name="unit_kerja" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:field class="relative mb-2">
                            <flux:label>Jabatan / Profesi</flux:label>
                            <flux:input wire:model="jabatan" placeholder="Contoh: Dokter Jaga, Perawat Pelaksana" />
                            <flux:error name="jabatan" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative mb-2">
                            <flux:label>Tanggal Mulai Bekerja</flux:label>
                            <flux:date-picker wire:model="tgl_mulai_kerja" />
                            <flux:error name="tgl_mulai_kerja" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>
                </div>
            @elseif ($role === 'mahasiswa')
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-emerald-900 dark:text-emerald-200">
                        Data Mahasiswa Praktikan
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:field class="relative mb-2">
                            <flux:label>Nomor Induk Mahasiswa (NIM)</flux:label>
                            <flux:input wire:model="nim" placeholder="NIM Mahasiswa..." />
                            <flux:error name="nim" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative mb-2">
                            <flux:label>Program Studi</flux:label>
                            <flux:input wire:model="prodi" placeholder="Contoh: S1 Keperawatan, Profesi Dokter" />
                            <flux:error name="prodi" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>
                </div>
            @elseif ($role === 'pembimbing_lapangan')
                <div
                    class="rounded-xl border border-purple-200 bg-purple-50/60 p-4 dark:border-purple-900/40 dark:bg-purple-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-purple-900 dark:text-purple-200">
                        Data Pembimbing Lapangan (CI RS)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:field class="relative mb-2">
                            <flux:label>NIP / NIK</flux:label>
                            <flux:input wire:model="nip_nik" placeholder="NIP/NIK..." />
                            <flux:error name="nip_nik" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative mb-2">
                            <flux:label>Nomor Kontak / WhatsApp</flux:label>
                            <flux:input wire:model="kontak" placeholder="08xxxxxxxxxx" />
                            <flux:error name="kontak" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>
                </div>
            @elseif ($role === 'pembimbing_dosen')
                <div
                    class="rounded-xl border border-indigo-200 bg-indigo-50/60 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-indigo-900 dark:text-indigo-200">
                        Data Pembimbing Dosen (PT)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:field class="relative mb-2">
                            <flux:label>NIDN / NIP</flux:label>
                            <flux:input wire:model="nidn" placeholder="NIDN Dosen..." />
                            <flux:error name="nidn" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative mb-2">
                            <flux:label>Nomor Kontak / WhatsApp</flux:label>
                            <flux:input wire:model="kontak" placeholder="08xxxxxxxxxx" />
                            <flux:error name="kontak" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>
                </div>
            @endif

            {{-- @chisel-email-verification --}}
            @if ($this->hasUnverifiedEmail)
                <div>
                    <flux:text class="mt-4">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium !dark:text-green-400 text-green-600!">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </flux:text>
                    @endif
                </div>
            @endif
            {{-- @end-chisel-email-verification --}}

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Simpan') }}
                </flux:button>
            </div>
        </form>

        {{-- @chisel-email-verification --}}
        @if ($this->showDeleteUser)
            {{-- @end-chisel-email-verification --}}
            <livewire:pages::settings.delete-user-form />
            {{-- @chisel-email-verification --}}
        @endif
        {{-- @end-chisel-email-verification --}}
    </x-pages::settings.layout>
</section>

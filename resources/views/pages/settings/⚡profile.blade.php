<?php

use App\Concerns\ProfileValidationRules;

use App\Models\Peserta;
use App\Models\User;

/* @chisel-email-verification */
use Illuminate\Contracts\Auth\MustVerifyEmail;
/* @end-chisel-email-verification */
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Title('Pengaturan Profil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $password = '';

    public string $role = 'peserta_diklat';
    public string $status = 'aktif';

    // Form Fields Ekstensi Profil Peserta Diklat
    public string $no_pegawai = '';

    public string $unit_kerja = '';

    public string $jabatan = '';

    public string $tgl_mulai_kerja = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;

        if ($this->role === 'peserta_diklat') {
            $peserta = Auth::user()->peserta;
            $this->no_pegawai = $peserta?->no_pegawai ?? '';
            $this->unit_kerja = $peserta?->unit_kerja ?? '';
            $this->jabatan = $peserta?->jabatan ?? '';
            $this->tgl_mulai_kerja = $peserta?->tgl_mulai_kerja?->format('Y-m-d') ?? '';
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

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

    public function saveUser(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::user()->id)],
            'role' => ['required', Rule::exists('roles', 'name')],
            'status' => 'required|in:aktif,nonaktif',
        ];

        if (!Auth::user()->id) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        // Validasi ekstensi profil sesuai role
        if ($this->role === 'peserta_diklat') {
            $rules['no_pegawai'] = 'required|string|max:50';
            $rules['unit_kerja'] = 'required|string|max:100';
            $rules['jabatan'] = 'required|string|max:100';
            $rules['tgl_mulai_kerja'] = 'required|date';
        } elseif ($this->role === 'mahasiswa') {
            $rules['pt_id'] = 'required|exists:perguruan_tinggis,id';
            $rules['nim'] = 'required|string|max:50';
            $rules['prodi'] = 'required|string|max:100';
        } elseif ($this->role === 'pembimbing_lapangan') {
            $rules['unit_id'] = 'required|exists:units,id';
        } elseif ($this->role === 'pembimbing_dosen') {
            $rules['pt_id'] = 'required|exists:perguruan_tinggis,id';
        }

        $this->validate($rules);

        DB::transaction(function () {
            if (Auth::user()->id) {
                $user = User::findOrFail(Auth::user()->id);
                $updateData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'role' => $this->role,
                    'status' => $this->status,
                ];

                if (!empty($this->password)) {
                    $updateData['password'] = Hash::make($this->password);
                }

                $user->update($updateData);

                // Update / Sync Profil Role
                $this->syncRoleProfile($user);

                session()->flash('message', "Akun pengguna '{$user->name}' berhasil diperbarui.");
            } else {
                $user = User::create([
                    'uuid' => Str::uuid(),
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role' => $this->role,
                    'status' => $this->status,
                ]);

                // Create Profil Role
                $this->syncRoleProfile($user);

                session()->flash('message', "Akun pengguna baru '{$user->name}' dengan peran {$user->role} berhasil ditambahkan.");
            }
        });
    }

    protected function syncRoleProfile(User $user): void
    {
        // Sync Spatie Role
        Role::firstOrCreate(['name' => $user->role, 'guard_name' => 'web']);
        $user->syncRoles([$user->role]);

        if ($user->role === 'peserta_diklat') {
            Peserta::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'no_pegawai' => $this->no_pegawai ?: 'PEG-' . rand(1000, 9999),
                    'unit_kerja' => $this->unit_kerja ?: 'Unit Medis RS',
                    'jabatan' => $this->jabatan ?: 'Staf Pelaksana',
                    'tgl_mulai_kerja' => $this->tgl_mulai_kerja ?: now()->toDateString(),
                ],
            );
        } elseif ($user->role === 'mahasiswa') {
            Mahasiswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'pt_id' => $this->pt_id,
                    'nim' => $this->nim ?: 'NIM-' . rand(10000, 99999),
                    'prodi' => $this->prodi ?: 'S1 Keperawatan / Ners',
                ],
            );
        } elseif ($user->role === 'pembimbing_lapangan') {
            PembimbingLapangan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'unit_id' => $this->unit_id,
                    'nip_nik' => $this->nip_nik,
                    'kontak' => $this->kontak,
                ],
            );
        } elseif ($user->role === 'pembimbing_dosen') {
            PembimbingDosen::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'pt_id' => $this->pt_id,
                    'nidn' => $this->nidn,
                    'kontak' => $this->kontak,
                ],
            );
        }
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Pengaturan Profil') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profil')" :subheading="__('Perbarui nama dan alamat email Anda')">
        @if (session()->has('message'))
            <div
                class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit="saveUser" class="my-6 w-full space-y-6">

            <div class="grid grid-cols-2 gap-4">
                <flux:field class="relative mb-2 ">
                    <flux:label>Nama Lengkap (dengan Gelar)</flux:label>
                    <flux:input wire:model="name" placeholder="Contoh: dr. Budi Santoso, Sp.A" />
                    <flux:error name="name" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                </flux:field>

                <flux:field class="relative mb-2">
                    <flux:label>Alamat Email Login</flux:label>
                    <flux:input type="email" wire:model="email" placeholder="nama@mandala.test" />
                    <flux:error name="email" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                </flux:field>
            </div>

            @if (Auth::user()->role === 'peserta_diklat')
                <div
                    class="rounded-xl border border-blue-200 bg-blue-50/60 p-4 dark:border-blue-900/40 dark:bg-blue-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-blue-900 dark:text-blue-200">Data
                        Peserta Diklat Rumah Sakit</h4>

                    <div class="grid grid-cols-2 gap-3">
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

                    <div class="grid grid-cols-2 gap-3">
                        <flux:field class="relative mb-2">
                            <flux:label>Jabatan / Profesi</flux:label>
                            <flux:input wire:model="jabatan" placeholder="Contoh: Dokter Jaga, Perawat Pelaksana" />
                            <flux:error name="jabatan" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative mb-2">
                            <flux:label>Tanggal Mulai Bekerja</flux:label>
                            <flux:input type="date" wire:model="tgl_mulai_kerja" />
                            <flux:error name="tgl_mulai_kerja" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
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
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Simpan') }}
                    </flux:button>
                </div>

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

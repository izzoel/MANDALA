<?php

use App\Models\Mahasiswa;
use App\Models\PembimbingDosen;
use App\Models\PembimbingLapangan;
use App\Models\PerguruanTinggi;
use App\Models\Peserta;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Title('Manajemen Akun & Hak Akses')] class extends Component
{
    public string $search = '';

    public string $filterRole = 'semua';

    public string $filterStatus = 'semua';

    // Modal State
    public bool $showModal = false;

    public ?int $editingUserId = null;

    // Form Fields User Utama
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

    // Form Fields Mahasiswa & Pembimbing Dosen
    public ?int $pt_id = null;

    public string $nim = '';

    public string $prodi = '';

    public string $nidn = '';

    // Form Fields Pembimbing Lapangan
    public ?int $unit_id = null;

    public string $nip_nik = '';

    public string $kontak = '';

    #[Computed]
    public function users()
    {
        return User::query()
            // ->with(['pegawaiNonAsn', 'mahasiswa.perguruanTinggi', 'pembimbingLapangan.unit', 'pembimbingDosen.perguruanTinggi'])
            ->with(['peserta', 'mahasiswa.perguruanTinggi', 'pembimbingLapangan.unit', 'pembimbingDosen.perguruanTinggi'])
            ->when($this->filterRole !== 'semua', fn ($q) => $q->where('role', $this->filterRole))
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(role) LIKE ?', [$term]);
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function perguruanTinggis()
    {
        return PerguruanTinggi::orderBy('nama_pt')->get();
    }

    #[Computed]
    public function units()
    {
        return Unit::orderBy('nama_unit')->get();
    }

    #[Computed]
    public function roles()
    {
        $user = Auth::user();

        if ($user) {
            if ($user->hasRole('admin_diklat')) {
                return Role::whereNotIn('name', ['super_admin', 'admin_diklat', 'mahasiswa'])->get();
            }
        }

        return Role::all();

    }

    public function openCreateModal(): void
    {
        $this->reset([
            'editingUserId', 'name', 'email', 'password', 'role', 'status',
            'no_pegawai', 'unit_kerja', 'jabatan', 'tgl_mulai_kerja',
            'pt_id', 'nim', 'prodi', 'nidn', 'unit_id', 'nip_nik', 'kontak',
        ]);
        $this->role = 'peserta_diklat';
        $this->status = 'aktif';
        $this->tgl_mulai_kerja = now()->toDateString();
        $this->showModal = true;
    }

    public function editUser(int $id): void
    {
        $user = User::with(['peserta', 'mahasiswa', 'pembimbingLapangan', 'pembimbingDosen'])->findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->status = $user->status;

        // Reset field profil
        $this->reset([
            'no_pegawai', 'unit_kerja', 'jabatan', 'tgl_mulai_kerja',
            'pt_id', 'nim', 'prodi', 'nidn', 'unit_id', 'nip_nik', 'kontak',
        ]);

        if ($user->role === 'peserta_diklat' && $user->peserta) {
            $this->no_pegawai = $user->peserta->no_pegawai;
            $this->unit_kerja = $user->peserta->unit_kerja;
            $this->jabatan = $user->peserta->jabatan;
            $this->tgl_mulai_kerja = $user->peserta->tgl_mulai_kerja ? $user->peserta->tgl_mulai_kerja->format('Y-m-d') : '';
        } elseif ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $this->pt_id = $user->mahasiswa->pt_id;
            $this->nim = $user->mahasiswa->nim;
            $this->prodi = $user->mahasiswa->prodi;
        } elseif ($user->role === 'pembimbing_lapangan' && $user->pembimbingLapangan) {
            $this->unit_id = $user->pembimbingLapangan->unit_id;
            $this->nip_nik = $user->pembimbingLapangan->nip_nik ?? '';
            $this->kontak = $user->pembimbingLapangan->kontak ?? '';
        } elseif ($user->role === 'pembimbing_dosen' && $user->pembimbingDosen) {
            $this->pt_id = $user->pembimbingDosen->pt_id;
            $this->nidn = $user->pembimbingDosen->nidn ?? '';
            $this->kontak = $user->pembimbingDosen->kontak ?? '';
        }

        $this->showModal = true;
    }

    public function saveUser(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'role' => ['required', Rule::exists('roles', 'name')],
            'status' => 'required|in:aktif,nonaktif',
        ];

        if (! $this->editingUserId) {
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
            if ($this->editingUserId) {
                $user = User::findOrFail($this->editingUserId);
                $updateData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'role' => $this->role,
                    'status' => $this->status,
                ];

                if (! empty($this->password)) {
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

        $this->showModal = false;
    }

    protected function syncRoleProfile(User $user): void
    {
        // Sync Spatie Role
        Role::firstOrCreate(['name' => $user->role, 'guard_name' => 'web']);
        $user->syncRoles([$user->role]);

        // if ($user->role === 'pegawai_non_asn') {
        if ($user->role === 'peserta_diklat') {
            // PegawaiNonAsn::updateOrCreate(
            Peserta::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'no_pegawai' => $this->no_pegawai ?: 'PEG-'.rand(1000, 9999),
                    'unit_kerja' => $this->unit_kerja ?: 'Unit Medis RS',
                    'jabatan' => $this->jabatan ?: 'Staf Pelaksana',
                    'tgl_mulai_kerja' => $this->tgl_mulai_kerja ?: now()->toDateString(),
                ]
            );
        } elseif ($user->role === 'mahasiswa') {
            Mahasiswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'pt_id' => $this->pt_id,
                    'nim' => $this->nim ?: 'NIM-'.rand(10000, 99999),
                    'prodi' => $this->prodi ?: 'S1 Keperawatan / Ners',
                ]
            );
        } elseif ($user->role === 'pembimbing_lapangan') {
            PembimbingLapangan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'unit_id' => $this->unit_id,
                    'nip_nik' => $this->nip_nik,
                    'kontak' => $this->kontak,
                ]
            );
        } elseif ($user->role === 'pembimbing_dosen') {
            PembimbingDosen::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'pt_id' => $this->pt_id,
                    'nidn' => $this->nidn,
                    'kontak' => $this->kontak,
                ]
            );
        }
    }

    public function toggleUserStatus(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan saat ini.');

            return;
        }

        $user = User::findOrFail($id);
        $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status' => $newStatus]);

        session()->flash('message', "Status akun '{$user->name}' berhasil diubah menjadi {$newStatus}.");
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

            return;
        }

        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();

        session()->flash('message', "Akun '{$userName}' berhasil dihapus dari sistem.");
    }
};

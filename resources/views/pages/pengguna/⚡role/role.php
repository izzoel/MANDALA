<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

new #[Title('Manajemen Role & Hak Akses (Spatie)')] class extends Component
{
    public string $search = '';

    // Modal Role
    public bool $showRoleModal = false;

    public string|int|null $editingRoleId = null;

    public string $role_name = '';

    public string $role_guard = 'web';

    public array $selectedPermissions = [];

    // Modal Permission
    public bool $showPermissionModal = false;

    public string $permission_name = '';

    public string $permission_category = 'Lainnya';

    // Core System Roles that cannot be deleted
    public array $coreRoles = [
        'super_admin',
        'admin_diklat',
        'admin_pt',
        'pegawai_non_asn',
        'pembimbing_lapangan',
        'pembimbing_dosen',
        'mahasiswa',
    ];

    // Metadata & Grouping Permission Sistem
    public array $permissionMetadata = [
        // 1. Sistem & Pengguna
        'kelola-role' => ['label' => 'Kelola Role & Permissions', 'group' => 'Manajemen Sistem & Pengguna', 'icon' => 'shield-check', 'desc' => 'Akses penuh konfigurasi peran dan izin Spatie'],
        'kelola-user' => ['label' => 'Kelola Akun Pengguna', 'group' => 'Manajemen Sistem & Pengguna', 'icon' => 'users', 'desc' => 'Menambah, mengedit, dan mengaktifkan akun pengguna'],
        'kelola-pengaturan-sistem' => ['label' => 'Kelola Pengaturan Sistem', 'group' => 'Manajemen Sistem & Pengguna', 'icon' => 'cog-6-tooth', 'desc' => 'Konfigurasi parameter aplikasi dan instansi rumah sakit'],

        // 2. Modul Diklat Pegawai
        'kelola-target-pelatihan' => ['label' => 'Kelola Target Pelatihan Pegawai', 'group' => 'Modul Diklat Peserta Diklat', 'icon' => 'academic-cap', 'desc' => 'Menetapkan target pelatihan wajib & fungsional tahunan'],
        'upload-sertifikat' => ['label' => 'Unggah Berkas Sertifikat', 'group' => 'Modul Diklat Peserta Diklat', 'icon' => 'arrow-up-tray', 'desc' => 'Mengunggah file sertifikat pelatihan ke sistem'],
        // 'verifikasi-sertifikat' => ['label' => 'Verifikasi Sertifikat Pegawai', 'group' => 'Modul Diklat Peserta Diklat', 'icon' => 'check-badge', 'desc' => 'Meninjau, menyetujui, dan menolak pengajuan sertifikat'],
        'arsip-sertifikat' => ['label' => 'Arsip Sertifikat Pegawai', 'group' => 'Modul Diklat Peserta Diklat', 'icon' => 'check-badge', 'desc' => 'Meninjau, menyetujui, dan menolak pengajuan sertifikat'],
        'lihat-rekap-pelatihan' => ['label' => 'Lihat Rekapitulasi Pelatihan Pegawai', 'group' => 'Modul Diklat Peserta Diklat', 'icon' => 'chart-bar', 'desc' => 'Memantau statistik pemenuhan target pelatihan'],

        // 3. Modul Diklit & Booking Praktik RS
        'kelola-pt-mou' => ['label' => 'Kelola Perguruan Tinggi & MoU', 'group' => 'Modul Praktik Mahasiswa RS (Diklit)', 'icon' => 'building-library', 'desc' => 'Mengatur institusi mitra dan masa berlaku MoU'],
        'kelola-unit-rs' => ['label' => 'Kelola Unit RS & Mode Kuota', 'group' => 'Modul Praktik Mahasiswa RS (Diklit)', 'icon' => 'building-office-2', 'desc' => 'Mengatur unit ruangan dan alokasi kuota per-prodi/gabungan'],
        'ajukan-booking-praktik' => ['label' => 'Ajukan Permohonan Booking Praktik', 'group' => 'Modul Praktik Mahasiswa RS (Diklit)', 'icon' => 'calendar-days', 'desc' => 'Mengajukan jadwal praktik klinik mahasiswa'],
        'persetujuan-booking' => ['label' => 'Persetujuan Permohonan Booking', 'group' => 'Modul Praktik Mahasiswa RS (Diklit)', 'icon' => 'document-check', 'desc' => 'Memproses review dan keputusan booking praktik'],
        'terbitkan-surat-rs' => ['label' => 'Terbitkan Surat Persetujuan RS', 'group' => 'Modul Praktik Mahasiswa RS (Diklit)', 'icon' => 'document-text', 'desc' => 'Menerbitkan nomor surat resmi persetujuan rumah sakit'],
        'penunjukan-pembimbing' => ['label' => 'Penunjukan Tim Pembimbing', 'group' => 'Modul Praktik Mahasiswa RS (Diklit)', 'icon' => 'user-plus', 'desc' => 'Menugaskan CI RS Lapangan dan Dosen Pembimbing'],

        // 4. Modul Penilaian & Evaluasi Klinis
        'kelola-kriteria-nilai' => ['label' => 'Kelola Kriteria & Bobot Nilai', 'group' => 'Modul Penilaian & Evaluasi Klinis', 'icon' => 'adjustments-horizontal', 'desc' => 'Mengatur parameter dan persentase bobot kelulusan stase'],
        'input-penilaian-praktik' => ['label' => 'Input Penilaian & Evaluasi Praktik', 'group' => 'Modul Penilaian & Evaluasi Klinis', 'icon' => 'clipboard-document-check', 'desc' => 'Mengisi nilai kompetensi klinis dan logbook mahasiswa'],
        'lihat-rekap-nilai' => ['label' => 'Lihat Transkrip & Rekap Nilai', 'group' => 'Modul Penilaian & Evaluasi Klinis', 'icon' => 'document-chart-bar', 'desc' => 'Melihat rekap nilai akhir dan transkrip kelulusan stase'],

        // 5. Laporan & Dokumen
        'ekspor-laporan-diklat' => ['label' => 'Ekspor Laporan Diklat Pegawai', 'group' => 'Laporan & Ekspor Dokumen', 'icon' => 'arrow-down-tray', 'desc' => 'Ekspor data pelatihan peserta diklat ke PDF/Excel'],
        'ekspor-laporan-diklit' => ['label' => 'Ekspor Laporan Praktik Mahasiswa', 'group' => 'Laporan & Ekspor Dokumen', 'icon' => 'arrow-down-tray', 'desc' => 'Ekspor data stase dan jadwal mahasiswa ke PDF/Excel'],
    ];

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->with(['permissions', 'users'])
            ->withCount('users')
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.strtolower($this->search).'%'))
            ->get();
    }

    #[Computed]
    public function allPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    #[Computed]
    public function groupedPermissions()
    {
        $permissions = Permission::orderBy('name')->get();

        $categoryIcons = [
            'Manajemen Sistem & Pengguna' => 'shield-check',
            'Modul Diklat Peserta Diklat' => 'academic-cap',
            'Modul Praktik Mahasiswa RS (Diklit)' => 'building-office-2',
            'Modul Penilaian & Evaluasi Klinis' => 'clipboard-document-check',
            'Laporan & Ekspor Dokumen' => 'document-chart-bar',
            'Izin Kustom & Ekstensi Lainnya' => 'cog-6-tooth',
        ];

        $grouped = [];
        foreach ($permissions as $perm) {
            $meta = $this->permissionMetadata[$perm->name] ?? [
                'label' => ucwords(str_replace(['-', '_'], ' ', $perm->name)),
                'group' => 'Izin Kustom & Ekstensi Lainnya',
                'icon' => 'key',
                'desc' => 'Izin kustom sistem tambahan',
            ];

            $groupName = $meta['group'];
            if (! isset($grouped[$groupName])) {
                $grouped[$groupName] = [
                    'category_icon' => $categoryIcons[$groupName] ?? 'key',
                    'permissions' => [],
                ];
            }

            $grouped[$groupName]['permissions'][] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'label' => $meta['label'],
                'icon' => $meta['icon'] ?? 'key',
                'desc' => $meta['desc'],
            ];
        }

        return $grouped;
    }

    public function openCreateRoleModal(): void
    {
        $this->reset(['editingRoleId', 'role_name', 'selectedPermissions']);
        $this->showRoleModal = true;
    }

    public function editRole(int $id): void
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->editingRoleId = $role->id;
        $this->role_name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showRoleModal = true;
    }

    public function selectAllPermissions(): void
    {
        $this->selectedPermissions = Permission::pluck('name')->toArray();
    }

    public function deselectAllPermissions(): void
    {
        $this->selectedPermissions = [];
    }

    public function toggleCategoryPermissions(string $category): void
    {
        $groupData = $this->groupedPermissions[$category] ?? null;
        if (! $groupData) {
            return;
        }

        $groupPermNames = array_column($groupData['permissions'], 'name');

        // Check if all in this category are currently selected
        $allSelected = count(array_intersect($groupPermNames, $this->selectedPermissions)) === count($groupPermNames);

        if ($allSelected) {
            // Deselect category
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $groupPermNames));
        } else {
            // Select all in category
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $groupPermNames)));
        }
    }

    public function applyPresetTemplate(string $preset): void
    {
        switch ($preset) {
            case 'super_admin':
                $this->selectedPermissions = Permission::pluck('name')->toArray();
                break;
            case 'admin_diklat':
                $this->selectedPermissions = [
                    'kelola-user',
                    'kelola-target-pelatihan',
                    'upload-sertifikat',
                    // 'verifikasi-sertifikat',
                    'arsip-sertifikat',
                    'lihat-rekap-pelatihan',
                    'kelola-pt-mou',
                    'kelola-unit-rs',
                    'persetujuan-booking',
                    'terbitkan-surat-rs',
                    'penunjukan-pembimbing',
                    'kelola-kriteria-nilai',
                    'lihat-rekap-nilai',
                    'ekspor-laporan-diklat',
                    'ekspor-laporan-diklit',
                ];
                break;
            case 'admin_pt':
                $this->selectedPermissions = [
                    'ajukan-booking-praktik',
                    'lihat-rekap-nilai',
                    'ekspor-laporan-diklit',
                ];
                break;
            case 'pembimbing_lapangan':
                $this->selectedPermissions = [
                    'input-penilaian-praktik',
                    'lihat-rekap-nilai',
                    'penunjukan-pembimbing',
                ];
                break;
            case 'pembimbing_dosen':
                $this->selectedPermissions = [
                    'input-penilaian-praktik',
                    'lihat-rekap-nilai',
                ];
                break;
            case 'pegawai_non_asn':
                $this->selectedPermissions = [
                    'upload-sertifikat',
                    'lihat-rekap-pelatihan',
                ];
                break;
            case 'mahasiswa':
                $this->selectedPermissions = [
                    'lihat-rekap-nilai',
                ];
                break;
        }

        // Filter only existing permissions
        $validPerms = Permission::pluck('name')->toArray();
        $this->selectedPermissions = array_values(array_intersect($this->selectedPermissions, $validPerms));
    }

    public function saveRole(): void
    {
        $this->validate([
            'role_name' => 'required|string|max:50|regex:/^[a-z0-9_\-]+$/',
            'selectedPermissions' => 'array',
        ], [
            'role_name.regex' => 'Format nama peran harus menggunakan huruf kecil, angka, garis bawah (_) atau strip (-).',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($this->editingRoleId) {
            $role = Role::findOrFail((int) $this->editingRoleId);

            // Core roles cannot be renamed
            if (! in_array($role->name, $this->coreRoles)) {
                $role->update(['name' => $this->role_name]);
            }

            $role->syncPermissions($this->selectedPermissions);
            session()->flash('message', "Peran '{$role->name}' dan hak akses izin berhasil diperbarui ({$role->permissions()->count()} izin aktif).");
        } else {
            $role = Role::create([
                'name' => $this->role_name,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($this->selectedPermissions);
            session()->flash('message', "Peran baru '{$role->name}' berhasil dibuat dengan {$role->permissions()->count()} izin yang dipilih.");
        }

        $this->showRoleModal = false;
    }

    public function deleteRole(int $id): void
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, $this->coreRoles)) {
            session()->flash('error', "Peran sistem inti '{$role->name}' dilindungi dan tidak boleh dihapus.");

            return;
        }

        if ($role->users()->count() > 0) {
            session()->flash('error', "Peran '{$role->name}' tidak dapat dihapus karena masih digunakan oleh {$role->users()->count()} pengguna.");

            return;
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        session()->flash('message', "Peran '{$roleName}' berhasil dihapus.");
    }

    public function openCreatePermissionModal(): void
    {
        $this->reset('permission_name');
        $this->showPermissionModal = true;
    }

    public function savePermission(): void
    {
        $this->validate([
            'permission_name' => 'required|string|max:50|unique:permissions,name|regex:/^[a-z0-9_\-]+$/',
        ], [
            'permission_name.regex' => 'Format nama izin harus menggunakan huruf kecil, angka, garis bawah (_) atau strip (-).',
        ]);

        Permission::create([
            'name' => $this->permission_name,
            'guard_name' => 'web',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showPermissionModal = false;
        session()->flash('message', "Izin (permission) baru '{$this->permission_name}' berhasil ditambahkan ke sistem.");
    }
};

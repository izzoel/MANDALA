<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new #[Title('Manajemen Role & Hak Akses (Spatie)')] class extends Component {
    public string $search = '';

    // Modal Role
    public bool $showRoleModal = false;
    public ?int $editingRoleId = null;
    public string $role_name = '';
    public array $selectedPermissions = [];

    // Modal Permission
    public bool $showPermissionModal = false;
    public string $permission_name = '';

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

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->with(['permissions', 'users'])
            ->withCount('users')
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%' . strtolower($this->search) . '%'))
            ->get();
    }

    #[Computed]
    public function allPermissions()
    {
        return Permission::orderBy('name')->get();
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

    public function saveRole(): void
    {
        $this->validate([
            'role_name' => 'required|string|max:50|regex:/^[a-z0-9_\-]+$/',
            'selectedPermissions' => 'array',
        ], [
            'role_name.regex' => 'Format nama peran harus menggunakan huruf kecil, angka, garis bawah (_) atau strip (-).',
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($this->editingRoleId) {
            $role = Role::findOrFail($this->editingRoleId);
            
            // Core roles cannot be renamed
            if (! in_array($role->name, $this->coreRoles)) {
                $role->update(['name' => $this->role_name]);
            }

            $role->syncPermissions($this->selectedPermissions);
            session()->flash('message', "Peran '{$role->name}' dan hak akses izin berhasil diperbarui.");
        } else {
            $role = Role::create([
                'name' => $this->role_name,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($this->selectedPermissions);
            session()->flash('message', "Peran baru '{$role->name}' berhasil dibuat dengan izin yang dipilih.");
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

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
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

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->showPermissionModal = false;
        session()->flash('message', "Izin (permission) baru '{$this->permission_name}' berhasil ditambahkan ke sistem.");
    }
};

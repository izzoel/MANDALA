<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-900">
                <flux:icon name="shield-check" class="size-3.5" />
                Khusus Super Administrator
            </div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white mt-1.5">Manajemen Role & Hak Akses (Laravel Spatie)</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Konfigurasi peran pengguna (Roles) dan matriks izin akses (Permissions) berbasis Spatie Permission engine.
            </flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="openCreatePermissionModal" variant="subtle" icon="key">
                Tambah Izin (Permission)
            </flux:button>
            <flux:button wire:click="openCreateRoleModal" variant="primary" icon="plus">
                Tambah Role Baru
            </flux:button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/50 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search Bar -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama peran (role)..." icon="magnifying-glass" />
    </div>

    <!-- Grid Kartu Role -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($this->roles as $r)
            @php
                $isCore = in_array($r->name, $this->coreRoles);
            @endphp
            <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base">{{ ucwords(str_replace('_', ' ', $r->name)) }}</h3>
                                @if($isCore)
                                    <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Inti</span>
                                @endif
                            </div>
                            <div class="text-xs font-mono text-zinc-400 mt-0.5">{{ $r->name }}</div>
                        </div>

                        <flux:dropdown align="end">
                            <flux:button size="xs" variant="subtle" icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item wire:click="editRole({{ $r->id }})" icon="pencil-square">Atur Izin Role</flux:menu.item>
                                @if(! $isCore)
                                    <flux:menu.separator />
                                    <flux:menu.item wire:click="deleteRole({{ $r->id }})" wire:confirm="Hapus peran ini?" icon="trash" class="text-rose-600">Hapus Role</flux:menu.item>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <!-- Badge Izin Terkait -->
                    <div class="space-y-2">
                        <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Izin Akses ({{ $r->permissions->count() }}):</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto pr-1">
                            @forelse($r->permissions as $p)
                                <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $p->name }}
                                </span>
                            @empty
                                <span class="text-xs text-zinc-400 italic">Belum ada izin yang ditetapkan</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                    <span class="flex items-center gap-1">
                        <flux:icon name="users" class="size-3.5" />
                        <strong>{{ $r->users_count }}</strong> Pengguna
                    </span>
                    <button wire:click="editRole({{ $r->id }})" class="text-primary-600 hover:underline font-semibold text-xs">
                        Ubah Hak Akses &rarr;
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500">
                Tidak ada peran (role) yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah / Edit Role -->
    <flux:modal wire:model="showRoleModal" class="min-w-2xl">
        <form wire:submit.prevent="saveRole" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $editingRoleId ? 'Konfigurasi Hak Akses Peran' : 'Tambah Peran (Role) Baru' }}</flux:heading>
                <flux:subheading>Tentukan nama peran dan centang izin-izin akses (Permissions) yang diperbolehkan.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Kode Nama Peran (Role Name)</flux:label>
                <flux:input wire:model="role_name" placeholder="contoh: kepala_instalasi, supervisor_klinis" :disabled="in_array($role_name, $coreRoles) && $editingRoleId" />
                <flux:error name="role_name" />
                @if(in_array($role_name, $coreRoles) && $editingRoleId)
                    <div class="text-[11px] text-zinc-400 mt-1">Nama peran sistem inti tidak dapat diubah, namun izin hak akses dapat disesuaikan.</div>
                @endif
            </flux:field>

            <!-- Matriks Checkbox Permissions -->
            <div class="space-y-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Pilih Izin Akses (Permissions)</span>
                    <span class="text-xs text-zinc-500">{{ count($selectedPermissions) }} Terpilih</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-64 overflow-y-auto p-1">
                    @foreach($this->allPermissions as $perm)
                        <label class="flex items-center gap-2 rounded-lg bg-white p-2.5 shadow-2xs cursor-pointer border border-zinc-200/60 dark:bg-zinc-900 dark:border-zinc-700 hover:border-primary-400">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $perm->name }}" class="rounded text-primary-600 focus:ring-primary-500 size-4" />
                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showRoleModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Konfigurasi Role</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Form Tambah Permission -->
    <flux:modal wire:model="showPermissionModal" class="min-w-md">
        <form wire:submit.prevent="savePermission" class="space-y-4">
            <div>
                <flux:heading size="lg">Tambah Izin Akses (Permission) Baru</flux:heading>
                <flux:subheading>Gunakan format lowercase dengan strip/garis bawah (misal: `cetak-laporan-diklit`).</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nama Izin (Permission Name)</flux:label>
                <flux:input wire:model="permission_name" placeholder="contoh: ekspor-laporan-keuangan" />
                <flux:error name="permission_name" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showPermissionModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Tambahkan Izin</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

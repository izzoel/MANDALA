<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-900">
                <flux:icon name="shield-check" class="size-3.5" />
                Khusus Super Administrator RS
            </div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white mt-1.5 flex items-center gap-2">
                <flux:icon name="key" class="size-6 text-primary-600 dark:text-primary-400" />
                Manajemen Role & Hak Akses (Laravel Spatie)
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Konfigurasi izin akses granular untuk seluruh peran administrator (Admin Diklat, Admin PT, CI Lapangan, Dosen, Pegawai) dan peran kustom rumah sakit.
            </flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="openCreatePermissionModal" variant="subtle" icon="plus-circle">
                Tambah Izin Baru
            </flux:button>
            <flux:button wire:click="openCreateRoleModal" variant="primary" icon="plus">
                Tambah Role Baru
            </flux:button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('message'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            <flux:icon name="check-circle" class="size-5 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/50 dark:text-rose-300">
            <flux:icon name="exclamation-triangle" class="size-5 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search Bar & Info -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama peran (role)..." icon="magnifying-glass" />
    </div>

    <!-- Grid Kartu Role -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($this->roles as $r)
            @php
                $isCore = in_array($r->name, $this->coreRoles);
                $permCount = $r->permissions->count();
                $totalPerms = $this->allPermissions->count();
                $permPct = $totalPerms > 0 ? round(($permCount / $totalPerms) * 100) : 0;
            @endphp
            <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base">{{ ucwords(str_replace('_', ' ', $r->name)) }}</h3>
                                @if($isCore)
                                    <span class="rounded bg-primary-100 px-2 py-0.5 text-[10px] font-bold text-primary-800 dark:bg-primary-950/60 dark:text-primary-300">Inti</span>
                                @else
                                    <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Kustom</span>
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

                    <!-- Coverage Progress Bar -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-zinc-500">Cakupan Izin Akses:</span>
                            <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $permCount }} / {{ $totalPerms }} Izin ({{ $permPct }}%)</span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-primary-500 transition-all duration-300" style="width: {{ $permPct }}%"></div>
                        </div>
                    </div>

                    <!-- Badge Izin Terkait -->
                    <div class="space-y-2">
                        <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto pr-1">
                            @forelse($r->permissions as $p)
                                @php
                                    $pMeta = $permissionMetadata[$p->name] ?? null;
                                    $pLabel = $pMeta ? $pMeta['label'] : $p->name;
                                @endphp
                                <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300" title="{{ $pMeta['desc'] ?? $p->name }}">
                                    {{ $pLabel }}
                                </span>
                            @empty
                                <span class="text-xs text-zinc-400 italic">Belum ada izin yang ditetapkan</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                    <span class="flex items-center gap-1 font-medium">
                        <flux:icon name="users" class="size-3.5" />
                        <strong>{{ $r->users_count }}</strong> Pengguna Terdaftar
                    </span>
                    <button wire:click="editRole({{ $r->id }})" class="text-primary-600 hover:underline font-semibold text-xs flex items-center gap-1">
                        Konfigurasi &rarr;
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500">
                Tidak ada peran (role) yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- ==================== MODAL FORM TAMBAH / EDIT ROLE ==================== -->
    <flux:modal wire:model="showRoleModal" class="min-w-2xl max-w-3xl">
        <form wire:submit.prevent="saveRole" class="space-y-5">
            <div>
                <flux:heading size="lg" class="font-bold">
                    {{ $editingRoleId ? 'Konfigurasi Hak Akses Peran: ' . ucwords(str_replace('_', ' ', $role_name)) : 'Tambah Peran (Role) Baru' }}
                </flux:heading>
                <flux:subheading>Tentukan izin akses yang dimiliki oleh peran ini. Anda dapat memilih izin per-kategori atau menerapkan template preset.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Kode Nama Peran (Role Name)</flux:label>
                <flux:input wire:model="role_name" placeholder="contoh: kepala_instalasi, supervisor_klinis" :disabled="in_array($role_name, $coreRoles) && $editingRoleId" />
                <flux:error name="role_name" />
                @if(in_array($role_name, $coreRoles) && $editingRoleId)
                    <div class="text-[11px] text-zinc-400 mt-1">Nama peran sistem inti tidak dapat diubah kodenya, namun izin hak akses dapat disesuaikan.</div>
                @endif
            </flux:field>

            <!-- Template Preset Quick Bar -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40 space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    <span class="flex items-center gap-1.5">
                        <flux:icon name="sparkles" class="size-4 text-amber-500" />
                        Terapkan Template Akses Otomatis:
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="selectAllPermissions" class="text-primary-600 hover:underline text-[11px] font-bold">
                            Pilih Semua
                        </button>
                        <span class="text-zinc-300">|</span>
                        <button type="button" wire:click="deselectAllPermissions" class="text-rose-600 hover:underline text-[11px] font-bold">
                            Hapus Semua
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5 pt-1">
                    <button type="button" wire:click="applyPresetTemplate('admin_diklat')" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-medium text-zinc-700 hover:border-primary-400 hover:bg-primary-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <flux:icon name="academic-cap" class="size-3.5 text-primary-500" />
                        Admin Diklat RS (Lengkap)
                    </button>
                    <button type="button" wire:click="applyPresetTemplate('admin_pt')" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-medium text-zinc-700 hover:border-primary-400 hover:bg-primary-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <flux:icon name="building-office-2" class="size-3.5 text-blue-500" />
                        Admin Perguruan Tinggi
                    </button>
                    <button type="button" wire:click="applyPresetTemplate('pembimbing_lapangan')" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-medium text-zinc-700 hover:border-primary-400 hover:bg-primary-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <flux:icon name="user-group" class="size-3.5 text-emerald-500" />
                        CI / Pembimbing Lapangan
                    </button>
                    <button type="button" wire:click="applyPresetTemplate('pembimbing_dosen')" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-medium text-zinc-700 hover:border-primary-400 hover:bg-primary-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <flux:icon name="book-open" class="size-3.5 text-purple-500" />
                        Dosen Pembimbing PT
                    </button>
                    <button type="button" wire:click="applyPresetTemplate('pegawai_non_asn')" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-medium text-zinc-700 hover:border-primary-400 hover:bg-primary-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <flux:icon name="identification" class="size-3.5 text-teal-500" />
                        Peserta Diklat
                    </button>
                    <button type="button" wire:click="applyPresetTemplate('super_admin')" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 hover:bg-rose-100 dark:border-rose-900 dark:bg-rose-950/60 dark:text-rose-300">
                        <flux:icon name="shield-check" class="size-3.5 text-rose-600" />
                        Super Admin (100% Akses)
                    </button>
                </div>
            </div>

            <!-- Matriks Checkbox Permissions Terkategori -->
            <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                @foreach($this->groupedPermissions as $categoryName => $categoryData)
                    @php
                        $perms = $categoryData['permissions'];
                        $categoryIcon = $categoryData['category_icon'] ?? 'key';
                        $groupPermNames = array_column($perms, 'name');
                        $selectedInGroup = count(array_intersect($groupPermNames, $selectedPermissions));
                        $totalInGroup = count($perms);
                    @endphp
                    <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-2 dark:border-zinc-800">
                            <div class="flex items-center gap-2">
                                <flux:icon name="{{ $categoryIcon }}" class="size-4 text-primary-600 dark:text-primary-400" />
                                <span class="font-bold text-xs uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                                    {{ $categoryName }}
                                </span>
                                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    {{ $selectedInGroup }}/{{ $totalInGroup }}
                                </span>
                            </div>

                            <button
                                type="button"
                                wire:click="toggleCategoryPermissions('{{ $categoryName }}')"
                                class="text-[11px] font-semibold text-primary-600 hover:underline"
                            >
                                {{ $selectedInGroup === $totalInGroup ? 'Batal Pilih Kategori' : 'Pilih Semua di Kategori Ini' }}
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($perms as $p)
                                <label class="flex items-start gap-2.5 rounded-lg border border-zinc-200/70 p-2.5 cursor-pointer transition hover:border-primary-400 hover:bg-zinc-50/50 dark:border-zinc-800 dark:hover:bg-zinc-800/40">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedPermissions"
                                        value="{{ $p['name'] }}"
                                        class="mt-0.5 rounded text-primary-600 focus:ring-primary-500 size-4"
                                    />
                                    <div class="space-y-0.5">
                                        <div class="text-xs font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                            <flux:icon name="{{ $p['icon'] }}" class="size-3.5 text-zinc-400" />
                                            <span>{{ $p['label'] }}</span>
                                        </div>
                                        <div class="text-[11px] text-zinc-500 leading-tight">{{ $p['desc'] }}</div>
                                        <div class="text-[10px] font-mono text-zinc-400 pt-0.5">{{ $p['name'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400">
                    Total: <strong class="text-primary-600">{{ count($selectedPermissions) }}</strong> Izin Terpilih
                </span>
                <div class="flex gap-2">
                    <flux:button type="button" variant="subtle" wire:click="$set('showRoleModal', false)">
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        Simpan Konfigurasi Role
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- ==================== MODAL TAMBAH PERMISSION ==================== -->
    <flux:modal wire:model="showPermissionModal" class="min-w-md">
        <form wire:submit.prevent="savePermission" class="space-y-4">
            <div>
                <flux:heading size="lg" class="font-bold">Tambah Izin Akses (Permission) Baru</flux:heading>
                <flux:subheading>Gunakan format huruf kecil dengan strip (kebab-case), contoh: `cetak-laporan-keuangan`.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Kode Nama Izin (Permission Name)</flux:label>
                <flux:input wire:model="permission_name" placeholder="contoh: ekspor-laporan-akreditasi" />
                <flux:error name="permission_name" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showPermissionModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Tambahkan Izin
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

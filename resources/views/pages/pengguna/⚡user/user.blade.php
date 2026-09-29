<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Manajemen Akun & Hak Akses (RBAC)
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pengelolaan akun pengguna, penetapan peran akses, status keaktifan, dan profil ekstensi tenaga kesehatan
                / mahasiswa.
            </flux:text>
        </div>

        <flux:button wire:click="openCreateModal" variant="primary" icon="user-plus">
            Tambah Akun Baru
        </flux:button>
    </div>

    @if (session()->has('message'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/50 dark:text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div
        class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama pengguna, email, atau peran..."
                icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterRole" variant="listbox" searchable>
                <flux:select.option value="semua">Semua Peran (Role)</flux:select.option>
                @foreach ($this->roles as $r)
                    <flux:select.option :wire:key="'filter-'.$r->id" value="{{ $r->name }}">
                        {{ ucwords(str_replace('_', ' ', $r->name)) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterStatus" variant="listbox">
                <flux:select.option value="semua">Semua Status</flux:select.option>
                <flux:select.option value="aktif">Aktif</flux:select.option>
                <flux:select.option value="nonaktif">Nonaktif</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div
        class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                <thead
                    class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pengguna & Email</th>
                        <th class="px-5 py-3.5">Peran (Role)</th>
                        <th class="px-5 py-3.5">Profil Terkait</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Tgl Dibuat</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($this->users as $u)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$u->name" :initials="$u->initials()" size="sm" />
                                    <div>
                                        <div class="font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                            {{ $u->name }}
                                            @if ($u->id === auth()->id())
                                                <span
                                                    class="rounded bg-primary-100 px-1.5 py-0.2 text-[10px] font-bold text-primary-800 dark:bg-primary-950 dark:text-primary-300">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-zinc-500">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $roleBadge = match ($u->role) {
                                        'super_admin'
                                            => 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300',
                                        'admin_diklat'
                                            => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-300',
                                        'admin_pt'
                                            => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950/50 dark:text-cyan-300',
                                        'peserta_diklat'
                                            => 'bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300',
                                        'pembimbing_lapangan'
                                            => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300',
                                        'pembimbing_dosen'
                                            => 'bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300',
                                        'mahasiswa'
                                            => 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
                                        default => 'bg-zinc-100 text-zinc-800',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $roleBadge }}">
                                    {{ ucwords(str_replace('_', ' ', $u->role)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400">
                                @if ($u->role === 'peserta_diklat' && $u->peserta)
                                    <div><strong
                                            class="text-zinc-800 dark:text-zinc-200">{{ $u->peserta->unit_kerja }}</strong>
                                        • {{ $u->peserta->jabatan }}</div>
                                    <div class="text-zinc-400">No: {{ $u->peserta->no_pegawai }}</div>
                                @elseif($u->role === 'mahasiswa' && $u->mahasiswa)
                                    <div><strong
                                            class="text-zinc-800 dark:text-zinc-200">{{ $u->mahasiswa->prodi }}</strong>
                                    </div>
                                    <div class="text-zinc-400">
                                        {{ $u->mahasiswa->perguruanTinggi->nama_pt ?? 'PT Mitra' }} (NIM:
                                        {{ $u->mahasiswa->nim }})</div>
                                @elseif($u->role === 'pembimbing_lapangan' && $u->pembimbingLapangan)
                                    <div><strong
                                            class="text-zinc-800 dark:text-zinc-200">{{ $u->pembimbingLapangan->unit->nama_unit ?? 'Unit RS' }}</strong>
                                    </div>
                                    <div class="text-zinc-400">NIP/NIK: {{ $u->pembimbingLapangan->nip_nik ?? '-' }}
                                    </div>
                                @elseif($u->role === 'pembimbing_dosen' && $u->pembimbingDosen)
                                    <div><strong
                                            class="text-zinc-800 dark:text-zinc-200">{{ $u->pembimbingDosen->perguruanTinggi->nama_pt ?? 'PT Mitra' }}</strong>
                                    </div>
                                    <div class="text-zinc-400">NIDN: {{ $u->pembimbingDosen->nidn ?? '-' }}</div>
                                @else
                                    <span class="text-zinc-400 italic">Akun Sistem Inti</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($u->status === 'aktif')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        <flux:icon name="check-circle" class="size-3" /> Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        <flux:icon name="no-symbol" class="size-3" /> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-zinc-400">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <flux:dropdown align="end">
                                    <flux:button size="sm" variant="subtle" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item wire:click="editUser({{ $u->id }})"
                                            icon="pencil-square">Edit Akun</flux:menu.item>
                                        @if ($u->id !== auth()->id())
                                            <flux:menu.item wire:click="toggleUserStatus({{ $u->id }})"
                                                icon="arrow-path">
                                                {{ $u->status === 'aktif' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="deleteUser({{ $u->id }})"
                                                wire:confirm="Yakin ingin menghapus akun ini secara permanen?"
                                                icon="trash" class="text-rose-600">
                                                Hapus Akun
                                            </flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-500">
                                Tidak ada akun pengguna yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit User -->
    <flux:modal wire:model="showModal" class="min-w-2xl">
        <form wire:submit.prevent="saveUser" class="space-y-4">
            <div>
                <flux:heading size="lg">
                    {{ $editingUserId ? 'Edit Akun & Peran Pengguna' : 'Tambah Akun Pengguna Baru' }}</flux:heading>
                <flux:subheading>Tentukan kredensial login, peran akses sistem (RBAC), serta data profil terkait.
                </flux:subheading>
            </div>

            <!-- Bagian 1: Akun Utama -->
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


            <div class="grid grid-cols-2 gap-4">
                <flux:field class="relative mb-2">
                    <flux:label>
                        {{ $editingUserId ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Kata Sandi (Password)' }}
                    </flux:label>
                    <flux:input type="password" wire:model="password" placeholder="Minimal 6 karakter" />
                    <flux:error name="password" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                </flux:field>

                <flux:field class="relative mb-2">
                    <flux:label>Peran Pengguna (Role RBAC)</flux:label>
                    <flux:select wire:model.live="role" variant="listbox" searchable
                        placeholder="Pilih peran pengguna...">
                        @foreach ($this->roles as $r)
                            <flux:select.option :wire:key="'role-'.$r->id" value="{{ $r->name }}">
                                {{ ucwords(str_replace('_', ' ', $r->name)) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="role" class="absolute left-0 -bottom-4 text-[11px] mt-3!" />
                </flux:field>

            </div>



            <!-- Bagian 2: Form Dinamis Ekstensi Profil Sesuai Role -->
            {{-- @if ($role === 'pegawai_non_asn') --}}
            @if ($role === 'peserta_diklat')
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
                            <flux:input wire:model="unit_kerja"
                                placeholder="Contoh: IGD, Rawat Inap Bedah, Farmasi" />
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
                            <flux:date-picker wire:model="tgl_mulai_kerja" />
                            <flux:error name="tgl_mulai_kerja" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>
                </div>
            @elseif($role === 'mahasiswa')
                <div
                    class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 dark:border-amber-900/40 dark:bg-amber-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-amber-900 dark:text-amber-200">Data
                        Mahasiswa Praktikan</h4>

                    <flux:field class="relative">
                        <flux:label>Perguruan Tinggi Asal</flux:label>
                        <flux:select wire:model="pt_id">
                            <flux:select.option value="">-- Pilih Institusi PT Mitra --</flux:select.option>
                            @foreach ($this->perguruanTinggis as $pt)
                                <flux:select.option :value="$pt->id">{{ $pt->nama_pt }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="pt_id" class="absolute left-0 -bottom-5 text-[11px] mt-3" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:field class="relative">
                            <flux:label>NIM (Nomor Induk Mahasiswa)</flux:label>
                            <flux:input wire:model="nim" placeholder="Contoh: 2106789012" />
                            <flux:error name="nim" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>

                        <flux:field class="relative">
                            <flux:label>Program Studi</flux:label>
                            <flux:input wire:model="prodi" placeholder="Contoh: S1 Keperawatan / Ners" />
                            <flux:error name="prodi" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                        </flux:field>
                    </div>
                </div>
            @elseif($role === 'pembimbing_lapangan')
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-emerald-900 dark:text-emerald-200">Data
                        Clinical Instructor (CI RS)</h4>

                    <flux:field class="relative">
                        <flux:label>Unit Penugasan di Rumah Sakit</flux:label>
                        <flux:select wire:model="unit_id">
                            <flux:select.option value="">-- Pilih Unit RS --</flux:select.option>
                            @foreach ($this->units as $u)
                                <flux:select.option :value="$u->id">{{ $u->nama_unit }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="unit_id" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:field class="relative">
                            <flux:label>NIP / NIK Pembimbing</flux:label>
                            <flux:input wire:model="nip_nik" placeholder="NIP/NIK resmi" />
                        </flux:field>

                        <flux:field class="relative">
                            <flux:label>Nomor Kontak / WhatsApp</flux:label>
                            <flux:input wire:model="kontak" placeholder="0812..." />
                        </flux:field>
                    </div>
                </div>
            @elseif($role === 'pembimbing_dosen')
                <div
                    class="rounded-xl border border-purple-200 bg-purple-50/60 p-4 dark:border-purple-900/40 dark:bg-purple-950/30 space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-purple-900 dark:text-purple-200">Data
                        Dosen Pembimbing Institusi PT</h4>

                    <flux:field class="relative">
                        <flux:label>Perguruan Tinggi Asal</flux:label>
                        <flux:select wire:model="pt_id">
                            <flux:select.option value="">-- Pilih Institusi PT Mitra --</flux:select.option>
                            @foreach ($this->perguruanTinggis as $pt)
                                <flux:select.option :value="$pt->id">{{ $pt->nama_pt }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="pt_id" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:field class="relative">
                            <flux:label>NIDN Dosen</flux:label>
                            <flux:input wire:model="nidn" placeholder="Nomor Induk Dosen Nasional" />
                        </flux:field>

                        <flux:field class="relative">
                            <flux:label>Nomor Kontak / WhatsApp</flux:label>
                            <flux:input wire:model="kontak" placeholder="0813..." />
                        </flux:field>
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showModal', false)">Batal
                </flux:button>
                <flux:button type="submit" variant="primary">Simpan Akun</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

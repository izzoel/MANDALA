<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Target Pelatihan Peserta Diklat</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pemantauan dan penugasan target pelatihan wajib & kompetensi fungsional tenaga kesehatan dan staf rumah sakit.
            </flux:text>
        </div>

        @if(auth()->user()->isAdminDiklat())
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Tambah Target Pelatihan
            </flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama pegawai, unit kerja, atau nama pelatihan..." icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="semua">Semua Status</flux:select.option>
                <flux:select.option value="belum">Belum Dimulai</flux:select.option>
                <flux:select.option value="proses">Dalam Proses</flux:select.option>
                <flux:select.option value="selesai">Selesai / Terpenuhi</flux:select.option>
            </flux:select>
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterPeriode">
                <flux:select.option value="semua">Semua Periode</flux:select.option>
                <flux:select.option value="2026">Tahun 2026</flux:select.option>
                <flux:select.option value="2025">Tahun 2025</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Table of Targets -->
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai & Unit Kerja</th>
                        <th class="px-5 py-3.5">Nama Target Pelatihan</th>
                        <th class="px-5 py-3.5">Kategori / Periode</th>
                        <th class="px-5 py-3.5">Tenggat Waktu</th>
                        <th class="px-5 py-3.5">Status Pemenuhan</th>
                        @if(auth()->user()->isAdminDiklat())
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($this->targets as $target)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $target->pegawai->nama ?? '-' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $target->pegawai->no_pegawai ?? '-' }} • {{ $target->pegawai->unit_kerja ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $target->nama_target }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $target->kategori === 'Wajib' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }}">
                                    {{ $target->kategori }}
                                </div>
                                <span class="ml-1.5 text-xs text-zinc-500">Tahun {{ $target->periode }}</span>
                            </td>
                            <td class="px-5 py-4 text-xs font-medium">
                                <div class="{{ $target->tenggat < now()->toDateString() && $target->status !== 'selesai' ? 'text-red-600 font-bold' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    {{ \Carbon\Carbon::parse($target->tenggat)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($target->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        <flux:icon name="check-circle" class="size-3.5" /> Selesai
                                    </span>
                                    @if($target->sertifikatPemenuhan)
                                        <div class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                                            No. Sert: {{ $target->sertifikatPemenuhan->no_sertifikat }}
                                        </div>
                                    @endif
                                @elseif($target->status === 'proses')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                        <flux:icon name="clock" class="size-3.5" /> Dalam Proses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                        Belum Dimulai
                                    </span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdminDiklat())
                                <td class="px-5 py-4 text-right">
                                    <flux:dropdown align="end">
                                        <flux:button size="sm" variant="subtle" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item wire:click="updateStatus({{ $target->id }}, 'selesai')" icon="check">Tandai Selesai</flux:menu.item>
                                            <flux:menu.item wire:click="updateStatus({{ $target->id }}, 'proses')" icon="arrow-path">Tandai Proses</flux:menu.item>
                                            <flux:menu.item wire:click="updateStatus({{ $target->id }}, 'belum')" icon="clock">Tandai Belum</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data target pelatihan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Target -->
    <flux:modal wire:model="showModal" class="min-w-lg">
        <form wire:submit.prevent="saveTarget" class="space-y-4">
            <div>
                <flux:heading size="lg">Penugasan Target Pelatihan</flux:heading>
                <flux:subheading>Tugaskan kewajiban pelatihan kompetensi bagi peserta diklat.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Pilih Peserta Diklat</flux:label>
                <flux:select wire:model="pegawai_id" placeholder="Pilih Pegawai">
                    @foreach($this->listPegawai as $pegawai)
                        <flux:select.option :value="$pegawai->id">{{ $pegawai->nama }} ({{ $pegawai->unit_kerja }} - {{ $pegawai->jabatan }})</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="pegawai_id" />
            </flux:field>

            <flux:field>
                <flux:label>Nama Target Pelatihan</flux:label>
                <flux:input wire:model="nama_target" placeholder="Contoh: Pelatihan BHD / ACLS / Keselamatan Pasien" />
                <flux:error name="nama_target" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="kategori">
                        <flux:select.option value="Wajib">Wajib (Klinis/K3)</flux:select.option>
                        <flux:select.option value="Fungsional">Fungsional Profesi</flux:select.option>
                        <flux:select.option value="Pilihan">Pilihan / Pengembangan</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Periode Tahun</flux:label>
                    <flux:input wire:model="periode" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Tenggat Waktu Pemenuhan</flux:label>
                <flux:input type="date" wire:model="tenggat" />
                <flux:error name="tenggat" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Target</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Perguruan Tinggi Mitra & Status
                MoU</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pencatatan institusi pendidikan mitra, masa berlaku nota kesepahaman (MoU), dan akses pengajuan praktik
                klinik RS.
            </flux:text>
        </div>

        @if (auth()->user()->isAdminDiklat())
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Tambah Institusi PT
            </flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search & Filter -->
    <div
        class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
        <div class="sm:col-span-8">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Cari nama perguruan tinggi, email, atau kontak..." icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-4">
            <flux:select wire:model.live="filterStatus" variant="listbox" placeholder="Pilih Status">
                <flux:select.option value="semua">Semua Status MoU</flux:select.option>
                <flux:select.option value="aktif">MoU Masih Aktif</flux:select.option>
                <flux:select.option value="expired">MoU Kedaluwarsa / Nonaktif</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Cards Perguruan Tinggi -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($this->perguruanTinggis as $pt)
            @php
                $isValid = $pt->isMouValid();
            @endphp
            <div
                class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isValid ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300' }}">
                            @if ($isValid)
                                <flux:icon name="check-badge" class="size-3.5" /> MoU Aktif
                            @else
                                <flux:icon name="exclamation-triangle" class="size-3.5" /> Kedaluwarsa / Non-Aktif
                            @endif
                        </span>

                        @if (auth()->user()->isAdminDiklat())
                            <flux:dropdown align="end">
                                <flux:button size="xs" variant="subtle" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item wire:click="editPt({{ $pt->id }})" icon="pencil-square">Edit
                                        Data</flux:menu.item>
                                    <flux:menu.item wire:click="toggleStatus({{ $pt->id }})" icon="arrow-path">
                                        Ubah Status MoU</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white line-clamp-2 text-base">{{ $pt->nama_pt }}
                        </h4>
                        <p class="text-xs text-zinc-500 mt-1">Kontak: {{ $pt->kontak ?? '-' }} •
                            {{ $pt->email_pt ?? '-' }}</p>
                    </div>

                    <div
                        class="rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300 space-y-1.5 border border-zinc-100 dark:border-zinc-800">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Mulai MoU:</span>
                            <span class="font-medium">{{ $pt->tgl_mulai_mou->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Berakhir MoU:</span>
                            <span
                                class="font-bold {{ $pt->tgl_akhir_mou < now() ? 'text-red-600' : 'text-emerald-600' }}">{{ $pt->tgl_akhir_mou->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                    <span>{{ $pt->mahasiswas_count }} Mahasiswa</span>
                    <span>{{ $pt->permohonan_praktiks_count }} Pengajuan Praktik</span>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500">
                Tidak ada data perguruan tinggi mitra yang terdaftar.
            </div>
        @endforelse
    </div>

    <!-- Modal Form PT -->
    <flux:modal wire:model="showModal" class="min-w-lg">
        <form wire:submit.prevent="savePt" class="space-y-4">
            <div>
                <flux:heading size="lg">
                    {{ $editingPtId ? 'Edit Perguruan Tinggi' : 'Tambah Perguruan Tinggi Mitra' }}</flux:heading>
                <flux:subheading>Pastikan tanggal masa berlaku MoU terisi akurat untuk gatekeeper permohonan praktik.
                </flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nama Perguruan Tinggi / Fakultas</flux:label>
                <flux:input wire:model="nama_pt" placeholder="Contoh: Universitas Indonesia - FIK" />
                <flux:error name="nama_pt" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Tanggal Mulai MoU</flux:label>
                    <flux:date-picker wire:model="tgl_mulai_mou" />
                    <flux:error name="tgl_mulai_mou" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Berakhir MoU</flux:label>
                    <flux:date-picker wire:model="tgl_akhir_mou" />
                    <flux:error name="tgl_akhir_mou" />
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Nomor Kontak / Telp</flux:label>
                    <flux:input wire:model="kontak" placeholder="021-..." />
                </flux:field>

                <flux:field>
                    <flux:label>Email Resmi Institusi</flux:label>
                    <flux:input type="email" wire:model="email_pt" placeholder="akademik@..." />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Status Nota Kesepahaman (MoU)</flux:label>
                <flux:select wire:model="status_mou" variant="listbox" placeholder="Pilih Status">
                    <flux:select.option value="1">Aktif (Bisa Ajukan Booking)</flux:select.option>
                    <flux:select.option value="0">Nonaktif (Dibekukan / Tidak Berlaku)</flux:select.option>
                </flux:select>
                <flux:error name="status_mou" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Data PT</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

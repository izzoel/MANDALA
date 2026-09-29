<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Unit RS & Manajemen Kuota Praktik
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pengaturan ruangan/instalasi rumah sakit, mode kapasitas kuota (Gabungan vs Per-Prodi), dan batas
                penerimaan mahasiswa.
            </flux:text>
        </div>

        @if (auth()->user()->isAdminDiklat())
            <flux:button wire:click="openCreateUnitModal" variant="primary" icon="plus">
                Tambah Unit RS
            </flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama unit instalasi atau deskripsi..."
            icon="magnifying-glass" />
    </div>

    <!-- Grid Unit RS -->
    <div class="grid gap-6 md:grid-cols-2">
        @forelse($this->units as $unit)
            <div
                class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-white text-lg">{{ $unit->nama_unit }}</h3>
                            <span
                                class="inline-flex items-center gap-1 rounded-md mt-1 px-2 py-0.5 text-xs font-semibold {{ $unit->mode_kuota === 'per_prodi' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300' }}">
                                Mode:
                                {{ $unit->mode_kuota === 'per_prodi' ? 'Alokasi Per-Prodi' : 'Kuota Gabungan / Total' }}
                            </span>
                        </div>

                        @if (auth()->user()->isAdminDiklat())
                            <flux:dropdown align="end">
                                <flux:button size="xs" variant="subtle" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item wire:click="editUnit({{ $unit->id }})" icon="pencil-square">Edit
                                        Unit</flux:menu.item>
                                    @if ($unit->mode_kuota === 'per_prodi')
                                        <flux:menu.item wire:click="openProdiModal({{ $unit->id }})" icon="plus">
                                            Tambah Kuota Prodi</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        @endif
                    </div>

                    @if ($unit->deskripsi)
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $unit->deskripsi }}</p>
                    @endif

                    <!-- Kuota Detail -->
                    <div
                        class="rounded-xl border border-zinc-100 bg-zinc-50 p-4 dark:border-zinc-800/80 dark:bg-zinc-800/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Kapasitas
                                Maksimal Unit:</span>
                            <span class="font-bold text-zinc-900 dark:text-white text-base">{{ $unit->kuota_maks }}
                                Mahasiswa</span>
                        </div>

                        @if ($unit->mode_kuota === 'per_prodi')
                            <div class="space-y-2 pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60">
                                <div
                                    class="flex items-center justify-between text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                    <span>Alokasi Per Program Studi:</span>
                                    @if (auth()->user()->isAdminDiklat())
                                        <button type="button" wire:click="openProdiModal({{ $unit->id }})"
                                            class="text-primary-600 hover:underline text-[11px] font-medium">+ Tambah
                                            Prodi</button>
                                    @endif
                                </div>

                                @if ($unit->prodiKuotas->count() > 0)
                                    <div class="space-y-1.5">
                                        @foreach ($unit->prodiKuotas as $pk)
                                            <div
                                                class="flex items-center justify-between rounded-lg bg-white px-3 py-1.5 text-xs shadow-2xs dark:bg-zinc-900">
                                                <span
                                                    class="text-zinc-700 dark:text-zinc-300">{{ $pk->prodi }}</span>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-bold text-primary-600 dark:text-primary-400">{{ $pk->kuota_maks }}
                                                        mhs</span>
                                                    @if (auth()->user()->isAdminDiklat())
                                                        <button type="button"
                                                            wire:click="deleteProdiKuota({{ $pk->id }})"
                                                            class="text-zinc-400 hover:text-red-600">×</button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-amber-600 italic">Belum ada rincian prodi. Silakan
                                        tambahkan kuota prodi.</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div
                    class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                    <span>{{ $unit->pembimbingLapangans->count() }} Pembimbing CI Lapangan</span>
                    <span class="text-emerald-600 font-medium">{{ $unit->permohonan_praktiks_count }} Kegiatan Praktik
                        Disetujui</span>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500">
                Belum ada unit rumah sakit yang dibuat.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Unit -->
    <flux:modal wire:model="showUnitModal" class="min-w-lg">
        <form wire:submit.prevent="saveUnit" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $editingUnitId ? 'Edit Unit Rumah Sakit' : 'Tambah Unit Rumah Sakit' }}
                </flux:heading>
                <flux:subheading>Tentukan mode kuota kapasitas untuk kontrol booking mahasiswa.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nama Unit / Instalasi / Ruang</flux:label>
                <flux:input wire:model="nama_unit" placeholder="Contoh: Instalasi Gawat Darurat (IGD)" />
                <flux:error name="nama_unit" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Mode Kuota</flux:label>
                    <flux:select wire:model="mode_kuota" variant="listbox" placeholder="Pilih Mode">
                        <flux:select.option value="gabungan">Kuota Gabungan (Total)</flux:select.option>
                        <flux:select.option value="per_prodi">Alokasi Per-Prodi</flux:select.option>
                    </flux:select>
                    <flux:error name="mode_kuota" />
                </flux:field>

                <flux:field>
                    <flux:label>Kapasitas Total Maksimal</flux:label>
                    <flux:input type="number" wire:model="kuota_maks" min="1" max="100" />
                    <flux:error name="kuota_maks" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Deskripsi Ruangan / Pelayanan</flux:label>
                <flux:textarea wire:model="deskripsi" placeholder="Deskripsi ringkas aktivitas dan fasilitas unit..."
                    rows="3" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showUnitModal', false)">Batal
                </flux:button>
                <flux:button type="submit" variant="primary">Simpan Unit</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Form Kuota Prodi -->
    <flux:modal wire:model="showProdiModal" class="min-w-md">
        <form wire:submit.prevent="saveProdiKuota" class="space-y-4">
            <div>
                <flux:heading size="lg">Tambah Alokasi Kuota Program Studi</flux:heading>
                <flux:subheading>Tetapkan kuota maksimal untuk program studi spesifik.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nama Program Studi</flux:label>
                <flux:input wire:model="new_prodi"
                    placeholder="Contoh: S1 Keperawatan / Ners, Profesi Dokter, D3 Kebidanan" />
                <flux:error name="new_prodi" />
            </flux:field>

            <flux:field>
                <flux:label>Kuota Maksimal Mahasiswa</flux:label>
                <flux:input type="number" wire:model="new_kuota_maks" min="1" max="50" />
                <flux:error name="new_kuota_maks" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showProdiModal', false)">Batal
                </flux:button>
                <flux:button type="submit" variant="primary">Simpan Kuota</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

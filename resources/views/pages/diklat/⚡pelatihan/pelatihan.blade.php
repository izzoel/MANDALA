<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Pelatihan Peserta Diklat
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pemantauan dan penugasan pelatihan wajib &amp; kompetensi fungsional tenaga kesehatan dan staf rumah
                sakit.
            </flux:text>
        </div>

        @if (auth()->user()->isAdminDiklat())
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Tambah Pelatihan
            </flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div
        class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Cari nama pelatihan, kategori, atau nama peserta..." icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterStatus" variant="listbox" searchable placeholder="Pilih Status">
                <flux:select.option value="semua">Semua Status</flux:select.option>
                <flux:select.option value="belum">Belum Dimulai</flux:select.option>
                <flux:select.option value="proses">Dalam Proses</flux:select.option>
                <flux:select.option value="selesai">Selesai / Terpenuhi</flux:select.option>
            </flux:select>
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterPeriode" variant="listbox" searchable placeholder="Pilih Periode">
                <flux:select.option value="semua">Semua Periode</flux:select.option>
                <flux:select.option value="2026">Tahun 2026</flux:select.option>
                <flux:select.option value="2025">Tahun 2025</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Cards Grid of Pelatihan -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($this->targets as $target)
            <div
                class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-3.5">
                    <!-- Top Badge: Category & Status -->
                    <div class="flex items-start justify-between gap-2">
                        <span
                            class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $target->kategori === 'Wajib' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' : ($target->kategori === 'Fungsional' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400') }}">
                            {{ $target->kategoriPelatihan->nama ?? ($target->kategori ?? 'Umum') }}
                        </span>

                        <div>
                            @if ($target->status === 'selesai')
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                    <flux:icon name="check-circle" class="size-3" /> Selesai
                                </span>
                            @elseif($target->status === 'proses')
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                    <flux:icon name="clock" class="size-3" /> Dalam Proses
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    Belum Dimulai
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Title & Periode -->
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white line-clamp-2 text-base">
                            {{ $target->nama_target }}
                        </h4>
                        <div class="mt-1 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                            <span>Tahun {{ $target->periode }}</span>
                            <span>•</span>
                            <span
                                class="{{ $target->tenggat < now()->toDateString() && $target->status !== 'selesai' ? 'text-red-600 font-bold' : '' }}">
                                Tenggat: {{ \Carbon\Carbon::parse($target->tenggat)->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Jumlah Peserta Terdaftar Box -->
                    <div
                        class="rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300 space-y-2 border border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-zinc-500 flex items-center gap-1.5">
                                <flux:icon name="user-group" class="size-3.5 text-brand-600 dark:text-brand-400" />
                                Peserta Terdaftar:
                            </span>
                            <span
                                class="font-bold text-zinc-900 dark:text-white px-2 py-0.5 bg-white dark:bg-zinc-700 rounded border border-zinc-200 dark:border-zinc-600">
                                {{ $target->peserta_terdaftar_count }} Orang
                            </span>
                        </div>

                        <!-- Daftar Nama Peserta yang Ditugaskan -->
                        @if ($target->pesertas && $target->pesertas->count() > 0)
                            <div class="pt-1.5 border-t border-zinc-200/60 dark:border-zinc-700/60 space-y-1">
                                <div class="text-[11px] font-medium text-zinc-600 dark:text-zinc-400">Peserta Terdaftar:
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($target->pesertas->take(3) as $p)
                                        <span
                                            class="inline-flex items-center rounded bg-zinc-200/70 px-1.5 py-0.5 text-[10px] font-medium text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200">
                                            {{ $p->nama }}
                                        </span>
                                    @endforeach
                                    @if ($target->pesertas->count() > 3)
                                        <span class="text-[10px] text-zinc-400 self-center">
                                            +{{ $target->pesertas->count() - 3 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @elseif ($target->pegawai)
                            <div
                                class="pt-1.5 border-t border-zinc-200/60 dark:border-zinc-700/60 text-[11px] text-zinc-500">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">Khusus:</span>
                                {{ $target->pegawai->nama }} ({{ $target->pegawai->unit_kerja ?? '-' }})
                            </div>
                        @else
                            <div
                                class="pt-1.5 border-t border-zinc-200/60 dark:border-zinc-700/60 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                                Terbuka untuk seluruh peserta diklat
                            </div>
                        @endif

                        {{-- @if ($target->sertifikatPemenuhan)
                            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 pt-0.5">
                                Sertifikat: No. {{ $target->sertifikatPemenuhan->no_sertifikat }}
                            </div>
                        @endif --}}
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div
                    class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-400">
                    @if (auth()->user()->isAdminDiklat())
                        <flux:button wire:click="openAddPesertaModal({{ $target->id }})" size="xs"
                            variant="primary" icon="user-plus">
                            Tambah Peserta
                        </flux:button>
                    @else
                        <span>{{ $target->created_at ? $target->created_at->diffForHumans() : 'Baru' }}</span>
                    @endif

                    @if (auth()->user()->isAdminDiklat())
                        <flux:dropdown align="end">
                            <flux:button size="xs" variant="subtle" icon="ellipsis-horizontal">
                                Opsi
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item wire:click="updateStatus({{ $target->id }}, 'selesai')"
                                    icon="check">
                                    Tandai Selesai
                                </flux:menu.item>
                                <flux:menu.item wire:click="updateStatus({{ $target->id }}, 'proses')"
                                    icon="arrow-path">
                                    Tandai Dalam Proses
                                </flux:menu.item>
                                <flux:menu.item wire:click="updateStatus({{ $target->id }}, 'belum')" icon="clock">
                                    Tandai Belum Dimulai
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    @endif
                </div>
            </div>
        @empty
            <div
                class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                Tidak ada data pelatihan yang sesuai dengan kriteria pencarian atau filter.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah Pelatihan -->
    <flux:modal wire:model="showModal" class="min-w-lg">
        <form wire:submit.prevent="saveTarget" class="space-y-4">
            <div>
                <flux:heading size="lg">Tambah Pelatihan</flux:heading>
                <flux:subheading>Tambahkan agenda pelatihan kompetensi bagi peserta diklat rumah sakit.
                </flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nama Pelatihan</flux:label>
                <flux:input wire:model="nama_target" placeholder="Contoh: Pelatihan BHD / ACLS / Keselamatan Pasien" />
                <flux:error name="nama_target" />
            </flux:field>

            <flux:field>
                <flux:label>Kategori Pelatihan</flux:label>
                <flux:select wire:model="kategoriId" variant="combobox" placeholder="Pilih atau cari kategori...">
                    <x-slot name="input">
                        <flux:select.input wire:model="searchKategori"
                            placeholder="Ketik untuk mencari atau membuat..." />
                    </x-slot>

                    @foreach ($this->kategoriPelatihans as $kategori)
                        <flux:select.option :value="$kategori->id" :wire:key="$kategori->id">{{ $kategori->nama }}
                        </flux:select.option>
                    @endforeach

                    <flux:select.option.create wire:click="createKategori" min-length="2">
                        Create "<span wire:text="searchKategori"></span>"
                    </flux:select.option.create>
                </flux:select>
                <flux:error name="kategoriId" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Periode Tahun</flux:label>
                    <flux:input wire:model="periode" />
                    <flux:error name="periode" />
                </flux:field>

                <flux:field>
                    <flux:label>Tenggat Waktu Pemenuhan</flux:label>
                    <flux:date-picker wire:model="tenggat" />
                    <flux:error name="tenggat" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Pelatihan</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Form Tambah / Kelola Peserta Pelatihan -->
    @if ($selectedTarget)
        <flux:modal wire:model="showPesertaModal" class="min-w-xl">
            <form wire:submit.prevent="savePesertaToPelatihan" class="space-y-4">
                <div>
                    <flux:heading size="lg">Tambah Peserta Pelatihan</flux:heading>
                    <flux:subheading>
                        Daftarkan peserta diklat untuk mengikuti <strong>{{ $selectedTarget->nama_target }}</strong>
                        (Tahun {{ $selectedTarget->periode }}).
                    </flux:subheading>
                </div>

                <div
                    class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/60 space-y-3">
                    <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400">
                        <span>Pilih satu atau beberapa peserta yang ditugaskan:</span>
                        <span class="font-bold text-brand-600">{{ count($selectedPesertaIds) }} Terpilih</span>
                    </div>

                    <div
                        class="max-h-60 overflow-y-auto divide-y divide-zinc-200/70 dark:divide-zinc-700/70 border border-zinc-200 rounded-lg bg-white dark:bg-zinc-900 dark:border-zinc-700">
                        @foreach ($this->listPeserta as $peserta)
                            <label
                                class="flex items-center gap-3 p-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 cursor-pointer text-xs">
                                <input type="checkbox" wire:model="selectedPesertaIds"
                                    value="{{ (string) $peserta->id }}"
                                    class="rounded text-brand-600 focus:ring-brand-500 size-4 border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
                                <div class="flex-1">
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $peserta->nama }}
                                    </div>
                                    <div class="text-[11px] text-zinc-500">{{ $peserta->unit_kerja }} •
                                        {{ $peserta->jabatan }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <flux:error name="selectedPesertaIds" />
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button type="button" variant="subtle" wire:click="$set('showPesertaModal', false)">Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary">Simpan Peserta</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>

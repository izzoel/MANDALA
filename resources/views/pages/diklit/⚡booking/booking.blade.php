<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <flux:icon name="calendar-days" class="size-6 text-primary-600 dark:text-primary-400" />
                Booking & Permohonan Praktik Mahasiswa
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pengecekan ketersediaan kuota unit rumah sakit secara real-time, jadwal terpadu, dan pengajuan
                permohonan praktik klinik.
            </flux:text>
        </div>

        <div>
            <flux:button wire:click="openBookingModal" variant="primary" icon="plus">
                Ajukan Permohonan Praktik
            </flux:button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('message'))
        <div
            class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            <flux:icon name="check-circle" class="size-5 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/50 dark:text-rose-300">
            <flux:icon name="exclamation-triangle" class="size-5 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Total Permohonan</span>
                <flux:icon name="document-text" class="size-4 text-zinc-400" />
            </div>
            <div class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</div>
            <div class="mt-1 text-[11px] text-zinc-500">Seluruh pengajuan praktik</div>
        </div>

        <div
            class="rounded-xl border border-amber-200/80 bg-amber-50/40 p-4 shadow-xs dark:border-amber-900/40 dark:bg-amber-950/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400">Menunggu Review</span>
                <flux:icon name="clock" class="size-4 text-amber-500" />
            </div>
            <div class="mt-2 text-2xl font-bold text-amber-900 dark:text-amber-200">{{ $this->stats['diajukan'] }}</div>
            <div class="mt-1 text-[11px] text-amber-600 dark:text-amber-400">Dalam antrean verifikasi</div>
        </div>

        <div
            class="rounded-xl border border-emerald-200/80 bg-emerald-50/40 p-4 shadow-xs dark:border-emerald-900/40 dark:bg-emerald-950/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Disetujui RS</span>
                <flux:icon name="check-badge" class="size-4 text-emerald-500" />
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-900 dark:text-emerald-200">{{ $this->stats['disetujui'] }}
            </div>
            <div class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400">Jadwal kuota terkonfirmasi</div>
        </div>

        <div
            class="rounded-xl border border-rose-200/80 bg-rose-50/40 p-4 shadow-xs dark:border-rose-900/40 dark:bg-rose-950/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-rose-700 dark:text-rose-400">Ditolak / Batal</span>
                <flux:icon name="x-circle" class="size-4 text-rose-500" />
            </div>
            <div class="mt-2 text-2xl font-bold text-rose-900 dark:text-rose-200">{{ $this->stats['ditolak'] }}</div>
            <div class="mt-1 text-[11px] text-rose-600 dark:text-rose-400">Kuota penuh / syarat MoU</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <flux:tabs wire:model.live="viewMode">
        <flux:tab name="list" icon="list-bullet">Daftar Permohonan</flux:tab>
        <flux:tab name="kalender" icon="calendar">Matriks Keterisian Unit</flux:tab>
    </flux:tabs>

    @if ($viewMode === 'kalender')
        <!-- ==================== TAMPILAN MATRIKS KALENDER KETERISIAN ==================== -->
        <div class="space-y-6">
            <!-- Calendar Navigation & Unit Filter Header -->
            <div
                class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <button wire:click="prevMonth" type="button"
                        class="rounded-lg border border-zinc-200 p-2 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        title="Bulan Sebelumnya">
                        <flux:icon name="chevron-left" class="size-4" />
                    </button>
                    <div class="min-w-40 text-center font-bold text-zinc-900 dark:text-white">
                        {{ $this->calendarData['monthName'] }}
                    </div>
                    <button wire:click="nextMonth" type="button"
                        class="rounded-lg border border-zinc-200 p-2 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        title="Bulan Berikutnya">
                        <flux:icon name="chevron-right" class="size-4" />
                    </button>
                    <button wire:click="resetCalendar" type="button"
                        class="ml-2 rounded-lg bg-zinc-100 px-2.5 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                        Bulan Ini
                    </button>
                </div>

                <div class="w-full sm:w-72">
                    <flux:select wire:model.live="calendarUnitId" placeholder="Semua Unit RS" variant="listbox">
                        <flux:select.option value="">Semua Unit RS</flux:select.option>
                        @foreach ($this->units as $u)
                            <flux:select.option :value="$u->id">{{ $u->nama_unit }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <!-- Unit Capacity Utilization Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->calendarData['unitOccupancy'] as $item)
                    @php
                        $pct = $item['persentase'];
                        $barColor = $pct >= 90 ? 'bg-rose-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        $badgeColor =
                            $pct >= 90
                                ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300'
                                : ($pct >= 70
                                    ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'
                                    : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300');
                        $statusText = $pct >= 100 ? 'Penuh' : ($pct >= 70 ? 'Hampir Penuh' : 'Tersedia');
                    @endphp
                    <div
                        class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-sm text-zinc-900 dark:text-white">
                                    {{ $item['unit']->nama_unit }}</h4>
                                <div class="text-xs text-zinc-500">
                                    Mode Kuota: <span
                                        class="capitalize font-semibold text-zinc-700 dark:text-zinc-300">{{ str_replace('_', ' ', $item['unit']->mode_kuota) }}</span>
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $badgeColor }}">
                                {{ $statusText }}
                            </span>
                        </div>

                        <!-- Progress Bar Keterisian -->
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-zinc-600 dark:text-zinc-400">Terpakai:
                                    <strong>{{ $item['total_mhs'] }}</strong> Mhs</span>
                                <span class="text-zinc-600 dark:text-zinc-400">Kapasitas:
                                    <strong>{{ $item['unit']->kuota_maks }}</strong></span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-full rounded-full transition-all duration-300 {{ $barColor }}"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between text-xs pt-2 border-t border-zinc-100 dark:border-zinc-800 text-zinc-500">
                            <span>{{ $item['total_bookings'] }} Jadwal Aktif Bulan Ini</span>
                            <button
                                wire:click="$set('filterUnitId', {{ $item['unit']->id }}); $set('viewMode', 'list');"
                                class="text-primary-600 hover:underline font-semibold">
                                Lihat Daftar &rarr;
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List Jadwal Praktik Terjadwal di Bulan Ini -->
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-base flex items-center gap-2">
                        <flux:icon name="calendar-days" class="size-5 text-primary-500" />
                        Daftar Praktik Terkonfirmasi di {{ $this->calendarData['monthName'] }}
                    </h3>
                    <span class="text-xs text-zinc-500">{{ $this->calendarData['bookings']->count() }} Jadwal
                        Terdaftar</span>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($this->calendarData['bookings'] as $b)
                        <div
                            class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/40 space-y-2 hover:border-primary-400 dark:hover:border-primary-600 transition-colors">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-bold text-sm text-zinc-900 dark:text-white">{{ $b->unit->nama_unit ?? '-' }}</span>
                                <span
                                    class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                    {{ $b->jumlah_mahasiswa }} Mahasiswa
                                </span>
                            </div>

                            <div class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                {{ $b->perguruanTinggi->nama_pt ?? '-' }}
                            </div>

                            <div class="text-xs text-zinc-500">
                                Prodi: <span
                                    class="font-medium text-zinc-700 dark:text-zinc-300">{{ $b->prodi }}</span>
                            </div>

                            <div
                                class="pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-between text-xs text-zinc-500">
                                <span class="flex items-center gap-1 font-medium text-zinc-700 dark:text-zinc-300">
                                    <flux:icon name="calendar" class="size-3.5 text-primary-500" />
                                    {{ $b->tgl_mulai->format('d M') }} - {{ $b->tgl_selesai->format('d M Y') }}
                                </span>
                                <button wire:click="viewDetail({{ $b->id }})"
                                    class="text-primary-600 hover:underline font-semibold">
                                    Detail
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-sm text-zinc-400">
                            Tidak ada jadwal praktik terkonfirmasi pada bulan {{ $this->calendarData['monthName'] }}.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <!-- ==================== TAMPILAN DAFTAR PERMOHONAN ==================== -->
        <div class="space-y-6">
            <!-- Filter & Pencarian -->
            <div
                class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
                <div class="sm:col-span-5">
                    <flux:input wire:model.live.debounce.300ms="search"
                        placeholder="Cari prodi, perguruan tinggi, atau unit..." icon="magnifying-glass" />
                </div>
                <div class="sm:col-span-4">
                    <flux:select wire:model.live="filterStatus" variant="listbox" placeholder="Pilih Status">
                        <flux:select.option value="semua">Semua Status Permohonan</flux:select.option>
                        <flux:select.option value="diajukan">Diajukan (Menunggu Review)</flux:select.option>
                        <flux:select.option value="disetujui">Disetujui RS</flux:select.option>
                        <flux:select.option value="ditolak">Ditolak</flux:select.option>
                    </flux:select>
                </div>
                <div class="sm:col-span-3">
                    <flux:select wire:model.live="filterUnitId" variant="listbox" placeholder="Pilih Unit RS"
                        searchable>
                        <flux:select.option value="">Semua Unit RS</flux:select.option>
                        @foreach ($this->units as $u)
                            <flux:select.option :value="$u->id">{{ $u->nama_unit }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <!-- Tabel Riwayat Permohonan Praktik -->
            <div
                class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                        <thead
                            class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                            <tr>
                                <th class="px-5 py-3.5">Perguruan Tinggi & Prodi</th>
                                <th class="px-5 py-3.5">Unit Rumah Sakit</th>
                                <th class="px-5 py-3.5">Jml Mahasiswa</th>
                                <th class="px-5 py-3.5">Periode Praktik</th>
                                <th class="px-5 py-3.5">Status Pengajuan</th>
                                <th class="px-5 py-3.5">Dokumen</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse($this->permohonans as $p)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-zinc-900 dark:text-white">
                                            {{ $p->perguruanTinggi->nama_pt ?? '-' }}</div>
                                        <div class="text-xs text-zinc-500">Prodi: <span
                                                class="font-medium text-zinc-700 dark:text-zinc-300">{{ $p->prodi }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $p->unit->nama_unit ?? '-' }}</div>
                                        <div class="text-[11px] text-zinc-500 capitalize">Mode:
                                            {{ str_replace('_', ' ', $p->unit->mode_kuota ?? '') }}</div>
                                    </td>
                                    <td class="px-5 py-4 font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $p->jumlah_mahasiswa }} Orang
                                    </td>
                                    <td class="px-5 py-4 text-xs font-medium">
                                        <div>{{ $p->tgl_mulai->format('d M Y') }} s/d</div>
                                        <div>{{ $p->tgl_selesai->format('d M Y') }}</div>
                                        <span class="text-[11px] text-zinc-400 font-normal">
                                            ({{ $p->tgl_mulai->diffInDays($p->tgl_selesai) + 1 }} hari)
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($p->status === 'disetujui')
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                                <flux:icon name="check-circle" class="size-3.5" /> Disetujui
                                            </span>
                                        @elseif($p->status === 'ditolak')
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-800 dark:bg-rose-950/50 dark:text-rose-300">
                                                <flux:icon name="x-circle" class="size-3.5" /> Ditolak
                                            </span>
                                            @if ($p->catatan_diklat)
                                                <div class="mt-1 text-[11px] text-rose-600 line-clamp-1"
                                                    title="{{ $p->catatan_diklat }}">{{ $p->catatan_diklat }}</div>
                                            @endif
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                                <flux:icon name="clock" class="size-3.5" /> Menunggu Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs space-y-1">
                                        @if ($p->file_surat_permohonan)
                                            <div>
                                                <a href="{{ asset('storage/' . $p->file_surat_permohonan) }}"
                                                    target="_blank"
                                                    class="text-primary-600 hover:underline inline-flex items-center gap-1">
                                                    <flux:icon name="document-text" class="size-3.5" /> Surat PT
                                                </a>
                                            </div>
                                        @endif

                                        @if ($p->suratPersetujuan)
                                            <div>
                                                <span
                                                    class="text-emerald-600 dark:text-emerald-400 font-semibold inline-flex items-center gap-1">
                                                    <flux:icon name="check-badge" class="size-3.5" />
                                                    {{ $p->suratPersetujuan->nomor_surat }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <flux:button wire:click="viewDetail({{ $p->id }})" size="sm"
                                                variant="subtle" icon="eye">
                                                Detail
                                            </flux:button>
                                            {{-- @if (auth()->user()?->isAdminDiklat() ||
    auth()->user()
        ?->hasRole(['admin_diklat', 'super_admin']))
                                                <flux:button wire:click="openReviewModal({{ $p->id }})"
                                                    size="sm" variant="primary" icon="clipboard-document-check"
                                                    title="Persetujuan & Surat RS">
                                                    Persetujuan
                                                </flux:button>
                                            @endif --}}
                                            @if ($p->status === 'diajukan' || auth()->user()?->isAdminDiklat())
                                                <flux:button wire:click="batalkanPermohonan({{ $p->id }})"
                                                    wire:confirm="Yakin ingin membatalkan dan menghapus permohonan ini?"
                                                    size="sm" variant="danger" icon="trash" />
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-zinc-500">
                                        <flux:icon name="inbox" class="mx-auto size-8 text-zinc-300 mb-2" />
                                        <p class="font-medium">Belum ada permohonan praktik yang sesuai dengan filter.
                                        </p>
                                        <p class="text-xs text-zinc-400 mt-1">Klik tombol "Ajukan Permohonan Praktik"
                                            untuk membuat permohonan baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== MODAL FORM PENGAJUAN BOOKING ==================== -->
    <flux:modal wire:model="showBookingModal" class="min-w-xl max-w-2xl">
        <form wire:submit.prevent="submitPermohonan" class="space-y-4">
            <div>
                <flux:heading size="lg" class="font-bold flex items-center gap-2">
                    <flux:icon name="calendar-days" class="size-5 text-primary-600" />
                    Form Pengajuan Permohonan Praktik RS
                </flux:heading>
                <flux:subheading>Sistem secara otomatis memverifikasi keaktifan masa berlaku MoU perguruan tinggi dan
                    mendeteksi ketersediaan kuota unit secara real-time.</flux:subheading>
            </div>

            @if ($errorMessage)
                <div
                    class="rounded-xl border border-rose-300 bg-rose-50 p-4 text-xs font-semibold text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/50 dark:text-rose-300 flex items-start gap-2">
                    <flux:icon name="exclamation-circle" class="size-4 shrink-0 text-rose-600 mt-0.5" />
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <flux:field>
                <flux:label>Perguruan Tinggi Mitra (MoU Aktif)</flux:label>
                <flux:select wire:model.live="pt_id" variant="listbox" placeholder="Pilih Perguruan Tinggi"
                    searchable>
                    @foreach ($this->perguruanTinggis as $pt)
                        <flux:select.option :value="$pt->id">
                            {{ $pt->nama_pt }} (Masa Berlaku MoU: s/d {{ $pt->tgl_akhir_mou->format('d/m/Y') }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="pt_id" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Pilih Unit Rumah Sakit</flux:label>
                    <flux:select wire:model.live="unit_id" variant="listbox" placeholder="Pilih Unit RS" searchable>
                        @foreach ($this->units as $u)
                            <flux:select.option :value="$u->id">
                                {{ $u->nama_unit }} (Mode:
                                {{ $u->mode_kuota == 'per_prodi' ? 'Per Prodi' : 'Gabungan' }} | Kuota:
                                {{ $u->kuota_maks }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="unit_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Program Studi Mahasiswa</flux:label>
                    @if ($this->selectedUnit && $this->selectedUnit->mode_kuota === 'per_prodi' && $this->selectedUnitProdis->isNotEmpty())
                        <flux:select wire:model.live="prodi" variant="listbox" placeholder="Pilih Program Studi"
                            searchable>
                            @foreach ($this->selectedUnitProdis as $pk)
                                <flux:select.option :value="$pk->prodi">
                                    {{ $pk->prodi }} (Kuota: {{ $pk->kuota_maks }} Mhs)
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    @else
                        <flux:input wire:model.live.debounce.400ms="prodi"
                            placeholder="Contoh: S1 Keperawatan / Profesi Dokter" />
                    @endif
                    <flux:error name="prodi" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Jumlah Mahasiswa</flux:label>
                    <flux:input type="number" wire:model.live.debounce.300ms="jumlah_mahasiswa" min="1"
                        max="50" />
                    <flux:error name="jumlah_mahasiswa" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Mulai</flux:label>
                    <flux:date-picker wire:model.live="tgl_mulai" />
                    <flux:error name="tgl_mulai" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Selesai</flux:label>
                    <flux:date-picker wire:model.live="tgl_selesai" />
                    <flux:error name="tgl_selesai" />
                </flux:field>
            </div>

            <!-- Real-time Kuota Feedback Panel -->
            @if ($kuotaCheckResult)
                <div
                    class="rounded-xl border p-4 text-xs transition-all {{ $kuotaCheckResult['available'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-200' : 'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-200' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 font-bold text-sm">
                            <flux:icon
                                name="{{ $kuotaCheckResult['available'] ? 'check-circle' : 'exclamation-circle' }}"
                                class="size-5 shrink-0" />
                            <span>{{ $kuotaCheckResult['available'] ? 'Slot Kuota Unit Tersedia' : 'Slot Kuota Tidak Memadai' }}</span>
                        </div>
                        <span
                            class="rounded-md px-2 py-0.5 font-bold text-[11px] {{ $kuotaCheckResult['available'] ? 'bg-emerald-200 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-100' : 'bg-rose-200 text-rose-900 dark:bg-rose-900 dark:text-rose-100' }}">
                            Sisa Kuota: {{ $kuotaCheckResult['sisa_kuota'] }} / {{ $kuotaCheckResult['kuota_maks'] }}
                        </span>
                    </div>
                    <div class="mt-2 leading-relaxed">
                        {{ $kuotaCheckResult['message'] }}
                    </div>
                </div>
            @endif

            <flux:field class="relative mb-4 mt-2">
                <flux:label>Surat Permohonan Resmi Perguruan Tinggi (PDF / Max 5MB)</flux:label>

                <flux:file-upload wire:model="file_surat_permohonan" accept="application/pdf" :error="false">
                    <flux:file-upload.dropzone heading="Seret file PDF atau klik untuk menelusuri"
                        text="Hanya dokumen PDF (Maksimal 5MB)" with-progress inline />
                </flux:file-upload>

                <flux:error name="file_surat_permohonan" class="absolute left-0 -bottom-5 text-[11px] mt-3!" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button type="button" variant="subtle" wire:click="$set('showBookingModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary" icon="paper-airplane">
                    Kirim Permohonan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- ==================== MODAL DETAIL PERMOHONAN ==================== -->
    <flux:modal wire:model="showDetailModal" class="min-w-xl max-w-2xl">
        @if ($selectedPermohonan)
            <div class="space-y-5">
                <div class="flex items-start justify-between border-b border-zinc-200 pb-4 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <flux:heading size="lg" class="font-bold">Detail Permohonan Praktik RS</flux:heading>
                            @if ($selectedPermohonan->status === 'disetujui')
                                <span
                                    class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Disetujui</span>
                            @elseif($selectedPermohonan->status === 'ditolak')
                                <span
                                    class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">Ditolak</span>
                            @else
                                <span
                                    class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Menunggu
                                    Review</span>
                            @endif
                        </div>
                        <flux:subheading>ID Pengajuan:
                            #PRK-{{ str_pad($selectedPermohonan->id, 5, '0', STR_PAD_LEFT) }} &bull; Tanggal Diajukan:
                            {{ $selectedPermohonan->created_at->format('d M Y H:i') }}</flux:subheading>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/60 space-y-1">
                        <span class="text-zinc-500 font-semibold uppercase text-[10px]">Perguruan Tinggi</span>
                        <div class="font-bold text-sm text-zinc-900 dark:text-white">
                            {{ $selectedPermohonan->perguruanTinggi->nama_pt ?? '-' }}</div>
                        <div class="text-zinc-500">Masa MoU: s/d
                            {{ $selectedPermohonan->perguruanTinggi?->tgl_akhir_mou?->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/60 space-y-1">
                        <span class="text-zinc-500 font-semibold uppercase text-[10px]">Unit & Program Studi</span>
                        <div class="font-bold text-sm text-zinc-900 dark:text-white">
                            {{ $selectedPermohonan->unit->nama_unit ?? '-' }}</div>
                        <div class="text-zinc-500">Prodi: {{ $selectedPermohonan->prodi }}</div>
                    </div>

                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/60 space-y-1">
                        <span class="text-zinc-500 font-semibold uppercase text-[10px]">Alokasi Mahasiswa</span>
                        <div class="font-bold text-sm text-zinc-900 dark:text-white">
                            {{ $selectedPermohonan->jumlah_mahasiswa }} Orang Mahasiswa</div>
                        <div class="text-zinc-500">Kapasitas Unit: {{ $selectedPermohonan->unit?->kuota_maks ?? 0 }}
                            Kuota</div>
                    </div>

                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/60 space-y-1">
                        <span class="text-zinc-500 font-semibold uppercase text-[10px]">Periode Praktik</span>
                        <div class="font-bold text-sm text-zinc-900 dark:text-white">
                            {{ $selectedPermohonan->tgl_mulai->format('d M Y') }} -
                            {{ $selectedPermohonan->tgl_selesai->format('d M Y') }}
                        </div>
                        <div class="text-zinc-500">Durasi:
                            {{ $selectedPermohonan->tgl_mulai->diffInDays($selectedPermohonan->tgl_selesai) + 1 }} Hari
                        </div>
                    </div>
                </div>

                <!-- Dokumen & Surat Persetujuan RS -->
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800 space-y-3">
                    <h4 class="font-bold text-xs uppercase text-zinc-500">Dokumen & Keputusan Diklat RS</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div
                            class="flex items-center justify-between rounded-lg border border-zinc-200/80 p-3 dark:border-zinc-800">
                            <span class="text-zinc-600 dark:text-zinc-400 font-medium">Surat Permohonan PT:</span>
                            @if ($selectedPermohonan->file_surat_permohonan)
                                <a href="{{ asset('storage/' . $selectedPermohonan->file_surat_permohonan) }}"
                                    target="_blank"
                                    class="font-semibold text-primary-600 hover:underline flex items-center gap-1">
                                    <flux:icon name="arrow-down-tray" class="size-3.5" /> Unduh PDF
                                </a>
                            @else
                                <span class="text-zinc-400 italic">Tidak dilampirkan</span>
                            @endif
                        </div>

                        <div
                            class="flex items-center justify-between rounded-lg border border-zinc-200/80 p-3 dark:border-zinc-800">
                            <span class="text-zinc-600 dark:text-zinc-400 font-medium">Surat Persetujuan RS:</span>
                            @if ($selectedPermohonan->suratPersetujuan)
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ $selectedPermohonan->suratPersetujuan->nomor_surat }}
                                </span>
                            @else
                                <span class="text-zinc-400 italic">Belum diterbitkan</span>
                            @endif
                        </div>
                    </div>

                    @if ($selectedPermohonan->catatan_diklat)
                        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/40 text-xs">
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">Catatan Diklat RS:</span>
                            <p class="text-zinc-600 dark:text-zinc-400 mt-1">{{ $selectedPermohonan->catatan_diklat }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Tim Pembimbing (Jika Sudah Ditunjuk) -->
                @if ($selectedPermohonan->penunjukanPembimbings->isNotEmpty())
                    <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800 space-y-2">
                        <h4 class="font-bold text-xs uppercase text-zinc-500">Daftar Mahasiswa & Tim Pembimbing</h4>
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($selectedPermohonan->penunjukanPembimbings as $penunjukan)
                                <div class="py-2 flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-bold text-zinc-900 dark:text-white">
                                            {{ $penunjukan->mahasiswa->nama ?? 'Mahasiswa' }} (NIM:
                                            {{ $penunjukan->mahasiswa->nim ?? '-' }})</div>
                                        <div class="text-zinc-500">
                                            CI Lapangan:
                                            <strong>{{ $penunjukan->pembimbingLapangan->nama ?? '-' }}</strong> &bull;
                                            Dosen: <strong>{{ $penunjukan->pembimbingDosen->nama ?? '-' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-between items-center pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    <div>
                        @if ($selectedPermohonan->status === 'diajukan' || auth()->user()?->isAdminDiklat())
                            <flux:button wire:click="batalkanPermohonan({{ $selectedPermohonan->id }})"
                                wire:confirm="Yakin ingin membatalkan dan menghapus permohonan ini?" variant="danger"
                                size="sm" icon="trash">
                                Batalkan Permohonan
                            </flux:button>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if (auth()->user()?->isAdminDiklat() ||
                                auth()->user()
                                    ?->hasRole(['admin_diklat', 'super_admin']))
                            <flux:button wire:click="openReviewModal({{ $selectedPermohonan->id }})"
                                variant="primary" size="sm" icon="clipboard-document-check">
                                {{ $selectedPermohonan->status === 'disetujui' ? 'Ubah Persetujuan / Surat RS' : 'Proses Persetujuan / Surat RS' }}
                            </flux:button>
                        @endif
                        <flux:button type="button" variant="subtle" wire:click="$set('showDetailModal', false)">
                            Tutup
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- ==================== MODAL REVIEW & PERSETUJUAN (PENERBITAN SURAT RS) ==================== -->
    @if ($selectedPermohonan)
        <flux:modal wire:model="showReviewModal" class="min-w-xl max-w-2xl">
            <form wire:submit.prevent="submitKeputusan" class="space-y-4">
                <div>
                    <flux:heading size="lg" class="font-bold flex items-center gap-2">
                        <flux:icon name="clipboard-document-check" class="size-5 text-primary-600" />
                        Review Persetujuan & Penerbitan Surat Praktik
                    </flux:heading>
                    <flux:subheading>Tentukan keputusan permohonan booking dan terbitkan nomor surat izin praktik resmi
                        Diklat RS.</flux:subheading>
                </div>

                <div
                    class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/60 space-y-2 text-xs">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Institusi PT:</span>
                        <span
                            class="col-span-2 font-semibold text-zinc-900 dark:text-white">{{ $selectedPermohonan->perguruanTinggi->nama_pt ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Program Studi:</span>
                        <span class="col-span-2 font-medium">{{ $selectedPermohonan->prodi }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Unit RS:</span>
                        <span
                            class="col-span-2 font-bold text-primary-600">{{ $selectedPermohonan->unit->nama_unit ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Jumlah Mahasiswa:</span>
                        <span class="col-span-2 font-bold">{{ $selectedPermohonan->jumlah_mahasiswa }} Orang</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Jadwal Praktik:</span>
                        <span class="col-span-2">{{ $selectedPermohonan->tgl_mulai->format('d M Y') }} s/d
                            {{ $selectedPermohonan->tgl_selesai->format('d M Y') }}</span>
                    </div>

                    @if ($selectedPermohonan->file_surat_permohonan)
                        <div
                            class="pt-2 border-t border-zinc-200 dark:border-zinc-700/60 flex items-center justify-between">
                            <span class="text-zinc-500">Berkas Pengantar PT:</span>
                            <a href="{{ asset('storage/' . $selectedPermohonan->file_surat_permohonan) }}"
                                target="_blank"
                                class="font-semibold text-primary-600 hover:underline flex items-center gap-1">
                                <flux:icon name="arrow-down-tray" class="size-3.5" /> Unduh Dokumen PDF
                            </a>
                        </div>
                    @endif
                </div>

                <flux:field>
                    <flux:label>Keputusan Tim Diklat RS</flux:label>
                    <flux:select wire:model.live="status_keputusan" variant="listbox">
                        <flux:select.option value="disetujui">Setujui Permohonan (Terbitkan Izin)</flux:select.option>
                        <flux:select.option value="ditolak">Tolak Permohonan</flux:select.option>
                    </flux:select>
                    <flux:error name="status_keputusan" />
                </flux:field>

                @if ($status_keputusan === 'disetujui')
                    <flux:field>
                        <flux:label>Nomor Surat Persetujuan RS</flux:label>
                        <flux:input wire:model="nomor_surat" placeholder="Contoh: 420/DIKLAT-RS/X/2026" />
                        <flux:error name="nomor_surat" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>Catatan / Instruksi Khusus Diklat RS</flux:label>
                    <flux:textarea wire:model="catatan_diklat"
                        placeholder="Catatan orientasi, syarat K3RS, atau alasan penolakan..." rows="3" />
                    <flux:error name="catatan_diklat" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button type="button" variant="subtle"
                        wire:click="$set('showReviewModal', false); $set('showDetailModal', true)">
                        Kembali ke Detail
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Simpan Keputusan & Surat
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>

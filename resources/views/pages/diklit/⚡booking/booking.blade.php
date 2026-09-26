<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Booking & Permohonan Praktik Mahasiswa</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Pengecekan ketersediaan kuota unit rumah sakit secara real-time dan pengajuan permohonan praktik klinik.
            </flux:text>
        </div>

        <flux:button wire:click="openBookingModal" variant="primary" icon="calendar-days">
            Ajukan Permohonan Praktik
        </flux:button>
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Kalender / Grid Jadwal Praktik Aktif Saat Ini -->
    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-zinc-900 dark:text-white text-base flex items-center gap-2">
                <flux:icon name="chart-bar" class="size-5 text-primary-500" />
                Aktivitas Praktik Mahasiswa Berjalan & Mendatang
            </h3>
            <span class="text-xs text-zinc-500">{{ $this->activeBookings->count() }} Jadwal Terkonfirmasi</span>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($this->activeBookings as $b)
                <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/40 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-zinc-900 dark:text-white">{{ $b->unit->nama_unit ?? '-' }}</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                            {{ $b->jumlah_mahasiswa }} Mhs
                        </span>
                    </div>

                    <div class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                        {{ $b->perguruanTinggi->nama_pt ?? '-' }}
                    </div>

                    <div class="text-xs text-zinc-500">
                        Prodi: <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $b->prodi }}</span>
                    </div>

                    <div class="pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-between text-xs text-zinc-500">
                        <span class="flex items-center gap-1">
                            <flux:icon name="calendar" class="size-3.5" />
                            {{ $b->tgl_mulai->format('d M') }} - {{ $b->tgl_selesai->format('d M Y') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-4 text-center text-xs text-zinc-400">
                    Tidak ada jadwal praktik aktif saat ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Search & Filter Permohonan -->
    <div class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
        <div class="sm:col-span-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari prodi, perguruan tinggi, atau unit..." icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="semua">Semua Status</flux:select.option>
                <flux:select.option value="diajukan">Diajukan (Review)</flux:select.option>
                <flux:select.option value="disetujui">Disetujui</flux:select.option>
                <flux:select.option value="ditolak">Ditolak</flux:select.option>
            </flux:select>
        </div>
        <div class="sm:col-span-3">
            <flux:select wire:model.live="filterUnitId">
                <flux:select.option value="">Semua Unit RS</flux:select.option>
                @foreach($this->units as $u)
                    <flux:select.option :value="$u->id">{{ $u->nama_unit }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Tabel Riwayat Permohonan Praktik -->
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3.5">Perguruan Tinggi & Prodi</th>
                        <th class="px-5 py-3.5">Unit Rumah Sakit</th>
                        <th class="px-5 py-3.5">Jml Mahasiswa</th>
                        <th class="px-5 py-3.5">Periode Praktik</th>
                        <th class="px-5 py-3.5">Status Pengajuan</th>
                        <th class="px-5 py-3.5">Dokumen / Surat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($this->permohonans as $p)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $p->perguruanTinggi->nama_pt ?? '-' }}</div>
                                <div class="text-xs text-zinc-500">Prodi: {{ $p->prodi }}</div>
                            </td>
                            <td class="px-5 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $p->unit->nama_unit ?? '-' }}
                            </td>
                            <td class="px-5 py-4 font-bold text-zinc-800 dark:text-zinc-200">
                                {{ $p->jumlah_mahasiswa }} Orang
                            </td>
                            <td class="px-5 py-4 text-xs font-medium">
                                {{ $p->tgl_mulai->format('d M Y') }} s/d {{ $p->tgl_selesai->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4">
                                @if($p->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        <flux:icon name="check-circle" class="size-3.5" /> Disetujui
                                    </span>
                                @elseif($p->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-800 dark:bg-rose-950/50 dark:text-rose-300">
                                        <flux:icon name="x-circle" class="size-3.5" /> Ditolak
                                    </span>
                                    @if($p->catatan_diklat)
                                        <div class="mt-1 text-[11px] text-rose-600">{{ $p->catatan_diklat }}</div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                        <flux:icon name="clock" class="size-3.5" /> Diajukan (Menunggu Review)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs space-y-1">
                                @if($p->file_surat_permohonan)
                                    <div>
                                        <a href="{{ asset('storage/' . $p->file_surat_permohonan) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                            <flux:icon name="document-text" class="size-3.5" /> Surat Pengantar PT
                                        </a>
                                    </div>
                                @endif

                                @if($p->suratPersetujuan)
                                    <div>
                                        <span class="text-emerald-600 font-semibold flex items-center gap-1">
                                            <flux:icon name="check-badge" class="size-3.5" /> Surat RS: {{ $p->suratPersetujuan->nomor_surat }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-500">
                                Belum ada permohonan praktik yang diajukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Pengajuan Booking Praktik -->
    <flux:modal wire:model="showBookingModal" class="min-w-xl">
        <form wire:submit.prevent="submitPermohonan" class="space-y-4">
            <div>
                <flux:heading size="lg">Form Pengajuan Permohonan Praktik RS</flux:heading>
                <flux:subheading>Sistem akan memvalidasi status MoU perguruan tinggi dan memeriksa ketersediaan kuota unit secara otomatis.</flux:subheading>
            </div>

            @if($errorMessage)
                <div class="rounded-xl border border-rose-300 bg-rose-50 p-4 text-sm font-semibold text-rose-800 dark:border-rose-800/40 dark:bg-rose-950/50 dark:text-rose-300">
                    {{ $errorMessage }}
                </div>
            @endif

            <flux:field>
                <flux:label>Perguruan Tinggi Mitra</flux:label>
                <flux:select wire:model.live="pt_id">
                    <flux:select.option value="">-- Pilih Perguruan Tinggi --</flux:select.option>
                    @foreach($this->perguruanTinggis as $pt)
                        <flux:select.option :value="$pt->id">{{ $pt->nama_pt }} (MoU s/d: {{ $pt->tgl_akhir_mou->format('d/m/Y') }})</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="pt_id" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Pilih Unit Rumah Sakit</flux:label>
                    <flux:select wire:model.live="unit_id">
                        <flux:select.option value="">-- Pilih Unit RS --</flux:select.option>
                        @foreach($this->units as $u)
                            <flux:select.option :value="$u->id">{{ $u->nama_unit }} (Mode: {{ $u->mode_kuota }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="unit_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Program Studi Mahasiswa</flux:label>
                    <flux:input wire:model.live.debounce.400ms="prodi" placeholder="Contoh: S1 Keperawatan / Ners" />
                    <flux:error name="prodi" />
                </flux:field>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Jumlah Mahasiswa</flux:label>
                    <flux:input type="number" wire:model.live.debounce.300ms="jumlah_mahasiswa" min="1" max="50" />
                    <flux:error name="jumlah_mahasiswa" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Mulai</flux:label>
                    <flux:input type="date" wire:model.live="tgl_mulai" />
                    <flux:error name="tgl_mulai" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Selesai</flux:label>
                    <flux:input type="date" wire:model.live="tgl_selesai" />
                    <flux:error name="tgl_selesai" />
                </flux:field>
            </div>

            <!-- Real-time Kuota Feedback Panel -->
            @if($kuotaCheckResult)
                <div class="rounded-xl border p-4 text-xs {{ $kuotaCheckResult['available'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-200' : 'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-200' }}">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <flux:icon name="{{ $kuotaCheckResult['available'] ? 'check-circle' : 'exclamation-circle' }}" class="size-4" />
                        {{ $kuotaCheckResult['available'] ? 'Slot Kuota Tersedia' : 'Slot Kuota Tidak Memadai' }}
                    </div>
                    <div class="mt-1 leading-relaxed">
                        {{ $kuotaCheckResult['message'] }}
                    </div>
                </div>
            @endif

            <flux:field>
                <flux:label>Unggah Surat Permohonan Resmi PT (PDF - Opsional / Max 5MB)</flux:label>
                <input type="file" wire:model="file_surat_permohonan" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                <flux:error name="file_surat_permohonan" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showBookingModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Ajukan Permohonan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

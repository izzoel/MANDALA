<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Arsip Sertifikat
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Antrean verifikasi keabsahan dokumen sertifikat pelatihan yang diunggah peserta diklat rumah sakit.
            </flux:text>
        </div>
    </div>

    @if (session()->has('message'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Tabs & Search -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800">
            <button wire:click="$set('tab', 'pending')"
                class="px-4 py-2 text-sm font-semibold border-b-2 transition {{ $tab === 'pending' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                Menunggu Verifikasi ({{ $this->pendingSertifikats->count() }})
            </button>
            <button wire:click="$set('tab', 'selesai')"
                class="px-4 py-2 text-sm font-semibold border-b-2 transition {{ $tab === 'selesai' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                Riwayat Selesai ({{ $this->historySertifikats->count() }})
            </button>
        </div>

        <div class="w-full sm:w-80">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari data..." icon="magnifying-glass" />
        </div>
    </div>

    @if ($tab === 'pending')
        <!-- Antrean Pending -->
        <div
            class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                    <thead
                        class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3.5">Pegawai & Unit</th>
                            <th class="px-5 py-3.5">Pelatihan & Penyelenggara</th>
                            <th class="px-5 py-3.5">No. Sertifikat</th>
                            <th class="px-5 py-3.5">Tgl Pelaksanaan</th>
                            <th class="px-5 py-3.5">Tgl Upload</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($this->pendingSertifikats as $s)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-white">
                                        {{ $s->pegawai->nama ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $s->pegawai->no_pegawai ?? '-' }} •
                                        {{ $s->pegawai->unit_kerja ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $s->nama_pelatihan }}
                                    </div>
                                    <div class="text-xs text-zinc-500">{{ $s->penyelenggara }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ $s->no_sertifikat }}
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    {{ \Carbon\Carbon::parse($s->tgl_pelaksanaan)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-xs text-zinc-400">
                                    {{ $s->created_at->diffForHumans() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <flux:button wire:click="openVerifyModal({{ $s->id }})" size="sm"
                                        variant="primary" icon="shield-check">
                                        Verifikasi
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-zinc-500">
                                    Tidak ada sertifikat yang menunggu verifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Riwayat Selesai -->
        <div
            class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                    <thead
                        class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3.5">Pegawai</th>
                            <th class="px-5 py-3.5">Pelatihan</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Verifikator & Waktu</th>
                            <th class="px-5 py-3.5">Catatan</th>
                            <th class="px-5 py-3.5 text-right">Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($this->historySertifikats as $s)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-zinc-900 dark:text-white">
                                        {{ $s->pegawai->nama ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $s->pegawai->unit_kerja ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $s->nama_pelatihan }}
                                    </div>
                                    <div class="text-xs text-zinc-500">No: {{ $s->no_sertifikat }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($s->status_verifikasi === 'disetujui')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            <flux:icon name="check-circle" class="size-3" /> Disetujui
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800 dark:bg-rose-950/50 dark:text-rose-300">
                                            <flux:icon name="x-circle" class="size-3" /> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $s->verifikator->name ?? '-' }}</div>
                                    <div class="text-zinc-400">
                                        {{ $s->verified_at ? $s->verified_at->format('d M Y H:i') : '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs text-zinc-600 dark:text-zinc-400 max-w-xs truncate">
                                    {{ $s->catatan_verifikator ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <flux:button href="{{ asset('storage/' . $s->file_path) }}" target="_blank"
                                        size="sm" variant="subtle" icon="arrow-down-tray">
                                        Unduh
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-zinc-500">
                                    Belum ada riwayat verifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal Form Verifikasi -->
    @if ($selectedSertifikat)
        <flux:modal wire:model="showVerifyModal" class="min-w-xl">
            <form wire:submit.prevent="submitVerifikasi" class="space-y-4">
                <div>
                    <flux:heading size="lg">Review & Verifikasi Dokumen Sertifikat</flux:heading>
                    <flux:subheading>Pastikan keaslian nomor sertifikat dan relevansi kompetensi pelatihan.
                    </flux:subheading>
                </div>

                <div
                    class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/60 space-y-2 text-sm">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Nama Pegawai:</span>
                        <span
                            class="col-span-2 font-semibold text-zinc-900 dark:text-white">{{ $selectedSertifikat->pegawai->nama ?? '-' }}
                            ({{ $selectedSertifikat->pegawai->unit_kerja ?? '-' }})</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Nama Pelatihan:</span>
                        <span class="col-span-2 font-medium">{{ $selectedSertifikat->nama_pelatihan }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Penyelenggara:</span>
                        <span class="col-span-2">{{ $selectedSertifikat->penyelenggara }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">No. Sertifikat:</span>
                        <span class="col-span-2 font-mono font-bold">{{ $selectedSertifikat->no_sertifikat }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="text-zinc-500">Tgl Pelaksanaan:</span>
                        <span
                            class="col-span-2">{{ \Carbon\Carbon::parse($selectedSertifikat->tgl_pelaksanaan)->format('d F Y') }}</span>
                    </div>

                    <div class="pt-2">
                        <flux:button href="{{ asset('storage/' . $selectedSertifikat->file_path) }}" target="_blank"
                            size="sm" variant="filled" icon="document-magnifying-glass">
                            Buka / Preview Berkas Sertifikat
                        </flux:button>
                    </div>
                </div>

                <flux:field>
                    <flux:label>Keputusan Verifikasi</flux:label>
                    <flux:select wire:model="status_verifikasi">
                        <flux:select.option value="disetujui">Setujui (Valid & Sah)</flux:select.option>
                        <flux:select.option value="ditolak">Tolak (Tidak Valid / Tidak Terbaca)</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Catatan Verifikator</flux:label>
                    <flux:textarea wire:model="catatan_verifikator"
                        placeholder="Berikan catatan keabsahan atau alasan jika ditolak..." rows="3" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button type="button" variant="subtle" wire:click="$set('showVerifyModal', false)">Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary">Simpan Keputusan</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>

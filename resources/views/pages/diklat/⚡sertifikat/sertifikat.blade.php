<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Sertifikat Pelatihan Pegawai
            </flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Penyimpanan dan verifikasi bukti sertifikat pelatihan kompetensi kesehatan & kepegawaian rumah sakit.
            </flux:text>
        </div>

        <flux:button wire:click="openModal" variant="primary" icon="arrow-up-tray">
            Unggah Sertifikat Baru
        </flux:button>
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
                placeholder="Cari nama pelatihan, nomor sertifikat, penyelenggara, atau pegawai..."
                icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-4">
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="semua">Semua Status Verifikasi</flux:select.option>
                <flux:select.option value="pending">Menunggu Verifikasi (Pending)</flux:select.option>
                <flux:select.option value="disetujui">Disetujui</flux:select.option>
                <flux:select.option value="ditolak">Ditolak</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Daftar Sertifikat -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($this->sertifikats as $sertifikat)
            <div
                class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sertifikat->status_verifikasi === 'disetujui' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300' : ($sertifikat->status_verifikasi === 'ditolak' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300') }}">
                            @if ($sertifikat->status_verifikasi === 'disetujui')
                                <flux:icon name="check-circle" class="size-3.5" /> Disetujui
                            @elseif($sertifikat->status_verifikasi === 'ditolak')
                                <flux:icon name="x-circle" class="size-3.5" /> Ditolak
                            @else
                                <flux:icon name="clock" class="size-3.5" /> Pending Verifikasi
                            @endif
                        </span>

                        <span class="text-xs text-zinc-400">
                            {{ \Carbon\Carbon::parse($sertifikat->tgl_pelaksanaan)->format('d M Y') }}
                        </span>
                    </div>

                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white line-clamp-2">
                            {{ $sertifikat->nama_pelatihan }}</h4>
                        <p class="text-xs text-zinc-500 mt-1">{{ $sertifikat->penyelenggara }}</p>
                    </div>

                    <div
                        class="rounded-lg bg-zinc-50 p-2.5 text-xs text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300 space-y-1">
                        <div><span class="text-zinc-400">No:</span> {{ $sertifikat->no_sertifikat }}</div>
                        <div><span class="text-zinc-400">Pegawai:</span> {{ $sertifikat->pegawai->nama ?? '-' }}
                            ({{ $sertifikat->pegawai->unit_kerja ?? '-' }})
                        </div>
                    </div>

                    @if ($sertifikat->catatan_verifikator)
                        <div
                            class="rounded-lg border border-amber-200 bg-amber-50/50 p-2.5 text-xs text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-300">
                            <strong>Catatan:</strong> {{ $sertifikat->catatan_verifikator }}
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <span class="text-xs text-zinc-400">
                        {{ $sertifikat->created_at->diffForHumans() }}
                    </span>

                    <flux:button href="{{ asset('storage/' . $sertifikat->file_path) }}" target="_blank" size="sm"
                        variant="subtle" icon="arrow-down-tray">
                        Lihat Berkas
                    </flux:button>
                </div>
            </div>
        @empty
            <div
                class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                Belum ada berkas sertifikat yang diunggah.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Upload -->
    <flux:modal wire:model="showUploadModal" class="min-w-lg">
        <form wire:submit.prevent="saveSertifikat" class="space-y-4">
            <div>
                <flux:heading size="lg">Unggah Sertifikat Pelatihan</flux:heading>
                <flux:subheading>Unggah bukti kelulusan pelatihan (PDF, JPG, PNG - Maksimal 5MB).</flux:subheading>
            </div>

            @can('lihat-seluruh-sertifikat')
                <flux:field>
                    <flux:label>Pilih Peserta</flux:label>
                    <flux:select wire:model.live="pegawai_id" variant="listbox" searchable>
                        <flux:select.option value="">-- Pilih Peserta --</flux:select.option>
                        @foreach ($this->listPegawai as $p)
                            <flux:select.option :value="$p->id">{{ $p->nama }} ({{ $p->unit_kerja }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="pegawai_id" />
                </flux:field>
            @endcan

            @if ($this->openTargets->count() > 0)
                <flux:field>
                    <flux:label>Hubungkan ke Target Pelatihan (Opsional)</flux:label>
                    <flux:select wire:model="target_pelatihan_id">
                        <flux:select.option value="">-- Bukan Target Khusus --</flux:select.option>
                        @foreach ($this->openTargets as $tgt)
                            <flux:select.option :value="$tgt->id">{{ $tgt->nama_target }} (Tenggat:
                                {{ $tgt->tenggat->format('d/m/Y') }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @endif

            <flux:field>
                <flux:label>Nama Pelatihan / Workshop</flux:label>
                <flux:input wire:model="nama_pelatihan"
                    placeholder="Contoh: Basic Trauma Cardiac Life Support (BTCLS)" />
                <flux:error name="nama_pelatihan" />
            </flux:field>

            <flux:field>
                <flux:label>Lembaga Penyelenggara</flux:label>
                <flux:input wire:model="penyelenggara" placeholder="Contoh: Bapelkes Kemenkes RI / IDI / PPNI" />
                <flux:error name="penyelenggara" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Tanggal Pelaksanaan</flux:label>
                    <flux:input type="date" wire:model="tgl_pelaksanaan" />
                    <flux:error name="tgl_pelaksanaan" />
                </flux:field>

                <flux:field>
                    <flux:label>Nomor Sertifikat</flux:label>
                    <flux:input wire:model="no_sertifikat" placeholder="No. Sertifikat resmi" />
                    <flux:error name="no_sertifikat" />
                </flux:field>
            </div>

            <flux:file-upload wire:model="file_sertifikat" label="File Dokumen Sertifikat">
                <flux:file-upload.dropzone heading="Seret file atau klik untuk menelusuri"
                    text="PDF, JPG, PNG maksimal 5MB" with-progress inline />
            </flux:file-upload>
            {{-- <flux:error name="file_sertifikat" /> --}}

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showUploadModal', false)">Batal
                </flux:button>
                <flux:button type="submit" variant="primary">Kirim Sertifikat</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

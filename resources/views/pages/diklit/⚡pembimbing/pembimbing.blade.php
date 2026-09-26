<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Penunjukan Pembimbing Lapangan & Dosen</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Penetapan Clinical Instructor (CI) Lapangan Rumah Sakit dan Dosen Pembimbing untuk mahasiswa praktik.
            </flux:text>
        </div>

        @if(auth()->user()->isAdminDiklat())
            <flux:button wire:click="openModal" variant="primary" icon="user-plus">
                Tetapkan Pembimbing
            </flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama mahasiswa, NIM, atau nama pembimbing..." icon="magnifying-glass" />
    </div>

    <!-- Tabel Penunjukan -->
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3.5">Mahasiswa Praktikan</th>
                        <th class="px-5 py-3.5">Unit & Periode</th>
                        <th class="px-5 py-3.5">Pembimbing Lapangan (CI RS)</th>
                        <th class="px-5 py-3.5">Pembimbing Dosen (PT)</th>
                        @if(auth()->user()->isAdminDiklat())
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($this->penunjukans as $pen)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $pen->mahasiswa->nama ?? '-' }}</div>
                                <div class="text-xs text-zinc-500">NIM: {{ $pen->mahasiswa->nim ?? '-' }} • {{ $pen->mahasiswa->prodi ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $pen->permohonan->unit->nama_unit ?? '-' }}</div>
                                <div class="text-xs text-zinc-500">
                                    {{ $pen->permohonan->tgl_mulai ? $pen->permohonan->tgl_mulai->format('d M') : '' }} - {{ $pen->permohonan->tgl_selesai ? $pen->permohonan->tgl_selesai->format('d M Y') : '' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($pen->pembimbingLapangan)
                                    <div class="font-semibold text-primary-700 dark:text-primary-400">{{ $pen->pembimbingLapangan->nama }}</div>
                                    <div class="text-xs text-zinc-500">Kontak: {{ $pen->pembimbingLapangan->kontak ?? '-' }}</div>
                                @else
                                    <span class="text-xs text-amber-600 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($pen->pembimbingDosen)
                                    <div class="font-semibold text-purple-700 dark:text-purple-400">{{ $pen->pembimbingDosen->nama }}</div>
                                    <div class="text-xs text-zinc-500">NIDN: {{ $pen->pembimbingDosen->nidn ?? '-' }}</div>
                                @else
                                    <span class="text-xs text-amber-600 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdminDiklat())
                                <td class="px-5 py-4 text-right">
                                    <flux:button wire:click="deletePenunjukan({{ $pen->id }})" wire:confirm="Hapus penunjukan pembimbing ini?" size="xs" variant="danger" icon="trash">
                                        Hapus
                                    </flux:button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-zinc-500">
                                Belum ada data penunjukan pembimbing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Penunjukan -->
    <flux:modal wire:model="showModal" class="min-w-lg">
        <form wire:submit.prevent="savePenunjukan" class="space-y-4">
            <div>
                <flux:heading size="lg">Penunjukan Pembimbing Mahasiswa</flux:heading>
                <flux:subheading>Pasangkan mahasiswa dengan pembimbing lapangan RS dan dosen pembimbing institusi.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Pilih Permohonan Praktik (Yang Disetujui)</flux:label>
                <flux:select wire:model="permohonan_id">
                    <flux:select.option value="">-- Pilih Kegiatan Praktik --</flux:select.option>
                    @foreach($this->approvedPermohonans as $appr)
                        <flux:select.option :value="$appr->id">{{ $appr->perguruanTinggi->nama_pt }} - {{ $appr->unit->nama_unit }} ({{ $appr->prodi }})</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="permohonan_id" />
            </flux:field>

            <flux:field>
                <flux:label>Pilih Mahasiswa</flux:label>
                <flux:select wire:model="mahasiswa_id">
                    <flux:select.option value="">-- Pilih Mahasiswa --</flux:select.option>
                    @foreach($this->listMahasiswa as $mhs)
                        <flux:select.option :value="$mhs->id">{{ $mhs->nama }} (NIM: {{ $mhs->nim }} - {{ $mhs->prodi }})</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="mahasiswa_id" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Pembimbing Lapangan (CI RS)</flux:label>
                    <flux:select wire:model="pembimbing_lapangan_id">
                        <flux:select.option value="">-- Pilih CI RS --</flux:select.option>
                        @foreach($this->listPembimbingLapangan as $pl)
                            <flux:select.option :value="$pl->id">{{ $pl->nama }} ({{ $pl->unit->nama_unit ?? 'RS' }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Pembimbing Dosen (PT)</flux:label>
                    <flux:select wire:model="pembimbing_dosen_id">
                        <flux:select.option value="">-- Pilih Dosen PT --</flux:select.option>
                        @foreach($this->listPembimbingDosen as $pd)
                            <flux:select.option :value="$pd->id">{{ $pd->nama }} ({{ $pd->perguruanTinggi->nama_pt }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Penunjukan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

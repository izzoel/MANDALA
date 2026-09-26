<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Master Kriteria & Pembobotan Penilaian Praktik</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Konfigurasi komponen evaluasi kompetensi mahasiswa yang diisi oleh pembimbing lapangan RS dan dosen PT.
            </flux:text>
        </div>

        @if(auth()->user()->isAdminDiklat())
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Tambah Kriteria
            </flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Total Bobot Indicator -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:icon name="scale" class="size-5 text-primary-500" />
            <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Total Akumulasi Bobot Aktif:</span>
        </div>
        <div class="text-base font-bold {{ $this->totalBobot == 100 ? 'text-emerald-600' : 'text-amber-600' }}">
            {{ $this->totalBobot }}% {{ $this->totalBobot == 100 ? '(Ideal 100%)' : '(Perhatian: Total harus 100%)' }}
        </div>
    </div>

    <!-- Tabel Kriteria -->
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3.5">Urutan</th>
                        <th class="px-5 py-3.5">Nama Aspek / Kriteria Evaluasi</th>
                        <th class="px-5 py-3.5">Bobot Nilai (%)</th>
                        <th class="px-5 py-3.5">Status</th>
                        @if(auth()->user()->isAdminDiklat())
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($this->kriteriaList as $k)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4 font-mono font-bold text-zinc-500">
                                #{{ $k->urutan }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-zinc-900 dark:text-white">
                                {{ $k->nama_kriteria }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-md bg-primary-50 px-2.5 py-1 text-xs font-bold text-primary-700 dark:bg-primary-950/50 dark:text-primary-300">
                                    {{ $k->bobot }}%
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($k->aktif)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdminDiklat())
                                <td class="px-5 py-4 text-right">
                                    <flux:dropdown align="end">
                                        <flux:button size="sm" variant="subtle" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item wire:click="editKriteria({{ $k->id }})" icon="pencil-square">Edit</flux:menu.item>
                                            <flux:menu.item wire:click="toggleAktif({{ $k->id }})" icon="arrow-path">Ubah Status Aktif</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-zinc-500">
                                Belum ada kriteria penilaian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Kriteria -->
    <flux:modal wire:model="showModal" class="min-w-lg">
        <form wire:submit.prevent="saveKriteria" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Kriteria Penilaian' : 'Tambah Kriteria Penilaian Baru' }}</flux:heading>
                <flux:subheading>Pastikan total persentase bobot keseluruhan komponen tetap seimbang (100%).</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nama Aspek / Kriteria Penilaian</flux:label>
                <flux:input wire:model="nama_kriteria" placeholder="Contoh: Keterampilan Klinis & Prosedur Tindakan" />
                <flux:error name="nama_kriteria" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Bobot Persentase (%)</flux:label>
                    <flux:input type="number" wire:model="bobot" min="1" max="100" step="0.5" />
                    <flux:error name="bobot" />
                </flux:field>

                <flux:field>
                    <flux:label>Urutan Tampilan</flux:label>
                    <flux:input type="number" wire:model="urutan" min="1" />
                    <flux:error name="urutan" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Status Kriteria</flux:label>
                <flux:select wire:model="aktif">
                    <flux:select.option :value="true">Aktif (Digunakan dalam formulir nilai)</flux:select.option>
                    <flux:select.option :value="false">Nonaktif (Diarsipkan)</flux:select.option>
                </flux:select>
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Kriteria</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

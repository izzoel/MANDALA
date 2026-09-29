<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Persetujuan & Penerbitan Surat
                Praktik</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Antrean verifikasi permohonan booking unit dari Perguruan Tinggi dan penerbitan nomor surat persetujuan
                resmi Diklat RS.
            </flux:text>
        </div>
    </div>

    @if (session()->has('message'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filter & Search -->
    <div
        class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:grid-cols-12">
        <div class="sm:col-span-8">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Cari nama perguruan tinggi, prodi, atau unit RS..." icon="magnifying-glass" />
        </div>
        <div class="sm:col-span-4">
            <flux:select wire:model.live="filterStatus">
                <flux:select.option value="diajukan">Menunggu Persetujuan (Diajukan)</flux:select.option>
                <flux:select.option value="disetujui">Telah Disetujui</flux:select.option>
                <flux:select.option value="ditolak">Ditolak</flux:select.option>
                <flux:select.option value="semua">Semua Status</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Tabel Permohonan -->
    <div
        class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                <thead
                    class="border-b border-zinc-200 bg-zinc-50 text-xs font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3.5">Perguruan Tinggi & Prodi</th>
                        <th class="px-5 py-3.5">Unit Tujuan</th>
                        <th class="px-5 py-3.5">Jumlah Mhs</th>
                        <th class="px-5 py-3.5">Rentang Tanggal</th>
                        <th class="px-5 py-3.5">Status & Surat</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($this->permohonans as $p)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-white">
                                    {{ $p->perguruanTinggi->nama_pt ?? '-' }}</div>
                                <div class="text-xs text-zinc-500">{{ $p->prodi }}</div>
                            </td>
                            <td class="px-5 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $p->unit->nama_unit ?? '-' }}
                            </td>
                            <td class="px-5 py-4 font-bold">
                                {{ $p->jumlah_mahasiswa }} Mhs
                            </td>
                            <td class="px-5 py-4 text-xs font-medium">
                                {{ $p->tgl_mulai->format('d/m/Y') }} - {{ $p->tgl_selesai->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($p->status === 'disetujui')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        <flux:icon name="check-circle" class="size-3" /> Disetujui
                                    </span>
                                    @if ($p->suratPersetujuan)
                                        <div class="mt-1 text-xs text-zinc-500 font-mono">
                                            No: {{ $p->suratPersetujuan->nomor_surat }}
                                        </div>
                                    @endif
                                @elseif($p->status === 'ditolak')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800 dark:bg-rose-950/50 dark:text-rose-300">
                                        <flux:icon name="x-circle" class="size-3" /> Ditolak
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                        <flux:icon name="clock" class="size-3" /> Menunggu Review
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <flux:button wire:click="openReviewModal({{ $p->id }})" size="sm"
                                    variant="primary" icon="clipboard-document-check">
                                    Review
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-zinc-500">
                                Tidak ada data permohonan yang perlu diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Review Permohonan -->
    @if ($selectedPermohonan)
        <flux:modal wire:model="showReviewModal" class="min-w-xl">
            <form wire:submit.prevent="submitKeputusan" class="space-y-4">
                <div>
                    <flux:heading size="lg">Review Permohonan Praktik Mahasiswa</flux:heading>
                    <flux:subheading>Tentukan persetujuan dan terbitkan nomor surat izin praktik rumah sakit.
                    </flux:subheading>
                </div>

                <div
                    class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/60 space-y-2 text-sm">
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
                        <div class="pt-2">
                            <flux:button href="{{ asset('storage/' . $selectedPermohonan->file_surat_permohonan) }}"
                                target="_blank" size="sm" variant="filled" icon="document-text">
                                Buka Berkas Pengantar PT
                            </flux:button>
                        </div>
                    @endif
                </div>

                <flux:field>
                    <flux:label>Keputusan Tim Diklat RS</flux:label>
                    <flux:select wire:model.live="status_keputusan">
                        <flux:select.option value="disetujui">Setujui Permohonan (Terbitkan Izin)</flux:select.option>
                        <flux:select.option value="ditolak">Tolak Permohonan</flux:select.option>
                    </flux:select>
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
                </flux:field>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button type="button" variant="subtle" wire:click="$set('showReviewModal', false)">Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary">Simpan Keputusan</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>

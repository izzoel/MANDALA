<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">Penilaian Praktik Klinik Mahasiswa</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Formulir evaluasi kompetensi klinis dinamis berbasis kriteria penilaian yang ditetapkan rumah sakit.
            </flux:text>
        </div>

        <flux:button wire:click="openInputModal" variant="primary" icon="star">
            Input Nilai Praktik
        </flux:button>
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/50 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama mahasiswa, NIM, atau nama penilai..." icon="magnifying-glass" />
    </div>

    <!-- Grid Riwayat Penilaian -->
    <div class="grid gap-6 md:grid-cols-2">
        @forelse($this->penilaians as $pen)
            <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-white text-base">{{ $pen->mahasiswa->nama ?? '-' }}</h3>
                            <div class="text-xs text-zinc-500">
                                NIM: {{ $pen->mahasiswa->nim ?? '-' }} • {{ $pen->mahasiswa->prodi ?? '-' }} ({{ $pen->mahasiswa->perguruanTinggi->nama_pt ?? '-' }})
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-bold {{ $pen->nilai_akhir >= 85 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300' : ($pen->nilai_akhir >= 70 ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300') }}">
                                {{ number_format($pen->nilai_akhir, 2) }}
                            </span>
                            <div class="text-[10pt] text-zinc-400 mt-0.5">Nilai Akhir</div>
                        </div>
                    </div>

                    <!-- Rincian Kriteria -->
                    <div class="rounded-lg bg-zinc-50 p-3 text-xs dark:bg-zinc-800/60 space-y-2 border border-zinc-100 dark:border-zinc-800">
                        <div class="font-semibold text-zinc-700 dark:text-zinc-300 border-b border-zinc-200/60 pb-1 dark:border-zinc-700">
                            Rincian Skor Kriteria:
                        </div>
                        <div class="space-y-1">
                            @foreach($pen->detailPenilaians as $dp)
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ $dp->kriteria->nama_kriteria ?? '-' }} (Bobot {{ $dp->kriteria->bobot ?? 0 }}%):</span>
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ number_format($dp->nilai, 1) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($pen->catatan_umum)
                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">
                            "{{ $pen->catatan_umum }}"
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                    <span class="flex items-center gap-1">
                        <flux:icon name="user-circle" class="size-3.5" />
                        Penilai: {{ $pen->penilai->name ?? '-' }} ({{ ucfirst($pen->peran_penilai) }})
                    </span>
                    <span>{{ $pen->tgl_isi->format('d M Y') }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-zinc-500">
                Belum ada data evaluasi penilaian praktik yang diinputkan.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Input Nilai -->
    <flux:modal wire:model="showNilaiModal" class="min-w-2xl">
        <form wire:submit.prevent="savePenilaian" class="space-y-4">
            <div>
                <flux:heading size="lg">Formulir Penilaian Praktik Mahasiswa</flux:heading>
                <flux:subheading>Input skor (skala 0 - 100) untuk masing-masing kriteria kompetensi.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Pilih Mahasiswa</flux:label>
                    <flux:select wire:model="mahasiswa_id">
                        <flux:select.option value="">-- Pilih Mahasiswa --</flux:select.option>
                        @foreach($this->listMahasiswa as $m)
                            <flux:select.option :value="$m->id">{{ $m->nama }} ({{ $m->nim }} - {{ $m->prodi }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="mahasiswa_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Kegiatan Praktik Terkait</flux:label>
                    <flux:select wire:model="permohonan_id">
                        <flux:select.option value="">-- Hubungkan ke Kegiatan Praktik --</flux:select.option>
                        @foreach($this->listPermohonan as $perm)
                            <flux:select.option :value="$perm->id">{{ $perm->unit->nama_unit }} - {{ $perm->perguruanTinggi->nama_pt }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Peran Penilai</flux:label>
                    <flux:select wire:model="peran_penilai">
                        <flux:select.option value="lapangan">Pembimbing Lapangan (CI RS)</flux:select.option>
                        <flux:select.option value="dosen">Dosen Pembimbing Akademik (PT)</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Penilaian</flux:label>
                    <flux:input type="date" wire:model="tgl_isi" />
                </flux:field>
            </div>

            <!-- Looping Dinamis Kriteria Penilaian -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/40 space-y-3">
                <h4 class="font-bold text-sm text-zinc-900 dark:text-white">Kriteria Evaluasi & Pembobotan</h4>
                
                @foreach($this->kriteriaList as $kriteria)
                    <div class="rounded-lg bg-white p-3 shadow-2xs dark:bg-zinc-900 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                            <span>{{ $kriteria->urutan }}. {{ $kriteria->nama_kriteria }}</span>
                            <span class="rounded bg-primary-50 px-2 py-0.5 text-primary-700 font-bold dark:bg-primary-950/50 dark:text-primary-300">Bobot: {{ $kriteria->bobot }}%</span>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <flux:input type="number" wire:model="nilaiKriteria.{{ $kriteria->id }}" min="0" max="100" step="0.5" placeholder="Skor (0-100)" />
                            <div class="col-span-2">
                                <flux:input wire:model="catatanKriteria.{{ $kriteria->id }}" placeholder="Catatan khusus kriteria ini (opsional)" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <flux:field>
                <flux:label>Catatan Umum & Kesimpulan Pembimbing</flux:label>
                <flux:textarea wire:model="catatan_umum" placeholder="Ulasan sikap, ketepatan tindakan, dan saran pengembangan mahasiswa..." rows="3" />
            </flux:field>

            <div class="flex justify-end gap-2 pt-4">
                <flux:button type="button" variant="subtle" wire:click="$set('showNilaiModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan & Hitung Nilai Akhir</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

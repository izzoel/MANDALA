<x-layouts::app :title="__('Dashboard Diklat & Diklit RS')">
    @php
        $totalTarget = \App\Models\TargetPelatihan::count();
        $targetSelesai = \App\Models\TargetPelatihan::where('status', 'selesai')->count();
        $sertifikatPending = \App\Models\Sertifikat::where('status_verifikasi', 'pending')->count();
        $totalPtAktif = \App\Models\PerguruanTinggi::where('status_mou', true)
            ->where('tgl_akhir_mou', '>=', now()->toDateString())
            ->count();
        $bookingDisetujui = \App\Models\PermohonanPraktik::where('status', 'disetujui')->count();
        $totalMhsPraktik = \App\Models\PermohonanPraktik::where('status', 'disetujui')->sum('jumlah_mahasiswa');
        $totalUnit = \App\Models\Unit::count();

        $units = \App\Models\Unit::withCount([
            'permohonanPraktiks' => fn($q) => $q->where('status', 'disetujui'),
        ])->get();
        $recentBookings = \App\Models\PermohonanPraktik::with(['perguruanTinggi', 'unit'])
            ->latest()
            ->take(5)
            ->get();
        $recentSertifikats = \App\Models\Sertifikat::with(['pegawai.user'])
            ->latest()
            ->take(5)
            ->get();
    @endphp

    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                    {{ __('Dashboard Utama') }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>
                    {{ __('Manajemen Diklat & Diklit RS') }}
                </flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <!-- Header Dashboard -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-1.5">
                <flux:heading size="xl" class="font-bold text-zinc-900 dark:text-white">
                    {{ __('Sistem Manajemen Diklat & Diklit Rumah Sakit') }}
                </flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">
                    {{ __('Pemantauan kompetensi pelatihan peserta diklat, kuota unit RS, dan izin praktik klinik mahasiswa.') }}
                </flux:text>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <flux:button :href="route('diklit.booking')" size="sm" variant="primary" icon="calendar-days"
                    wire:navigate>
                    {{ __('Booking Praktik') }}
                </flux:button>
                <flux:button :href="route('diklat.sertifikat')" size="sm" variant="subtle" icon="arrow-up-tray"
                    wire:navigate>
                    {{ __('Unggah Sertifikat') }}
                </flux:button>
                @if (auth()->user()->isAdminDiklat())
                    {{-- <flux:button :href="route('diklat.verifikasi')" size="sm" variant="filled" icon="shield-check" wire:navigate> --}}
                    <flux:button :href="route('diklat.arsip')" size="sm" variant="filled" icon="shield-check"
                        wire:navigate>
                        {{ __('Verifikasi (' . $sertifikatPending . ')') }}
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card 1: Pelatihan Pegawai -->
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Pelatihan Pegawai</span>
                    <span class="rounded-full bg-blue-50 p-2 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <flux:icon name="academic-cap" class="size-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalTarget }} Pelatihan</div>
                    <div class="mt-1 text-xs text-emerald-600 font-medium flex items-center gap-1">
                        <flux:icon name="check-circle" class="size-3" /> {{ $targetSelesai }} telah selesai
                    </div>
                </div>
            </div>

            <!-- Card 2: Sertifikat Pending -->
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Antrean Verifikasi</span>
                    <span class="rounded-full bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                        <flux:icon name="clock" class="size-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $sertifikatPending }} Berkas</div>
                    <div class="mt-1 text-xs text-amber-600 font-medium">Menunggu review Admin Diklat</div>
                </div>
            </div>

            <!-- Card 3: Mahasiswa Praktik Aktif -->
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-zinc-500">Mahasiswa Praktik</span>
                    <span
                        class="rounded-full bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <flux:icon name="user-group" class="size-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalMhsPraktik }} Orang</div>
                    <div class="mt-1 text-xs text-zinc-500">Dari {{ $bookingDisetujui }} permohonan disetujui</div>
                </div>
            </div>

            <!-- Card 4: Perguruan Tinggi Mitra -->
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-zinc-500">PT Mitra & MoU Aktif</span>
                    <span
                        class="rounded-full bg-purple-50 p-2 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400">
                        <flux:icon name="building-library" class="size-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalPtAktif }} Institusi</div>
                    <div class="mt-1 text-xs text-emerald-600 font-medium">MoU resmi berlaku</div>
                </div>
            </div>
        </div>

        <!-- Section: Kuota Unit RS & Permohonan Terbaru -->
        <div class="grid gap-6 lg:grid-cols-12">
            <!-- Left: Kapasitas Unit RS -->
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-base">Alokasi & Kapasitas Unit RS</h3>
                    <flux:button :href="route('diklit.unit')" size="xs" variant="subtle" wire:navigate>Lihat Semua
                    </flux:button>
                </div>

                <div class="space-y-3">
                    @forelse($units as $u)
                        <div
                            class="rounded-lg border border-zinc-100 bg-zinc-50 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-bold text-sm text-zinc-900 dark:text-white">{{ $u->nama_unit }}</span>
                                <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">
                                    Kapasitas: {{ $u->kuota_maks }} Mhs
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-zinc-500">
                                <span>Mode: {{ $u->mode_kuota === 'per_prodi' ? 'Per-Prodi' : 'Gabungan' }}</span>
                                <span class="text-emerald-600 font-medium">{{ $u->permohonan_praktiks_count }} Kegiatan
                                    Praktik</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-zinc-400">Belum ada unit yang dikonfigurasi.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Permohonan Booking Praktik Terbaru -->
            <div
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-base">Permohonan Praktik Mahasiswa Terbaru
                    </h3>
                    <flux:button :href="route('diklit.booking')" size="xs" variant="subtle" wire:navigate>Kelola
                        Booking</flux:button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                        <thead
                            class="border-b border-zinc-200 bg-zinc-50 text-[11px] font-semibold uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-3 py-2.5">Institusi & Prodi</th>
                                <th class="px-3 py-2.5">Unit Tujuan</th>
                                <th class="px-3 py-2.5">Jml</th>
                                <th class="px-3 py-2.5">Jadwal</th>
                                <th class="px-3 py-2.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse($recentBookings as $rb)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                                    <td class="px-3 py-3">
                                        <div class="font-semibold text-zinc-900 dark:text-white">
                                            {{ $rb->perguruanTinggi->nama_pt ?? '-' }}</div>
                                        <div class="text-zinc-500 text-[10px]">{{ $rb->prodi }}</div>
                                    </td>
                                    <td class="px-3 py-3 font-medium">{{ $rb->unit->nama_unit ?? '-' }}</td>
                                    <td class="px-3 py-3 font-bold">{{ $rb->jumlah_mahasiswa }}</td>
                                    <td class="px-3 py-3 text-[11px]">{{ $rb->tgl_mulai->format('d/m') }} -
                                        {{ $rb->tgl_selesai->format('d/m/Y') }}</td>
                                    <td class="px-3 py-3">
                                        @if ($rb->status === 'disetujui')
                                            <span
                                                class="rounded-full bg-emerald-100 px-2 py-0.5 font-bold text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">Disetujui</span>
                                        @elseif($rb->status === 'ditolak')
                                            <span
                                                class="rounded-full bg-rose-100 px-2 py-0.5 font-bold text-rose-800 dark:bg-rose-950/50 dark:text-rose-300">Ditolak</span>
                                        @else
                                            <span
                                                class="rounded-full bg-amber-100 px-2 py-0.5 font-bold text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">Diajukan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-zinc-400">Belum ada permohonan
                                        booking.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>

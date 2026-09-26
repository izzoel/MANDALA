<?php

use App\Models\PerguruanTinggi;
use App\Models\PermohonanPraktik;
use App\Models\Unit;
use App\Models\UnitProdiKuota;
use App\Services\BookingKuotaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Booking & Permohonan Praktik RS')] class extends Component {
    use WithFileUploads;

    // View Mode: 'list' atau 'kalender'
    public string $viewMode = 'list';

    // Filter & Search
    public string $search = '';
    public string $filterStatus = 'semua';
    public string|int|null $filterUnitId = null;

    // Calendar State
    public int $calendarMonth = 0;
    public int $calendarYear = 0;
    public string|int|null $calendarUnitId = null;

    // Form Permohonan Booking
    public bool $showBookingModal = false;
    public string|int|null $pt_id = null;
    public string|int|null $unit_id = null;
    public string $prodi = '';
    public int $jumlah_mahasiswa = 1;
    public string $tgl_mulai = '';
    public string $tgl_selesai = '';
    public $file_surat_permohonan;

    // Detail Modal
    public bool $showDetailModal = false;
    public ?PermohonanPraktik $selectedPermohonan = null;

    // Real-time Kuota Feedback
    public ?array $kuotaCheckResult = null;
    public string $errorMessage = '';

    public function mount(): void
    {
        $this->tgl_mulai = now()->addDays(7)->toDateString();
        $this->tgl_selesai = now()->addDays(35)->toDateString();
        $this->calendarMonth = (int) now()->format('n');
        $this->calendarYear = (int) now()->format('Y');

        $user = auth()->user();
        if ($user) {
            if ($user->mahasiswa && $user->mahasiswa->pt_id) {
                $this->pt_id = $user->mahasiswa->pt_id;
            } elseif ($user->pembimbingDosen && $user->pembimbingDosen->pt_id) {
                $this->pt_id = $user->pembimbingDosen->pt_id;
            }
        }
    }

    #[Computed]
    public function perguruanTinggis()
    {
        return PerguruanTinggi::where('status_mou', true)
            ->where('tgl_akhir_mou', '>=', now()->toDateString())
            ->orderBy('nama_pt')
            ->get();
    }

    #[Computed]
    public function allPerguruanTinggis()
    {
        return PerguruanTinggi::orderBy('nama_pt')->get();
    }

    #[Computed]
    public function units()
    {
        return Unit::with('prodiKuotas')->orderBy('nama_unit')->get();
    }

    #[Computed]
    public function selectedUnit()
    {
        return $this->unit_id ? Unit::with('prodiKuotas')->find((int) $this->unit_id) : null;
    }

    #[Computed]
    public function selectedUnitProdis()
    {
        if (! $this->unit_id) {
            return collect();
        }

        $unit = Unit::with('prodiKuotas')->find((int) $this->unit_id);
        return $unit ? $unit->prodiKuotas : collect();
    }

    #[Computed]
    public function permohonans()
    {
        return PermohonanPraktik::query()
            ->with(['perguruanTinggi', 'unit.prodiKuotas', 'suratPersetujuan.diterbitkanOleh', 'penunjukanPembimbings.mahasiswa', 'penunjukanPembimbings.pembimbingLapangan', 'penunjukanPembimbings.pembimbingDosen'])
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->when(! empty($this->filterUnitId), fn ($q) => $q->where('unit_id', (int) $this->filterUnitId))
            ->when($this->search !== '', function ($q) {
                $term = '%' . strtolower($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(prodi) LIKE ?', [$term])
                        ->orWhereHas('perguruanTinggi', fn ($pt) => $pt->whereRaw('LOWER(nama_pt) LIKE ?', [$term]))
                        ->orWhereHas('unit', fn ($u) => $u->whereRaw('LOWER(nama_unit) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function activeBookings()
    {
        return PermohonanPraktik::with(['perguruanTinggi', 'unit'])
            ->where('status', 'disetujui')
            ->where('tgl_selesai', '>=', now()->toDateString())
            ->orderBy('tgl_mulai')
            ->get();
    }

    #[Computed]
    public function stats()
    {
        $all = PermohonanPraktik::count();
        $diajukan = PermohonanPraktik::where('status', 'diajukan')->count();
        $disetujui = PermohonanPraktik::where('status', 'disetujui')->count();
        $ditolak = PermohonanPraktik::where('status', 'ditolak')->count();

        return [
            'total' => $all,
            'diajukan' => $diajukan,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
        ];
    }

    #[Computed]
    public function calendarData()
    {
        $startOfMonth = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $bookings = PermohonanPraktik::with(['perguruanTinggi', 'unit'])
            ->where('status', 'disetujui')
            ->when(! empty($this->calendarUnitId), fn ($q) => $q->where('unit_id', (int) $this->calendarUnitId))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tgl_mulai', '<=', $endOfMonth->toDateString())
                  ->where('tgl_selesai', '>=', $startOfMonth->toDateString());
            })
            ->orderBy('tgl_mulai')
            ->get();

        // Keterisian per Unit di bulan terpilih
        $unitOccupancy = $this->units->map(function ($unit) use ($startOfMonth, $endOfMonth) {
            $unitBookings = PermohonanPraktik::where('unit_id', $unit->id)
                ->where('status', 'disetujui')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('tgl_mulai', '<=', $endOfMonth->toDateString())
                      ->where('tgl_selesai', '>=', $startOfMonth->toDateString());
                })
                ->get();

            $totalMhs = $unitBookings->sum('jumlah_mahasiswa');
            $persentase = $unit->kuota_maks > 0 ? min(100, round(($totalMhs / $unit->kuota_maks) * 100)) : 0;

            return [
                'unit' => $unit,
                'total_bookings' => $unitBookings->count(),
                'total_mhs' => $totalMhs,
                'persentase' => $persentase,
                'bookings' => $unitBookings,
            ];
        });

        return [
            'monthName' => $startOfMonth->translatedFormat('F Y'),
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
            'daysInMonth' => $startOfMonth->daysInMonth,
            'bookings' => $bookings,
            'unitOccupancy' => $unitOccupancy,
        ];
    }

    public function prevMonth(): void
    {
        $current = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = (int) $current->format('n');
        $this->calendarYear = (int) $current->format('Y');
    }

    public function nextMonth(): void
    {
        $current = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = (int) $current->format('n');
        $this->calendarYear = (int) $current->format('Y');
    }

    public function resetCalendar(): void
    {
        $this->calendarMonth = (int) now()->format('n');
        $this->calendarYear = (int) now()->format('Y');
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'unit_id') {
            if ($this->unit_id) {
                $unit = Unit::with('prodiKuotas')->find((int) $this->unit_id);
                if ($unit && $unit->mode_kuota === 'per_prodi' && $unit->prodiKuotas->isNotEmpty()) {
                    $this->prodi = $unit->prodiKuotas->first()->prodi;
                }
            } else {
                $this->prodi = '';
            }
            $this->checkLiveKuota();
        } elseif (in_array($propertyName, ['prodi', 'tgl_mulai', 'tgl_selesai', 'jumlah_mahasiswa', 'pt_id'])) {
            $this->checkLiveKuota();
        }
    }

    public function checkLiveKuota(): void
    {
        $this->errorMessage = '';
        $this->kuotaCheckResult = null;

        if (! empty($this->unit_id) && $this->tgl_mulai && $this->tgl_selesai && $this->tgl_selesai >= $this->tgl_mulai) {
            $service = app(BookingKuotaService::class);
            $this->kuotaCheckResult = $service->checkKuotaAvailability(
                (int) $this->unit_id,
                (string) $this->prodi,
                $this->tgl_mulai,
                $this->tgl_selesai,
                (int) $this->jumlah_mahasiswa
            );
        }
    }

    public function openBookingModal(): void
    {
        $this->reset(['unit_id', 'prodi', 'file_surat_permohonan', 'kuotaCheckResult', 'errorMessage']);
        $this->jumlah_mahasiswa = 1;
        $this->tgl_mulai = now()->addDays(7)->toDateString();
        $this->tgl_selesai = now()->addDays(35)->toDateString();

        $user = auth()->user();
        if ($user && $user->mahasiswa && $user->mahasiswa->pt_id) {
            $this->pt_id = $user->mahasiswa->pt_id;
        }

        $this->showBookingModal = true;
    }

    public function viewDetail(int $id): void
    {
        $this->selectedPermohonan = PermohonanPraktik::with([
            'perguruanTinggi',
            'unit.prodiKuotas',
            'suratPersetujuan.diterbitkanOleh',
            'penunjukanPembimbings.mahasiswa',
            'penunjukanPembimbings.pembimbingLapangan',
            'penunjukanPembimbings.pembimbingDosen',
        ])->find($id);

        if ($this->selectedPermohonan) {
            $this->showDetailModal = true;
        }
    }

    public function batalkanPermohonan(int $id): void
    {
        $permohonan = PermohonanPraktik::findOrFail($id);

        // Hanya permohonan 'diajukan' yang bisa dibatalkan atau pengguna dengan hak admin
        if ($permohonan->status !== 'diajukan' && ! auth()->user()?->isAdminDiklat()) {
            session()->flash('error', 'Permohonan yang telah disetujui/ditolak tidak dapat dibatalkan.');
            return;
        }

        if ($permohonan->file_surat_permohonan && Storage::disk('public')->exists($permohonan->file_surat_permohonan)) {
            Storage::disk('public')->delete($permohonan->file_surat_permohonan);
        }

        $permohonan->delete();
        $this->showDetailModal = false;
        session()->flash('message', 'Permohonan praktik berhasil dibatalkan dan dihapus.');
    }

    public function submitPermohonan(): void
    {
        $this->errorMessage = '';

        $this->validate([
            'pt_id' => 'required|exists:perguruan_tinggis,id',
            'unit_id' => 'required|exists:units,id',
            'prodi' => 'required|string|max:150',
            'jumlah_mahasiswa' => 'required|integer|min:1|max:50',
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'file_surat_permohonan' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'pt_id.required' => 'Perguruan tinggi mitra wajib dipilih.',
            'unit_id.required' => 'Unit rumah sakit wajib dipilih.',
            'prodi.required' => 'Program studi wajib diisi atau dipilih.',
            'jumlah_mahasiswa.min' => 'Jumlah mahasiswa minimal 1 orang.',
            'tgl_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'tgl_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'file_surat_permohonan.mimes' => 'File surat permohonan harus berformat PDF.',
            'file_surat_permohonan.max' => 'Ukuran file surat permohonan maksimal 5MB.',
        ]);

        try {
            $filePath = null;
            if ($this->file_surat_permohonan) {
                $filePath = $this->file_surat_permohonan->store('surat_permohonan', 'public');
            }

            $service = app(BookingKuotaService::class);
            $service->ajukanPermohonan([
                'pt_id' => (int) $this->pt_id,
                'unit_id' => (int) $this->unit_id,
                'prodi' => $this->prodi,
                'jumlah_mahasiswa' => (int) $this->jumlah_mahasiswa,
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
                'file_surat_permohonan' => $filePath,
            ]);

            $this->showBookingModal = false;
            session()->flash('message', 'Permohonan booking praktik berhasil diajukan dan masuk ke antrean verifikasi Diklat RS.');
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
};

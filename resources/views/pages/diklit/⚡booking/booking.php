<?php

use App\Models\PerguruanTinggi;
use App\Models\PermohonanPraktik;
use App\Models\Unit;
use App\Services\BookingKuotaService;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Booking & Permohonan Praktik RS')] class extends Component {
    use WithFileUploads;

    public string $search = '';
    public string $filterStatus = 'semua';
    public ?int $filterUnitId = null;

    // Form Permohonan Booking
    public bool $showBookingModal = false;
    public ?int $pt_id = null;
    public ?int $unit_id = null;
    public string $prodi = '';
    public int $jumlah_mahasiswa = 1;
    public string $tgl_mulai = '';
    public string $tgl_selesai = '';
    public $file_surat_permohonan;

    // Real-time Kuota Feedback
    public ?array $kuotaCheckResult = null;
    public string $errorMessage = '';

    public function mount(): void
    {
        $this->tgl_mulai = now()->addDays(7)->toDateString();
        $this->tgl_selesai = now()->addDays(35)->toDateString();

        $user = auth()->user();
        if ($user && $user->isAdminPt() && $user->mahasiswa) {
            $this->pt_id = $user->mahasiswa->pt_id;
        }
    }

    #[Computed]
    public function perguruanTinggis()
    {
        return PerguruanTinggi::where('status_mou', true)->orderBy('nama_pt')->get();
    }

    #[Computed]
    public function units()
    {
        return Unit::with('prodiKuotas')->orderBy('nama_unit')->get();
    }

    #[Computed]
    public function permohonans()
    {
        return PermohonanPraktik::query()
            ->with(['perguruanTinggi', 'unit', 'suratPersetujuan', 'penunjukanPembimbings'])
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUnitId, fn ($q) => $q->where('unit_id', $this->filterUnitId))
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

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['unit_id', 'prodi', 'tgl_mulai', 'tgl_selesai', 'jumlah_mahasiswa'])) {
            $this->checkLiveKuota();
        }
    }

    public function checkLiveKuota(): void
    {
        $this->errorMessage = '';
        $this->kuotaCheckResult = null;

        if ($this->unit_id && $this->tgl_mulai && $this->tgl_selesai && $this->tgl_selesai >= $this->tgl_mulai) {
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
        $this->showBookingModal = true;
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
        ]);

        try {
            $filePath = null;
            if ($this->file_surat_permohonan) {
                $filePath = $this->file_surat_permohonan->store('surat_permohonan', 'public');
            }

            $service = app(BookingKuotaService::class);
            $service->ajukanPermohonan([
                'pt_id' => $this->pt_id,
                'unit_id' => $this->unit_id,
                'prodi' => $this->prodi,
                'jumlah_mahasiswa' => $this->jumlah_mahasiswa,
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
                'file_surat_permohonan' => $filePath,
            ]);

            $this->showBookingModal = false;
            session()->flash('message', 'Permohonan booking praktik berhasil diajukan dan masuk ke antrean verifikasi Diklat RS.');
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
};

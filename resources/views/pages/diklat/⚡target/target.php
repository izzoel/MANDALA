<?php

use App\Models\Peserta;
use App\Models\TargetPelatihan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Target Pelatihan Pegawai')] class extends Component
{
    public string $search = '';

    public string $filterStatus = 'semua';

    public string $filterPeriode = '2026';

    // Form Modal
    public bool $showModal = false;

    public ?int $pegawai_id = null;

    public string $nama_target = '';

    public string $kategori = 'Wajib';

    public string $periode = '2026';

    public string $tenggat = '';

    #[Computed]
    public function targets()
    {
        return TargetPelatihan::query()
            ->with(['pegawai.user', 'sertifikatPemenuhan'])
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPeriode !== 'semua', fn ($q) => $q->where('periode', $this->filterPeriode))
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_target) LIKE ?', [$term])
                        ->orWhereHas('pegawai', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term])->orWhereRaw('LOWER(unit_kerja) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function listPeserta()
    {
        return Peserta::orderBy('nama')->get();
    }

    public function openCreateModal(): void
    {
        $this->reset(['pegawai_id', 'nama_target', 'kategori', 'periode', 'tenggat']);
        $this->tenggat = now()->addMonths(3)->toDateString();
        $this->periode = '2026';
        $this->showModal = true;
    }

    public function saveTarget(): void
    {
        $this->validate([
            // 'pegawai_id' => 'required|exists:pegawai_non_asns,id',
            'pegawai_id' => 'required|exists:pesertas,id',
            'nama_target' => 'required|string|max:255',
            'kategori' => 'required|in:Wajib,Pilihan,Fungsional',
            'periode' => 'required|string|max:10',
            'tenggat' => 'required|date',
        ]);

        TargetPelatihan::create([
            'pegawai_id' => $this->pegawai_id,
            'nama_target' => $this->nama_target,
            'kategori' => $this->kategori,
            'periode' => $this->periode,
            'tenggat' => $this->tenggat,
            'status' => 'belum',
            'created_by' => auth()->id(),
        ]);

        $this->showModal = false;
        session()->flash('message', 'Target pelatihan berhasil ditugaskan ke pegawai.');
    }

    public function updateStatus(int $targetId, string $status): void
    {
        $target = TargetPelatihan::findOrFail($targetId);
        $target->update(['status' => $status]);
        session()->flash('message', 'Status target pelatihan berhasil diperbarui.');
    }
};

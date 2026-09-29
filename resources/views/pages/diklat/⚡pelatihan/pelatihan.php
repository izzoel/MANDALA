<?php

use App\Models\KategoriPelatihan;
use App\Models\Peserta;
use App\Models\TargetPelatihan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pelatihan Pegawai')] class extends Component
{
    public string $search = '';

    public string $filterStatus = 'semua';

    public string $filterPeriode = '2026';

    // Form Modal Tambah Pelatihan
    public bool $showModal = false;

    public string $nama_target = '';

    public string $searchKategori = '';

    public ?int $kategoriId = null;

    public string $periode = '2026';

    public string $tenggat = '';

    // Form Modal Tambah / Kelola Peserta
    public bool $showPesertaModal = false;

    public ?TargetPelatihan $selectedTarget = null;

    public array $selectedPesertaIds = [];

    #[Computed]
    public function targets()
    {
        return TargetPelatihan::query()
            ->with(['pegawai.user', 'sertifikatPemenuhan', 'kategoriPelatihan', 'pesertas'])
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPeriode !== 'semua', fn ($q) => $q->where('periode', $this->filterPeriode))
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_target) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(kategori) LIKE ?', [$term])
                        ->orWhereHas('pegawai', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term])->orWhereRaw('LOWER(unit_kerja) LIKE ?', [$term]))
                        ->orWhereHas('pesertas', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term])->orWhereRaw('LOWER(unit_kerja) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function kategoriPelatihans()
    {
        return KategoriPelatihan::query()
            ->when($this->searchKategori !== '', fn ($q) => $q->whereRaw('LOWER(nama) LIKE ?', ['%'.strtolower($this->searchKategori).'%']))
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function listPeserta()
    {
        return Peserta::orderBy('nama')->get();
    }

    public function createKategori(): void
    {
        $nama = trim($this->searchKategori);
        if (strlen($nama) < 2) {
            return;
        }

        $kategori = KategoriPelatihan::firstOrCreate([
            'nama' => $nama,
        ]);

        $this->kategoriId = $kategori->id;
    }

    public function openCreateModal(): void
    {
        $this->reset(['nama_target', 'kategoriId', 'searchKategori', 'periode', 'tenggat']);
        $this->tenggat = now()->addMonths(3)->toDateString();
        $this->periode = '2026';
        $this->showModal = true;
    }

    public function saveTarget(): void
    {
        $this->validate([
            'nama_target' => 'required|string|max:255',
            'kategoriId' => 'required|exists:kategori_pelatihans,id',
            'periode' => 'required|string|max:10',
            'tenggat' => 'required|date',
        ], [
            'nama_target.required' => 'Nama pelatihan wajib diisi.',
            'kategoriId.required' => 'Kategori pelatihan wajib dipilih atau dibuat.',
            'kategoriId.exists' => 'Kategori pelatihan yang dipilih tidak valid.',
            'periode.required' => 'Periode tahun wajib diisi.',
            'tenggat.required' => 'Tenggat waktu wajib diisi.',
        ]);

        $kategori = KategoriPelatihan::find($this->kategoriId);

        TargetPelatihan::create([
            'pegawai_id' => null,
            'nama_target' => $this->nama_target,
            'kategori_id' => $this->kategoriId,
            'kategori' => $kategori ? $kategori->nama : 'Umum',
            'periode' => $this->periode,
            'tenggat' => $this->tenggat,
            'status' => 'belum',
            'created_by' => auth()->id(),
        ]);

        $this->showModal = false;
        session()->flash('message', 'Pelatihan berhasil ditambahkan.');
    }

    public function openAddPesertaModal(int $targetId): void
    {
        $this->selectedTarget = TargetPelatihan::with('pesertas')->findOrFail($targetId);
        $this->selectedPesertaIds = $this->selectedTarget->pesertas->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->showPesertaModal = true;
    }

    public function savePesertaToPelatihan(): void
    {
        if (! $this->selectedTarget) {
            return;
        }

        $this->validate([
            'selectedPesertaIds' => 'required|array|min:1',
            'selectedPesertaIds.*' => 'exists:pesertas,id',
        ], [
            'selectedPesertaIds.required' => 'Pilih minimal satu peserta diklat.',
            'selectedPesertaIds.min' => 'Pilih minimal satu peserta diklat.',
        ]);

        $this->selectedTarget->pesertas()->sync($this->selectedPesertaIds);

        $this->showPesertaModal = false;
        session()->flash('message', 'Daftar peserta untuk pelatihan "'.$this->selectedTarget->nama_target.'" berhasil diperbarui.');
    }

    public function updateStatus(int $targetId, string $status): void
    {
        $target = TargetPelatihan::findOrFail($targetId);
        $target->update(['status' => $status]);
        session()->flash('message', 'Status pelatihan berhasil diperbarui.');
    }
};

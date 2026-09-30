<?php

use App\Models\Unit;
use App\Models\UnitProdiKuota;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Unit RS & Alokasi Kuota')] class extends Component
{
    public string $search = '';

    // Form Modal Unit
    public bool $showUnitModal = false;

    public ?int $editingUnitId = null;

    public string $nama_unit = '';

    public string $mode_kuota = 'gabungan';

    public int $kuota_maks = 10;

    public string $deskripsi = '';

    // Form Modal Alokasi Prodi Kuota
    public bool $showProdiModal = false;

    public ?int $selectedUnitId = null;

    public string $new_prodi = '';

    public int $new_kuota_maks = 5;

    #[Computed]
    public function units()
    {
        return Unit::query()
            ->with(['prodiKuotas', 'pembimbingLapangans'])
            ->withCount(['permohonanPraktiks' => fn ($q) => $q->where('status', 'disetujui')])
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_unit) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(deskripsi) LIKE ?', [$term]);
                });
            })
            ->orderBy('nama_unit')
            ->get();
    }

    public function openCreateUnitModal(): void
    {
        $this->reset(['editingUnitId', 'nama_unit', 'mode_kuota', 'kuota_maks', 'deskripsi']);
        $this->mode_kuota = 'gabungan';
        $this->kuota_maks = 10;
        $this->showUnitModal = true;
    }

    public function editUnit(int $id): void
    {
        $unit = Unit::findOrFail($id);
        $this->editingUnitId = $unit->id;
        $this->nama_unit = $unit->nama_unit;
        $this->mode_kuota = $unit->mode_kuota;
        $this->kuota_maks = $unit->kuota_maks;
        $this->deskripsi = $unit->deskripsi ?? '';
        $this->showUnitModal = true;
    }

    public function saveUnit(): void
    {
        $this->validate([
            'nama_unit' => 'required|string|max:255',
            'mode_kuota' => 'required|in:gabungan,per_prodi',
            'kuota_maks' => 'required|integer|min:1|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        if ($this->editingUnitId) {
            $unit = Unit::findOrFail($this->editingUnitId);
            $unit->update([
                'nama_unit' => $this->nama_unit,
                'mode_kuota' => $this->mode_kuota,
                'kuota_maks' => $this->kuota_maks,
                'deskripsi' => $this->deskripsi,
            ]);
            session()->flash('message', 'Unit rumah sakit berhasil diperbarui.');
        } else {
            Unit::create([
                'nama_unit' => $this->nama_unit,
                'mode_kuota' => $this->mode_kuota,
                'kuota_maks' => $this->kuota_maks,
                'deskripsi' => $this->deskripsi,
            ]);
            session()->flash('message', 'Unit rumah sakit baru berhasil ditambahkan.');
        }

        $this->showUnitModal = false;
    }

    public function openProdiModal(int $unitId): void
    {
        $this->selectedUnitId = $unitId;
        $this->reset(['new_prodi', 'new_kuota_maks']);
        $this->new_kuota_maks = 5;
        $this->showProdiModal = true;
    }

    public function saveProdiKuota(): void
    {
        $this->validate([
            'selectedUnitId' => 'required|exists:units,id',
            'new_prodi' => 'required|string|max:150',
            'new_kuota_maks' => 'required|integer|min:1|max:50',
        ]);

        UnitProdiKuota::updateOrCreate(
            ['unit_id' => $this->selectedUnitId, 'prodi' => $this->new_prodi],
            ['kuota_maks' => $this->new_kuota_maks]
        );

        $this->showProdiModal = false;
        session()->flash('message', 'Alokasi kuota prodi berhasil disimpan.');
    }

    public function deleteProdiKuota(int $id): void
    {
        UnitProdiKuota::destroy($id);
        session()->flash('message', 'Alokasi kuota prodi berhasil dihapus.');
    }
};

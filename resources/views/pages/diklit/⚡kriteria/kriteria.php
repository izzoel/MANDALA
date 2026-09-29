<?php

use App\Models\KriteriaPenilaian;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kriteria Penilaian Praktik')] class extends Component {
    // Form Modal
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $nama_kriteria = '';
    public float $bobot = 20.0;
    public int $urutan = 1;
    public string $aktif = '1';

    #[Computed]
    public function kriteriaList()
    {
        return KriteriaPenilaian::orderBy('urutan')->get();
    }

    #[Computed]
    public function totalBobot()
    {
        return (float) KriteriaPenilaian::where('aktif', true)->sum('bobot');
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'nama_kriteria', 'bobot', 'urutan', 'aktif']);
        $this->bobot = 20.0;
        $this->urutan = KriteriaPenilaian::count() + 1;
        $this->aktif = '1';
        $this->showModal = true;
    }

    public function editKriteria(int $id): void
    {
        $k = KriteriaPenilaian::findOrFail($id);
        $this->editingId = $k->id;
        $this->nama_kriteria = $k->nama_kriteria;
        $this->bobot = (float) $k->bobot;
        $this->urutan = $k->urutan;
        $this->aktif = $k->aktif ? '1' : '0';
        $this->showModal = true;
    }

    public function saveKriteria(): void
    {
        $this->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:1|max:100',
            'urutan' => 'required|integer|min:1',
            'aktif' => 'required|in:0,1',
        ]);

        $aktifBool = $this->aktif === '1';

        if ($this->editingId) {
            $k = KriteriaPenilaian::findOrFail($this->editingId);
            $k->update([
                'nama_kriteria' => $this->nama_kriteria,
                'bobot' => $this->bobot,
                'urutan' => $this->urutan,
                'aktif' => $aktifBool,
            ]);
            session()->flash('message', 'Kriteria penilaian berhasil diperbarui.');
        } else {
            KriteriaPenilaian::create([
                'nama_kriteria' => $this->nama_kriteria,
                'bobot' => $this->bobot,
                'urutan' => $this->urutan,
                'aktif' => $aktifBool,
            ]);
            session()->flash('message', 'Kriteria penilaian baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleAktif(int $id): void
    {
        $k = KriteriaPenilaian::findOrFail($id);
        $k->update(['aktif' => ! $k->aktif]);
        session()->flash('message', 'Status kriteria berhasil diperbarui.');
    }
};

<?php

use App\Models\Mahasiswa;
use App\Models\PembimbingDosen;
use App\Models\PembimbingLapangan;
use App\Models\PenunjukanPembimbing;
use App\Models\PermohonanPraktik;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Penunjukan Pembimbing Praktik')] class extends Component {
    public string $search = '';

    // Modal Form
    public bool $showModal = false;
    public ?int $permohonan_id = null;
    public ?int $mahasiswa_id = null;
    public ?int $pembimbing_lapangan_id = null;
    public ?int $pembimbing_dosen_id = null;

    #[Computed]
    public function penunjukans()
    {
        return PenunjukanPembimbing::query()
            ->with(['permohonan.unit', 'permohonan.perguruanTinggi', 'mahasiswa', 'pembimbingLapangan', 'pembimbingDosen'])
            ->when($this->search !== '', function ($q) {
                $term = '%' . strtolower($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('mahasiswa', fn ($m) => $m->whereRaw('LOWER(nama) LIKE ?', [$term])->orWhereRaw('LOWER(nim) LIKE ?', [$term]))
                        ->orWhereHas('pembimbingLapangan', fn ($pl) => $pl->whereRaw('LOWER(nama) LIKE ?', [$term]))
                        ->orWhereHas('pembimbingDosen', fn ($pd) => $pd->whereRaw('LOWER(nama) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function approvedPermohonans()
    {
        return PermohonanPraktik::with(['perguruanTinggi', 'unit'])
            ->where('status', 'disetujui')
            ->latest()
            ->get();
    }

    #[Computed]
    public function listMahasiswa()
    {
        return Mahasiswa::with('perguruanTinggi')->orderBy('nama')->get();
    }

    #[Computed]
    public function listPembimbingLapangan()
    {
        return PembimbingLapangan::with('unit')->orderBy('nama')->get();
    }

    #[Computed]
    public function listPembimbingDosen()
    {
        return PembimbingDosen::with('perguruanTinggi')->orderBy('nama')->get();
    }

    public function openModal(): void
    {
        $this->reset(['permohonan_id', 'mahasiswa_id', 'pembimbing_lapangan_id', 'pembimbing_dosen_id']);
        $this->showModal = true;
    }

    public function savePenunjukan(): void
    {
        $this->validate([
            'permohonan_id' => 'required|exists:permohonan_praktiks,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'pembimbing_lapangan_id' => 'nullable|exists:pembimbing_lapangans,id',
            'pembimbing_dosen_id' => 'nullable|exists:pembimbing_dosens,id',
        ]);

        PenunjukanPembimbing::updateOrCreate(
            ['permohonan_id' => $this->permohonan_id, 'mahasiswa_id' => $this->mahasiswa_id],
            [
                'pembimbing_lapangan_id' => $this->pembimbing_lapangan_id,
                'pembimbing_dosen_id' => $this->pembimbing_dosen_id,
            ]
        );

        // Sinkronkan ke data mahasiswa
        $mhs = Mahasiswa::find($this->mahasiswa_id);
        if ($mhs) {
            $mhs->update([
                'id_pembimbing_lapangan' => $this->pembimbing_lapangan_id,
                'id_pembimbing_dosen' => $this->pembimbing_dosen_id,
            ]);
        }

        $this->showModal = false;
        session()->flash('message', 'Penunjukan pembimbing berhasil disimpan.');
    }

    public function deletePenunjukan(int $id): void
    {
        PenunjukanPembimbing::destroy($id);
        session()->flash('message', 'Penunjukan pembimbing berhasil dihapus.');
    }
};

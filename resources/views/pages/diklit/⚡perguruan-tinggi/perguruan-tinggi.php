<?php

use App\Models\PerguruanTinggi;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Perguruan Tinggi & Status MoU')] class extends Component {
    public string $search = '';
    public string $filterStatus = 'semua';

    // Form Modal
    public bool $showModal = false;
    public ?int $editingPtId = null;
    public string $nama_pt = '';
    public bool $status_mou = true;
    public string $tgl_mulai_mou = '';
    public string $tgl_akhir_mou = '';
    public string $kontak = '';
    public string $email_pt = '';

    #[Computed]
    public function perguruanTinggis()
    {
        return PerguruanTinggi::query()
            ->withCount(['mahasiswas', 'permohonanPraktiks'])
            ->when($this->filterStatus === 'aktif', fn ($q) => $q->where('status_mou', true)->where('tgl_akhir_mou', '>=', now()->toDateString()))
            ->when($this->filterStatus === 'expired', fn ($q) => $q->where(fn ($sub) => $sub->where('status_mou', false)->orWhere('tgl_akhir_mou', '<', now()->toDateString())))
            ->when($this->search !== '', function ($q) {
                $term = '%' . strtolower($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_pt) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(email_pt) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(kontak) LIKE ?', [$term]);
                });
            })
            ->orderBy('nama_pt')
            ->get();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingPtId', 'nama_pt', 'status_mou', 'tgl_mulai_mou', 'tgl_akhir_mou', 'kontak', 'email_pt']);
        $this->status_mou = true;
        $this->tgl_mulai_mou = now()->toDateString();
        $this->tgl_akhir_mou = now()->addYears(2)->toDateString();
        $this->showModal = true;
    }

    public function editPt(int $id): void
    {
        $pt = PerguruanTinggi::findOrFail($id);
        $this->editingPtId = $pt->id;
        $this->nama_pt = $pt->nama_pt;
        $this->status_mou = (bool) $pt->status_mou;
        $this->tgl_mulai_mou = $pt->tgl_mulai_mou->format('Y-m-d');
        $this->tgl_akhir_mou = $pt->tgl_akhir_mou->format('Y-m-d');
        $this->kontak = $pt->kontak ?? '';
        $this->email_pt = $pt->email_pt ?? '';
        $this->showModal = true;
    }

    public function savePt(): void
    {
        $this->validate([
            'nama_pt' => 'required|string|max:255',
            'status_mou' => 'required|boolean',
            'tgl_mulai_mou' => 'required|date',
            'tgl_akhir_mou' => 'required|date|after:tgl_mulai_mou',
            'kontak' => 'nullable|string|max:100',
            'email_pt' => 'nullable|email|max:150',
        ]);

        if ($this->editingPtId) {
            $pt = PerguruanTinggi::findOrFail($this->editingPtId);
            $pt->update([
                'nama_pt' => $this->nama_pt,
                'status_mou' => $this->status_mou,
                'tgl_mulai_mou' => $this->tgl_mulai_mou,
                'tgl_akhir_mou' => $this->tgl_akhir_mou,
                'kontak' => $this->kontak,
                'email_pt' => $this->email_pt,
            ]);
            session()->flash('message', 'Data Perguruan Tinggi berhasil diperbarui.');
        } else {
            PerguruanTinggi::create([
                'nama_pt' => $this->nama_pt,
                'status_mou' => $this->status_mou,
                'tgl_mulai_mou' => $this->tgl_mulai_mou,
                'tgl_akhir_mou' => $this->tgl_akhir_mou,
                'kontak' => $this->kontak,
                'email_pt' => $this->email_pt,
            ]);
            session()->flash('message', 'Perguruan Tinggi mitra baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleStatus(int $id): void
    {
        $pt = PerguruanTinggi::findOrFail($id);
        $pt->update(['status_mou' => ! $pt->status_mou]);
        session()->flash('message', 'Status MoU berhasil diperbarui.');
    }
};

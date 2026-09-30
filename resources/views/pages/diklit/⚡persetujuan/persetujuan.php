<?php

use App\Models\PermohonanPraktik;
use App\Models\SuratPersetujuan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Persetujuan Permohonan Praktik RS')] class extends Component
{
    public string $search = '';

    public string $filterStatus = 'diajukan'; // diajukan, disetujui, ditolak

    // Modal Review
    public bool $showReviewModal = false;

    public ?PermohonanPraktik $selectedPermohonan = null;

    public string $status_keputusan = 'disetujui';

    public string $nomor_surat = '';

    public string $catatan_diklat = '';

    #[Computed]
    public function permohonans()
    {
        return PermohonanPraktik::query()
            ->with(['perguruanTinggi', 'unit', 'suratPersetujuan'])
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(prodi) LIKE ?', [$term])
                        ->orWhereHas('perguruanTinggi', fn ($pt) => $pt->whereRaw('LOWER(nama_pt) LIKE ?', [$term]))
                        ->orWhereHas('unit', fn ($u) => $u->whereRaw('LOWER(nama_unit) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    public function openReviewModal(int $id): void
    {
        $this->selectedPermohonan = PermohonanPraktik::with(['perguruanTinggi', 'unit'])->findOrFail($id);
        $this->status_keputusan = 'disetujui';
        $this->nomor_surat = rand(100, 999).'/DIKLAT-RS/'.date('m/Y');
        $this->catatan_diklat = 'Disetujui. Mahasiswa wajib mematuhi SOP dan tata tertib keselamatan rumah sakit.';
        $this->showReviewModal = true;
    }

    public function submitKeputusan(): void
    {
        if (! $this->selectedPermohonan) {
            return;
        }

        $this->validate([
            'status_keputusan' => 'required|in:disetujui,ditolak',
            'nomor_surat' => 'required_if:status_keputusan,disetujui|nullable|string|max:100',
            'catatan_diklat' => 'nullable|string|max:500',
        ]);

        $this->selectedPermohonan->update([
            'status' => $this->status_keputusan,
            'catatan_diklat' => $this->catatan_diklat,
        ]);

        if ($this->status_keputusan === 'disetujui') {
            SuratPersetujuan::updateOrCreate(
                ['permohonan_id' => $this->selectedPermohonan->id],
                [
                    'nomor_surat' => $this->nomor_surat,
                    'diterbitkan_oleh' => auth()->id(),
                    'tgl_terbit' => now()->toDateString(),
                    'catatan' => $this->catatan_diklat,
                ]
            );
        }

        $this->showReviewModal = false;
        session()->flash('message', 'Keputusan permohonan praktik berhasil diproses dan dicatat.');
    }
};

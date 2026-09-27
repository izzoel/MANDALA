<?php

use App\Models\Sertifikat;
use App\Models\TargetPelatihan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Verifikasi Sertifikat Pelatihan')] class extends Component
{
    public string $search = '';

    public string $tab = 'pending'; // pending, selesai

    // Modal Verifikasi
    public bool $showVerifyModal = false;

    public ?Sertifikat $selectedSertifikat = null;

    public string $status_verifikasi = 'disetujui';

    public string $catatan_verifikator = '';

    #[Computed]
    public function pendingSertifikats()
    {
        return Sertifikat::query()
            ->with(['pegawai.user'])
            ->where('status_verifikasi', 'pending')
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_pelatihan) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(no_sertifikat) LIKE ?', [$term])
                        ->orWhereHas('pegawai', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function historySertifikats()
    {
        return Sertifikat::query()
            ->with(['pegawai.user', 'verifikator'])
            ->whereIn('status_verifikasi', ['disetujui', 'ditolak'])
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_pelatihan) LIKE ?', [$term])
                        ->orWhereHas('pegawai', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term]));
                });
            })
            ->latest('verified_at')
            ->get();
    }

    public function openVerifyModal(int $id): void
    {
        $this->selectedSertifikat = Sertifikat::with(['pegawai.user'])->findOrFail($id);
        $this->status_verifikasi = 'disetujui';
        $this->catatan_verifikator = '';
        $this->showVerifyModal = true;
    }

    public function submitVerifikasi(): void
    {
        if (! $this->selectedSertifikat) {
            return;
        }

        $this->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan_verifikator' => 'nullable|string|max:500',
        ]);

        $this->selectedSertifikat->update([
            'status_verifikasi' => $this->status_verifikasi,
            'catatan_verifikator' => $this->catatan_verifikator,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Jika disetujui, cek target pelatihan terkait
        if ($this->status_verifikasi === 'disetujui') {
            $target = TargetPelatihan::where('sertifikat_pemenuhan_id', $this->selectedSertifikat->id)->first();
            if ($target) {
                $target->update(['status' => 'selesai']);
            }
        }

        $this->showVerifyModal = false;
        session()->flash('message', 'Hasil verifikasi sertifikat berhasil disimpan.');
    }
};

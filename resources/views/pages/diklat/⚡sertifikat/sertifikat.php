<?php

use App\Models\PegawaiNonAsn;
use App\Models\Sertifikat;
use App\Models\TargetPelatihan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Sertifikat Pelatihan')] class extends Component {
    use WithFileUploads;

    public string $search = '';
    public string $filterStatus = 'semua';

    // Form Upload
    public bool $showUploadModal = false;
    public ?int $pegawai_id = null;
    public ?int $target_pelatihan_id = null;
    public string $nama_pelatihan = '';
    public string $penyelenggara = '';
    public string $tgl_pelaksanaan = '';
    public string $no_sertifikat = '';
    public $file_sertifikat;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && $user->isPegawaiNonAsn() && $user->pegawaiNonAsn) {
            $this->pegawai_id = $user->pegawaiNonAsn->id;
        }
    }

    #[Computed]
    public function sertifikats()
    {
        $user = auth()->user();

        return Sertifikat::query()
            ->with(['pegawai.user', 'verifikator'])
            ->when($user->isPegawaiNonAsn() && $user->pegawaiNonAsn, function ($q) use ($user) {
                $q->where('pegawai_id', $user->pegawaiNonAsn->id);
            })
            ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status_verifikasi', $this->filterStatus))
            ->when($this->search !== '', function ($q) {
                $term = '%' . strtolower($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(nama_pelatihan) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(no_sertifikat) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(penyelenggara) LIKE ?', [$term])
                        ->orWhereHas('pegawai', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function listPegawai()
    {
        return PegawaiNonAsn::orderBy('nama')->get();
    }

    #[Computed]
    public function openTargets()
    {
        if (! $this->pegawai_id) {
            return collect();
        }

        return TargetPelatihan::where('pegawai_id', $this->pegawai_id)
            ->where('status', '!=', 'selesai')
            ->get();
    }

    public function openModal(): void
    {
        $this->reset(['nama_pelatihan', 'penyelenggara', 'tgl_pelaksanaan', 'no_sertifikat', 'file_sertifikat', 'target_pelatihan_id']);
        $this->tgl_pelaksanaan = now()->toDateString();
        
        $user = auth()->user();
        if ($user && $user->isPegawaiNonAsn() && $user->pegawaiNonAsn) {
            $this->pegawai_id = $user->pegawaiNonAsn->id;
        }
        
        $this->showUploadModal = true;
    }

    public function saveSertifikat(): void
    {
        $this->validate([
            'pegawai_id' => 'required|exists:pegawai_non_asns,id',
            'nama_pelatihan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tgl_pelaksanaan' => 'required|date',
            'no_sertifikat' => 'required|string|max:100',
            'file_sertifikat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // max 5MB sesuai spec
        ]);

        $filePath = $this->file_sertifikat->store('sertifikat', 'public');

        $sertifikat = Sertifikat::create([
            'pegawai_id' => $this->pegawai_id,
            'nama_pelatihan' => $this->nama_pelatihan,
            'penyelenggara' => $this->penyelenggara,
            'tgl_pelaksanaan' => $this->tgl_pelaksanaan,
            'no_sertifikat' => $this->no_sertifikat,
            'file_path' => $filePath,
            'status_verifikasi' => 'pending',
        ]);

        if ($this->target_pelatihan_id) {
            $target = TargetPelatihan::find($this->target_pelatihan_id);
            if ($target) {
                $target->update([
                    'status' => 'proses',
                    'sertifikat_pemenuhan_id' => $sertifikat->id,
                ]);
            }
        }

        $this->showUploadModal = false;
        session()->flash('message', 'Sertifikat berhasil diunggah dan sedang dalam antrean verifikasi Admin Diklat.');
    }
};

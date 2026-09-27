<?php

// use App\Models\PegawaiNonAsn;
use App\Models\Peserta;
use App\Models\Sertifikat;
use App\Models\TargetPelatihan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Sertifikat Pelatihan')] class extends Component
{
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

    // Modal Verifikasi
    public bool $showVerifyModal = false;
    public ?Sertifikat $selectedSertifikat = null;
    public string $status_verifikasi = 'disetujui';
    public string $catatan_verifikator = '';

    public function mount(): void
    {
        $user = auth()->user();
        // if ($user && ! $user->can('lihat-seluruh-sertifikat') && $user->pegawaiNonAsn) {
        //     $this->pegawai_id = $user->pegawaiNonAsn->id;
        // }
        if ($user && ! $user->can('lihat-seluruh-sertifikat') && $user->peserta) {
            $this->pegawai_id = $user->peserta->id;
        }
    }

    // #[Computed]
    // public function sertifikats()
    // {
    //     $user = auth()->user();

    //     // dd(Sertifikat::where('pegawai_id', $this->pegawai_id)->get());

    //     return Sertifikat::query()
    //         ->with(['pegawai.user', 'verifikator'])
    //         ->when(! $user->can('lihat-seluruh-sertifikat'), function ($q) use ($user) {
    //             // if ($user->can('lihat-sertifikat-sendiri') && $user->pegawaiNonAsn) {
    //             //     $q->where('pegawai_id', $user->pegawaiNonAsn->id);
    //             if ($user->can('lihat-sertifikat-sendiri') && $user->peserta) {
    //                 $q->where('pegawai_id', $user->peserta->id);
    //             } else {
    //                 $q->whereNull('id'); // Sembunyikan jika tidak punya akses keduanya
    //             }
    //         })
    //         ->when($this->filterStatus !== 'semua', fn ($q) => $q->where('status_verifikasi', $this->filterStatus))
    //         ->when($this->search !== '', function ($q) {
    //             $term = '%'.strtolower($this->search).'%';
    //             $q->where(function ($sub) use ($term) {
    //                 $sub->whereRaw('LOWER(nama_pelatihan) LIKE ?', [$term])
    //                     ->orWhereRaw('LOWER(no_sertifikat) LIKE ?', [$term])
    //                     ->orWhereRaw('LOWER(penyelenggara) LIKE ?', [$term])
    //                     ->orWhereHas('pegawai', fn ($p) => $p->whereRaw('LOWER(nama) LIKE ?', [$term]));
    //             });
    //         })
    //         ->latest()
    //         ->get();
    // }

    #[Computed]
    public function sertifikats()
    {
        $user = auth()->user();

        if ($user && ! $user->can('lihat-seluruh-sertifikat') && $user->peserta) {
            return Sertifikat::where('pegawai_id', $user->peserta->id)->get();
        }

        return Sertifikat::all();
    }

    #[Computed]
    public function listPegawai()
    {
        // return PegawaiNonAsn::orderBy('nama')->get();
        return Peserta::orderBy('nama')->get();
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
        $this->reset(['pegawai_id', 'nama_pelatihan', 'penyelenggara', 'tgl_pelaksanaan', 'no_sertifikat', 'file_sertifikat', 'target_pelatihan_id']);
        $this->tgl_pelaksanaan = now()->toDateString();

        $user = auth()->user();
        // if ($user && ! $user->can('lihat-seluruh-sertifikat') && $user->pegawaiNonAsn) {
        //     $this->pegawai_id = $user->pegawaiNonAsn->id;
        // }
        if ($user && ! $user->can('lihat-seluruh-sertifikat') && $user->peserta) {
            $this->pegawai_id = $user->peserta->id;
        }

        $this->showUploadModal = true;
    }

    public function saveSertifikat(): void
    {
        $this->validate([
            'pegawai_id' => 'required|exists:pesertas,id',
            'nama_pelatihan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tgl_pelaksanaan' => 'required|date',
            'no_sertifikat' => 'required|string|max:100',
            'file_sertifikat' => 'required|file|mimes:pdf|max:5120',
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

        $this->reset([
            'nama_pelatihan',
            'penyelenggara',
            'tgl_pelaksanaan',
            'no_sertifikat',
            'file_sertifikat',
            'target_pelatihan_id',
        ]);

        $this->showUploadModal = false;
        session()->flash('message', 'Sertifikat berhasil diunggah dan sedang dalam antrean verifikasi Admin Diklat.');
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

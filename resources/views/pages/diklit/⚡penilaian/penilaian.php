<?php

use App\Models\DetailPenilaian;
use App\Models\KriteriaPenilaian;
use App\Models\Mahasiswa;
use App\Models\PenilaianPraktik;
use App\Models\PermohonanPraktik;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Penilaian Praktik Mahasiswa')] class extends Component {
    public string $search = '';

    // Form Modal Input Nilai
    public bool $showNilaiModal = false;
    public ?int $mahasiswa_id = null;
    public ?int $permohonan_id = null;
    public string $peran_penilai = 'lapangan'; // lapangan, dosen
    public string $tgl_isi = '';
    public string $catatan_umum = '';

    // Nilai Dinamis per Kriteria [kriteria_id => nilai] & Catatan [kriteria_id => catatan]
    public array $nilaiKriteria = [];
    public array $catatanKriteria = [];

    public function mount(): void
    {
        $this->tgl_isi = now()->toDateString();
    }

    #[Computed]
    public function penilaians()
    {
        return PenilaianPraktik::query()
            ->with(['mahasiswa.perguruanTinggi', 'penilai', 'detailPenilaians.kriteria', 'permohonan.unit'])
            ->when($this->search !== '', function ($q) {
                $term = '%' . strtolower($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('mahasiswa', fn ($m) => $m->whereRaw('LOWER(nama) LIKE ?', [$term])->orWhereRaw('LOWER(nim) LIKE ?', [$term]))
                        ->orWhereHas('penilai', fn ($p) => $p->whereRaw('LOWER(name) LIKE ?', [$term]));
                });
            })
            ->latest()
            ->get();
    }

    #[Computed]
    public function kriteriaList()
    {
        return KriteriaPenilaian::where('aktif', true)->orderBy('urutan')->get();
    }

    #[Computed]
    public function listMahasiswa()
    {
        return Mahasiswa::with(['perguruanTinggi'])->orderBy('nama')->get();
    }

    #[Computed]
    public function listPermohonan()
    {
        return PermohonanPraktik::with(['perguruanTinggi', 'unit'])->where('status', 'disetujui')->latest()->get();
    }

    public function openInputModal(): void
    {
        $this->reset(['mahasiswa_id', 'permohonan_id', 'catatan_umum', 'nilaiKriteria', 'catatanKriteria']);
        $this->tgl_isi = now()->toDateString();
        $this->peran_penilai = auth()->user()->role === 'pembimbing_dosen' ? 'dosen' : 'lapangan';

        // Inisialisasi nilai awal
        foreach ($this->kriteriaList as $k) {
            $this->nilaiKriteria[$k->id] = 80;
            $this->catatanKriteria[$k->id] = '';
        }

        $this->showNilaiModal = true;
    }

    public function savePenilaian(): void
    {
        $this->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'peran_penilai' => 'required|in:lapangan,dosen',
            'tgl_isi' => 'required|date',
            'catatan_umum' => 'nullable|string|max:500',
            'nilaiKriteria.*' => 'required|numeric|min:0|max:100',
        ]);

        // Hitung nilai akhir berbobot
        $totalNilaiBerbobot = 0;
        $totalBobot = 0;

        foreach ($this->kriteriaList as $k) {
            $nilai = (float) ($this->nilaiKriteria[$k->id] ?? 0);
            $bobot = (float) $k->bobot;
            $totalNilaiBerbobot += ($nilai * $bobot);
            $totalBobot += $bobot;
        }

        $nilaiAkhir = $totalBobot > 0 ? ($totalNilaiBerbobot / $totalBobot) : 0;

        $penilaian = PenilaianPraktik::create([
            'mahasiswa_id' => $this->mahasiswa_id,
            'permohonan_id' => $this->permohonan_id,
            'penilai_id' => auth()->id(),
            'peran_penilai' => $this->peran_penilai,
            'tgl_isi' => $this->tgl_isi,
            'nilai_akhir' => round($nilaiAkhir, 2),
            'catatan_umum' => $this->catatan_umum,
        ]);

        foreach ($this->kriteriaList as $k) {
            DetailPenilaian::create([
                'penilaian_id' => $penilaian->id,
                'kriteria_id' => $k->id,
                'nilai' => $this->nilaiKriteria[$k->id] ?? 0,
                'catatan' => $this->catatanKriteria[$k->id] ?? null,
            ]);
        }

        $this->showNilaiModal = false;
        session()->flash('message', 'Penilaian praktik mahasiswa berhasil disimpan (Nilai Akhir: ' . round($nilaiAkhir, 2) . ').');
    }
};

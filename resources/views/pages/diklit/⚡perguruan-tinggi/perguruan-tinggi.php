<?php

use App\Models\PerguruanTinggi;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

new #[Title('Perguruan Tinggi & Status MoU')] class extends Component
{
    use WithFileUploads;

    public string $search = '';

    public string $filterStatus = 'semua';

    // Form Modal
    public bool $showModal = false;

    public ?int $editingPtId = null;

    public string $nama_pt = '';

    public string $status_mou = '1';

    public string $tgl_mulai_mou = '';

    public string $tgl_akhir_mou = '';

    public string $kontak = '';

    public string $email_pt = '';

    public $file_mou;

    #[Computed]
    public function perguruanTinggis()
    {
        return PerguruanTinggi::query()
            ->with(['adminUser'])
            ->withCount(['mahasiswas', 'permohonanPraktiks'])
            ->when($this->filterStatus === 'aktif', fn ($q) => $q->where('status_mou', true)->where('tgl_akhir_mou', '>=', now()->toDateString()))
            ->when($this->filterStatus === 'expired', fn ($q) => $q->where(fn ($sub) => $sub->where('status_mou', false)->orWhere('tgl_akhir_mou', '<', now()->toDateString())))
            ->when($this->search !== '', function ($q) {
                $term = '%'.strtolower($this->search).'%';
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
        $this->reset(['editingPtId', 'nama_pt', 'status_mou', 'tgl_mulai_mou', 'tgl_akhir_mou', 'kontak', 'email_pt', 'file_mou']);
        $this->status_mou = '1';
        $this->tgl_mulai_mou = now()->toDateString();
        $this->tgl_akhir_mou = now()->addYears(2)->toDateString();
        $this->showModal = true;
    }

    public function editPt(int $id): void
    {
        $pt = PerguruanTinggi::findOrFail($id);
        $this->editingPtId = $pt->id;
        $this->nama_pt = $pt->nama_pt;
        $this->status_mou = $pt->status_mou ? '1' : '0';
        $this->tgl_mulai_mou = $pt->tgl_mulai_mou ? $pt->tgl_mulai_mou->format('Y-m-d') : '';
        $this->tgl_akhir_mou = $pt->tgl_akhir_mou ? $pt->tgl_akhir_mou->format('Y-m-d') : '';
        $this->kontak = $pt->kontak ?? '';
        $this->email_pt = $pt->email_pt ?? '';
        $this->file_mou = null;
        $this->showModal = true;
    }

    public function savePt(): void
    {
        $this->validate([
            'nama_pt' => 'required|string|max:255',
            'status_mou' => 'required|in:0,1',
            'tgl_mulai_mou' => 'required|date',
            'tgl_akhir_mou' => 'required|date|after:tgl_mulai_mou',
            'kontak' => 'nullable|string|max:100',
            'email_pt' => 'nullable|email|max:150',
            'file_mou' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'file_mou.mimes' => 'Berkas MoU harus berformat PDF (.pdf).',
            'file_mou.max' => 'Ukuran berkas MoU maksimal 5MB.',
        ]);

        $statusBool = $this->status_mou === '1';

        $driveData = [];
        if ($this->file_mou) {
            $uploadResult = app(GoogleDriveService::class)->uploadWithFallback($this->file_mou, 'mou');
            $driveData = [
                'file_mou' => $uploadResult['file_path'],
                'drive_file_id' => $uploadResult['drive_file_id'],
                'drive_link' => $uploadResult['drive_link'],
            ];
        }

        if ($this->editingPtId) {
            $pt = PerguruanTinggi::findOrFail($this->editingPtId);
            $pt->update(array_merge([
                'nama_pt' => $this->nama_pt,
                'status_mou' => $statusBool,
                'tgl_mulai_mou' => $this->tgl_mulai_mou,
                'tgl_akhir_mou' => $this->tgl_akhir_mou,
                'kontak' => $this->kontak,
                'email_pt' => $this->email_pt,
            ], $driveData));
            session()->flash('message', 'Data Perguruan Tinggi & Berkas MoU berhasil diperbarui.');
        } else {
            // Generate email [nama PT]@mandala.test
            $emailSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->nama_pt));
            if (empty($emailSlug)) {
                $emailSlug = 'adminpt'.rand(100, 999);
            }
            $adminEmail = $emailSlug.'@mandala.test';

            // Pastikan email unik jika nama PT sama
            $counter = 1;
            while (User::where('email', $adminEmail)->exists()) {
                $adminEmail = $emailSlug.$counter.'@mandala.test';
                $counter++;
            }

            // Buat Akun Admin PT
            $adminUser = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Admin '.$this->nama_pt,
                'email' => $adminEmail,
                'password' => Hash::make('password'),
                'role' => 'admin_pt',
                'status' => 'aktif',
            ]);

            $roleAdminPt = Role::firstOrCreate(['name' => 'admin_pt', 'guard_name' => 'web']);
            $adminUser->assignRole($roleAdminPt);

            // Buat Data PT dan tautkan user_id
            $pt = PerguruanTinggi::create(array_merge([
                'user_id' => $adminUser->id,
                'nama_pt' => $this->nama_pt,
                'status_mou' => $statusBool,
                'tgl_mulai_mou' => $this->tgl_mulai_mou,
                'tgl_akhir_mou' => $this->tgl_akhir_mou,
                'kontak' => $this->kontak,
                'email_pt' => $this->email_pt ?: $adminEmail,
            ], $driveData));

            session()->flash('message', "Perguruan Tinggi baru dan akun Admin PT ({$adminEmail}) dengan password default 'password' berhasil dibuat.");
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

<?php

use App\Models\Aplikasi;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component
{
    public string $search = '';


    public ?int $editingIntegrasiId = null;

    #[Computed]
    public function integrasis()
    {
        return Aplikasi::query()
        ->when($this->search !== '', function ($query) {
            $search = '%' . str($this->search)->lower()->toString() . '%';

            $query->where(function ($query) use ($search) {
                $query
                ->whereRaw('LOWER(nama) LIKE ?', [$search]);
            });
        })
        ->orderBy('nama')
        ->get();
    }
};

<?php

use App\Models\Aplikasi;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public ?int $editingIntegrasiId = null;

    #[Computed]
    public function integrasis()
    {
        return Aplikasi::query()
            ->when($this->search !== '', function ($query) {
                $search = '%'.str($this->search)->lower()->toString().'%';

                $query->where(function ($query) use ($search) {
                    $query
                        ->whereRaw('LOWER(nama) LIKE ?', [$search]);
                });
            })
            ->orderBy('nama')
            ->get();
    }
};

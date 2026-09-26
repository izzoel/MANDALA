<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoriumProfilResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'fakultas' => $this->fakultas,
            'gedung' => $this->gedung,
            'deskripsi' => $this->deskripsi,
            'gambar' => $this->gambar
            ? asset('storage/' . $this->gambar)
            : null,
            'laboran' => $this->laboran,
            'warna' => $this->warna,
            'created_at' => $this->created_at?->format('d-m-Y H:i'),
        ];
    }
}

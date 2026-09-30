<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPersetujuan extends Model
{
    use HasFactory;

    protected $table = 'surat_persetujuans';

    protected $fillable = [
        'permohonan_id',
        'nomor_surat',
        'file_pdf',
        'drive_file_id',
        'drive_link',
        'diterbitkan_oleh',
        'tgl_terbit',
        'catatan',
    ];

    protected $casts = [
        'tgl_terbit' => 'date',
    ];

    public function getFileUrlAttribute(): ?string
    {
        if (! empty($this->drive_link)) {
            return $this->drive_link;
        }

        if (! empty($this->drive_file_id)) {
            return route('document.view', ['type' => 'surat_persetujuan', 'id' => $this->id]);
        }

        if (! empty($this->file_pdf)) {
            return str_starts_with($this->file_pdf, 'http') ? $this->file_pdf : asset('storage/'.$this->file_pdf);
        }

        return null;
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPraktik::class, 'permohonan_id');
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    public function diterbitkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}

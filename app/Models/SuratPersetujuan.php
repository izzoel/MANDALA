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
        'diterbitkan_oleh',
        'tgl_terbit',
        'catatan',
    ];

    protected $casts = [
        'tgl_terbit' => 'date',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPraktik::class, 'permohonan_id');
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}

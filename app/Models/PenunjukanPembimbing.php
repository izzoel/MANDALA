<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenunjukanPembimbing extends Model
{
    use HasFactory;

    protected $table = 'penunjukan_pembimbings';

    protected $fillable = [
        'permohonan_id',
        'mahasiswa_id',
        'pembimbing_lapangan_id',
        'pembimbing_dosen_id',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPraktik::class, 'permohonan_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function pembimbingLapangan(): BelongsTo
    {
        return $this->belongsTo(PembimbingLapangan::class, 'pembimbing_lapangan_id');
    }

    public function pembimbingDosen(): BelongsTo
    {
        return $this->belongsTo(PembimbingDosen::class, 'pembimbing_dosen_id');
    }
}

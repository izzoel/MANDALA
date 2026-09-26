<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenilaian extends Model
{
    use HasFactory;

    protected $table = 'detail_penilaians';

    protected $fillable = [
        'penilaian_id',
        'kriteria_id',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(PenilaianPraktik::class, 'penilaian_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(KriteriaPenilaian::class, 'kriteria_id');
    }
}

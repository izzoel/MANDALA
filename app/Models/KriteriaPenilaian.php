<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KriteriaPenilaian extends Model
{
    use HasFactory;

    protected $table = 'kriteria_penilaians';

    protected $fillable = [
        'nama_kriteria',
        'bobot',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    public function detailPenilaians(): HasMany
    {
        return $this->hasMany(DetailPenilaian::class, 'kriteria_id');
    }
}

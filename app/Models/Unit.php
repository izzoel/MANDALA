<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'nama_unit',
        'mode_kuota',
        'kuota_maks',
        'deskripsi',
    ];

    public function prodiKuotas(): HasMany
    {
        return $this->hasMany(UnitProdiKuota::class, 'unit_id');
    }

    public function permohonanPraktiks(): HasMany
    {
        return $this->hasMany(PermohonanPraktik::class, 'unit_id');
    }

    public function pembimbingLapangans(): HasMany
    {
        return $this->hasMany(PembimbingLapangan::class, 'unit_id');
    }
}

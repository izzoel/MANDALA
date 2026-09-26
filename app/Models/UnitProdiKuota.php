<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitProdiKuota extends Model
{
    use HasFactory;

    protected $table = 'unit_prodi_kuotas';

    protected $fillable = [
        'unit_id',
        'prodi',
        'kuota_maks',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

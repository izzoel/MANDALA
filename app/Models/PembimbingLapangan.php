<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembimbingLapangan extends Model
{
    use HasFactory;

    protected $table = 'pembimbing_lapangans';

    protected $fillable = [
        'user_id',
        'nama',
        'unit_id',
        'kontak',
        'nip_nik',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function penunjukanPembimbings(): HasMany
    {
        return $this->hasMany(PenunjukanPembimbing::class, 'pembimbing_lapangan_id');
    }
}

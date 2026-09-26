<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembimbingDosen extends Model
{
    use HasFactory;

    protected $table = 'pembimbing_dosens';

    protected $fillable = [
        'user_id',
        'pt_id',
        'nama',
        'kontak',
        'nidn',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function perguruanTinggi(): BelongsTo
    {
        return $this->belongsTo(PerguruanTinggi::class, 'pt_id');
    }

    public function penunjukanPembimbings(): HasMany
    {
        return $this->hasMany(PenunjukanPembimbing::class, 'pembimbing_dosen_id');
    }
}

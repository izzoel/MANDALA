<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'pt_id',
        'nama',
        'nim',
        'prodi',
        'id_pembimbing_lapangan',
        'id_pembimbing_dosen',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function perguruanTinggi(): BelongsTo
    {
        return $this->belongsTo(PerguruanTinggi::class, 'pt_id');
    }

    public function pembimbingLapangan(): BelongsTo
    {
        return $this->belongsTo(PembimbingLapangan::class, 'id_pembimbing_lapangan');
    }

    public function pembimbingDosen(): BelongsTo
    {
        return $this->belongsTo(PembimbingDosen::class, 'id_pembimbing_dosen');
    }

    public function penunjukanPembimbings(): HasMany
    {
        return $this->hasMany(PenunjukanPembimbing::class, 'mahasiswa_id');
    }

    public function penilaianPraktiks(): HasMany
    {
        return $this->hasMany(PenilaianPraktik::class, 'mahasiswa_id');
    }
}

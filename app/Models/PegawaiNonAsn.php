<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PegawaiNonAsn extends Model
{
    use HasFactory;

    protected $table = 'pegawai_non_asns';

    protected $fillable = [
        'user_id',
        'nama',
        'no_pegawai',
        'unit_kerja',
        'jabatan',
        'tgl_mulai_kerja',
    ];

    protected $casts = [
        'tgl_mulai_kerja' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sertifikats(): HasMany
    {
        return $this->hasMany(Sertifikat::class, 'pegawai_id');
    }

    public function targetPelatihans(): HasMany
    {
        return $this->hasMany(TargetPelatihan::class, 'pegawai_id');
    }
}

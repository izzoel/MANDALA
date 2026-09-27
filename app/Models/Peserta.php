<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'uuid',
    'user_id',
    'nama',
    'no_pegawai',
    'unit_kerja',
    'jabatan',
    'tgl_mulai_kerja',
])]
class Peserta extends Model
{
    protected $casts = [
        'tgl_mulai_kerja' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

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

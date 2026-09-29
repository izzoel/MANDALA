<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TargetPelatihan extends Model
{
    use HasFactory;

    protected $table = 'target_pelatihans';

    protected $fillable = [
        'pegawai_id',
        'nama_target',
        'kategori',
        'kategori_id',
        'periode',
        'tenggat',
        'status',
        'sertifikat_pemenuhan_id',
        'created_by',
    ];

    protected $casts = [
        'tenggat' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'pegawai_id');
    }

    public function pesertas(): BelongsToMany
    {
        return $this->belongsToMany(Peserta::class, 'target_pelatihan_peserta', 'target_pelatihan_id', 'peserta_id')
            ->withPivot(['status', 'sertifikat_id'])
            ->withTimestamps();
    }

    public function sertifikatPemenuhan(): BelongsTo
    {
        return $this->belongsTo(Sertifikat::class, 'sertifikat_pemenuhan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kategoriPelatihan(): BelongsTo
    {
        return $this->belongsTo(KategoriPelatihan::class, 'kategori_id');
    }

    public function getPesertaTerdaftarCountAttribute(): int
    {
        $pivotCount = $this->pesertas()->count();
        if ($pivotCount > 0) {
            return $pivotCount;
        }

        if ($this->pegawai_id) {
            return 1;
        }

        $certCount = Sertifikat::where('nama_pelatihan', $this->nama_target)
            ->distinct('pegawai_id')
            ->count('pegawai_id');

        return $certCount > 0 ? $certCount : 0;
    }
}

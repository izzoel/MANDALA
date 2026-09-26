<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenilaianPraktik extends Model
{
    use HasFactory;

    protected $table = 'penilaian_praktiks';

    protected $fillable = [
        'mahasiswa_id',
        'permohonan_id',
        'penilai_id',
        'peran_penilai',
        'tgl_isi',
        'nilai_akhir',
        'catatan_umum',
    ];

    protected $casts = [
        'tgl_isi' => 'date',
        'nilai_akhir' => 'decimal:2',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanPraktik::class, 'permohonan_id');
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function detailPenilaians(): HasMany
    {
        return $this->hasMany(DetailPenilaian::class, 'penilaian_id');
    }
}

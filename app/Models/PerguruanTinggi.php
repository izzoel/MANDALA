<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerguruanTinggi extends Model
{
    use HasFactory;

    protected $table = 'perguruan_tinggis';

    protected $fillable = [
        'nama_pt',
        'status_mou',
        'tgl_mulai_mou',
        'tgl_akhir_mou',
        'kontak',
        'email_pt',
    ];

    protected $casts = [
        'status_mou' => 'boolean',
        'tgl_mulai_mou' => 'date',
        'tgl_akhir_mou' => 'date',
    ];

    public function isMouValid(): bool
    {
        return $this->status_mou && $this->tgl_akhir_mou >= now()->toDateString();
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'pt_id');
    }

    public function pembimbingDosens(): HasMany
    {
        return $this->hasMany(PembimbingDosen::class, 'pt_id');
    }

    public function permohonanPraktiks(): HasMany
    {
        return $this->hasMany(PermohonanPraktik::class, 'pt_id');
    }
}

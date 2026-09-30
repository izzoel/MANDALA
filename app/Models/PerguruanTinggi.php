<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerguruanTinggi extends Model
{
    use HasFactory;

    protected $table = 'perguruan_tinggis';

    protected $fillable = [
        'user_id',
        'nama_pt',
        'status_mou',
        'tgl_mulai_mou',
        'tgl_akhir_mou',
        'kontak',
        'email_pt',
        'file_mou',
        'drive_file_id',
        'drive_link',
    ];

    protected $casts = [
        'status_mou' => 'boolean',
        'tgl_mulai_mou' => 'date',
        'tgl_akhir_mou' => 'date',
    ];

    public function getMouUrlAttribute(): ?string
    {
        if (! empty($this->drive_link)) {
            return $this->drive_link;
        }

        if (! empty($this->drive_file_id)) {
            return route('document.view', ['type' => 'mou', 'id' => $this->id]);
        }

        if (! empty($this->file_mou)) {
            return str_starts_with($this->file_mou, 'http') ? $this->file_mou : asset('storage/'.$this->file_mou);
        }

        return null;
    }

    public function isMouValid(): bool
    {
        return $this->status_mou && $this->tgl_akhir_mou >= now()->toDateString();
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

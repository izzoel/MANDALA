<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PermohonanPraktik extends Model
{
    use HasFactory;

    protected $table = 'permohonan_praktiks';

    protected $fillable = [
        'pt_id',
        'unit_id',
        'prodi',
        'jumlah_mahasiswa',
        'tgl_mulai',
        'tgl_selesai',
        'file_surat_permohonan',
        'status',
        'catatan_diklat',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function perguruanTinggi(): BelongsTo
    {
        return $this->belongsTo(PerguruanTinggi::class, 'pt_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function suratPersetujuan(): HasOne
    {
        return $this->hasOne(SuratPersetujuan::class, 'permohonan_id');
    }

    public function penunjukanPembimbings(): HasMany
    {
        return $this->hasMany(PenunjukanPembimbing::class, 'permohonan_id');
    }
}

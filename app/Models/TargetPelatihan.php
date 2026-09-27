<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetPelatihan extends Model
{
    use HasFactory;

    protected $table = 'target_pelatihans';

    protected $fillable = [
        'pegawai_id',
        'nama_target',
        'kategori',
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
        // return $this->belongsTo(PegawaiNonAsn::class, 'pegawai_id');
        return $this->belongsTo(Peserta::class, 'pegawai_id');
    }

    public function sertifikatPemenuhan(): BelongsTo
    {
        return $this->belongsTo(Sertifikat::class, 'sertifikat_pemenuhan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

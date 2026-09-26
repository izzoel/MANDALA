<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikats';

    protected $fillable = [
        'pegawai_id',
        'nama_pelatihan',
        'penyelenggara',
        'tgl_pelaksanaan',
        'no_sertifikat',
        'file_path',
        'status_verifikasi',
        'catatan_verifikator',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tgl_pelaksanaan' => 'date',
        'verified_at' => 'datetime',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(PegawaiNonAsn::class, 'pegawai_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

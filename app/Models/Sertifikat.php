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
        'drive_file_id',
        'drive_link',
        'status_verifikasi',
        'catatan_verifikator',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tgl_pelaksanaan' => 'date',
        'verified_at' => 'datetime',
    ];

    public function getFileUrlAttribute(): string
    {
        if (! empty($this->drive_link)) {
            return $this->drive_link;
        }

        if (! empty($this->drive_file_id)) {
            return route('document.view', ['type' => 'sertifikat', 'id' => $this->id]);
        }

        if (! empty($this->file_path)) {
            return str_starts_with($this->file_path, 'http') ? $this->file_path : asset('storage/'.$this->file_path);
        }

        return '#';
    }

    public function pegawai(): BelongsTo
    {
        // return $this->belongsTo(PegawaiNonAsn::class, 'pegawai_id');
        return $this->belongsTo(Peserta::class, 'pegawai_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

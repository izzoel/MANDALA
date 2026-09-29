<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPelatihan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pelatihans';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function targetPelatihans(): HasMany
    {
        return $this->hasMany(TargetPelatihan::class, 'kategori_id');
    }
}

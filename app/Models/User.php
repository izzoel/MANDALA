<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function pegawaiNonAsn(): HasOne
    {
        return $this->hasOne(PegawaiNonAsn::class, 'user_id');
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    public function pembimbingLapangan(): HasOne
    {
        return $this->hasOne(PembimbingLapangan::class, 'user_id');
    }

    public function pembimbingDosen(): HasOne
    {
        return $this->hasOne(PembimbingDosen::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->hasRole('super_admin');
    }

    public function isAdminDiklat(): bool
    {
        return in_array($this->role, ['admin_diklat', 'super_admin'])
            || $this->hasAnyRole(['admin_diklat', 'super_admin']);
    }

    public function isAdminPt(): bool
    {
        return in_array($this->role, ['admin_pt', 'super_admin'])
            || $this->hasAnyRole(['admin_pt', 'super_admin']);
    }

    public function isPegawaiNonAsn(): bool
    {
        return $this->role === 'pegawai_non_asn' || $this->hasRole('pegawai_non_asn');
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa' || $this->hasRole('mahasiswa');
    }

    public function isPembimbing(): bool
    {
        return in_array($this->role, ['pembimbing_lapangan', 'pembimbing_dosen', 'super_admin'])
            || $this->hasAnyRole(['pembimbing_lapangan', 'pembimbing_dosen', 'super_admin']);
    }
}

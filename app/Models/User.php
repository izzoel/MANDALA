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

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

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

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles) || $this->role === 'super_admin';
        }

        return $this->role === $roles || $this->role === 'super_admin';
    }

    public function isAdminDiklat(): bool
    {
        return in_array($this->role, ['admin_diklat', 'super_admin']);
    }

    public function isAdminPt(): bool
    {
        return in_array($this->role, ['admin_pt', 'super_admin']);
    }

    public function isPegawaiNonAsn(): bool
    {
        return $this->role === 'pegawai_non_asn';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isPembimbing(): bool
    {
        return in_array($this->role, ['pembimbing_lapangan', 'pembimbing_dosen', 'super_admin']);
    }
}

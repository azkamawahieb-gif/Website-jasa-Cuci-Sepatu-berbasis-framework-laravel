<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'address', 'avatar', 'bio', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'role' => Role::class,
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Alamat utama tampil paling atas, lalu yang terbaru.
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('is_default')->orderByDesc('id');
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function isPetugas(): bool
    {
        return $this->role === Role::Petugas;
    }

    public function isPengguna(): bool
    {
        return $this->role === Role::Pengguna;
    }
}
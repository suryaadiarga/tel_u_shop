<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property float $wallet_balance
 * @property int|null $role_id
 * @property string|null $two_factor_secret
 * @property string|null $remember_token
 * @property-read \App\Models\Role|null $role
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    public const ROLE_ADMIN    = 'admin';
    public const ROLE_MERCHANT = 'merchant';
    public const ROLE_CUSTOMER = 'customer';

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'nim',
        'kelas',
        'phone',
        'avatar_url',
        'wallet_balance',
        'role_id',
        'student_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'wallet_balance' => 'float',
    ];

    // Relasi
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role?->name === self::ROLE_ADMIN;
    }

    public function isMerchant(): bool
    {
        return $this->role?->name === self::ROLE_MERCHANT;
    }

    public function isCustomer(): bool
    {
        return !$this->role || $this->role->name === self::ROLE_CUSTOMER;
    }

    // Mutator
    public function setPasswordAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}

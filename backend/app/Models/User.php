<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    /**
     * Role constant (opsional helper)
     */
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_MERCHANT = 'merchant';
    public const ROLE_CUSTOMER = 'customer';

    /**
     * Kolom yang boleh diisi mass assignment
     */
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
        'merchant_status',
        'is_banned',
        'banned_at',
    ];

    /**
     * Kolom yang disembunyikan dari response JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting kolom
     */
    protected $casts = [
        'password' => 'hashed',
        'wallet_balance' => 'float',
        'loyalty_points' => 'integer',
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->resolveRoleName() === self::ROLE_ADMIN;
    }

    public function isMerchant(): bool
    {
        return $this->resolveRoleName() === self::ROLE_MERCHANT;
    }

    public function isCustomer(): bool
    {
        return $this->resolveRoleName() === self::ROLE_CUSTOMER;
    }

    public function hasRole(string $role): bool
    {
        return $this->resolveRoleName() === $role;
    }

    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }

    public function isMerchantApproved(): bool
    {
        return $this->isMerchant() && $this->merchant_status === 'approved';
    }

    private function resolveRoleName(): ?string
    {
        if ($this->role?->name) {
            return $this->role->name;
        }

        if (!$this->role_id) {
            return null;
        }

        switch ((int) $this->role_id) {
            case 1:
                return self::ROLE_ADMIN;
            case 2:
                return self::ROLE_MERCHANT;
            case 3:
                return self::ROLE_CUSTOMER;
            default:
                return null;
        }
    }
}

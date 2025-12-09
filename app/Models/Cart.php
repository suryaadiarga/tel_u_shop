<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    /**
     * Relasi ke user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke item dalam cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Hitung total harga cart.
     */
    public function total(): int
    {
        // Gunakan collection sum agar lebih aman dan portable
        return (int) $this->items->sum(function ($item) {
            return $item->qty * $item->price_snapshot;
        });
    }
}
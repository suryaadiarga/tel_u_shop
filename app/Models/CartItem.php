<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
        'price_snapshot',
    ];

    /**
     * Relasi ke Cart.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relasi ke Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor subtotal per item.
     */
    public function getSubtotalAttribute(): int
    {
        return $this->qty * $this->price_snapshot;
    }
}
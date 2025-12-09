<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
        'description',
        'price',
        'stock',
        'image_url',
        'category',
        'prep_time',
        'is_available',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'is_available' => 'boolean',
        'prep_time' => 'integer',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getStockStatusAttribute(): string
    {
        return $this->stock > 0 ? 'Tersedia' : 'Habis';
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}

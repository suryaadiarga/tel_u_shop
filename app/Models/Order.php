<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status','total','placed-at'];
    protected $casts = ['placed_at' => 'datetime'];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
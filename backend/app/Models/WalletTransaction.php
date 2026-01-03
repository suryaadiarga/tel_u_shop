<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
  protected $fillable = [
    'user_id',
    'amount',
    'title',
    'order_id',
    'type',
    'description',
  ];

  protected $casts = [
    'amount' => 'float',
  ];

  /**
   * Relasi ke User.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Relasi ke Order (jika transaksi terkait order).
   */
  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class);
  }

  /**
   * Accessor untuk format nominal.
   */
  public function getFormattedAmountAttribute(): string
  {
    return 'Rp ' . number_format($this->amount, 0, ',', '.');
  }

  /**
   * Scope untuk filter transaksi berdasarkan user.
   */
  public function scopeForUser($query, int $userId)
  {
    return $query->where('user_id', $userId);
  }
}

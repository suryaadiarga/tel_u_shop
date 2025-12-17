<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $points
 * @property string $type
 * @property string|null $description
 * @property int|null $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'order_id',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    /**
     * Relasi: LoyaltyPoint milik User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: LoyaltyPoint terkait dengan Order (opsional).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope untuk poin earned.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope untuk poin redeemed.
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    /**
     * Scope untuk poin expired.
     */
    public function scopeExpired($query)
    {
        return $query->where('type', 'expired');
    }

    /**
     * Scope untuk user tertentu.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Hitung total poin user.
     */
    public static function getTotalPoints(int $userId): int
    {
        $earned = self::where('user_id', $userId)
            ->where('type', 'earned')
            ->sum('points');

        $redeemed = self::where('user_id', $userId)
            ->where('type', 'redeemed')
            ->sum('points');

        return $earned - $redeemed;
    }

    /**
     * Tambah poin earned.
     */
    public static function earnPoints(int $userId, int $points, string $description = null, int $orderId = null): self
    {
        return self::create([
            'user_id' => $userId,
            'points' => $points,
            'type' => 'earned',
            'description' => $description,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Kurangi poin redeemed.
     */
    public static function redeemPoints(int $userId, int $points, string $description = null): self
    {
        return self::create([
            'user_id' => $userId,
            'points' => $points,
            'type' => 'redeemed',
            'description' => $description,
        ]);
    }

    /**
     * Get point types.
     */
    public static function getTypes(): array
    {
        return [
            'earned' => 'Points Earned',
            'redeemed' => 'Points Redeemed',
            'expired' => 'Points Expired',
        ];
    }
}

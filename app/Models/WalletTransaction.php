<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['user_id', 'title', 'amount'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

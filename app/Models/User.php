<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'kelas',
        'phone',
        'avatar_url',
        'ewallet_balance',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}

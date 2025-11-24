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
        'username',
        'nim',
        'kelas',
        'avatar_url',
        'ewallet_balance',
    ];
    protected $hidden = ['password', 'remember_token'];

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
    ];

    /**
     * Relasi: Role memiliki banyak User.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Accessor label untuk API.
     */
    public function getLabelAttribute(): string
    {
        return $this->display_name ?? ucfirst($this->name);
    }

    /**
     * Scope untuk mencari role berdasarkan nama.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }
}
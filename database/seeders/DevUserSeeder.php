<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'suryaadi@student.telkomuniversity.ac.id'],
            [
                'name' => 'Surya Adi',
                'username' => 'suryaadi',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@koperasi.test'],
            [
                'name' => 'Admin Koperasi',
                'password' => Hash::make('password'),
                'role_id' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'merchant@koperasi.test'],
            [
                'name' => 'Merchant A',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@koperasi.test'],
            [
                'name' => 'Customer A',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ]
        );

        User::updateOrCreate(
            ['email' => 'suryaadiarga@student.telkomuniversity.ac.id'],
            [
                'name' => 'Surya Adiarga',
                'password' => Hash::make('12345'),
                'role_id' => 1,
            ]
        );
    }
}

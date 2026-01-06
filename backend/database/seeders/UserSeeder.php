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
            ['email' => 'surya@admin.test'],
            [
                'name' => 'Surya Admin',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'rahmadrafi@admin.test'],
            [
                'name' => 'Rahmad Rafi Admin',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'galang@merchant.test'],
            [
                'name' => 'Galang Merchant',
                'password' => Hash::make('12345678'),
                'role_id' => 2,
                'merchant_status' => 'approved',
                'is_banned' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'faruq@merchant.test'],
            [
                'name' => 'Faruq Merchant',
                'password' => Hash::make('12345678'),
                'role_id' => 2,
                'merchant_status' => 'approved',
                'is_banned' => false,
            ]
        );


        User::updateOrCreate(
            ['email' => 'rahmadrafi@customer.test'],
            [
                'name' => 'Rahmad Rafi Customer',
                'password' => Hash::make('12345678'),
                'role_id' => 3,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mutiara_nl@customer.test'],
            [
                'name' => 'Mutiara NL Customer',
                'password' => Hash::make('12345678'),
                'role_id' => 3,
                'wallet_balance' => 2000000,
            ]
        );

        User::updateOrCreate(
            ['email' => 'faiqotul_faridah@customer.test'],
            [
                'name' => 'Faiqotul Faridah Customer',
                'password' => Hash::make('12345678'),
                'role_id' => 3,
                'wallet_balance' => 300000,
            ]
        );
    }
}

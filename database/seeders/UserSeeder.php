<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::factory()->create([
            'name' => 'Admin Koperasi',
            'email' => 'admin@koperasi.test',
            'role_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Merchant A',
            'email' => 'merchant@koperasi.test',
            'role_id' => 2,
        ]);

        User::factory()->create([
            'name' => 'Customer A',
            'email' => 'customer@koperasi.test',
            'role_id' => 3,
        ]);
    }
}

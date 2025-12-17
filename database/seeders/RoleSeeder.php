<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'admin', 'display_name' => 'Administrator'],
            ['id' => 2, 'name' => 'merchant', 'display_name' => 'Merchant'],
            ['id' => 3, 'name' => 'customer', 'display_name' => 'Customer'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                $role
            );
        }
    }
}

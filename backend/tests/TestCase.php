<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function seedRoles()
    {
        if (Role::query()->exists()) {
            return;
        }

        $this->seed(RoleSeeder::class);
    }

    protected function createUser(string $roleName = User::ROLE_CUSTOMER, array $overrides = []): User
    {
        $this->seedRoles();

        $role = Role::query()->where('name', $roleName)->firstOrFail();

        $defaults = [
            'role_id' => $role->id,
            'is_banned' => false,
        ];

        if ($roleName === User::ROLE_MERCHANT && !array_key_exists('merchant_status', $overrides)) {
            $defaults['merchant_status'] = 'approved';
        }

        return User::factory()->create(array_merge($defaults, $overrides));
    }

    protected function approveMerchant(User $user): User
    {
        $user->update(['merchant_status' => 'approved']);

        return $user;
    }

    protected function authAs(User $user): void
    {
        Sanctum::actingAs($user, ['*']);
    }
}

<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testAdminIndexPositive(): void
    {
        $admin = $this->createUser(User::ROLE_ADMIN);

        $this->authAs($admin);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame($admin->id, $response->json('data.data.0.id'));
    }

    public function testAdminIndexNegative(): void
    {
        $customer = $this->createUser(User::ROLE_CUSTOMER);

        $this->authAs($customer);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(403)->assertJson([
            'success' => false,
        ]);
    }
}

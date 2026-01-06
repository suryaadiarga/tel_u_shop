<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginPositive(): void
    {
        $this->seedRoles();

        $user = $this->createUser(User::ROLE_CUSTOMER, ['password' => 'secret']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame($user->id, $response->json('data.user.id'));
        $this->assertNotEmpty($response->json('data.access_token'));
    }

    public function testLoginNegative(): void
    {
        $this->seedRoles();

        $user = $this->createUser(User::ROLE_CUSTOMER, ['password' => 'secret']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)->assertJson([
            'success' => false,
        ]);
    }
}

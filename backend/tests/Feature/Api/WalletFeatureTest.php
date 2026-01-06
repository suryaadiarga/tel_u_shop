<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testTopupPositive(): void
    {
        $customer = $this->createUser(User::ROLE_CUSTOMER);

        $this->authAs($customer);

        $response = $this->postJson('/api/wallet/topup', [
            'amount' => 2000,
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame(2000.0, $customer->fresh()->wallet_balance);
    }

    public function testTopupNegative(): void
    {
        $customer = $this->createUser(User::ROLE_CUSTOMER);

        $this->authAs($customer);

        $response = $this->postJson('/api/wallet/topup', [
            'amount' => 500,
        ]);

        $response->assertStatus(422)->assertJson([
            'success' => false,
        ]);
    }
}

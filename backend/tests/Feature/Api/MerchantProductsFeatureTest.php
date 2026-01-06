<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantProductsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateProductPositive(): void
    {
        $merchant = $this->createUser(User::ROLE_MERCHANT, ['merchant_status' => 'approved']);

        $this->authAs($merchant);

        $response = $this->postJson('/api/merchant/products', [
            'name' => 'Kopi Susu',
            'description' => 'Minuman kopi susu hangat.',
            'price' => 15000,
            'stock' => 10,
            'category' => 'Makanan & Minuman',
            'prep_time' => 5,
            'is_available' => true,
        ]);

        $response->assertStatus(201)->assertJson([
            'success' => true,
        ]);

        $this->assertNotEmpty($response->json('data.id'));
    }

    public function testCreateProductNegative(): void
    {
        $merchant = $this->createUser(User::ROLE_MERCHANT, ['merchant_status' => 'pending']);

        $this->authAs($merchant);

        $response = $this->postJson('/api/merchant/products', [
            'name' => 'Kopi Susu',
            'description' => 'Minuman kopi susu hangat.',
            'price' => 15000,
            'stock' => 10,
            'category' => 'Makanan & Minuman',
            'prep_time' => 5,
            'is_available' => true,
        ]);

        $response->assertStatus(403)->assertJson([
            'success' => false,
        ]);
    }
}

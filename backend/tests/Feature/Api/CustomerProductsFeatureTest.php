<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerProductsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testProductIndexPositive(): void
    {
        $customer = $this->createUser(User::ROLE_CUSTOMER);
        $merchant = $this->createUser(User::ROLE_MERCHANT);

        $product = Product::factory()->create([
            'merchant_id' => $merchant->id,
            'is_available' => true,
            'stock' => 10,
            'price' => 15000,
        ]);

        $this->authAs($customer);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame($product->id, $response->json('data.data.0.id'));
    }

    public function testProductShowNegative(): void
    {
        $customer = $this->createUser(User::ROLE_CUSTOMER);
        $merchant = $this->createUser(User::ROLE_MERCHANT);

        $product = Product::factory()->create([
            'merchant_id' => $merchant->id,
            'is_available' => false,
            'stock' => 10,
        ]);

        $this->authAs($customer);

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertStatus(404)->assertJson([
            'success' => false,
        ]);
    }
}

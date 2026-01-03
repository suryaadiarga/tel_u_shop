<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CoreContractTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): array
    {
        return [
            'admin' => Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']),
            'merchant' => Role::firstOrCreate(['name' => 'merchant'], ['display_name' => 'Merchant']),
            'customer' => Role::firstOrCreate(['name' => 'customer'], ['display_name' => 'Customer']),
        ];
    }

    private function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $roles = $this->seedRoles();

        $defaults = [
            'role_id' => $roles[$roleName]->id,
            'password' => Hash::make('secret'),
            'is_banned' => false,
        ];

        if ($roleName === 'merchant') {
            $defaults['merchant_status'] = 'approved';
        }

        return User::factory()->create(array_merge($defaults, $attributes));
    }

    private function assertLoginAndMeForRole(string $roleName): void
    {
        $roles = $this->seedRoles();
        $user = $this->createUserWithRole($roleName);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $login->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame($roles[$roleName]->id, $login->json('data.user.role_id'));

        $token = $login->json('data.access_token');
        $this->assertNotEmpty($token);

        $me = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api/me');

        $me->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame($roleName, $me->json('data.role.name'));
    }

    public function test_login_and_me_for_admin(): void
    {
        $this->assertLoginAndMeForRole('admin');
    }

    public function test_login_and_me_for_merchant(): void
    {
        $this->assertLoginAndMeForRole('merchant');
    }

    public function test_login_and_me_for_customer(): void
    {
        $this->assertLoginAndMeForRole('customer');
    }

    public function test_merchant_cannot_update_other_merchant_product(): void
    {
        $merchantA = $this->createUserWithRole('merchant');
        $merchantB = $this->createUserWithRole('merchant');

        $product = Product::factory()->create([
            'merchant_id' => $merchantB->id,
            'is_available' => true,
            'stock' => 10,
        ]);

        $response = $this->actingAs($merchantA, 'sanctum')
            ->putJson('/api/merchant/products/' . $product->id, [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(404)->assertJson([
            'success' => false,
        ]);
    }

    public function test_merchant_cannot_update_other_merchant_order_status(): void
    {
        $merchantA = $this->createUserWithRole('merchant');
        $merchantB = $this->createUserWithRole('merchant');
        $customer = $this->createUserWithRole('customer');

        $product = Product::factory()->create([
            'merchant_id' => $merchantB->id,
            'is_available' => true,
            'stock' => 10,
            'price' => 15000,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'total_amount' => 15000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 15000,
            'qty' => 1,
            'subtotal' => 15000,
        ]);

        $response = $this->actingAs($merchantA, 'sanctum')
            ->putJson('/api/merchant/orders/' . $order->id . '/status', [
                'status' => 'paid',
            ]);

        $response->assertStatus(403)->assertJson([
            'success' => false,
        ]);
    }

    public function test_customer_cannot_access_other_customers_order(): void
    {
        $customerA = $this->createUserWithRole('customer');
        $customerB = $this->createUserWithRole('customer');

        $order = Order::create([
            'user_id' => $customerB->id,
            'total_amount' => 20000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($customerA, 'sanctum')
            ->getJson('/api/activities/' . $order->id);

        $response->assertStatus(404)->assertJson([
            'success' => false,
        ]);
    }

    public function test_product_listing_pagination_and_sorting_contract(): void
    {
        $customer = $this->createUserWithRole('customer');
        $merchant = $this->createUserWithRole('merchant');

        Product::factory()->create([
            'merchant_id' => $merchant->id,
            'price' => 20000,
            'is_available' => true,
            'stock' => 10,
        ]);

        Product::factory()->create([
            'merchant_id' => $merchant->id,
            'price' => 10000,
            'is_available' => true,
            'stock' => 10,
        ]);

        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/products?per_page=1&sort_by=price&sort_order=asc');

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $this->assertSame(1, $response->json('data.per_page'));
        $this->assertCount(1, $response->json('data.data'));
        $this->assertSame(10000, $response->json('data.data.0.price'));
    }

    public function test_order_access_rules_per_role(): void
    {
        $admin = $this->createUserWithRole('admin');
        $merchant = $this->createUserWithRole('merchant');
        $customer = $this->createUserWithRole('customer');

        $product = Product::factory()->create([
            'merchant_id' => $merchant->id,
            'is_available' => true,
            'stock' => 5,
            'price' => 25000,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'total_amount' => 25000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 25000,
            'qty' => 1,
            'subtotal' => 25000,
        ]);

        $adminResponse = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/orders/' . $order->id);

        $adminResponse->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $merchantResponse = $this->actingAs($merchant, 'sanctum')
            ->getJson('/api/merchant/orders');

        $merchantResponse->assertStatus(200)->assertJson([
            'success' => true,
        ]);

        $orderIds = collect($merchantResponse->json('data.data'))->pluck('id')->all();
        $this->assertContains($order->id, $orderIds);

        $customerResponse = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/admin/orders/' . $order->id);

        $customerResponse->assertStatus(403)->assertJson([
            'success' => false,
        ]);
    }
}

<?php

/**
 * Comprehensive API Test Suite
 * Tests: Register → Login → All Features for Each Role
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colors
$c = [
    'green' => "\033[92m",
    'red' => "\033[91m",
    'yellow' => "\033[93m",
    'blue' => "\033[94m",
    'cyan' => "\033[96m",
    'reset' => "\033[0m",
];

$BASE_URL = 'http://localhost:3000/api'; // Updated to port 3000
$PASS_COUNT = 0;
$FAIL_COUNT = 0;

// API Call Helper
function request($method, $endpoint, $data = [], $token = null)
{
    global $BASE_URL;
    $url = $BASE_URL . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return [
            'code' => 0,
            'data' => null,
            'error' => $error,
            'raw' => ''
        ];
    }

    return [
        'code' => $http_code,
        'data' => json_decode($response, true),
        'raw' => $response
    ];
}

// Test Output Helper
function test($title, $result, $details = '')
{
    global $c, $PASS_COUNT, $FAIL_COUNT;

    if ($result) {
        $PASS_COUNT++;
        echo "{$c['green']}✓ PASS{$c['reset']} - $title";
    } else {
        $FAIL_COUNT++;
        echo "{$c['red']}✗ FAIL{$c['reset']} - $title";
    }

    if ($details) {
        echo " {$c['yellow']}($details){$c['reset']}";
    }
    echo "\n";
}

function section($title)
{
    global $c;
    echo "\n{$c['cyan']}========== $title =========={$c['reset']}\n";
}

// ===============================================
// TEST SUITE
// ===============================================

section("1. AUTHENTICATION TESTS");

// Register as Customer
echo "\n{$c['blue']}→ Registering Customer{$c['reset']}\n";
$customer_email = 'customer_' . time() . '@test.com';
$response = request('POST', '/register', [
    'name' => 'Test Customer',
    'email' => $customer_email,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 3
]);
test("Customer Registration", $response['code'] == 201, "Status: {$response['code']}");
if ($response['code'] != 201) {
    echo "  Error: " . json_encode($response['data']) . "\n";
}
$customer_token = $response['data']['data']['access_token'] ?? $response['data']['access_token'] ?? null;

// Register as Merchant
echo "\n{$c['blue']}→ Registering Merchant{$c['reset']}\n";
$merchant_email = 'merchant_' . time() . '@test.com';
$response = request('POST', '/register', [
    'name' => 'Test Merchant',
    'email' => $merchant_email,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 2
]);
test("Merchant Registration", $response['code'] == 201, "Status: {$response['code']}");
if ($response['code'] != 201) {
    echo "  Error: " . json_encode($response['data']) . "\n";
}
$merchant_token = $response['data']['data']['access_token'] ?? $response['data']['access_token'] ?? null;

// Register as Admin
echo "\n{$c['blue']}→ Registering Admin{$c['reset']}\n";
$admin_email = 'admin_' . time() . '@test.com';
$response = request('POST', '/register', [
    'name' => 'Test Admin',
    'email' => $admin_email,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 1
]);
test("Admin Registration", $response['code'] == 201, "Status: {$response['code']}");
if ($response['code'] != 201) {
    echo "  Error: " . json_encode($response['data']) . "\n";
}
$admin_token = $response['data']['data']['access_token'] ?? $response['data']['access_token'] ?? null;

// Login Test
echo "\n{$c['blue']}→ Login Tests{$c['reset']}\n";
$response = request('POST', '/login', [
    'email' => $customer_email,
    'password' => 'password123'
]);
test("Customer Login", $response['code'] == 200, "Status: {$response['code']}");
if ($response['code'] == 200) {
    $customer_token = $response['data']['token'] ?? null;
}

// Get Me
if ($customer_token) {
    echo "\n{$c['blue']}→ Get User Info{$c['reset']}\n";
    $response = request('GET', '/me', [], $customer_token);
    test("Get /me (Customer)", $response['code'] == 200, "Status: {$response['code']}");
}

// ===============================================
section("2. CUSTOMER - CART & CHECKOUT");
// ===============================================

if ($customer_token && $merchant_token) {
    // Get available products
    echo "\n{$c['blue']}→ Getting Products{$c['reset']}\n";
    $response = request('GET', '/merchant/products', [], $merchant_token);
    $products = $response['data']['data'] ?? [];
    test("List Products", $response['code'] == 200, "Status: {$response['code']}, Found: " . count($products));

    // If no products, create one as merchant
    if (empty($products)) {
        echo "\n{$c['yellow']}→ Creating test product as merchant{$c['reset']}\n";
        $response = request('POST', '/merchant/products', [
            'name' => 'Test Product',
            'description' => 'A test product',
            'price' => 100000,
            'stock' => 10
        ], $merchant_token);
        test("Create Product", $response['code'] == 201, "Status: {$response['code']}");
        $product_id = $response['data']['data']['id'] ?? 1;
    } else {
        $product_id = $products[0]['id'] ?? 1;
    }

    // View Cart
    echo "\n{$c['blue']}→ Cart Operations{$c['reset']}\n";
    $response = request('GET', '/cart', [], $customer_token);
    test("View Cart", $response['code'] == 200, "Status: {$response['code']}");
    $cart_count = count($response['data']['items'] ?? []);

    // Add to Cart
    $response = request('POST', "/cart/add/$product_id", [], $customer_token);
    test("Add to Cart", $response['code'] == 200, "Status: {$response['code']}");
    $cart_item_id = $response['data']['item_id'] ?? null;

    // View Cart Again
    $response = request('GET', '/cart', [], $customer_token);
    test("View Cart After Add", $response['code'] == 200, "Status: {$response['code']}");
    $new_cart_count = count($response['data']['items'] ?? []);
    test("Cart Item Added", $new_cart_count > $cart_count, "Items: $cart_count → $new_cart_count");

    // Update Cart Quantity
    if ($cart_item_id) {
        $response = request('PUT', "/cart/update/$cart_item_id", ['quantity' => 2], $customer_token);
        test("Update Cart Quantity", $response['code'] == 200, "Status: {$response['code']}");
    }

    // Top up Wallet First
    echo "\n{$c['blue']}→ Wallet Operations{$c['reset']}\n";
    $response = request('POST', '/wallet/topup', [
        'amount' => 5000000
    ], $customer_token);
    test("Wallet Top-up", $response['code'] == 200, "Status: {$response['code']}");

    // Check Balance
    $response = request('GET', '/wallet/balance', [], $customer_token);
    test("Check Wallet Balance", $response['code'] == 200, "Status: {$response['code']}");
    if ($response['code'] == 200) {
        $balance = $response['data']['balance'] ?? 0;
        echo "  Current balance: Rp. " . number_format($balance, 0, ',', '.') . "\n";
    }

    // View Transactions
    $response = request('GET', '/wallet/transactions', [], $customer_token);
    test("View Wallet Transactions", $response['code'] == 200, "Status: {$response['code']}");

    // Checkout
    echo "\n{$c['blue']}→ Checkout & Orders{$c['reset']}\n";
    $response = request('POST', '/checkout', [
        'payment_method' => 'wallet',
        'shipping_address' => 'Test Address 123'
    ], $customer_token);
    test("Checkout", in_array($response['code'], [200, 201]), "Status: {$response['code']}");

    // View Activities (Orders)
    $response = request('GET', '/activities', [], $customer_token);
    test("View Activities", $response['code'] == 200, "Status: {$response['code']}");
    if ($response['code'] == 200) {
        $activities = $response['data']['data'] ?? [];
        echo "  Total orders: " . count($activities) . "\n";
    }

    // Clear Cart
    $response = request('DELETE', '/cart/clear', [], $customer_token);
    test("Clear Cart", $response['code'] == 200, "Status: {$response['code']}");
}

// ===============================================
section("3. MERCHANT - PRODUCT & ORDER MANAGEMENT");
// ===============================================

if ($merchant_token) {
    echo "\n{$c['blue']}→ Create Product{$c['reset']}\n";
    $product_name = 'Merchant Test Product ' . time();
    $response = request('POST', '/merchant/products', [
        'name' => $product_name,
        'description' => 'Test product for merchant',
        'price' => 250000,
        'stock' => 50
    ], $merchant_token);
    test("Create Product", $response['code'] == 201, "Status: {$response['code']}");
    $new_product_id = $response['data']['data']['id'] ?? null;

    // Update Product
    if ($new_product_id) {
        echo "\n{$c['blue']}→ Update Product{$c['reset']}\n";
        $response = request('PUT', "/merchant/products/$new_product_id", [
            'name' => $product_name . ' (Updated)',
            'price' => 300000,
            'stock' => 40
        ], $merchant_token);
        test("Update Product", $response['code'] == 200, "Status: {$response['code']}");
    }

    // List Merchant Products
    echo "\n{$c['blue']}→ List Merchant Products{$c['reset']}\n";
    $response = request('GET', '/merchant/products', [], $merchant_token);
    test("List Merchant Products", $response['code'] == 200, "Status: {$response['code']}");
    if ($response['code'] == 200) {
        $merchant_products = $response['data']['data'] ?? [];
        echo "  Total products: " . count($merchant_products) . "\n";
    }

    // View Merchant Orders
    echo "\n{$c['blue']}→ View Merchant Orders{$c['reset']}\n";
    $response = request('GET', '/merchant/orders', [], $merchant_token);
    test("List Merchant Orders", $response['code'] == 200, "Status: {$response['code']}");
    if ($response['code'] == 200) {
        $merchant_orders = $response['data']['data'] ?? [];
        echo "  Total orders: " . count($merchant_orders) . "\n";
    }
}

// ===============================================
section("4. ADMIN - USER & ORDER MANAGEMENT");
// ===============================================

if ($admin_token) {
    echo "\n{$c['blue']}→ Admin User Management{$c['reset']}\n";
    $response = request('GET', '/admin/users', [], $admin_token);
    test("List All Users", $response['code'] == 200, "Status: {$response['code']}");
    if ($response['code'] == 200) {
        $all_users = $response['data']['data'] ?? [];
        echo "  Total users: " . count($all_users) . "\n";
    }

    // View All Orders
    echo "\n{$c['blue']}→ Admin Order Management{$c['reset']}\n";
    $response = request('GET', '/admin/orders', [], $admin_token);
    test("List All Orders", $response['code'] == 200, "Status: {$response['code']}");
    if ($response['code'] == 200) {
        $all_orders = $response['data']['data'] ?? [];
        echo "  Total orders: " . count($all_orders) . "\n";
    }
}

// ===============================================
section("5. LOGOUT TESTS");
// ===============================================

if ($customer_token) {
    echo "\n{$c['blue']}→ Logout{$c['reset']}\n";
    $response = request('POST', '/logout', [], $customer_token);
    test("Customer Logout", $response['code'] == 200, "Status: {$response['code']}");

    // Try accessing protected endpoint after logout
    $response = request('GET', '/me', [], $customer_token);
    test("Access Denied After Logout", $response['code'] == 401, "Status: {$response['code']}");
}

// ===============================================
section("SUMMARY");
// ===============================================

$total = $PASS_COUNT + $FAIL_COUNT;
$pass_rate = $total > 0 ? round(($PASS_COUNT / $total) * 100, 1) : 0;

echo "\n{$c['green']}✓ PASSED: $PASS_COUNT{$c['reset']}\n";
echo "{$c['red']}✗ FAILED: $FAIL_COUNT{$c['reset']}\n";
echo "{$c['blue']}TOTAL: $total{$c['reset']}\n";
echo "{$c['cyan']}SUCCESS RATE: {$pass_rate}%{$c['reset']}\n";

if ($FAIL_COUNT === 0) {
    echo "\n{$c['green']}🎉 ALL TESTS PASSED!{$c['reset']}\n";
} else {
    echo "\n{$c['yellow']}⚠️  Some tests failed. Check details above.{$c['reset']}\n";
}

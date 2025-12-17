<?php

$baseUrl = 'http://127.0.0.1:8000/api';

// Function to make API call
function apiCall($method, $endpoint, $data = [], $token = null)
{
    $url = $baseUrl . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        ...$token ? ["Authorization: Bearer $token"] : []
    ]);

    if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    ($ch);

    return [
        'http_code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Test cart update route
echo "Testing cart update route...\n";

// First, register a user
$registerRes = apiCall('POST', '/register', [
    'name' => 'Test User',
    'username' => 'testuser',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'nim' => '12345678',
    'kelas' => 'TI-3A',
    'phone' => '081234567890',
    'role' => 3
]);

if ($registerRes['http_code'] !== 201) {
    echo "Registration failed: " . json_encode($registerRes) . "\n";
    exit(1);
}

$token = $registerRes['response']['data']['access_token'];
$userId = $registerRes['response']['data']['user']['id'];

echo "User registered, token: " . substr($token, 0, 20) . "...\n";

// Create a product as merchant
$merchantRegisterRes = apiCall('POST', '/register', [
    'name' => 'Merchant User',
    'username' => 'merchantuser',
    'email' => 'merchant@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'nim' => '87654321',
    'kelas' => 'TI-3B',
    'phone' => '081234567891',
    'role' => 2
]);

$merchantToken = $merchantRegisterRes['response']['data']['access_token'];

$productRes = apiCall('POST', '/merchant/products', [
    'name' => 'Test Product',
    'description' => 'Test description',
    'price' => 10000,
    'stock' => 10,
    'category' => 'test',
    'prep_time' => 5,
    'is_available' => true
], $merchantToken);

if ($productRes['http_code'] !== 201) {
    echo "Product creation failed: " . json_encode($productRes) . "\n";
    exit(1);
}

$productId = $productRes['response']['data']['id'];
echo "Product created, ID: $productId\n";

// Add to cart
$addToCartRes = apiCall('POST', '/cart/add/' . $productId, [
    'qty' => 1
], $token);

if ($addToCartRes['http_code'] !== 201) {
    echo "Add to cart failed: " . json_encode($addToCartRes) . "\n";
    exit(1);
}

$cartItemId = $addToCartRes['response']['data']['id'];
echo "Added to cart, item ID: $cartItemId\n";

// Now try to update cart
$updateRes = apiCall('PUT', '/cart/update/' . $cartItemId, [
    'qty' => 2
], $token);

echo "Update cart response:\n";
echo "HTTP Code: " . $updateRes['http_code'] . "\n";
echo "Response: " . json_encode($updateRes['response'], JSON_PRETTY_PRINT) . "\n";

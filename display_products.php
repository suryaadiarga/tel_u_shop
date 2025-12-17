<?php

/**
 * Display All Products Script
 * Uses curl to fetch and display products from the running Laravel API
 */

// Helper function to make API calls
function apiCall($method, $endpoint, $data = [], $token = null)
{
    $baseUrl = 'http://127.0.0.1:8000/api';
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
    curl_close($ch);

    return json_decode($response, true);
}

// Login as merchant
echo "🔐 Logging in as merchant to fetch products...\n";
$loginRes = apiCall('POST', '/login', [
    'email' => 'merchant@koperasi.test',
    'password' => 'password'
]);

if (!isset($loginRes['data']['access_token'])) {
    echo "❌ Login failed: " . json_encode($loginRes) . "\n";
    exit(1);
}

$token = $loginRes['data']['access_token'];
echo "✅ Login successful.\n\n";

// Fetch products
echo "📦 Fetching all products...\n";
$productsRes = apiCall('GET', '/merchant/products', [], $token);

if (!isset($productsRes['data'])) {
    echo "❌ Failed to fetch products: " . json_encode($productsRes) . "\n";
    exit(1);
}

$products = $productsRes['data'];

echo "📊 Product Summary:\n";
echo "- Total Products: " . count($products) . "\n";
$categories = array_count_values(array_column($products, 'category'));
echo "- Categories: " . implode(', ', array_map(fn($cat, $count) => "$cat ($count)", array_keys($categories), $categories)) . "\n\n";

echo "📋 Complete Product List:\n";
echo str_repeat("=", 100) . "\n";
printf("%-3s | %-30s | %-10s | %-10s | %-5s | %-5s | %-10s\n", "ID", "NAME", "CATEGORY", "PRICE", "STOCK", "PREP", "STATUS");
echo str_repeat("-", 100) . "\n";

foreach ($products as $product) {
    $status = $product['is_available'] ? 'Available' : 'Unavailable';
    $price = 'Rp ' . number_format($product['price'], 0, ',', '.');
    $prep = $product['prep_time'] . 'min';

    printf(
        "%-3d | %-30s | %-10s | %-10s | %-5d | %-5s | %-10s\n",
        $product['id'],
        substr($product['name'], 0, 30),
        $product['category'],
        $price,
        $product['stock'],
        $prep,
        $status
    );
}

echo str_repeat("=", 100) . "\n";
echo "✅ All products displayed successfully!\n";

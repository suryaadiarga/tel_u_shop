<?php

/**
 * Add 50 Dummy Products with Variants Script
 * Uses curl to call the running Laravel API
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
echo "🔐 Logging in as merchant...\n";
$loginRes = apiCall('POST', '/login', [
    'email' => 'merchant@koperasi.test',
    'password' => 'password'
]);

if (!isset($loginRes['data']['access_token'])) {
    echo "❌ Login failed: " . json_encode($loginRes) . "\n";
    exit(1);
}

$token = $loginRes['data']['access_token'];
echo "✅ Login successful. Token: " . substr($token, 0, 20) . "...\n\n";

// Product data
$categories = [
    'food' => [
        'variants' => ['Spicy', 'Mild', 'Extra Spicy'],
        'products' => [
            'Nasi Goreng',
            'Ayam Bakar',
            'Soto Ayam',
            'Rendang',
            'Gado-Gado',
            'Nasi Uduk',
            'Ayam Goreng',
            'Bakso',
            'Mie Goreng',
            'Capcay',
            'Kwetiau',
            'Bihun Goreng',
            'Ayam Rica-Rica',
            'Ikan Bakar',
            'Cumi Saus Tiram'
        ]
    ],
    'drink' => [
        'variants' => ['Hot', 'Cold', 'Iced'],
        'products' => [
            'Es Teh',
            'Jus Mangga',
            'Kopi Susu',
            'Es Jeruk',
            'Teh Tarik',
            'Matcha Latte',
            'Thai Tea',
            'Smoothie',
            'Milkshake',
            'Cappuccino'
        ]
    ],
    'snack' => [
        'variants' => ['Original', 'Spicy', 'Cheese'],
        'products' => [
            'Keripik Kentang',
            'Keripik Singkong',
            'Keripik Pisang',
            'Kerupuk Udang',
            'Popcorn',
            'Chiki',
            'Donut',
            'Pisang Goreng',
            'Tempe Goreng',
            'Tahu Goreng'
        ]
    ],
    'dessert' => [
        'variants' => ['Chocolate', 'Vanilla', 'Strawberry'],
        'products' => [
            'Ice Cream',
            'Gelato',
            'Cheesecake',
            'Tiramisu',
            'Macaron',
            'Fruit Tart',
            'Brownies',
            'Pudding',
            'Waffle',
            'Crepes'
        ]
    ],
    'appetizer' => [
        'variants' => ['Small', 'Medium', 'Large'],
        'products' => [
            'Spring Rolls',
            'Samosa',
            'Bruschetta',
            'Nachos',
            'Calamari',
            'Chicken Wings',
            'Mozzarella Sticks',
            'Onion Rings',
            'Garlic Bread',
            'Deviled Eggs'
        ]
    ]
];

$productCount = 0;

echo "📦 Adding 50 dummy products...\n";
echo str_repeat("=", 60) . "\n";

foreach ($categories as $category => $categoryData) {
    $variants = $categoryData['variants'];
    $products = $categoryData['products'];

    foreach ($products as $productName) {
        foreach ($variants as $variant) {
            if ($productCount >= 50) break 3;

            $fullName = "$productName $variant";
            $price = rand(5000, 50000); // Random price between 5k-50k
            $stock = rand(10, 100); // Random stock
            $prepTime = rand(5, 30); // Random prep time

            $productData = [
                'name' => $fullName,
                'description' => "Delicious $fullName - $category category",
                'price' => $price,
                'stock' => $stock,
                'category' => $category,
                'prep_time' => $prepTime,
                'is_available' => true
            ];

            $addRes = apiCall('POST', '/merchant/products', $productData, $token);

            if (isset($addRes['data'])) {
                $productCount++;
                echo "✅ [$productCount/50] Added: $fullName (Rp " . number_format($price) . ")\n";
            } else {
                echo "❌ Failed to add: $fullName - " . json_encode($addRes) . "\n";
            }
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 Successfully added $productCount dummy products!\n";
echo "📊 Categories used: " . implode(', ', array_keys($categories)) . "\n";
echo "🔄 Each product has variants for better testing\n";
echo str_repeat("=", 60) . "\n";

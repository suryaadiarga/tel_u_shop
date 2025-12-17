<?php
error_reporting(E_ALL & ~E_DEPRECATED);

$baseUrl = 'http://127.0.0.1:8080/api';
$email = 'test_' . time() . '@test.com';

function curl_req($method, $endpoint, $data = [], $token = null) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            ...$token ? ["Authorization: Bearer $token"] : []
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'data' => json_decode($res, true), 'raw' => $res];
}

echo "========== API TEST ==========\n";

// 1. Register Customer
echo "\n1. REGISTER CUSTOMER\n";
$res = curl_req('POST', '/register', [
    'name' => 'Test Customer',
    'email' => $email,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 3
]);
echo "Status: {$res['code']}\n";
if ($res['code'] === 201) {
    echo "✓ SUCCESS\n";
    $token = $res['data']['data']['access_token'] ?? null;
} else {
    echo "✗ FAILED\n";
    var_dump($res['data']);
    exit(1);
}

// 2. Get Me
echo "\n2. GET /ME\n";
$res = curl_req('GET', '/me', [], $token);
echo "Status: {$res['code']}\n";
echo $res['code'] === 200 ? "✓ SUCCESS\n" : "✗ FAILED\n";

// 3. Get Cart
echo "\n3. GET CART\n";
$res = curl_req('GET', '/cart', [], $token);
echo "Status: {$res['code']}\n";
echo $res['code'] === 200 ? "✓ SUCCESS\n" : "✗ FAILED\n";

// 4. Wallet Balance
echo "\n4. GET WALLET BALANCE\n";
$res = curl_req('GET', '/wallet/balance', [], $token);
echo "Status: {$res['code']}\n";
echo $res['code'] === 200 ? "✓ SUCCESS\n" : "✗ FAILED\n";

// 5. Logout
echo "\n5. LOGOUT\n";
$res = curl_req('POST', '/logout', [], $token);
echo "Status: {$res['code']}\n";
echo $res['code'] === 200 ? "✓ SUCCESS\n" : "✗ FAILED\n";

echo "\n========== DONE ==========\n";
?>

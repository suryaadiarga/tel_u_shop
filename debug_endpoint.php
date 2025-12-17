<?php
function req($method, $url, $data = [], $token = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = ['Content-Type: application/json', 'Accept: application/json'];
    if ($token) $headers[] = "Authorization: Bearer $token";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($data)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    ($ch);
    return [$code, $res];
}
$base = 'http://127.0.0.1:8000/api';
// Register merchant
list($code, $res) = req('POST', $base . '/register', ['name' => 'Dbg Merchant', 'email' => 'dbg_merchant_' . time() . '@test.com', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 2]);
echo "REGISTER ($code) => $res\n";
$data = json_decode($res, true);
$token = $data['data']['access_token'] ?? $data['data']['access_token'] ?? $data['access_token'] ?? null;
// Try list products
list($c, $r) = req('GET', $base . '/merchant/products', [], $token);
echo "GET /merchant/products ($c) => $r\n";
// Try create product
list($c2, $r2) = req('POST', $base . '/merchant/products', ['name' => 'dbg', 'description' => 'dbg', 'price' => 1000, 'stock' => 5], $token);
echo "POST /merchant/products ($c2) => $r2\n";

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
$email = 'dbg_customer_' . time() . '@test.com';
list($c, $r) = req('POST', $base . '/register', ['name' => 'Dbg Customer', 'email' => $email, 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 3]);
echo "REGISTER ($c) => $r\n";
list($c2, $r2) = req('POST', $base . '/login', ['email' => $email, 'password' => 'password123']);
echo "LOGIN ($c2) => $r2\n";
$data = json_decode($r2, true);
$token = $data['data']['access_token'] ?? $data['access_token'] ?? null;
list($c3, $r3) = req('POST', $base . '/wallet/topup', ['amount' => 100000], $token);
echo "TOPUP ($c3) => $r3\n";
list($c4, $r4) = req('GET', $base . '/merchant/products', [], null);
echo "PRODUCTS ($c4) => $r4\n";
// attempt add to cart for first product id if any
$products = json_decode($r4, true)['data'] ?? [];
$pid = $products[0]['id'] ?? 1;
list($c5, $r5) = req('POST', $base . '/cart/add/' . $pid, [], $token);
echo "ADD TO CART ($c5) => $r5\n";
list($c6, $r6) = req('POST', $base . '/checkout', ['payment_method' => 'wallet', 'shipping_address' => 'addr'], $token);
echo "CHECKOUT ($c6) => $r6\n";

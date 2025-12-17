<?php
$ch = curl_init('http://127.0.0.1:8080/api/register');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'name' => 'Test User',
        'email' => 'test_' . time() . '@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 3
    ]),
    CURLOPT_CONNECTTIMEOUT => 30,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_VERBOSE => true
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);

echo "=== CURL REQUEST DEBUG ===\n";
echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Content Type: " . $info['content_type'] . "\n";
if ($error) echo "Error: $error\n";
echo "\n=== RESPONSE ===\n";
echo $response . "\n";

curl_close($ch);

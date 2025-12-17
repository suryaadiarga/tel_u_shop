<?php
$url = 'http://localhost:3000/api/register';
$data = ['name' => 'Test', 'email' => 'test@test.com', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 3];

echo "Testing curl to: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: $http_code\n";
echo "Error: $error\n";
echo "Response:\n";
var_dump($response);
echo "\nDecoded:\n";
var_dump(json_decode($response, true));

curl_close($ch);

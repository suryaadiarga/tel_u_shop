<?php

/**
 * Comprehensive API Testing Script for Laravel Backend
 * Tests all major endpoints and features for React frontend integration
 */

class APITester
{
    private $baseUrl = 'http://127.0.0.1:8000/api';
    private $tokens = [];

    public function runTests()
    {
        echo "🚀 Starting Comprehensive Laravel API Tests\n";
        echo "==========================================\n\n";

        // Test 1: Authentication
        $this->testAuthentication();

        // Test 2: Cart Management
        $this->testCartManagement();

        // Test 3: Product Management (Merchant)
        $this->testProductManagement();

        // Test 4: Order Management
        $this->testOrderManagement();

        // Test 5: Wallet Management
        $this->testWalletManagement();

        // Test 6: Admin Features
        $this->testAdminFeatures();

        echo "\n✅ All API tests completed!\n";
    }

    private function makeRequest($method, $endpoint, $data = null, $token = null)
    {
        $url = $this->baseUrl . $endpoint;
        $headers = ['Content-Type: application/json'];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['response' => json_decode($response, true), 'http_code' => $httpCode];
    }

    private function testAuthentication()
    {
        echo "🔐 Testing Authentication\n";
        echo "------------------------\n";

        // Use existing users from seeder instead of registering new ones
        // Login as customer
        $loginData = [
            'email' => 'customer@koperasi.test',
            'password' => 'password'
        ];

        $result = $this->makeRequest('POST', '/login', $loginData);
        if ($result['http_code'] === 200) {
            echo "✅ Customer login successful\n";
            $this->tokens['customer'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Customer login failed: " . json_encode($result['response']) . "\n";
        }

        // Login as merchant
        $loginData = [
            'email' => 'merchant@koperasi.test',
            'password' => 'password'
        ];

        $result = $this->makeRequest('POST', '/login', $loginData);
        if ($result['http_code'] === 200) {
            echo "✅ Merchant login successful\n";
            $this->tokens['merchant'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Merchant login failed: " . json_encode($result['response']) . "\n";
        }

        // Get user profile
        $result = $this->makeRequest('GET', '/me', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Get user profile successful\n";
        } else {
            echo "❌ Get user profile failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }

    private function testCartManagement()
    {
        echo "🛒 Testing Cart Management\n";
        echo "-------------------------\n";

        // Get cart (should be empty)
        $result = $this->makeRequest('GET', '/cart', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Get empty cart successful\n";
        } else {
            echo "❌ Get cart failed: " . json_encode($result['response']) . "\n";
        }

        // Add product to cart (assuming product ID 1 exists)
        $cartData = ['qty' => 2];
        $result = $this->makeRequest('POST', '/cart/add/1', $cartData, $this->tokens['customer']);
        if ($result['http_code'] === 201) {
            echo "✅ Add product to cart successful\n";
            $cartItemId = $result['response']['data']['id'];
        } else {
            echo "❌ Add to cart failed: " . json_encode($result['response']) . "\n";
            $cartItemId = null;
        }

        // Get cart with items
        $result = $this->makeRequest('GET', '/cart', null, $this->tokens['customer']);
        if ($result['http_code'] === 200 && !empty($result['response']['data']['items'])) {
            echo "✅ Get cart with items successful\n";
        } else {
            echo "❌ Get cart with items failed: " . json_encode($result['response']) . "\n";
        }

        // Update cart item quantity
        if ($cartItemId) {
            $updateData = ['qty' => 3];
            $result = $this->makeRequest('PUT', '/cart/update/' . $cartItemId, $updateData, $this->tokens['customer']);
            if ($result['http_code'] === 200) {
                echo "✅ Update cart item quantity successful\n";
            } else {
                echo "❌ Update cart item failed: " . json_encode($result['response']) . "\n";
            }
        }

        echo "\n";
    }

    private function testProductManagement()
    {
        echo "📦 Testing Product Management (Merchant)\n";
        echo "---------------------------------------\n";

        // Get merchant products
        $result = $this->makeRequest('GET', '/merchant/products', null, $this->tokens['merchant']);
        if ($result['http_code'] === 200) {
            echo "✅ Get merchant products successful\n";
        } else {
            echo "❌ Get merchant products failed: " . json_encode($result['response']) . "\n";
        }

        // Create product
        $productData = [
            'name' => 'Test Product',
            'description' => 'A test product',
            'price' => 25000,
            'stock' => 10,
            'category' => 'Food',
            'preparation_time' => 15
        ];

        $result = $this->makeRequest('POST', '/merchant/products', $productData, $this->tokens['merchant']);
        if ($result['http_code'] === 201) {
            echo "✅ Create product successful\n";
            $productId = $result['response']['data']['id'];
        } else {
            echo "❌ Create product failed: " . json_encode($result['response']) . "\n";
            $productId = null;
        }

        // Update product
        if ($productId) {
            $updateData = ['price' => 30000, 'stock' => 15];
            $result = $this->makeRequest('PUT', '/merchant/products/' . $productId, $updateData, $this->tokens['merchant']);
            if ($result['http_code'] === 200) {
                echo "✅ Update product successful\n";
            } else {
                echo "❌ Update product failed: " . json_encode($result['response']) . "\n";
            }
        }

        echo "\n";
    }

    private function testOrderManagement()
    {
        echo "📋 Testing Order Management\n";
        echo "---------------------------\n";

        // Get customer activities/orders
        $result = $this->makeRequest('GET', '/activities', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Get customer activities successful\n";
        } else {
            echo "❌ Get customer activities failed: " . json_encode($result['response']) . "\n";
        }

        // Get merchant orders
        $result = $this->makeRequest('GET', '/merchant/orders', null, $this->tokens['merchant']);
        if ($result['http_code'] === 200) {
            echo "✅ Get merchant orders successful\n";
        } else {
            echo "❌ Get merchant orders failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }

    private function testWalletManagement()
    {
        echo "💰 Testing Wallet Management\n";
        echo "----------------------------\n";

        // Get wallet balance
        $result = $this->makeRequest('GET', '/wallet/balance', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Get wallet balance successful\n";
        } else {
            echo "❌ Get wallet balance failed: " . json_encode($result['response']) . "\n";
        }

        // Top up wallet
        $topupData = ['amount' => 50000];
        $result = $this->makeRequest('POST', '/wallet/topup', $topupData, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Wallet topup successful\n";
        } else {
            echo "❌ Wallet topup failed: " . json_encode($result['response']) . "\n";
        }

        // Get wallet transactions
        $result = $this->makeRequest('GET', '/wallet/transactions', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Get wallet transactions successful\n";
        } else {
            echo "❌ Get wallet transactions failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }

    private function testAdminFeatures()
    {
        echo "👑 Testing Admin Features\n";
        echo "-------------------------\n";

        // Note: Admin token would need to be obtained separately
        // For now, just test the endpoints exist and return proper auth errors

        $adminToken = 'admin_token_here'; // This would be obtained by logging in as admin

        // Get admin users (should fail without admin token)
        $result = $this->makeRequest('GET', '/admin/users', null, $this->tokens['customer']);
        if ($result['http_code'] === 403) {
            echo "✅ Admin access properly restricted for non-admin users\n";
        } else {
            echo "⚠️ Admin access check returned unexpected status: " . $result['http_code'] . "\n";
        }

        echo "\n";
    }
}

// Run the tests
$tester = new APITester();
$tester->runTests();

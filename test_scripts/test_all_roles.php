<?php

/**
 * Comprehensive Test for All User Roles: Admin, Merchant, Customer
 * Tests all features available to each role
 */

class AllRolesTester
{
    private $baseUrl = 'http://127.0.0.1:8000/api';
    private $tokens = [];
    private $testData = [];

    public function runTests()
    {
        echo "🚀 Comprehensive All-Roles Testing\n";
        echo "==================================\n\n";

        // Phase 1: Register Users for Each Role
        $this->registerAllRoles();

        // Phase 2: Login as Each Role
        $this->loginAllRoles();

        // Phase 3: Test Admin Features
        $this->testAdminFeatures();

        // Phase 4: Test Merchant Features
        $this->testMerchantFeatures();

        // Phase 5: Test Customer Features
        $this->testCustomerFeatures();

        echo "\n✅ All roles testing completed!\n";
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

    private function registerAllRoles()
    {
        echo "📝 Phase 1: Registering Users for All Roles\n";
        echo "==========================================\n";

        // Register Admin
        $adminData = [
            'name' => 'Test Admin',
            'email' => 'testadmin@koperasi.test',
            'password' => 'adminpass',
            'password_confirmation' => 'adminpass',
            'role' => 1
        ];

        $result = $this->makeRequest('POST', '/register', $adminData);
        if ($result['http_code'] === 201) {
            echo "✅ Admin registration successful\n";
            $this->tokens['admin'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Admin registration failed: " . json_encode($result['response']) . "\n";
        }

        // Register Merchant
        $merchantData = [
            'name' => 'Test Merchant',
            'email' => 'testmerchant@koperasi.test',
            'password' => 'merchantpass',
            'password_confirmation' => 'merchantpass',
            'role' => 2
        ];

        $result = $this->makeRequest('POST', '/register', $merchantData);
        if ($result['http_code'] === 201) {
            echo "✅ Merchant registration successful\n";
            $this->tokens['merchant'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Merchant registration failed: " . json_encode($result['response']) . "\n";
        }

        // Register Customer
        $customerData = [
            'name' => 'Test Customer',
            'email' => 'testcustomer@koperasi.test',
            'password' => 'customerpass',
            'password_confirmation' => 'customerpass',
            'role' => 3
        ];

        $result = $this->makeRequest('POST', '/register', $customerData);
        if ($result['http_code'] === 201) {
            echo "✅ Customer registration successful\n";
            $this->tokens['customer'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Customer registration failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }

    private function loginAllRoles()
    {
        echo "🔐 Phase 2: Logging in All Roles\n";
        echo "===============================\n";

        // Login as Admin
        $loginData = ['email' => 'testadmin@koperasi.test', 'password' => 'adminpass'];
        $result = $this->makeRequest('POST', '/login', $loginData);
        if ($result['http_code'] === 200) {
            echo "✅ Admin login successful\n";
            $this->tokens['admin'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Admin login failed: " . json_encode($result['response']) . "\n";
        }

        // Login as Merchant
        $loginData = ['email' => 'testmerchant@koperasi.test', 'password' => 'merchantpass'];
        $result = $this->makeRequest('POST', '/login', $loginData);
        if ($result['http_code'] === 200) {
            echo "✅ Merchant login successful\n";
            $this->tokens['merchant'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Merchant login failed: " . json_encode($result['response']) . "\n";
        }

        // Login as Customer
        $loginData = ['email' => 'testcustomer@koperasi.test', 'password' => 'customerpass'];
        $result = $this->makeRequest('POST', '/login', $loginData);
        if ($result['http_code'] === 200) {
            echo "✅ Customer login successful\n";
            $this->tokens['customer'] = $result['response']['data']['access_token'];
        } else {
            echo "❌ Customer login failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }

    private function testAdminFeatures()
    {
        echo "👑 Phase 3: Testing Admin Features\n";
        echo "=================================\n";

        if (!isset($this->tokens['admin'])) {
            echo "❌ No admin token available, skipping admin tests\n";
            return;
        }

        // Get all users
        $result = $this->makeRequest('GET', '/admin/users', null, $this->tokens['admin']);
        if ($result['http_code'] === 200) {
            echo "✅ Admin: Get all users successful\n";
        } else {
            echo "❌ Admin: Get all users failed: " . json_encode($result['response']) . "\n";
        }

        // Get all orders
        $result = $this->makeRequest('GET', '/admin/orders', null, $this->tokens['admin']);
        if ($result['http_code'] === 200) {
            echo "✅ Admin: Get all orders successful\n";
        } else {
            echo "❌ Admin: Get all orders failed: " . json_encode($result['response']) . "\n";
        }

        // Try to access customer-only endpoint (should fail)
        $result = $this->makeRequest('GET', '/cart', null, $this->tokens['admin']);
        if ($result['http_code'] === 403) {
            echo "✅ Admin: Properly restricted from customer endpoints\n";
        } else {
            echo "⚠️ Admin: Unexpected access to customer endpoint: " . $result['http_code'] . "\n";
        }

        echo "\n";
    }

    private function testMerchantFeatures()
    {
        echo "🏪 Phase 4: Testing Merchant Features\n";
        echo "====================================\n";

        if (!isset($this->tokens['merchant'])) {
            echo "❌ No merchant token available, skipping merchant tests\n";
            return;
        }

        // Get merchant products
        $result = $this->makeRequest('GET', '/merchant/products', null, $this->tokens['merchant']);
        if ($result['http_code'] === 200) {
            echo "✅ Merchant: Get products successful\n";
        } else {
            echo "❌ Merchant: Get products failed: " . json_encode($result['response']) . "\n";
        }

        // Create product
        $productData = [
            'name' => 'Merchant Test Product',
            'description' => 'A test product by merchant',
            'price' => 15000,
            'stock' => 20,
            'category' => 'Food',
            'preparation_time' => 10
        ];

        $result = $this->makeRequest('POST', '/merchant/products', $productData, $this->tokens['merchant']);
        if ($result['http_code'] === 201) {
            echo "✅ Merchant: Create product successful\n";
            $this->testData['product_id'] = $result['response']['data']['id'];
        } else {
            echo "❌ Merchant: Create product failed: " . json_encode($result['response']) . "\n";
        }

        // Get merchant orders
        $result = $this->makeRequest('GET', '/merchant/orders', null, $this->tokens['merchant']);
        if ($result['http_code'] === 200) {
            echo "✅ Merchant: Get orders successful\n";
        } else {
            echo "❌ Merchant: Get orders failed: " . json_encode($result['response']) . "\n";
        }

        // Try to access admin endpoint (should fail)
        $result = $this->makeRequest('GET', '/admin/users', null, $this->tokens['merchant']);
        if ($result['http_code'] === 403) {
            echo "✅ Merchant: Properly restricted from admin endpoints\n";
        } else {
            echo "⚠️ Merchant: Unexpected access to admin endpoint: " . $result['http_code'] . "\n";
        }

        echo "\n";
    }

    private function testCustomerFeatures()
    {
        echo "🛒 Phase 5: Testing Customer Features\n";
        echo "====================================\n";

        if (!isset($this->tokens['customer'])) {
            echo "❌ No customer token available, skipping customer tests\n";
            return;
        }

        // Get cart
        $result = $this->makeRequest('GET', '/cart', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Customer: Get cart successful\n";
        } else {
            echo "❌ Customer: Get cart failed: " . json_encode($result['response']) . "\n";
        }

        // Add product to cart (using product ID from merchant test or default to 1)
        $productId = $this->testData['product_id'] ?? 1;
        $cartData = ['qty' => 2];
        $result = $this->makeRequest('POST', '/cart/add/' . $productId, $cartData, $this->tokens['customer']);
        if ($result['http_code'] === 201) {
            echo "✅ Customer: Add to cart successful\n";
            $this->testData['cart_item_id'] = $result['response']['data']['id'];
        } else {
            echo "❌ Customer: Add to cart failed: " . json_encode($result['response']) . "\n";
        }

        // Get wallet balance
        $result = $this->makeRequest('GET', '/wallet/balance', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Customer: Get wallet balance successful\n";
        } else {
            echo "❌ Customer: Get wallet balance failed: " . json_encode($result['response']) . "\n";
        }

        // Top up wallet
        $topupData = ['amount' => 25000];
        $result = $this->makeRequest('POST', '/wallet/topup', $topupData, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Customer: Wallet topup successful\n";
        } else {
            echo "❌ Customer: Wallet topup failed: " . json_encode($result['response']) . "\n";
        }

        // Get activities
        $result = $this->makeRequest('GET', '/activities', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Customer: Get activities successful\n";
        } else {
            echo "❌ Customer: Get activities failed: " . json_encode($result['response']) . "\n";
        }

        // Get reviews
        $result = $this->makeRequest('GET', '/reviews', null, $this->tokens['customer']);
        if ($result['http_code'] === 200) {
            echo "✅ Customer: Get reviews successful\n";
        } else {
            echo "❌ Customer: Get reviews failed: " . json_encode($result['response']) . "\n";
        }

        // Create review
        $reviewData = [
            'product_id' => $productId,
            'rating' => 5,
            'comment' => 'Great product from merchant!'
        ];
        $result = $this->makeRequest('POST', '/reviews', $reviewData, $this->tokens['customer']);
        if ($result['http_code'] === 201) {
            echo "✅ Customer: Create review successful\n";
        } else {
            echo "❌ Customer: Create review failed: " . json_encode($result['response']) . "\n";
        }

        // Try to access merchant endpoint (should fail)
        $result = $this->makeRequest('GET', '/merchant/products', null, $this->tokens['customer']);
        if ($result['http_code'] === 403) {
            echo "✅ Customer: Properly restricted from merchant endpoints\n";
        } else {
            echo "⚠️ Customer: Unexpected access to merchant endpoint: " . $result['http_code'] . "\n";
        }

        echo "\n";
    }
}

// Run the comprehensive tests
$tester = new AllRolesTester();
$tester->runTests();

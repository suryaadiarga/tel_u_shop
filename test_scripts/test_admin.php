<?php

/**
 * Test Admin Registration and Login
 */

class AdminTester
{
    private $baseUrl = 'http://127.0.0.1:8000/api';

    public function runTests()
    {
        echo "👑 Testing Admin Registration and Login\n";
        echo "=======================================\n\n";

        // Test 1: Register Admin
        $this->testAdminRegistration();

        // Test 2: Login as Admin
        $this->testAdminLogin();

        // Test 3: Test Admin Endpoints
        $this->testAdminEndpoints();

        echo "\n✅ Admin tests completed!\n";
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
        } elseif ($method === 'GET') {
            // Default is GET
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['response' => json_decode($response, true), 'http_code' => $httpCode];
    }

    private function testAdminRegistration()
    {
        echo "📝 Testing Admin Registration\n";
        echo "-----------------------------\n";

        $registerData = [
            'name' => 'New Admin User',
            'email' => 'newadmin@koperasi.test',
            'password' => 'adminpassword',
            'password_confirmation' => 'adminpassword',
            'role' => 1 // Admin role
        ];

        $result = $this->makeRequest('POST', '/register', $registerData);
        if ($result['http_code'] === 201) {
            echo "✅ Admin registration successful\n";
            $this->adminToken = $result['response']['data']['access_token'];
        } else {
            echo "❌ Admin registration failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }

    private function testAdminLogin()
    {
        echo "🔐 Testing Admin Login\n";
        echo "---------------------\n";

        // First try with seeded admin
        $loginData = [
            'email' => 'admin@koperasi.test',
            'password' => 'password'
        ];

        $result = $this->makeRequest('POST', '/login', $loginData);
        if ($result['http_code'] === 200) {
            echo "✅ Seeded admin login successful\n";
            $this->adminToken = $result['response']['data']['access_token'];
        } else {
            echo "❌ Seeded admin login failed: " . json_encode($result['response']) . "\n";

            // Try with newly registered admin
            $loginData = [
                'email' => 'newadmin@koperasi.test',
                'password' => 'adminpassword'
            ];

            $result = $this->makeRequest('POST', '/login', $loginData);
            if ($result['http_code'] === 200) {
                echo "✅ New admin login successful\n";
                $this->adminToken = $result['response']['data']['access_token'];
            } else {
                echo "❌ New admin login failed: " . json_encode($result['response']) . "\n";
            }
        }

        echo "\n";
    }

    private function testAdminEndpoints()
    {
        echo "⚙️ Testing Admin Endpoints\n";
        echo "-------------------------\n";

        if (!$this->adminToken) {
            echo "❌ No admin token available, skipping endpoint tests\n";
            return;
        }

        // Test getting admin users
        $result = $this->makeRequest('GET', '/admin/users', null, $this->adminToken);
        if ($result['http_code'] === 200) {
            echo "✅ Get admin users successful\n";
        } else {
            echo "❌ Get admin users failed: " . json_encode($result['response']) . "\n";
        }

        // Test getting admin orders
        $result = $this->makeRequest('GET', '/admin/orders', null, $this->adminToken);
        if ($result['http_code'] === 200) {
            echo "✅ Get admin orders successful\n";
        } else {
            echo "❌ Get admin orders failed: " . json_encode($result['response']) . "\n";
        }

        echo "\n";
    }
}

// Run the tests
$tester = new AdminTester();
$tester->runTests();

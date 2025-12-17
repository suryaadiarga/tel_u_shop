<?php

/**
 * Simple Review API Test
 */

class ReviewTester
{
    private $baseUrl = 'http://127.0.0.1:8000/api';
    private $customerToken = null;

    public function runTests()
    {
        echo "🧪 Testing Review API\n";
        echo "=====================\n\n";

        // Login as customer
        $this->login();

        // Test review functionality
        if ($this->customerToken) {
            $this->testReviewOperations();
        }

        echo "\n✅ Review tests completed!\n";
    }

    private function login()
    {
        echo "🔐 Logging in...\n";

        $loginData = [
            'email' => 'testcustomer@example.com',
            'password' => 'password123'
        ];

        $result = $this->makeRequest('POST', '/login', $loginData);

        if ($result['http_code'] === 200) {
            $this->customerToken = $result['response']['data']['access_token'];
            echo "✅ Login successful\n";
        } else {
            echo "❌ Login failed\n";
        }
        echo "\n";
    }

    private function testReviewOperations()
    {
        echo "📝 Testing Review Operations\n";
        echo "----------------------------\n";

        // Get reviews for product (assuming product ID 1 exists)
        $result = $this->makeRequest('GET', '/products/1/reviews', null, $this->customerToken);
        echo "Get reviews: " . ($result['http_code'] === 200 ? "✅" : "❌") . "\n";

        // Create a review
        $reviewData = [
            'rating' => 5,
            'comment' => 'Great product!'
        ];

        $result = $this->makeRequest('POST', '/products/1/reviews', $reviewData, $this->customerToken);
        if ($result['http_code'] === 201) {
            echo "Create review: ✅\n";
            $reviewId = $result['response']['data']['id'];
        } else {
            echo "Create review: ❌ - " . json_encode($result['response']) . "\n";
            $reviewId = null;
        }

        // Get user's reviews
        $result = $this->makeRequest('GET', '/my-reviews', null, $this->customerToken);
        echo "Get my reviews: " . ($result['http_code'] === 200 ? "✅" : "❌") . "\n";

        echo "\n";
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
}

// Run the tests
$tester = new ReviewTester();
$tester->runTests();

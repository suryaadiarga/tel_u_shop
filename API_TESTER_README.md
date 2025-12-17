# Laravel API Comprehensive Tester

A comprehensive testing script for Laravel API endpoints with detailed debugging output and JSON support for CI/CD integration.

## Features

- 🔍 **Comprehensive Testing**: Tests all major API endpoints
- 🐛 **Detailed Debugging**: Verbose output with request/response details
- 📊 **Performance Metrics**: Response time and size tracking
- 🎨 **Colored Output**: Easy-to-read console output with colors
- 📋 **JSON Export**: Machine-readable output for CI/CD pipelines
- 🎯 **Endpoint Filtering**: Test specific endpoints only
- ✅ **Pass/Fail Reporting**: Clear test results with success rates

## Installation

1. Place `test_api_debug.php` in your project root directory
2. Ensure your Laravel API server is running (default: `http://localhost:8000`)
3. Make sure PHP and cURL are available

## Usage

### Basic Usage

```bash
# Run all tests with colored output
php test_api_debug.php

# Run with verbose debugging
php test_api_debug.php --verbose

# Export results to JSON
php test_api_debug.php --json

# Test only authentication endpoints
php test_api_debug.php --endpoint=auth

# Combine options
php test_api_debug.php --endpoint=cart --verbose
```

### Command Line Options

| Option | Description | Example |
|--------|-------------|---------|
| `--json` | Output results in JSON format | `php test_api_debug.php --json` |
| `--verbose` | Show detailed request/response info | `php test_api_debug.php --verbose` |
| `--endpoint=<name>` | Test only specific endpoints | `php test_api_debug.php --endpoint=auth` |

### Endpoint Filters

- `auth` - Authentication endpoints (register, login, profile, logout)
- `cart` - Shopping cart endpoints
- `products` - Product management (merchant)
- `wallet` - Wallet and transactions
- `activities` - User activities
- `wishlist` - Wishlist management
- `reviews` - Product reviews
- `loyalty` - Loyalty points
- `notifications` - User notifications

## Output Examples

### Console Output (Default)

```
🔍 Laravel API Comprehensive Tester
=====================================

[14:30:15] 🚀 Starting Authentication Tests
[14:30:15] Testing: auth.register - Register new user
[14:30:16] ✅ PASS: auth.register - HTTP 201 (0.245s)
[14:30:16] ✅ Auth token acquired
[14:30:16] Testing: auth.login - User login
[14:30:16] ✅ PASS: auth.login - HTTP 200 (0.123s)

==================================================
📊 TEST RESULTS SUMMARY
==================================================
Total Tests: 15
Passed: 12
Failed: 3
Success Rate: 80.00%
Timestamp: 2024-01-15 14:30:20
Base URL: http://localhost:8000/api

❌ FAILED TESTS:
------------------------------
• cart.add - Expected 200, got 404
  Error: Product not found
• products.merchant_index - Expected 200, got 403
  Error: Access denied

✅ PASSED TESTS: 12

⚡ PERFORMANCE SUMMARY:
Average Response Time: 0.156s
Fastest Response: 0.089s
Slowest Response: 0.245s

==================================================
```

### JSON Output

```json
{
  "summary": {
    "total_tests": 15,
    "passed": 12,
    "failed": 3,
    "success_rate": 80.0
  },
  "tests": [
    {
      "name": "auth.register",
      "endpoint": "/register",
      "method": "POST",
      "expected_code": 201,
      "actual_code": 201,
      "response_time": 0.245,
      "success": true,
      "error": "",
      "response": "{\"status\":\"success\",\"message\":\"Pendaftaran berhasil.\",\"data\":{\"user\":{...},\"access_token\":\"...\"}}"
    }
  ],
  "timestamp": "2024-01-15 14:30:20",
  "base_url": "http://localhost:8000/api"
}
```

### Verbose Output

When using `--verbose`, each request shows:

```
--- Request Details ---
URL: http://localhost:8000/api/register
Method: POST
Headers: {
  "Content-Type": "application/json",
  "Accept": "application/json"
}
Data: {
  "name": "Test User",
  "username": "testuser",
  "email": "test@example.com",
  "password": "password123",
  "nim": "123456789",
  "kelas": "Test Class",
  "phone": "081234567890",
  "role": 3
}

--- Response Details ---
HTTP Code: 201
Response Time: 0.245s
Response Size: 1024 bytes
Response: {
  "status": "success",
  "message": "Pendaftaran berhasil.",
  "data": {
    "user": {...},
    "access_token": "..."
  }
}
--- End ---
```

## Configuration

### Base URL

Edit the `$baseUrl` property in the script to change the API endpoint:

```php
private $baseUrl = 'http://localhost:8000/api'; // Change this
```

### Test Data

Modify the `$testUser` array to use different test credentials:

```php
private $testUser = [
    'name' => 'Your Test User',
    'username' => 'testuser',
    'email' => 'test@example.com',
    'password' => 'password123',
    'nim' => '123456789',
    'kelas' => 'Test Class',
    'phone' => '081234567890',
    'role' => 3 // 1=Admin, 2=Customer, 3=Customer
];
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: API Tests
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Start Laravel
        run: |
          cp .env.ci .env
          php artisan key:generate
          php artisan migrate --seed
          php artisan serve --host=0.0.0.0 --port=8000 &
          sleep 5
      - name: Run API Tests
        run: php test_api_debug.php --json > test_results.json
      - name: Upload Results
        uses: actions/upload-artifact@v2
        with:
          name: test-results
          path: test_results.json
```

### Jenkins Pipeline Example

```groovy
pipeline {
    agent any
    stages {
        stage('Test API') {
            steps {
                sh 'php test_api_debug.php --json > test_results.json'
                archiveArtifacts artifacts: 'test_results.json', fingerprint: true
            }
        }
    }
    post {
        always {
            script {
                def results = readJSON file: 'test_results.json'
                echo "Tests: ${results.summary.total_tests}"
                echo "Passed: ${results.summary.passed}"
                echo "Failed: ${results.summary.failed}"
                echo "Success Rate: ${results.summary.success_rate}%"
            }
        }
    }
}
```

## Troubleshooting

### Common Issues

1. **Connection Refused**
   - Ensure Laravel server is running: `php artisan serve`
   - Check if the port (8000) is correct

2. **Authentication Failures**
   - Verify the test user credentials
   - Check if the API uses different authentication middleware

3. **403 Forbidden on Merchant Routes**
   - The test user might not have merchant role
   - Change role to 2 (merchant) in test data

4. **404 Not Found**
   - Verify routes are properly registered
   - Check if API prefix is correct

### Debug Mode

Run with verbose output to see detailed request/response information:

```bash
php test_api_debug.php --verbose --endpoint=auth
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add new test cases or improve existing ones
4. Test thoroughly
5. Submit a pull request

## License

This script is provided as-is for testing Laravel APIs. Modify and distribute freely.

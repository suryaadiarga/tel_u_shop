<?php
// Simple PHP development server test without curl - using stream context
$url = 'http://localhost:8000/api';
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/json\r\nAccept: application/json\r\n"
    ]
];

echo "Testing with stream context to: $url\n";
$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "Stream context failed\n";
    echo "Checking if server listening on ports...\n";
    exec('netstat -ano | findstr :8000', $netstat_output);
    echo implode("\n", $netstat_output) . "\n";
} else {
    echo "Response:\n";
    var_dump($response);
}

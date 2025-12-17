<?php

require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';

echo "✓ App bootstrapped successfully\n";

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';port=' . env('DB_PORT', 3306) . ';dbname=' . env('DB_DATABASE', 'tel_u_shop'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', ''),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Database connection successful\n";

    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Found " . count($tables) . " tables\n";
    echo "  Tables: " . implode(", ", array_slice($tables, 0, 5)) . "...\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "  DB_HOST: " . env('DB_HOST') . "\n";
    echo "  DB_DATABASE: " . env('DB_DATABASE') . "\n";
}

echo "\n✓ Setup check complete. You can now start the server with: php artisan serve\n";

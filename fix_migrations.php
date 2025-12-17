<?php
$host = getenv('DB_HOST') ?: '192.168.1.32';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'tel_u_shop';
$user = getenv('DB_USERNAME') ?: 'telushop';
$pass = getenv('DB_PASSWORD') ?: 'telushop12345';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Mark sessions migration as already run
    $stmt = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (:migration, :batch) ON DUPLICATE KEY UPDATE batch = batch');
    $stmt->execute(['migration' => '0001_01_01_000003_create_sessions_table', 'batch' => 1]);
    echo "Marked sessions migration as run\n";

    // Now run other migrations
    exec('php artisan migrate --force 2>&1', $output);
    echo implode("\n", $output) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

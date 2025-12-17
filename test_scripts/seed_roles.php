<?php
$host = getenv('DB_HOST') ?: '192.168.1.32';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'tel_u_shop';
$user = getenv('DB_USERNAME') ?: 'telushop';
$pass = getenv('DB_PASSWORD') ?: 'telushop12345';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $roles = [
        ['id' => 1, 'name' => 'admin', 'display_name' => 'Administrator'],
        ['id' => 2, 'name' => 'merchant', 'display_name' => 'Merchant'],
        ['id' => 3, 'name' => 'customer', 'display_name' => 'Customer'],
    ];
    foreach ($roles as $r) {
        $stmt = $pdo->prepare('INSERT INTO roles (id, name, display_name, created_at, updated_at) VALUES (:id, :name, :display_name, NOW(), NOW()) ON DUPLICATE KEY UPDATE name = name');
        $stmt->execute(['id' => $r['id'], 'name' => $r['name'], 'display_name' => $r['display_name']]);
        echo "Upserted role: {$r['name']}\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

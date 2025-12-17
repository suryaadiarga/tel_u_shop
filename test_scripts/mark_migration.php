<?php
// mark_migration.php - helper to mark a migration as run in the migrations table
$host = getenv('DB_HOST') ?: '192.168.1.32';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'tel_u_shop';
$user = getenv('DB_USERNAME') ?: 'telushop';
$pass = getenv('DB_PASSWORD') ?: 'telushop12345';
$migration = $argv[1] ?? '0001_01_01_000003_create_sessions_table';
$batch = intval($argv[2] ?? 1);

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)');
    $stmt->execute(['migration' => $migration, 'batch' => $batch]);
    echo "Inserted migration: $migration (batch $batch)\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

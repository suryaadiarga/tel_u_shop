<?php
try {
    $pdo = new PDO('mysql:host=192.168.1.32;port=3306;dbname=tel_u_shop', 'telushop', 'telushop12345', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "PDO_OK\n";
} catch (Exception $e) {
    echo "PDO_ERR: " . $e->getMessage() . "\n";
}

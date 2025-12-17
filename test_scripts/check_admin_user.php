<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::where('email', 'suryaadiarga@student.telkomuniversity.ac.id')->first();

    if ($user) {
        echo "User found:\n";
        echo "Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Role ID: " . $user->role_id . "\n";
        echo "Password Hash: " . substr($user->password, 0, 20) . "...\n";

        if ($user->role_id == 1) {
            echo "Role: Admin ✓\n";
        } else {
            echo "Role: Not Admin ✗\n";
        }

        if (Hash::check('12345', $user->password)) {
            echo "Password: Correct ✓\n";
        } else {
            echo "Password: Incorrect ✗\n";
        }
    } else {
        echo "User not found ✗\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Checking users in database:\n\n";

$emails = [
    'admin@koperasi.test',
    'merchant@koperasi.test',
    'customer@koperasi.test'
];

foreach ($emails as $email) {
    $user = User::where('email', $email)->first();

    if ($user) {
        echo "✅ User found: $email\n";
        echo "   ID: {$user->id}\n";
        echo "   Name: {$user->name}\n";
        echo "   Role ID: {$user->role_id}\n";
        echo "   Password hash starts with: " . substr($user->password, 0, 20) . "...\n";

        // Test password
        $testPassword = 'password';
        if (Hash::check($testPassword, $user->password)) {
            echo "   ✅ Password 'password' is correct\n";
        } else {
            echo "   ❌ Password 'password' is incorrect\n";
        }
    } else {
        echo "❌ User not found: $email\n";
    }

    echo "\n";
}

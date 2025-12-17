<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $user = User::where('email', 'suryaadiarga@student.telkomuniversity.ac.id')->first();

    if ($user) {
        $user->password = Hash::make('12345');
        $user->save();
        echo "Admin password updated successfully.\n";
    } else {
        echo "Admin user not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

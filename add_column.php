<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('products', function (Blueprint $table) {
    $table->unsignedBigInteger('user_id')->nullable()->after('id');
    $table->foreign('user_id')->references('id')->on('users');
});

echo "Column added successfully\n";

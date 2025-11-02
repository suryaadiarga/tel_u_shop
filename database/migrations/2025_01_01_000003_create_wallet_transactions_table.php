<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->integer('amount'); // + topup, - belanja
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

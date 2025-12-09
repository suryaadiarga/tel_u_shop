<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel carts
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            // 1 user hanya boleh punya 1 cart
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();
            $table->timestamps();
        });

        // Tabel cart_items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->timestamps();

            // 1 produk hanya boleh muncul sekali per cart
            $table->unique(['cart_id', 'product_id']);
        });
    }

    public function down(): void
    {
        // Drop foreign keys dulu biar aman rollback
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('cart_items');

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('carts');
    }
};
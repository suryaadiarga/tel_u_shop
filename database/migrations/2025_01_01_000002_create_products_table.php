<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->unsignedInteger('price'); // dalam rupiah
            $t->string('image_url')->nullable();
            $t->decimal('rating', 3, 1)->default(4.5);
            $t->unsignedInteger('reviews_count')->default(0);
            $t->string('category')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

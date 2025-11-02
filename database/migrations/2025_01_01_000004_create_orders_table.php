<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('status')->default('Selesai');
            $t->integer('total')->default(0);
            $t->timestamp('placed_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};

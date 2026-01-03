<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('prep_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', callback: function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }
};
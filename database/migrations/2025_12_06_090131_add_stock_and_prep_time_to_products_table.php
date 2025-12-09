<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Tambahkan kolom prep_time jika belum ada
            if (!Schema::hasColumn('products', 'prep_time')) {
                $table->unsignedInteger('prep_time')->default(0)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'prep_time')) {
                $table->dropColumn('prep_time');
            }
        });
    }
};
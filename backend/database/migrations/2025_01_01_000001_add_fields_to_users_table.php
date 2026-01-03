<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('nim')->nullable();
            $t->string('kelas')->nullable();
            $t->string('phone')->nullable();
            $t->string('avatar_url')->nullable();
            $t->decimal('ewallet_balance', 14, 2)->default(0);
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['nim', 'kelas', 'phone', 'avatar_url', 'ewallet_balance']);
        });
    }
};

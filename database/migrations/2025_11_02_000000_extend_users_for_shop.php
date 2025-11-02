<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'nim'))
                $t->string('nim', 50)->nullable()->after('email');
            if (!Schema::hasColumn('users', 'kelas'))
                $t->string('kelas', 50)->nullable()->after('nim');
            if (!Schema::hasColumn('users', 'phone'))
                $t->string('phone', 50)->nullable()->after('kelas');
            if (!Schema::hasColumn('users', 'avatar_url'))
                $t->string('avatar_url', 255)->nullable()->after('phone');
            if (!Schema::hasColumn('users', 'ewallet_balance'))
                $t->unsignedBigInteger('ewallet_balance')->default(0)->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            foreach (['nim', 'kelas', 'phone', 'avatar_url', 'ewallet_balance'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};

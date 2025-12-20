<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // USERS
        Schema::table('users', function (Blueprint $table) use ($driver) {
            if (!Schema::hasColumn('users', 'wallet_balance') && Schema::hasColumn('users', 'ewallet_balance')) {
                if ($driver === 'sqlite') {
                    $table->decimal('wallet_balance', 12, 2)->default(0)->after('avatar_url');
                } else {
                    DB::statement("ALTER TABLE users CHANGE ewallet_balance wallet_balance DECIMAL(12,2) NOT NULL DEFAULT 0");
                }
            } elseif (!Schema::hasColumn('users', 'wallet_balance')) {
                $table->decimal('wallet_balance', 12, 2)->default(0)->after('avatar_url');
            }

            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('role_id');
            }
        });

        if ($driver === 'sqlite' && Schema::hasColumn('users', 'ewallet_balance') && Schema::hasColumn('users', 'wallet_balance')) {
            DB::table('users')->update([
                'wallet_balance' => DB::raw('ewallet_balance'),
            ]);
        }

        // ORDERS
        if (Schema::hasTable('orders')) {
            if (Schema::hasColumn('orders', 'total') && !Schema::hasColumn('orders', 'total_amount')) {
                if ($driver === 'sqlite') {
                    Schema::table('orders', function (Blueprint $table) use ($driver) {
                        $table->decimal('total_amount', 12, 2)->default(0)->after('user_id');
                    });
                } else {
                    DB::statement("ALTER TABLE orders CHANGE total total_amount DECIMAL(12,2) NOT NULL DEFAULT 0");
                }
            } elseif (!Schema::hasColumn('orders', 'total_amount')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->decimal('total_amount', 12, 2)->default(0)->after('user_id');
                });
            }
        }

        if ($driver === 'sqlite' && Schema::hasColumn('orders', 'total') && Schema::hasColumn('orders', 'total_amount')) {
            DB::table('orders')->update([
                'total_amount' => DB::raw('total'),
            ]);
        }

        // PRODUCTS
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'merchant_id')) {
                $table->foreignId('merchant_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        // WALLET_TRANSACTIONS
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'type')) {
                $table->string('type')->nullable()->after('title')->index();
            }
            if (!Schema::hasColumn('wallet_transactions', 'description')) {
                $table->text('description')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        // Revert changes (opsional)
    }
};

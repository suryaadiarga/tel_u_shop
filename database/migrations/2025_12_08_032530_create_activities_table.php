<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel activities untuk mencatat aktivitas user.
     */
    public function up(): void
    {
        // Jika tabel sudah ada (mis. dibuat manual/oleh migrasi lain), jangan buat ulang.
        if (Schema::hasTable('activities')) {
            return;
        }

        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Jenis aktivitas (mis: login, checkout, topup, qr_scan)
            $table->string('type')->index();

            // Deskripsi bebas
            $table->text('description')->nullable();

            // Metadata tambahan (JSON), contoh: { "order_id": 123, "ip": "1.2.3.4" }
            $table->json('meta')->nullable();

            // Waktu kejadian aktivitas (opsional, selain timestamps default)
            $table->timestamp('occurred_at')->nullable();

            // Timestamps default (created_at, updated_at)
            $table->timestamps();

            // Index gabungan untuk query cepat per-user dan tipe
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Drop tabel activities.
     */
    public function down(): void
    {
        // Hanya drop jika memang ada (aman pada berbagai environment)
        if (Schema::hasTable('activities')) {
            Schema::dropIfExists('activities');
        }
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('makanan', function (Blueprint $table){
            $table->id('id')->primary();
            $table->string('nama');
            $table->string('jenis');
            $table->integer('harga')->nullable();
        });

        Schema::create('minuman', function (Blueprint $table){
            $table->id('id')->primary();
            $table->string('nama');
            $table->string('jenis');
            $table->integer('harga')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makanan');
        Schema::dropIfExists('minuman');
    }
};

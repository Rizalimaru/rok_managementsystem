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
        Schema::create('kingdoms', function (Blueprint $table) {
            $table->id();
            $table->string('server_number')->unique(); // Contoh: "1920"
            $table->string('name')->nullable();
            // Harga per 1M Resource (Dalam Rupiah)
            $table->decimal('price_food', 12, 2)->default(0);
            $table->decimal('price_wood', 12, 2)->default(0);
            $table->decimal('price_stone', 12, 2)->default(0);
            $table->decimal('price_gold', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kingdoms');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('kingdom_id')->constrained('kingdoms');
            
            $table->enum('resource_type', ['food', 'wood', 'stone', 'gold']);
            $table->unsignedBigInteger('amount'); // Jumlah total order
            $table->decimal('total_price', 15, 2); // Harga Deal
            
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('scheduled_at')->nullable(); // Jika null = kerjakan sekarang
            
            $table->foreignId('created_by')->constrained('users'); // Siapa admin yang buat order
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

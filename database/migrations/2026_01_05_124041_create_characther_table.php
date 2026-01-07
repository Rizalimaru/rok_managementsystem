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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_account_id')->constrained('game_accounts')->cascadeOnDelete();
            $table->foreignId('kingdom_id')->constrained('kingdoms');
            $table->string('ign'); // In Game Name
            $table->string('governor_id')->nullable(); // ID Angka (Opsional tapi berguna)
            
            // Resource Stocks (Pakai BigInteger karena resource bisa Milyaran)
            $table->unsignedBigInteger('food')->default(0);
            $table->unsignedBigInteger('wood')->default(0);
            $table->unsignedBigInteger('stone')->default(0);
            $table->unsignedBigInteger('gold')->default(0);
            
            $table->timestamp('last_updated_at')->nullable(); // Untuk tracking kapan terakhir update manual
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characther');
    }
};

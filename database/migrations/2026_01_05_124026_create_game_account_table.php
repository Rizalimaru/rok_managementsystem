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
        Schema::create('game_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username'); // Email atau User ID login
            $table->string('password')->nullable(); // Bisa nullable jika login via medsos
            $table->string('login_method')->default('email'); // google, email, facebook
            $table->string('status')->default('active'); // active, banned, maintenance
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Menangani deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_account');
    }
};

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
        // 1. Tabel Tugas (Siapa pegang karakter apa)
        Schema::create('order_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Admin yg mengerjakan
            $table->foreignId('character_id')->constrained('characters'); // Akun yg dipakai
            $table->timestamps();
        });

        // 2. Tabel Log Harian (Laporan per sesi kirim)
        Schema::create('task_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_task_id')->constrained('order_tasks')->cascadeOnDelete();
            $table->decimal('amount_sent', 10, 2); // Input misal 2.0 (artinya 2M)
            $table->string('proof_image')->nullable(); // Opsional: Screenshot bukti
            $table->timestamps();
        });

        // 3. Update tabel order_items untuk simpan progress real-time
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('amount_filled', 15, 2)->default(0); // Berapa yang sudah terkirim
            $table->boolean('is_completed')->default(false);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_processing_tables');
    }
};

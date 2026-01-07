<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel baru untuk rincian barang
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('resource_type', ['food', 'wood', 'stone', 'gold']);
            $table->unsignedBigInteger('amount'); // Jumlah resource
            $table->decimal('subtotal_price', 15, 2); // Harga per baris ini
            $table->timestamps();
        });

        // 2. Hapus kolom resource_type & amount dari tabel orders (karena sudah dipindah ke items)
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['resource_type', 'amount']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

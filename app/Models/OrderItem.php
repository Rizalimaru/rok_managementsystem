<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'resource_type', 'amount', 'subtotal_price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // TAMBAHKAN KODE INI
    protected static function booted(): void
    {
        static::creating(function ($item) {
            // Jika subtotal masih kosong/null saat mau disimpan
            if (empty($item->subtotal_price)) {
                
                // Ambil data Order & Kingdom terkait
                $order = $item->order ?? \App\Models\Order::find($item->order_id);
                
                if ($order && $kingdom = $order->kingdom) {
                    // Tentukan harga satuan
                    $pricePer1M = match ($item->resource_type) {
                        'food' => $kingdom->price_food,
                        'wood' => $kingdom->price_wood,
                        'stone' => $kingdom->price_stone,
                        'gold' => $kingdom->price_gold,
                        default => 0,
                    };

                    // Hitung dan isi subtotal otomatis
                    $item->subtotal_price = ($item->amount / 1000000) * $pricePer1M;
                }
            }
        });
    }

    public function tasks()
    {
        return $this->hasMany(OrderTask::class);
    }
}
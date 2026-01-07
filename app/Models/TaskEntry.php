<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskEntry extends Model
{
    protected $fillable = ['order_task_id', 'amount_sent'];

    public function task()
    {
        // PERBAIKAN: Tambahkan 'order_task_id' sebagai parameter kedua
        return $this->belongsTo(OrderTask::class, 'order_task_id');
    }
    
    // Logika Otomatis: Saat entry dibuat, kurangi stok karakter & update progress order
    protected static function booted(): void
    {
        static::created(function ($entry) {
            // Karena relasi sudah diperbaiki, $entry->task sekarang akan menemukan datanya
            $task = $entry->task; 

            // Pengecekan keamanan tambahan (Jaga-jaga jika task terhapus)
            if (!$task) return; 

            $orderItem = $task->orderItem;
            $character = $task->character;

            if ($orderItem) {
                // 1. Update Progress di Order Item
                $orderItem->amount_filled += $entry->amount_sent * 1000000; // Konversi ke angka asli
                $orderItem->save();

                // 2. Kurangi Stok Karakter (Inventory)
                $colName = $orderItem->resource_type; // food, wood, dll
                
                if ($character) {
                    // Kurangi stok (misal 2.000.000)
                    $deduction = $entry->amount_sent * 1000000;
                    $character->$colName = max(0, $character->$colName - $deduction);
                    $character->save();
                }
            }
        });
    }
}
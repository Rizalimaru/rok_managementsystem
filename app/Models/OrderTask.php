<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTask extends Model
{
    protected $fillable = ['order_item_id', 'user_id', 'character_id'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function entries()
    {
        return $this->hasMany(TaskEntry::class);
    }
    
    // Hitung total yg dikirim oleh tugas ini saja
    public function getTotalSentAttribute()
    {
        return $this->entries()->sum('amount_sent');
    }
}
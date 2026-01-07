<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAllocation extends Model
{
    protected $fillable = [
        'order_id', 'character_id', 
        'target_amount', 'sent_amount', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kingdom extends Model
{
    protected $fillable = [
        'server_number', 'name', 
        'price_food', 'price_wood', 'price_stone', 'price_gold'
    ];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
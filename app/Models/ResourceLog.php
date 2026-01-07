<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceLog extends Model
{
    protected $fillable = [
        'character_id', 'user_id', 
        'type', 'amount_change', 'description'
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
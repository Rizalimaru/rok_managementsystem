<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'username', 'password', 'login_method', 'status', 'notes'
    ];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }
}
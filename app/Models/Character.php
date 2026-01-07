<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Character extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'game_account_id', 'kingdom_id', 'ign', 'governor_id',
        'food', 'wood', 'stone', 'gold', 'last_updated_at'
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    public function gameAccount()
    {
        return $this->belongsTo(GameAccount::class);
    }

    public function kingdom()
    {
        return $this->belongsTo(Kingdom::class);
    }

    public function orderAllocations()
    {
        return $this->hasMany(OrderAllocation::class);
    }

    public function resourceLogs()
    {
        return $this->hasMany(ResourceLog::class);
    }
}
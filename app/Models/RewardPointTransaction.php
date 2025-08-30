<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardPointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',      // earn|redeem
        'points',    // integer (+ for earn, - for redeem)
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

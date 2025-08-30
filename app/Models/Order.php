<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',           // pending|paid|shipped|completed|cancelled
        'total_price',      // decimal(12,2)
        'shipping_address', // string
        'payment_method',   // bank_transfer|ewallet|cod (string)
    ];

    public function getEarnedPointsAttribute(): int
    {
        $ok = in_array($this->status, config('ecomart.pointable_status', []));
        if (!$ok) return 0;

        $rate = (float) config('ecomart.points_per_rupiah', 0);
        return (int) floor($this->total_price * $rate);
    }
    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

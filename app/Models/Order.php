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

    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',              // pending|paid|shipped|completed|cancelled|delivered
        'total_price',         // decimal(12,2) atau integer (rupiah) — sesuaikan DB
        'shipping_address',
        'payment_method',      // bank_transfer|ewallet|cod
        // ===== untuk fitur redeem poin (pastikan kolom ini ada di migration) =====
        'discount_points',     // int
        'discount_amount',     // int (rupiah)
    ];

    protected $casts = [
        // Catatan: cast 'decimal:2' menghasilkan STRING.
        // Kalau di DB kamu simpan rupiah sebagai integer, ganti jadi 'integer'.
        'total_price'     => 'float',
        'discount_points' => 'integer',
        'discount_amount' => 'integer',
    ];

    /** Relasi */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Apakah order ini berhak menghasilkan poin? */
    public function getIsPointableAttribute(): bool
    {
        $pointable = config('ecomart.pointable_status', ['paid','completed','delivered']);
        $min = (int) config('ecomart.min_total_price_for_points', 0);

        // bandingkan status secara case-insensitive + cek minimal total
        return in_array(strtolower((string) $this->status), $pointable, true)
            && (int) floor($this->total_price) >= $min;
    }

    /** Poin yang dihasilkan dari order ini (on the fly) */
    public function getEarnedPointsAttribute(): int
    {
        if (! $this->is_pointable) return 0;

        $rate = (float) config('ecomart.points_per_rupiah', 1/10000); // default 1 poin / Rp10.000
        // total_price bisa float/string tergantung cast—pakai floor biar aman
        return (int) floor(((float) $this->total_price) * $rate);
    }

    /** Scope: ambil order yang berhak menghasilkan poin */
    public function scopePointable($query)
    {
        $pointable = config('ecomart.pointable_status', ['paid','completed','delivered']);
        $min = (int) config('ecomart.min_total_price_for_points', 0);

        return $query->whereIn('status', $pointable)
                     ->where('total_price', '>=', $min);
    }
}

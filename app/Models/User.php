<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'avatar',
        'phone',
        'address',
        // 'role', // aktifkan jika kamu pakai mass-assign role
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        // pastikan kolom ini ada di DB (kamu sudah punya migration add_referral_points_to_users_table)
        'referral_points'   => 'integer',
    ];

    /** Role helper */
    public function isAdmin(): bool
    {
        return ($this->role ?? null) === 'admin';
    }

    /** Relasi bawaan */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    /** Relasi transaksi poin (baru) */
    public function rewardPointTransactions()
    {
        return $this->hasMany(RewardPointTransaction::class);
    }

    /** Helper wishlist */
    public function hasInWishlist(int $productId): bool
    {
        return $this->wishlists()->where('product_id', $productId)->exists();
    }

    public function toggleWishlist(int $productId): bool
    {
        $row = $this->wishlists()->where('product_id', $productId)->first();
        if ($row) { $row->delete(); return false; }
        $this->wishlists()->create(['product_id' => $productId]);
        return true;
    }

    /* ==========================
     |   ACCESSOR POIN (baru)
     |==========================*/

    // Poin dari order yang berhak poin (lihat scope & accessor di Order)
    public function getPurchasePointsAttribute(): int
    {
        return $this->orders()->pointable()->get()->sum(fn($o) => $o->earned_points);
    }

    // Poin referral dari kolom users.referral_points (default 0 bila null)
    public function getReferralPointsAttribute(): int
    {
        return (int) ($this->attributes['referral_points'] ?? 0);
    }

    // Akumulasi dari tabel reward_point_transactions (redeem negatif, bonus/adjust bisa +/−)
    public function getTransactionPointsAttribute(): int
    {
        // gunakan aggregate langsung untuk efisiensi
        return (int) $this->rewardPointTransactions()->sum('points');
    }

    // Total poin yang bisa dipakai saat checkout
    public function getAvailablePointsAttribute(): int
    {
        $total = $this->purchase_points + $this->referral_points + $this->transaction_points;
        return max(0, (int) $total);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <— penting
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'avatar',    // opsional: kalau kolom ada
        'phone',     // opsional
        'address',   // opsional
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // RELASI
    public function wishlists()
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function testimonials()
    {
        return $this->hasMany(\App\Models\Testimonial::class);
    }

    // HELPER: cek product ada di wishlist
    public function hasInWishlist(int $productId): bool
    {
        return $this->wishlists()->where('product_id', $productId)->exists();
    }

    // HELPER: toggle wishlist (opsional)
    public function toggleWishlist(int $productId): bool
    {
        $row = $this->wishlists()->where('product_id', $productId)->first();
        if ($row) { $row->delete(); return false; }
        $this->wishlists()->create(['product_id' => $productId]);
        return true;
    }
}
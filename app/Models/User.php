<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',   // kalau ada
        'phone',    // opsional: hanya kalau kolomnya ada
        'address',  // opsional: hanya kalau kolomnya ada
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi yang kamu pakai di ProfileController
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
}
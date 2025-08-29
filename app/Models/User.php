<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import model-model yang akan direlasikan
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\Testimonial;
use App\Models\Product; // <-- Tambahkan import untuk Product

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar', // Pastikan avatar bisa diisi
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI ---

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

    // --- FUNGSI PEMBANTU (HELPER) ---

    /**
     * Mengecek apakah produk tertentu sudah ada di wishlist pengguna.
     * Fungsi ini sekarang bisa menerima objek Product atau integer (ID produk).
     */
    public function hasInWishlist($product): bool
    {
        // Dapatkan ID produk, baik dari objek maupun integer
        $productId = $product instanceof Product ? $product->id : $product;

        // Cek di dalam relasi wishlists apakah ada entri
        // yang product_id-nya cocok dengan ID produk yang diberikan.
        return $this->wishlists()->where('product_id', $productId)->exists();
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Wishlist;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cartCount = 0;       // total quantity di cart
            $wishlistCount = 0;   // jumlah produk di wishlist

            if (Auth::check()) {
                $cart = Cart::firstOrCreate(['user_id' => Auth::id()])->load('items');
                $cartCount = $cart->items->sum('quantity');
                $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
            }

            $view->with(compact('cartCount', 'wishlistCount'));
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;

class WishlistController extends Controller
{
    protected function currentUser(): User
    {
        return Auth::user() ?? User::firstOrCreate(
            ['email' => 'guest@ecomart.local'],
            ['name' => 'Guest', 'password' => bcrypt(str()->random(12))]
        );
    }

    public function index(Request $request)
    {
        $user = $this->currentUser();
        $sort = $request->get('sort', 'newest');

        $query = Product::select('products.*')
            ->join('wishlists', 'wishlists.product_id', '=', 'products.id')
            ->where('wishlists.user_id', $user->id);

        match ($sort) {
            'price-asc'  => $query->orderBy('products.price', 'asc'),
            'price-desc' => $query->orderBy('products.price', 'desc'),
            'eco-desc'   => $query->orderBy('products.eco_score', 'desc'),
            default      => $query->orderBy('wishlists.created_at', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();
        return view('wishlist', compact('products', 'sort'));
    }

    public function toggle(Product $product)
    {
        $user = $this->currentUser();
        $exists = $user->wishlists()->where('product_id', $product->id)->exists();

        if ($exists) {
            $user->wishlists()->where('product_id', $product->id)->delete();
            $state = 'removed';
        } else {
            $user->wishlists()->create(['product_id' => $product->id]);
            $state = 'added';
        }

        return response()->json([
            'ok'             => true,
            'state'          => $state,
            'product_id'     => $product->id,
            'wishlist_count' => $user->wishlists()->count(),
            'badge_total'    => ($user->wishlists()->count() + session('cart_count', 0)),
        ]);
    }

    public function bulkRemove(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $user = $this->currentUser();
        $user->wishlists()->whereIn('product_id', $request->ids)->delete();
        return back()->with('success', 'Selected items removed.');
    }

    public function bulkAddToCart(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $user = $this->currentUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        DB::transaction(function () use ($cart, $request) {
            foreach ($request->ids as $pid) {
                $item = CartItem::firstOrNew(['cart_id' => $cart->id, 'product_id' => $pid]);
                $item->quantity = ($item->exists ? $item->quantity : 0) + 1;
                $item->save();
            }
        });

        return back()->with('success', 'Dipindahkan ke cart.');
    }
}
    
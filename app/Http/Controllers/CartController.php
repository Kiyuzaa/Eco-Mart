<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    protected function currentCart(): Cart
    {
        $userId = Auth::id();
        if (!$userId) {
            // guest user (fallback)
            $guest = \App\Models\User::firstOrCreate(
                ['email' => 'guest@ecomart.local'],
                ['name' => 'Guest', 'password' => bcrypt(str()->random(12))]
            );
            $userId = $guest->id;
        }
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function index()
    {
        $cart  = $this->currentCart()->load('items.product.category');
        $items = $cart->items;

        $subtotal = $items->sum(fn($i) => (float) ($i->product->price ?? 0) * (int) $i->quantity);

        // === Seragamkan dengan Checkout: default 15.000
        $shipping = $subtotal > 0 ? 15000 : 0;

        // Hormati kupon FREESHIP di cart
        if (session('cart_free_shipping')) {
            $shipping = 0;
        }

        $discount = (int) session('cart_discount', 0);
        $total    = max(0, $subtotal + $shipping - $discount);

        $recommend = Product::with('category')->inRandomOrder()->take(4)->get();

        return view('cart', compact('items','subtotal','shipping','discount','total','recommend'));
    }

    public function applyCode(Request $request)
    {
        $request->validate(['code' => 'nullable|string|max:32']);
        $code = strtoupper(trim($request->code ?? ''));

        // reset flag freeship dulu
        session()->forget('cart_free_shipping');
        $discount = 0;

        if ($code === 'ECO10') {
            $discount = 10000;
        } elseif ($code === 'FREESHIP') {
            session(['cart_free_shipping' => true]);
        } else {
            session()->forget(['cart_discount','cart_code','cart_free_shipping']);
            return back()->with('success','Kode tidak valid.');
        }

        session(['cart_discount' => $discount,'cart_code'=>$code]);
        return back()->with('success','Kode berhasil diterapkan.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'quantity'   => ['nullable','integer','min:1'],
        ]);

        $cart = $this->currentCart();
        $qty  = (int) ($data['quantity'] ?? 1);

        $item = CartItem::where('cart_id',$cart->id)->where('product_id',$data['product_id'])->first();

        if ($item) {
            $item->increment('quantity',$qty);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $data['product_id'],
                'quantity'   => $qty,
            ]);
        }

        return redirect()->route('cart.index')->with('success','Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $item)
    {
        if ($item->cart_id !== $this->currentCart()->id) abort(403);

        $action = $request->input('action','set');

        if ($action==='inc') {
            $item->increment('quantity',1);
        } elseif ($action==='dec') {
            if ($item->quantity > 1) $item->decrement('quantity',1);
        } else {
            $data = $request->validate(['quantity'=>['required','integer','min:1']]);
            $item->update(['quantity'=>(int)$data['quantity']]);
        }

        return back()->with('success','Jumlah item diperbarui.');
    }

    public function destroy(CartItem $item)
    {
        if ($item->cart_id !== $this->currentCart()->id) abort(403);
        $item->delete();
        return back()->with('success','Item dihapus dari keranjang.');
    }
}

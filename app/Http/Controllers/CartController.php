<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    protected function currentCart()
{
    $userId = Auth::id();

    if (!$userId) {
        // pakai / buat akun guest satu kali
        $guest = User::firstOrCreate(
            ['email' => 'guest@ecomart.local'],
            ['name' => 'Guest', 'password' => bcrypt(str()->random(12))]
        );
        $userId = $guest->id;
    }

    return Cart::firstOrCreate(['user_id' => $userId]);
}

    public function index()
    {
        $cart = $this->currentCart()->load('items.product.category');
        $items = $cart->items;

        $subtotal = $items->sum(fn($i) => ($i->product->price ?? 0) * $i->quantity);
        $shipping = $subtotal > 0 ? 8990 : 0;
        if (session('cart_free_shipping')) { $shipping = 0; }
        $discount = session('cart_discount', 0);
        $total = max(0, $subtotal + $shipping - $discount);

        $recommend = Product::with('category')->inRandomOrder()->take(4)->get();

        return view('cart', compact('items','subtotal','shipping','discount','total','recommend'));
    }

    public function applyCode(Request $request)
    {
        $request->validate(['code' => 'nullable|string|max:32']);
        $code = strtoupper(trim($request->code ?? ''));

        session()->forget('cart_free_shipping');

        $discount = 0;
        if ($code === 'ECO10') {
            $discount = 10000;
        } elseif ($code === 'FREESHIP') {
            session(['cart_free_shipping' => true]);
        } else {
            session()->forget(['cart_discount','cart_code','cart_free_shipping']);
            return back()->with('success', 'Invalid code');
        }

        session(['cart_discount' => $discount, 'cart_code' => $code]);

        return back()->with('success', 'Discount applied');
    }
    public function store(Request $request)
{
    $data = $request->validate([
        'product_id' => ['required','integer','exists:products,id'],
        'quantity'   => ['nullable','integer','min:1'],
    ]);

    $cart = $this->currentCart();
    $qty  = $data['quantity'] ?? 1;

    // Cek apakah item sudah ada di cart
    $item = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $data['product_id'])
        ->first();

    if ($item) {
        $item->increment('quantity', $qty);
    } else {
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $data['product_id'],
            'quantity'   => $qty,
        ]);
    }

    return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
}

public function update(Request $request, CartItem $item)
{
    $data = $request->validate([
        'quantity' => ['required','integer','min:1'],
    ]);

    // optional: pastikan item milik cart user saat ini
    if ($item->cart_id !== $this->currentCart()->id) {
        abort(403);
    }

    $item->update(['quantity' => $data['quantity']]);

    return back()->with('success', 'Jumlah item diperbarui.');
}

public function destroy(CartItem $item)
{
    if ($item->cart_id !== $this->currentCart()->id) {
        abort(403);
    }

    $item->delete();

    return back()->with('success', 'Item dihapus dari keranjang.');
}

}

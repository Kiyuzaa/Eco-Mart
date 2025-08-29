<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\{Order, OrderItem, Cart};

class CheckoutController extends Controller
{
    protected function currentCart(): Cart
    {
        $userId = Auth::id();
        if (!$userId) {
            // fallback guest (sesuaikan kalau kamu sudah pakai guest account)
            $guest = \App\Models\User::firstOrCreate(
                ['email' => 'guest@ecomart.local'],
                ['name' => 'Guest', 'password' => bcrypt(Str::random(12))]
            );
            $userId = $guest->id;
        }
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function show(Request $request)
    {
        $cart = $this->currentCart()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        // Hitung subtotal
        $subtotal = $cart->items->sum(function($i){
            return (float)($i->product->price ?? 0) * (int)$i->quantity;
        });

        // Opsi UI (tidak disimpan ke DB kamu—kolomnya belum ada)
        $shippingCost = $request->get('shipping') === 'express' ? 25000 : 15000;
        $tax = (int) round($subtotal * 0.10);
        $total = $subtotal + $shippingCost + $tax;

        return view('checkout', compact('cart','subtotal','shippingCost','tax','total'));
    }

    public function placeOrder(Request $request)
    {
        $cart = $this->currentCart()->load('items.product');
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'shipping_address' => ['required','string','max:1000'],
            'payment_method'   => ['required','in:bank_transfer,ewallet,cod'],
            'shipping_method'  => ['nullable','in:regular,express'], // hanya untuk perhitungan
            'phone'            => ['nullable','string','max:30'], // opsional buat ditaruh di alamat
            'name'             => ['nullable','string','max:120'], // opsional buat ditaruh di alamat
        ]);

        // Hitung total (selaras dengan show)
        $subtotal = $cart->items->sum(fn($i) => (float)($i->product->price ?? 0) * (int)$i->quantity);
        $shippingCost = ($data['shipping_method'] ?? 'regular') === 'express' ? 25000 : 15000;
        $tax = (int) round($subtotal * 0.10);
        $grandTotal = $subtotal + $shippingCost + $tax;

        // Susun alamat final (boleh gabungkan nama/telepon agar praktis)
        $address = trim(($data['name'] ?? '')."\n".($data['phone'] ?? '')."\n".$data['shipping_address']);

        // Buat order (pakai kolom yang ada di migrasi kamu)
        $order = Order::create([
            'user_id'         => Auth::id(),
            'status'          => 'pending',
            'total_price'     => $grandTotal,     // hanya satu angka total sesuai skema
            'shipping_address'=> $address,
            'payment_method'  => $data['payment_method'],
        ]);

        // Buat order_items (snapshot harga & qty)
        foreach ($cart->items as $it) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $it->product_id,
                'quantity'   => (int)$it->quantity,
                'price'      => (float)($it->product->price ?? 0), // harga saat beli
            ]);
        }

        // Kosongkan cart
        $cart->items()->delete();

        return redirect()->route('orders.waiting', $order);
    }

    public function waiting(Order $order)
    {
        // (opsional) batasi agar hanya owner yang bisa lihat
        if (Auth::id() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403);
        }
        return view('order-waiting', compact('order'));
    }
}
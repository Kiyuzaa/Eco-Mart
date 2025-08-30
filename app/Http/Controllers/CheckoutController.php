<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\RewardPointTransaction;

class CheckoutController extends Controller
{
    /**
     * Ambil / buat cart milik user saat ini.
     */
    protected function currentCart(): Cart
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(403, 'Harus login untuk checkout');
        }
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Halaman Checkout (tampilkan ringkasan dan form).
     */
    public function show()
    {
        $user  = Auth::user();
        $cart  = $this->currentCart()->load('items.product');
        $items = $cart->items;

        // Hitung subtotal & pajak
        $subtotal = (int) $items->sum(
            fn($i) => (int) $i->quantity * (float) ($i->product->price ?? 0)
        );
        $tax = (int) round($subtotal * 0.11);

        // Ongkir default
        $shippingCost = 15000;

        // Hormati kupon FREESHIP di halaman checkout
        if (session('cart_free_shipping')) {
            $shippingCost = 0;
        }

        // Diskon kupon nominal (contoh ECO10 = 10.000)
        $sessionDiscount = (int) session('cart_discount', 0);

        $total = max(0, $subtotal + $tax + $shippingCost - $sessionDiscount);

        return view('checkout', compact(
            'user', 'cart', 'subtotal', 'tax', 'shippingCost', 'sessionDiscount', 'total'
        ))->with('discount', $sessionDiscount);
    }

    /**
     * Proses pembuatan order ketika user menekan "Bayar Sekarang".
     * Setelah sukses: set status -> waiting, catat redeem & earn points, kosongkan keranjang,
     * redirect ke halaman orders.waiting.
     */
    public function place(Request $request)
    {
        $user = Auth::user();
        $cart = $this->currentCart()->load('items.product');

        // Validasi form
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:30',
            'shipping_address' => 'required|string|max:1000',
            'city_ui'          => 'required|string|max:120',
            'postal_ui'        => ['required','regex:/^\d{5}$/'],
            'shipping_method'  => 'required|in:regular,express',
            'payment_method'   => 'required|in:bank_transfer,ewallet,cod',
            'redeem_points'    => 'nullable|integer|min:0',
        ]);

        if ($cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong.']);
        }

        // Hitung subtotal, pajak, ongkir (tergantung metode)
        $subtotal = (int) $cart->items->sum(
            fn($i) => (int) $i->quantity * (float) ($i->product->price ?? 0)
        );
        $tax = (int) round($subtotal * 0.11);
        $shippingCost = $data['shipping_method'] === 'express' ? 25000 : 15000;

        // Terapkan kupon (nominal) & freeship (nol-kan ongkir)
        $couponDiscount = (int) session('cart_discount', 0);
        if (session('cart_free_shipping')) {
            $shippingCost = 0;
        }

        // Aturan poin
        $inputPoints = (int) ($data['redeem_points'] ?? 0);
        $available   = (int) $user->available_points;

        $minRedeem  = (int) config('ecomart.points.min_redeem', 100);
        $conversion = (int) config('ecomart.points.conversion_value', 100); // 1 poin = Rp100
        $maxPct     = (int) config('ecomart.points.max_percentage_discount', 50); // max 50% dari base

        if ($inputPoints > 0) {
            if ($inputPoints < $minRedeem) {
                return back()->withErrors(['redeem_points' => "Minimal {$minRedeem} poin."]);
            }
            if ($inputPoints > $available) {
                return back()->withErrors(['redeem_points' => "Poin tidak cukup."]);
            }
        }

        // Base = subtotal + pajak + ongkir - diskon kupon nominal
        $base = max(0, $subtotal + $tax + $shippingCost - $couponDiscount);

        // Diskon poin dibatasi maxPct dari base
        $rawDiscount    = $inputPoints * $conversion;
        $maxPointDisc   = (int) floor($base * ($maxPct / 100));
        $pointsDiscount = min($rawDiscount, $maxPointDisc);

        // Grand total setelah diskon poin
        $grand = max(0, $base - $pointsDiscount);

        try {
            DB::beginTransaction();

            // Buat order -> langsung set 'waiting'
            $order = Order::create([
                'user_id'          => $user->id,
                'status'           => 'waiting', // langsung waiting/confirmed sesuai alurmu
                'total_price'      => $grand,
                'shipping_address' => sprintf(
                    "%s\nKota: %s\nKode Pos: %s",
                    $data['shipping_address'],
                    $data['city_ui'],
                    $data['postal_ui']
                ),
                'payment_method'   => $data['payment_method'],
                'discount_points'  => $inputPoints,
                'discount_amount'  => $pointsDiscount,
            ]);

            // Simpan item order
            foreach ($cart->items as $it) {
                $order->items()->create([
                    'product_id' => $it->product_id,
                    'quantity'   => $it->quantity,
                    'price'      => (int) round((float) ($it->product->price ?? 0)),
                ]);
            }

            // Catat transaksi poin jika dipakai (REDEEM = negatif)
            if ($inputPoints > 0 && $pointsDiscount > 0) {
                RewardPointTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'redeem',
                    'points'      => -$inputPoints,
                    'description' => "Redeem for Order #{$order->id}",
                ]);
            }

            // Tambah poin (EARN) dari pembelian: 1 poin per Rp {conversion}
            $earnPoints = (int) floor($grand / max(1, $conversion));
            if ($earnPoints > 0) {
                RewardPointTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'earn',
                    'points'      => $earnPoints,
                    'description' => "Earn from Order #{$order->id}",
                ]);
            }

            // Kosongkan keranjang & sesi kupon
            CartItem::where('cart_id', $cart->id)->delete();
            session()->forget(['cart_discount', 'cart_code', 'cart_free_shipping']);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['checkout' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }

        // Redirect ke halaman "menunggu"
        return redirect()
            ->route('orders.waiting', ['order' => $order->id])
            ->with('success', 'Pesanan dikonfirmasi. Poin didapat: ' . number_format($earnPoints ?? 0, 0, ',', '.')
                . ($pointsDiscount > 0 ? (' | Diskon poin: Rp ' . number_format($pointsDiscount, 0, ',', '.')) : '')
            );
    }

    /**
     * Halaman menunggu (order waiting) setelah checkout.
     */
    public function waiting(Order $order)
    {
        return view('orders.waiting', compact('order'));
    }
}

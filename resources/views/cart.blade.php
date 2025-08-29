@extends('layouts.app')

@section('title','Cart — EcoMart')

@section('content')
  {{-- Stepper tepat di bawah navbar --}}
  <x-checkout-steps current="cart" />

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-500 mb-6 mt-4">
    <ol class="flex items-center gap-2">
      <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
      <li>›</li>
      <li><a href="{{ route('product.index') }}" class="hover:underline">Shop</a></li>
      <li>›</li>
      <li class="text-gray-800 font-medium">Cart</li>
    </ol>
  </nav>

  <h1 class="text-2xl font-semibold mb-1">Shopping Cart</h1>
  <p class="text-gray-500 mb-6">Review your items before checkout</p>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- LEFT: Items --}}
    <div class="lg:col-span-2 space-y-4">
      @php $hasItems = isset($items) && count($items); @endphp

      @if($hasItems)
        @foreach($items as $item)
          @php
            $p = $item->product;
            $unit = (float)($p->price ?? 0);
            $qty  = (int)$item->quantity;
            $line = $unit * $qty;
          @endphp

          <div class="border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between gap-4">
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-md bg-gray-100 overflow-hidden flex items-center justify-center">
                  <img src="{{ $p->image ?? 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=800&auto=format&fit=crop' }}"
                       alt="{{ $p->name }}" class="object-cover w-full h-full">
                </div>
                <div>
                  <h4 class="font-medium text-gray-900">{{ $p->name }}</h4>
                  <p class="text-xs text-gray-500">Size: M, Color: Natural</p>
                  <p class="text-sm text-gray-600">
                    {{ $p->category?->name }} · ${{ number_format($unit,2) }}
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-4">
                {{-- qty controls --}}
                <div class="flex items-center gap-2">
                  {{-- Decrease --}}
                  <form action="{{ route('cart.update',$item) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="dec">
                    <button
                      class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-50 disabled:opacity-40"
                      title="Decrease" {{ $qty <= 1 ? 'disabled' : '' }}>−</button>
                  </form>

                  {{-- Set exact qty (auto submit on change) --}}
                  <form action="{{ route('cart.update',$item) }}" method="POST" class="w-12 qty-set-form">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="set">
                    <input name="quantity" type="number" min="1"
                           value="{{ $qty }}"
                           class="w-full h-8 text-center border rounded qty-input">
                  </form>

                  {{-- Increase --}}
                  <form action="{{ route('cart.update',$item) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="inc">
                    <button class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-50" title="Increase">+</button>
                  </form>
                </div>

                <div class="w-24 text-right font-semibold text-gray-900">
                  ${{ number_format($line,2) }}
                </div>

                {{-- Remove --}}
                <form action="{{ route('cart.remove',$item) }}" method="POST">
                  @csrf @method('DELETE')
                  <button class="text-gray-400 hover:text-red-600" title="Remove">
                    {{-- Heroicon: trash --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8a1 1 0 001-1V5a1 1 0 00-1-1h-3m-4 0H8a1 1 0 00-1 1v1"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      @else
        {{-- Empty state --}}
        <div class="bg-white border rounded-xl p-10 text-center text-gray-600">
          <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">🛒</div>
          <h3 class="font-semibold text-lg">Your cart is empty</h3>
          <p class="text-sm mt-1">Browse our eco-friendly products and start adding items.</p>
          <a href="{{ route('product.index') }}"
             class="inline-block mt-4 px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800">
            Go to Shop
          </a>
        </div>
      @endif
    </div>

    {{-- RIGHT: Summary (sticky) --}}
    <aside class="lg:sticky lg:top-20 border rounded-xl p-6 bg-white h-fit shadow-sm">
      <h3 class="text-lg font-semibold mb-4">Order Summary</h3>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span>${{ number_format($subtotal ?? 97.96,2) }}</span>
        </div>
        <div class="flex justify-between">
          <span>Shipping</span>
          <span>${{ number_format($shipping ?? 8.99,2) }}</span>
        </div>

        @if( isset($discount) && $discount>0 )
          <div class="flex justify-between text-green-600">
            <span>Discount ({{ session('cart_code') }})</span>
            <span>- ${{ number_format($discount,2) }}</span>
          </div>
        @endif
      </div>

      <form action="{{ route('cart.apply-code') }}" method="POST" class="mt-4 flex gap-2">
        @csrf
        <input type="text" name="code" placeholder="Enter code"
               value="{{ session('cart_code') }}"
               class="flex-1 border rounded px-3 py-2 text-sm">
        <button class="bg-gray-900 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">Apply</button>
      </form>

      <div class="flex justify-between items-center mt-4 pt-4 border-t">
        <span class="text-gray-600">Total</span>
        <span class="text-2xl font-semibold">
          ${{ number_format($total ?? (($subtotal ?? 97.96)+($shipping ?? 8.99)-($discount ?? 0)),2) }}
        </span>
      </div>

      {{-- Tombol diperbaiki --}}
      <a href="{{ route('checkout') }}"
         class="block w-full text-center bg-gray-900 text-white py-3 rounded-lg mt-4 hover:bg-gray-800">
        Proceed to Checkout
      </a>
      <a href={{ route('product.index') }}
         class="block text-center text-sm text-gray-600 hover:underline mt-2">
        Continue Shopping
      </a>
    </aside>
  </div>

  {{-- Recommendations --}}
  <section class="mt-12">
    <h3 class="text-lg font-semibold mb-6">You may also like</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      @foreach(($recommend ?? []) as $product)
        <x-product-card :product="$product"/>
      @endforeach

      @if(empty($recommend) || !count($recommend))
        @for($i=0;$i<4;$i++)
          <x-product-card :product="[
            'title' => 'Product',
            'category' => 'Eco',
            'price' => 0,
            'image' => 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f?q=80&w=800&auto=format&fit=crop'
          ]"/>
        @endfor
      @endif
    </div>
  </section>

  {{-- Auto-submit qty setter --}}
  <script>
    document.querySelectorAll('.qty-input').forEach(inp => {
      inp.addEventListener('change', (e) => {
        const v = Math.max(1, parseInt(e.target.value || '1', 10));
        e.target.value = v;
        e.target.closest('form').submit();
      });
    });
  </script>
@endsection

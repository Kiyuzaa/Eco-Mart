@props([
  // cart | checkout | order
  'current' => 'cart'
])

@php
  $is = fn($k) => $current === $k;
  $done = fn($k) => ['checkout'=>'cart','order'=>'checkout'][$current] ?? null === $k;
  $baseCircle = 'w-8 h-8 rounded-full grid place-items-center';
  $inactive  = $baseCircle.' bg-gray-300 text-white';
  $active    = $baseCircle.' bg-gray-900 text-white';
  $doneCls   = $baseCircle.' bg-gray-300 text-white'; // sesuai screenshot: abu-abu dgn ceklis
  $label     = 'text-sm sm:text-base text-gray-600';
  $sep       = 'hidden sm:block w-16 md:w-24 h-px bg-gray-300 mx-2';
@endphp

<div class="bg-gray-50 border-b">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-center gap-3 sm:gap-5 py-4">
      {{-- Keranjang --}}
      <div class="flex items-center gap-2">
        <div class="{{ $is('cart') ? $active : ($done('cart') ? $doneCls : $inactive) }}">
          {{-- heroicon: shopping-cart (solid) --}}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.26 0 .487.18.546.433l.38 1.616A3.75 3.75 0 008.25 8.25h7.513l-.477 2.386a.75.75 0 00.734.914H20.25a.75.75 0 000-1.5h-3.06l1.2-6A.75.75 0 0017.65 3H6.801l-.19-.808A2.25 2.25 0 003.636 1.5H2.25zM9 20.25a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm9 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
          </svg>
        </div>
        <span class="{{ $label }}">Keranjang</span>
      </div>

      <span class="{{ $sep }}"></span>

      {{-- Checkout --}}
      <div class="flex items-center gap-2">
        <div class="{{ $is('checkout') ? $active : ($done('checkout') ? $doneCls : $inactive) }}">
          {{-- heroicon: credit-card (solid) --}}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v.75H2.25v-.75zM2.25 9.75h19.5v7.5A2.25 2.25 0 0119.5 19.5h-15A2.25 2.25 0 012.25 17.25v-7.5z"/>
          </svg>
        </div>
        <span class="{{ $label }}">Checkout</span>
      </div>

      <span class="{{ $sep }}"></span>

      {{-- Pesanan --}}
      <div class="flex items-center gap-2">
        <div class="{{ $is('order') ? $active : $inactive }}">
          {{-- heroicon: check (solid) --}}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1119.5 0 9.75 9.75 0 01-19.5 0zm13.036-3.036a.75.75 0 10-1.061-1.061L10.5 11.627 9.025 10.15a.75.75 0 10-1.06 1.06l2.005 2.006a.75.75 0 001.061 0l4.255-4.252z" clip-rule="evenodd"/>
          </svg>
        </div>
        <span class="{{ $label }}">Pesanan</span>
      </div>
    </div>
  </div>
</div>

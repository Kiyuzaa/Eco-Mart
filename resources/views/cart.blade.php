@extends('layouts.app')

@section('title','Keranjang — EcoMart')

@section('content')
  <x-checkout-steps current="cart" />

  <nav class="text-sm text-gray-500 mb-6 mt-4">
    <ol class="flex items-center gap-2">
      <li><a href="{{ route('home') }}" class="hover:underline">Beranda</a></li>
      <li>›</li>
      <li><a href="{{ route('product.index') }}" class="hover:underline">Belanja</a></li>
      <li>›</li>
      <li class="text-gray-800 font-medium">Keranjang</li>
    </ol>
  </nav>

  <h1 class="text-2xl font-semibold mb-1 text-emerald-900">Keranjang Belanja</h1>
  <p class="text-gray-600 mb-6">Tinjau item kamu sebelum melanjutkan ke pembayaran.</p>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-4">
      @php $hasItems = isset($items) && count($items); @endphp

      @if($hasItems)
        @foreach($items as $item)
          @php
            $p    = $item->product;
            $unit = (float)($p->price ?? 0);
            $qty  = (int)$item->quantity;
            $line = $unit * $qty;
            $img = $p->image;
            if (!$img) {
              $img = 'https://via.placeholder.com/160x160';
            } elseif (!filter_var($img, FILTER_VALIDATE_URL)) {
              $img = asset('storage/'.$img);
            }
          @endphp

          <div class="border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex items-center gap-4">
                <a href="{{ route('products.show', ['product'=>$p->slug]) }}" class="w-16 h-16 rounded-md bg-gray-100 overflow-hidden flex items-center justify-center shrink-0">
                  <img src="{{ $img }}" alt="{{ $p->name }}" class="object-cover w-full h-full">
                </a>
                <div>
                  <a href="{{ route('products.show', ['product'=>$p->slug]) }}" class="font-medium text-gray-900 hover:text-emerald-800">
                    {{ $p->name }}
                  </a>
                  <p class="text-sm text-gray-600">
                    {{ $p->category?->name ?? 'Tanpa Kategori' }} ·
                    <span class="font-medium text-gray-900">Rp {{ number_format($unit,0,',','.') }}</span>
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-4 sm:justify-end">
                <div class="flex items-center gap-2">
                  <form action="{{ route('cart.update',$item) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="dec">
                    <button class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-50 disabled:opacity-40" title="Kurangi" {{ $qty <= 1 ? 'disabled' : '' }}>−</button>
                  </form>

                  <form action="{{ route('cart.update',$item) }}" method="POST" class="w-14 qty-set-form">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="set">
                    <input name="quantity" type="number" min="1" value="{{ $qty }}" class="w-full h-9 text-center border rounded qty-input focus:outline-none focus:ring-2 focus:ring-emerald-600">
                  </form>

                  <form action="{{ route('cart.update',$item) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="inc">
                    <button class="w-8 h-8 rounded border flex items-center justify-center hover:bg-gray-50" title="Tambah">+</button>
                  </form>
                </div>

                <div class="w-28 text-right font-semibold text-gray-900">
                  Rp {{ number_format($line,0,',','.') }}
                </div>

                <form action="{{ route('cart.remove',$item) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?')">
                  @csrf @method('DELETE')
                  <button class="text-gray-400 hover:text-red-600" title="Hapus">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8a1 1 0 001-1V5a1 1 0 00-1-1h-3m-4 0H8a1 1 0 00-1 1v1"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div class="bg-white border rounded-xl p-10 text-center text-gray-600">
          <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">🛒</div>
          <h3 class="font-semibold text-lg text-emerald-900">Keranjang kamu kosong</h3>
          <p class="text-sm mt-1">Jelajahi produk ramah lingkungan dan mulai tambahkan ke keranjang.</p>
          <a href="{{ route('product.index') }}" class="inline-block mt-4 px-4 py-2 rounded-lg bg-emerald-700 text-white hover:bg-emerald-800">
            Belanja Sekarang
          </a>
        </div>
      @endif
    </div>

    <aside class="lg:sticky lg:top-20 border rounded-xl p-6 bg-white h-fit shadow-sm">
      <h3 class="text-lg font-semibold mb-4 text-emerald-900">Ringkasan Pesanan</h3>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span>Rp {{ number_format($subtotal ?? 0,0,',','.') }}</span>
        </div>
        <div class="flex justify-between">
          <span>Ongkir</span>
          <span>{{ ($shipping ?? 0) > 0 ? 'Rp '.number_format($shipping,0,',','.') : 'Gratis' }}</span>
        </div>

        @if(isset($discount) && $discount>0)
          <div class="flex justify-between text-emerald-700">
            <span>Diskon ({{ session('cart_code') }})</span>
            <span>- Rp {{ number_format($discount,0,',','.') }}</span>
          </div>
        @endif
      </div>

      <form action="{{ route('cart.apply-code') }}" method="POST" class="mt-4 flex gap-2">
        @csrf
        <input type="text" name="code" placeholder="Masukkan kode (ECO10 / FREESHIP)"
               value="{{ session('cart_code') }}"
               class="flex-1 border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600">
        <button class="bg-emerald-700 text-white px-4 py-2 rounded text-sm hover:bg-emerald-800">Terapkan</button>
      </form>

      <div class="flex justify-between items-center mt-4 pt-4 border-t">
        <span class="text-gray-600">Total</span>
        <span class="text-2xl font-semibold text-gray-900">
          Rp {{ number_format($total ?? (($subtotal ?? 0)+($shipping ?? 0)-($discount ?? 0)),0,',','.') }}
        </span>
      </div>

      <a href="{{ route('checkout') }}" class="block w-full text-center bg-emerald-700 text-white py-3 rounded-lg mt-4 hover:bg-emerald-800">
        Lanjut ke Pembayaran
      </a>
      <a href="{{ route('product.index') }}" class="block text-center text-sm text-gray-600 hover:underline mt-2">
        Lanjut Belanja
      </a>
    </aside>
  </div>

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

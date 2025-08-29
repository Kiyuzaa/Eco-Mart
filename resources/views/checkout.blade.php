<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Checkout — EcoMart</title>

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @else
    <link rel="preconnect" href="https://fonts.bunny.net"/>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
  @endif

  <style>
    html,body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px}
    .ipt{width:100%;border:1px solid #e5e7eb;border-radius:10px;padding:.65rem .75rem}
    .label{font-size:.875rem;color:#374151;margin-bottom:.25rem}
    .radio{display:flex;align-items:center;gap:.75rem;border:1px solid #e5e7eb;border-radius:10px;padding:.7rem .8rem}
    .badge{font-size:.75rem;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:.25rem .5rem;color:#6b7280}
  </style>
</head>
<body class="bg-gray-50">

  {{-- NAVBAR dari komponen --}}
  <x-navbar />

  {{-- STEPPER tepat di bawah navbar --}}
  <x-checkout-steps current="checkout" />

  <main class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KIRI: FORM CHECKOUT (satu-satunya form) --}}
    <form id="checkout-form" action="{{ route('checkout.place') }}" method="POST" class="card p-5 space-y-5 lg:col-span-2">
      @csrf

      <h2 class="text-lg font-semibold">Data Pengiriman</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <div class="label">Nama Lengkap</div>
          <input name="name" class="ipt" placeholder="Masukkan nama lengkap" value="{{ old('name') }}">
        </div>
        <div>
          <div class="label">Nomor Telepon</div>
          <input name="phone" class="ipt" placeholder="+62 812 3456 7890" value="{{ old('phone') }}">
        </div>
        <div class="sm:col-span-2">
          <div class="label">Alamat Lengkap</div>
          <textarea name="shipping_address" class="ipt" rows="3" placeholder="Masukkan alamat lengkap" required>{{ old('shipping_address') }}</textarea>
          @error('shipping_address')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        {{-- field kota/kode pos hanya untuk UI (tidak divalidasi di controller) --}}
        <div>
          <div class="label">Kota</div>
          <input name="city_ui" class="ipt" placeholder="Kota" value="{{ old('city_ui') }}">
        </div>
        <div>
          <div class="label">Kode Pos</div>
          <input name="postal_ui" class="ipt" placeholder="12345" value="{{ old('postal_ui') }}">
        </div>
      </div>

      <div class="space-y-3">
        <div class="label">Opsi Pengiriman</div>
        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="shipping_method" value="regular" {{ old('shipping_method','regular')==='regular'?'checked':'' }}>
            <span>
              <div class="font-medium">Reguler (3–5 hari)</div>
              <div class="text-sm text-gray-500">Rp 15.000</div>
            </span>
          </span>
        </label>
        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="shipping_method" value="express" {{ old('shipping_method')==='express'?'checked':'' }}>
            <span>
              <div class="font-medium">Express (1–2 hari)</div>
              <div class="text-sm text-gray-500">Rp 25.000</div>
            </span>
          </span>
        </label>
      </div>

      <div class="space-y-3">
        <div class="label">Metode Pembayaran</div>
        <label class="radio">
          <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method','bank_transfer')==='bank_transfer'?'checked':'' }}>
          {{-- Heroicon: Banknotes --}}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M2.25 12c0-2.347 1.903-4.25 4.25-4.25h11a4.25 4.25 0 010 8.5h-11A4.25 4.25 0 012.25 12zm13.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
          </svg>
          <span>Transfer Bank</span>
        </label>
        <label class="radio">
          <input type="radio" name="payment_method" value="ewallet" {{ old('payment_method')==='ewallet'?'checked':'' }}>
          {{-- Heroicon: DevicePhoneMobile --}}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M10.5 1.5h3A2.25 2.25 0 0115.75 3.75v16.5A2.25 2.25 0 0113.5 22.5h-3A2.25 2.25 0 018.25 20.25V3.75A2.25 2.25 0 0110.5 1.5z"/>
          </svg>
          <span>E-Wallet</span>
        </label>
        <label class="radio">
          <input type="radio" name="payment_method" value="cod" {{ old('payment_method')==='cod'?'checked':'' }}>
          {{-- Heroicon: CurrencyDollar --}}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M12 6v12m0-12a4.5 4.5 0 010 9m0-9a4.5 4.5 0 000 9"/>
          </svg>
          <span>Bayar di Tempat (COD)</span>
        </label>
        @error('payment_method')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
      </div>

      {{-- Tombol untuk layar kecil; di desktop kita pakai tombol kanan --}}
      <div class="lg:hidden">
        <button class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg">
          🔒 Bayar Sekarang
        </button>
        <div class="text-xs text-gray-500 text-center mt-2">Pembayaran aman dan terenkripsi</div>
      </div>
    </form>

    {{-- KANAN: RINGKASAN PESANAN --}}
    <aside class="card p-5 space-y-4 h-fit">
      <h2 class="text-lg font-semibold">Ringkasan Pesanan</h2>

      <div class="divide-y">
        @foreach($cart->items as $it)
          @php
            $p = $it->product;
            $line = (float)($p->price ?? 0) * (int)$it->quantity;
          @endphp
          <div class="py-3 flex items-start justify-between gap-3">
            <div class="flex items-start gap-2">
              <span class="badge">Produk</span>
              <div>
                <div class="font-medium">{{ $p->name ?? $p->title ?? 'Produk' }}</div>
                <div class="text-sm text-gray-500">Kuantitas: {{ $it->quantity }}</div>
              </div>
            </div>
            <div class="font-medium whitespace-nowrap">Rp {{ number_format($line,0,',','.') }}</div>
          </div>
        @endforeach
      </div>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span id="subtotal-text">Rp {{ number_format($subtotal,0,',','.') }}</span>
        </div>
        <div class="flex justify-between">
          <span>Ongkos Kirim</span>
          <span id="shipping-text"
                data-regular="15000"
                data-express="25000">Rp {{ number_format($shippingCost,0,',','.') }}</span>
        </div>
        <div class="flex justify-between">
          <span>Pajak</span>
          <span id="tax-text">Rp {{ number_format($tax,0,',','.') }}</span>
        </div>
        <div class="flex justify-between font-semibold text-base pt-2 border-t">
          <span>Total</span>
          <span id="total-text"
                data-subtotal="{{ (int)$subtotal }}"
                data-tax="{{ (int)$tax }}">Rp {{ number_format($total,0,',','.') }}</span>
        </div>
      </div>

      {{-- TOMBOL KANAN: submit form kiri --}}
      <button form="checkout-form"
              class="w-full bg-gray-900 text-white py-3 rounded-lg hover:bg-gray-800 font-semibold flex items-center justify-center gap-2">
        {{-- Heroicon outline: LockClosed --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3m-.75 0h10.5a.75.75 0 01.75.75v7.5A2.25 2.25 0 0115.75 21H8.25A2.25 2.25 0 016 18.75v-7.5a.75.75 0 01.75-.75z" />
        </svg>
        Bayar Sekarang
      </button>
      <div class="text-[11px] text-gray-500 text-center">Pembayaran aman dan terenkripsi</div>
    </aside>
  </main>

  <footer class="text-center text-gray-400 py-8 border-t">
    © {{ date('Y') }} EcoMart. Semua hak dilindungi.
  </footer>

  {{-- Sinkronisasi angka saat opsi pengiriman berubah (preview sisi kanan) --}}
  <script>
    (function(){
      const shipText = document.getElementById('shipping-text');
      const totalText = document.getElementById('total-text');

      if (!shipText || !totalText) return;

      const radios = document.querySelectorAll('input[name="shipping_method"]');
      const fmt = (n)=> new Intl.NumberFormat('id-ID').format(n);

      const baseSubtotal = parseInt(totalText.dataset.subtotal || '0', 10);
      const baseTax      = parseInt(totalText.dataset.tax || '0', 10);

      function updateTotal() {
        const val = document.querySelector('input[name="shipping_method"]:checked')?.value || 'regular';
        const ship = parseInt(shipText.dataset[val] || '0', 10);

        shipText.textContent  = 'Rp ' + fmt(ship);
        const total = baseSubtotal + baseTax + ship;
        totalText.textContent = 'Rp ' + fmt(total);
      }

      radios.forEach(r => r.addEventListener('change', updateTotal));
      updateTotal(); // init
    })();
  </script>
</body>
</html>
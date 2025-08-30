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
    .ipt:focus{outline:none;box-shadow:0 0 0 2px rgb(5 150 105 / .35);border-color:#10b981}
    .label{font-size:.875rem;color:#374151;margin-bottom:.25rem}
    .radio{display:flex;align-items:center;gap:.75rem;border:1px solid #e5e7eb;border-radius:10px;padding:.7rem .8rem}
    .radio input{accent-color:#10b981}
    .badge{font-size:.75rem;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:.25rem .5rem;color:#6b7280}
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <x-navbar />
  <x-checkout-steps current="checkout" />

  <main class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <form id="checkout-form" action="{{ route('checkout.place') }}" method="POST" class="card p-5 space-y-5 lg:col-span-2" novalidate>
      @csrf

      <h2 class="text-lg font-semibold text-emerald-900">Data Pengiriman</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="label" for="name">Nama Lengkap <span class="text-red-600">*</span></label>
          <input id="name" name="name" class="ipt" placeholder="Masukkan nama lengkap" value="{{ old('name', $user->name) }}" required>
          @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="label" for="phone">Nomor Telepon <span class="text-red-600">*</span></label>
          <input id="phone" name="phone" class="ipt" placeholder="+62 812 3456 7890" value="{{ old('phone', $user->phone) }}" required>
          @error('phone')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="sm:col-span-2">
          <label class="label" for="shipping_address">Alamat Lengkap <span class="text-red-600">*</span></label>
          <textarea id="shipping_address" name="shipping_address" class="ipt" rows="3" placeholder="Nama jalan, RT/RW, kecamatan, kota/kabupaten, provinsi" required>{{ old('shipping_address') }}</textarea>
          @error('shipping_address')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
          <label class="label" for="city_ui">Kota <span class="text-red-600">*</span></label>
          <input id="city_ui" name="city_ui" class="ipt" placeholder="Kota" value="{{ old('city_ui') }}" required>
          @error('city_ui')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="label" for="postal_ui">Kode Pos <span class="text-red-600">*</span></label>
          <input id="postal_ui" name="postal_ui" class="ipt" placeholder="12345" value="{{ old('postal_ui') }}"
                 inputmode="numeric" pattern="\d{5}" minlength="5" maxlength="5" required>
          <div class="text-xs text-gray-500 mt-1">Masukkan 5 digit angka.</div>
          @error('postal_ui')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Opsi Pengiriman --}}
      <div class="space-y-3">
        <div class="label">Opsi Pengiriman</div>
        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="shipping_method" value="regular" {{ old('shipping_method','regular')==='regular'?'checked':'' }} required>
            <span>
              <div class="font-medium">Reguler (3–5 hari)</div>
              <div class="text-sm text-gray-500">Rp 15.000</div>
            </span>
          </span>
        </label>
        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="shipping_method" value="express" {{ old('shipping_method')==='express'?'checked':'' }} required>
            <span>
              <div class="font-medium">Express (1–2 hari)</div>
              <div class="text-sm text-gray-500">Rp 25.000</div>
            </span>
          </span>
        </label>
        @error('shipping_method')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
      </div>

      {{-- Metode Pembayaran --}}
      <div class="space-y-3">
        <div class="label">Metode Pembayaran <span class="text-red-600">*</span></div>

        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="payment_method" value="bank_transfer"
                   {{ old('payment_method','bank_transfer')==='bank_transfer'?'checked':'' }} required>
            <span>
              <div class="font-medium">Bank Transfer</div>
              <div class="text-sm text-gray-500">BCA • BNI • BRI • Mandiri</div>
            </span>
          </span>
        </label>

        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="payment_method" value="ewallet"
                   {{ old('payment_method')==='ewallet'?'checked':'' }} required>
            <span>
              <div class="font-medium">E-Wallet</div>
              <div class="text-sm text-gray-500">Dana • OVO • GoPay • ShopeePay</div>
            </span>
          </span>
        </label>

        <label class="radio justify-between">
          <span class="flex items-center gap-3">
            <input type="radio" name="payment_method" value="cod"
                   {{ old('payment_method')==='cod'?'checked':'' }} required>
            <span>
              <div class="font-medium">COD (Bayar di Tempat)</div>
              <div class="text-sm text-gray-500">Bayar saat barang diterima</div>
            </span>
          </span>
        </label>

        @error('payment_method')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
      </div>

      {{-- Gunakan Poin --}}
      @php
        $availablePoints = (int) (auth()->user()->available_points ?? 0);
        $minRedeem = (int) config('ecomart.points.min_redeem', 100);
        $conversion= (int) config('ecomart.points.conversion_value', 100);
        $maxPct    = (int) config('ecomart.points.max_percentage_discount', 50);
      @endphp

      <div class="space-y-2">
        <div class="label">Gunakan Poin</div>
        @if($availablePoints > 0)
          <div class="flex items-start gap-3">
            <input type="number" name="redeem_points" id="redeem_points"
                   class="ipt w-40"
                   min="0" step="{{ $minRedeem }}" max="{{ $availablePoints }}"
                   value="{{ old('redeem_points', 0) }}" placeholder="0">
            <div class="text-sm text-gray-500">
              Poin tersedia: <span class="font-semibold">{{ number_format($availablePoints,0,',','.') }}</span><br>
              {{ $minRedeem }} poin = Rp{{ number_format($minRedeem*$conversion,0,',','.') }}<br>
              Maks diskon: {{ $maxPct }}% dari total belanja
            </div>
          </div>
          @error('redeem_points')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        @else
          <div class="text-sm text-gray-500">Kamu belum memiliki poin untuk ditukarkan.</div>
        @endif
      </div>

      <div class="lg:hidden">
        <button class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold py-3 rounded-lg">
          🔒 Bayar Sekarang
        </button>
        <div class="text-xs text-gray-500 text-center mt-2">Pembayaran aman dan terenkripsi</div>
      </div>
    </form>

    {{-- Ringkasan Pesanan --}}
    <aside class="card p-5 space-y-4 h-fit">
      <h2 class="text-lg font-semibold text-emerald-900">Ringkasan Pesanan</h2>

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
                <div class="text-sm text-gray-500">Qty: {{ $it->quantity }}</div>
              </div>
            </div>
            <div class="font-medium whitespace-nowrap">Rp {{ number_format($line,0,',','.') }}</div>
          </div>
        @endforeach
      </div>

      <div class="space-y-2 text-sm">
        <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($subtotal,0,',','.') }}</span></div>
        <div class="flex justify-between"><span>Ongkos Kirim</span><span>Rp {{ number_format($shippingCost,0,',','.') }}</span></div>
        <div class="flex justify-between"><span>Pajak</span><span>Rp {{ number_format($tax,0,',','.') }}</span></div>
        <div class="flex justify-between"><span>Diskon Kupon</span><span>- Rp {{ number_format($discount,0,',','.') }}</span></div>
        <div class="flex justify-between"><span>Diskon Poin</span><span id="points-discount">—</span></div>
        <div class="flex justify-between font-semibold text-base pt-2 border-t">
          <span>Total</span>
          <span>Rp {{ number_format($total,0,',','.') }}</span>
        </div>
      </div>

      <button form="checkout-form"
              class="w-full bg-emerald-700 text-white py-3 rounded-lg hover:bg-emerald-800 font-semibold flex items-center justify-center gap-2">
        Bayar Sekarang
      </button>
      <div class="text-[11px] text-gray-500 text-center">Pembayaran aman dan terenkripsi</div>
    </aside>
  </main>

  <x-footer />
</body>
</html>

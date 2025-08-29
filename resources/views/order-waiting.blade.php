<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Pesanan Diproses — EcoMart</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50" style="font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial">
  <x-navbar />
  <x-checkout-steps current="order" />

  <div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="max-w-xl text-center bg-white border rounded-2xl p-8">
      {{-- Heroicon: Clock --}}
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 mx-auto text-gray-700 mb-3">
        <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zM12.75 6a.75.75 0 00-1.5 0v6a.75.75 0 00.33.62l3.75 2.5a.75.75 0 10.84-1.24l-3.42-2.28V6z" clip-rule="evenodd"/>
      </svg>
      <h1 class="text-2xl font-semibold">Pesananmu Sedang Diproses</h1>
      <p class="text-gray-600 mt-2">Terima kasih telah berbelanja di EcoMart.</p>

      <div class="mt-6 p-4 bg-gray-50 border rounded-xl text-left">
        <div class="text-sm text-gray-500">Nomor Pesanan</div>
        <div class="font-mono font-semibold text-lg">{{ $order->id ?? '—' }}</div>
        <div class="mt-2 text-sm text-gray-500">Total</div>
        <div class="font-semibold text-lg">Rp {{ number_format($order->total_price ?? 0,0,',','.') }}</div>
        <div class="mt-2 text-sm text-gray-500">Metode Pembayaran</div>
        <div class="font-medium">{{ strtoupper(str_replace('_',' ', $order->payment_method ?? 'BANK_TRANSFER')) }}</div>
      </div>

      <div class="mt-6 flex gap-3 justify-center">
        <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white">Kembali ke Beranda</a>
        <a href="{{ url('/account/orders') }}" class="px-5 py-2.5 rounded-lg border text-gray-700">Riwayat Pesanan</a>
      </div>
    </div>
  </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Pesanan Diproses — EcoMart</title>

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @else
    <link rel="preconnect" href="https://fonts.bunny.net"/>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
  @endif

  <style>
    body{font-family:Figtree,system-ui,Segoe UI,Roboto,Helvetica,Arial}
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <x-navbar />
  <x-checkout-steps current="order" />

  <main class="min-h-[60vh] flex items-center justify-center px-4 py-10">
    <section class="w-full max-w-xl text-center bg-white border rounded-2xl p-8">
      {{-- Ikon Jam --}}
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
           fill="currentColor" class="w-12 h-12 mx-auto text-emerald-700 mb-3">
        <path fill-rule="evenodd"
              d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zM12.75 6a.75.75 0 00-1.5 0v6a.75.75 0 00.33.62l3.75 2.5a.75.75 0 10.84-1.24l-3.42-2.28V6z"
              clip-rule="evenodd"/>
      </svg>

      <h1 class="text-2xl md:text-3xl font-bold text-emerald-900">Pesanan Sedang Diproses</h1>
      <p class="text-gray-600 mt-2">Terima kasih telah berbelanja di EcoMart. Kami akan mengonfirmasi pembayaran/penyiapan pesananmu secepatnya.</p>

      <div class="mt-6 p-4 bg-gray-50 border rounded-xl text-left">
        <div class="text-sm text-gray-500">Nomor Pesanan</div>
        <div class="font-mono font-semibold text-lg">#{{ $order->id ?? '—' }}</div>

        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
          <div>
            <div class="text-gray-500">Total</div>
            {{-- SELARASKAN DENGAN MODEL ORDER KITA: gunakan $order->total --}}
            <div class="font-semibold">Rp {{ number_format($order->total ?? 0,0,',','.') }}</div>
          </div>
          <div>
            <div class="text-gray-500">Metode Pembayaran</div>
            <div class="font-medium">
              {{ strtoupper(str_replace('_',' ', $order->payment_method ?? 'BANK_TRANSFER')) }}
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex flex-wrap gap-3 justify-center">
        <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white">
          Kembali ke Beranda
        </a>
        {{-- Sesuaikan ke route riwayat pesananmu (mis. orders.index / profile.orders) --}}
        <a href="{{ route('profile') }}" class="px-5 py-2.5 rounded-lg border hover:bg-gray-50">
          Lihat Riwayat Pesanan
        </a>
      </div>
    </section>
  </main>

  <x-footer />
</body>
</html>

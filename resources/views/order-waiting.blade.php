@extends('layouts.app')

@section('title','Pesanan Diproses — EcoMart')

@section('content')
  <x-checkout-steps current="order" />

  <main class="min-h-[60vh] flex items-center justify-center px-4 py-10">
    <section class="w-full max-w-xl text-center bg-white border rounded-2xl p-8">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
           fill="currentColor" class="w-12 h-12 mx-auto text-emerald-700 mb-3">
        <path fill-rule="evenodd"
              d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zM12.75 6a.75.75 0 00-1.5 0v6a.75.75 0 00.33.62l3.75 2.5a.75.75 0 10.84-1.24l-3.42-2.28V6z"
              clip-rule="evenodd"/>
      </svg>

      <h1 class="text-2xl md:text-3xl font-bold text-emerald-900">Pesanan Sedang Diproses</h1>
      <p class="text-gray-600 mt-2">
        Terima kasih telah berbelanja di EcoMart. Kami akan memproses pesananmu secepatnya.
      </p>

      <div class="mt-6 p-4 bg-gray-50 border rounded-xl text-left">
        <div class="text-sm text-gray-500">Nomor Pesanan</div>
        <div class="font-mono font-semibold text-lg">#{{ $order->id ?? '—' }}</div>

        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
          <div>
            <div class="text-gray-500">Total</div>
            <div class="font-semibold">
              Rp {{ number_format((int)($order->total_price ?? 0), 0, ',', '.') }}
            </div>
          </div>
          <div>
            <div class="text-gray-500">Metode Pembayaran</div>
            <div class="font-medium">
              {{ strtoupper(str_replace('_',' ', $order->payment_method ?? 'BANK_TRANSFER')) }}
            </div>
          </div>
        </div>

        @if(!empty($order->shipping_address))
          <div class="mt-3 text-sm">
            <div class="text-gray-500">Alamat Pengiriman</div>
            <pre class="whitespace-pre-wrap font-sans">{{ $order->shipping_address }}</pre>
          </div>
        @endif
      </div>

      <div class="mt-6 flex flex-wrap gap-3 justify-center">
        <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white">
          Kembali ke Beranda
        </a>

        <a href="{{ url('/profile') }}" class="px-5 py-2.5 rounded-lg border hover:bg-gray-50">
          Lihat Profil
        </a>
      </div>
    </section>
  </main>
@endsection

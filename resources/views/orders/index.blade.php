@extends('layouts.app')

@section('title','Daftar Pesanan — EcoMart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-8">
  <h1 class="text-2xl font-semibold mb-4">Daftar Pesanan</h1>

  <div class="space-y-4">
    @forelse($orders as $order)
      <div class="p-4 rounded-lg border bg-white">
        <div class="flex items-center justify-between">
          <div>
            <div class="font-semibold">Pesanan #{{ $order->id }}</div>
            <div class="text-sm text-gray-600">
              {{ $order->created_at->format('d M Y') }} · Status: {{ ucfirst($order->status) }}
            </div>
          </div>
          <div class="text-right">
            <div class="font-semibold">Rp {{ number_format($order->total_price,0,',','.') }}</div>
          </div>
        </div>
      </div>
    @empty
      <p class="text-gray-600">Belum ada pesanan.</p>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $orders->links() }}
  </div>
</div>
@endsection

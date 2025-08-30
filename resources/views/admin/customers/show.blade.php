@extends('admin.layout')

@section('title', 'Pelanggan — '.$user->name)
@section('header-title', 'Detail Pelanggan')
@section('header-subtitle', 'Riwayat pembelian '.$user->name)

@section('content')
  {{-- Kartu Header --}}
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-4">
        <img class="w-12 h-12 rounded-full border border-slate-200"
             src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=10b981&color=fff" alt="">
        <div>
          <div class="text-lg font-semibold text-slate-800">{{ $user->name }}</div>
          <div class="text-sm text-slate-600">{{ $user->email }}</div>
          <div class="text-xs text-slate-500">Bergabung: {{ $user->created_at?->format('d M Y') }}</div>
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3 text-center">
        <div class="bg-slate-50 rounded-lg p-3">
          <div class="text-xs text-slate-500">Pesanan</div>
          <div class="text-lg font-semibold text-slate-800">{{ $stats['orders_count'] }}</div>
        </div>
        <div class="bg-slate-50 rounded-lg p-3">
          <div class="text-xs text-slate-500">Total Belanja</div>
          <div class="text-lg font-semibold text-slate-800">$
            {{ number_format($stats['spent_total'], 2) }}
          </div>
        </div>
        <div class="bg-slate-50 rounded-lg p-3">
          <div class="text-xs text-slate-500">Pesanan Terakhir</div>
          <div class="text-sm font-medium text-slate-800">
            {{ $stats['last_order_at']?->format('d M Y') ?? '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabel Pesanan --}}
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-slate-800">Riwayat Pembelian</h3>
      {{-- opsional: filter/status/rentang tanggal bisa ditambahkan di sini --}}
    </div>

    @if($orders->count())
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-5 py-3 text-left font-semibold">No. Pesanan</th>
              <th class="px-5 py-3 text-left font-semibold">Tanggal</th>
              <th class="px-5 py-3 text-left font-semibold">Item</th>
              <th class="px-5 py-3 text-left font-semibold">Status</th>
              <th class="px-5 py-3 text-left font-semibold">Total</th>
              <th class="px-5 py-3 text-left font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($orders as $o)
              @php
                $itemsCount = $o->items->sum('qty');
              @endphp
              <tr class="hover:bg-slate-50/60 align-top">
                <td class="px-5 py-3 font-medium text-slate-800">#{{ $o->id }}</td>
                <td class="px-5 py-3 text-slate-700">{{ $o->created_at?->format('d M Y, H:i') }}</td>
                <td class="px-5 py-3 text-slate-700">
                  <ul class="space-y-1">
                    @foreach($o->items->take(3) as $it)
                      <li class="flex items-center gap-2">
                        <span class="text-slate-800">{{ $it->product?->name ?? 'Produk tidak diketahui' }}</span>
                        <span class="text-xs text-slate-500">×{{ $it->qty }}</span>
                      </li>
                    @endforeach
                    @if($o->items->count() > 3)
                      <li class="text-xs text-slate-500">+ {{ $o->items->count() - 3 }} item lainnya…</li>
                    @endif
                  </ul>
                </td>
                <td class="px-5 py-3">
                  @php
                    $status = strtolower($o->status ?? 'pending');
                    $map = [
                      'pending'   => 'bg-amber-100 text-amber-700 border-amber-200',
                      'paid'      => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                      'shipped'   => 'bg-blue-100 text-blue-700 border-blue-200',
                      'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                      'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200',
                    ];
                    $labels = [
                      'pending'   => 'Menunggu',
                      'paid'      => 'Dibayar',
                      'shipped'   => 'Dikirim',
                      'completed' => 'Selesai',
                      'cancelled' => 'Dibatalkan',
                    ];
                  @endphp
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs border {{ $map[$status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    {{ $labels[$status] ?? ucfirst($status) }}
                  </span>
                </td>
                <td class="px-5 py-3 font-semibold text-slate-800">
                  ${{ number_format($o->total_price, 2) }}
                </td>
                <td class="px-5 py-3">
                  {{-- kalau punya halaman detail pesanan admin --}}
                  @if(Route::has('admin.orders.show'))
                    <a href="{{ route('admin.orders.show', $o) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                      Lihat
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-5 py-3 border-t border-slate-200 bg-white">
        {{ $orders->links() }}
      </div>
    @else
      <div class="px-6 py-10 text-center text-slate-500">Belum ada pembelian.</div>
    @endif
  </div>

  <div class="mt-4">
    <a href="{{ route('admin.customers.index') }}" class="text-emerald-600 hover:underline text-sm">&larr; Kembali ke daftar pelanggan</a>
  </div>
@endsection

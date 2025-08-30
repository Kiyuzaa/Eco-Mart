@extends('admin.layout')

@section('title','Manajemen Produk')
@section('header-title','Manajemen Produk')
@section('header-subtitle','Kelola persediaan produk Anda')

@section('header-button')
  @if(Route::has('admin.products.create'))
    <a href="{{ route('admin.products.create') }}"
       class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-xl shadow font-medium">+ Tambah Produk</a>
  @endif
@endsection

@section('content')
  {{-- Kartu tambah cepat (opsional) --}}
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6">
    <div class="px-5 py-4 flex items-center justify-between">
      <div>
        <div class="text-[15px] font-semibold text-slate-800">Tambah Produk Baru</div>
        <div class="text-xs text-slate-500">Tambah item baru ke katalog Anda dengan cepat</div>
      </div>
      @if(Route::has('admin.products.create'))
        <a href="{{ route('admin.products.create') }}" class="text-emerald-700 hover:underline font-medium">Mulai</a>
      @endif
    </div>
  </div>

  {{-- Daftar Produk --}}
  <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
      <h3 class="font-semibold text-slate-800 text-lg">Daftar Produk</h3>
      <form method="GET" class="flex items-center gap-2">
        <div class="relative">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk…"
                 class="peer h-10 w-56 md:w-72 rounded-lg border border-slate-300 bg-white px-3 pr-9 text-sm text-slate-700 placeholder:text-slate-400
                        focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"/>
          <svg xmlns="http://www.w3.org/2000/svg"
               class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400 peer-focus:text-emerald-500"
               viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                  d="m21 21-4.3-4.3m1.8-4.7a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/>
          </svg>
        </div>
        <button class="h-10 px-4 rounded-lg border border-emerald-500 text-emerald-600 text-sm font-medium hover:bg-emerald-50">Cari</button>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-5 py-3 font-semibold">Produk</th>
            <th class="text-left px-5 py-3 font-semibold">Kategori</th>
            <th class="text-left px-5 py-3 font-semibold">Harga</th>
            <th class="text-left px-5 py-3 font-semibold">Stok</th>
            <th class="text-left px-5 py-3 font-semibold">Gambar</th>
            <th class="text-left px-5 py-3 font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($products as $p)
            <tr class="hover:bg-slate-50/60">
              <td class="px-5 py-3">
                <div class="font-medium text-slate-900">{{ $p->name }}</div>
                <div class="text-slate-500 text-xs">{{ $p->slug }}</div>
              </td>
              <td class="px-5 py-3">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs text-slate-700">
                  {{ $p->category->name ?? '—' }}
                </span>
              </td>
              <td class="px-5 py-3 font-semibold text-slate-900">
                {{ function_exists('format_rupiah') ? format_rupiah($p->price) : 'Rp'.number_format($p->price,0,',','.') }}
              </td>
              <td class="px-5 py-3">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                  {{ $p->stock ?? 0 }}
                </span>
              </td>
              <td class="px-5 py-3">
                @if($p->image)
                  <img src="{{ Str::startsWith($p->image,['http','https']) ? $p->image : asset('storage/'.$p->image) }}" alt="{{ $p->name }}"
                       class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                @else
                  <span class="text-slate-400">Tidak ada gambar</span>
                @endif
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-2">
                  @if(Route::has('admin.products.edit'))
                    <a href="{{ route('admin.products.edit',$p) }}"
                       class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Ubah</a>
                  @endif
                  @if(Route::has('admin.products.destroy'))
                    <form action="{{ route('admin.products.destroy',$p) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                      @csrf @method('DELETE')
                      <button class="inline-flex items-center rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">Hapus</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Produk tidak ditemukan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($products instanceof \Illuminate\Contracts\Pagination\Paginator && $products->hasPages())
      <div class="px-5 py-3 border-t border-slate-200">
        {{ $products->withQueryString()->links() }}
      </div>
    @endif
  </div>
@endsection